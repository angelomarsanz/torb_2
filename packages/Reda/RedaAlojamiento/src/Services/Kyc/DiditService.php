<?php

/**
 * Resumen: Cliente de la API REST de Didit (https://docs.didit.me). Solo implementa los endpoints
 * documentados oficialmente que necesita el KYC:
 *  - POST /v3/session/                     crear la sesión de verificación.
 *  - GET  /v3/session/{session_id}/decision/  consultar el resultado (confirmación y consulta de respaldo).
 * Autentica con el header x-api-key. Las credenciales se leen de config('services.didit') (que sale de
 * .env) y nunca se escriben en registros, respuestas ni excepciones. No sigue redirecciones para no
 * reenviar la API key a otro host. Los errores se convierten en DiditException con mensajes seguros.
 * No modifica la base de datos: KycService decide qué hacer con los resultados.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Services\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Services\Kyc;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class DiditService
{
    private const PATRON_UUID = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';

    /**
     * Indica si están definidas todas las credenciales necesarias (API key, workflow y secreto del webhook).
     */
    public function estaConfigurado(): bool
    {
        return $this->configuracion('api_key') !== ''
            && $this->configuracion('workflow_id') !== ''
            && $this->configuracion('webhook_secret') !== ''
            && str_starts_with($this->configuracion('api_url'), 'https://');
    }

    /**
     * Secreto con el que Didit firma los webhooks.
     */
    public function secretoWebhook(): string
    {
        return $this->configuracion('webhook_secret');
    }

    /**
     * Valor del campo "environment" que Didit envía en los webhooks para el entorno configurado.
     * DIDIT_ENVIRONMENT=production corresponde a "live"; cualquier otro valor se trata como "sandbox".
     */
    public function entornoEsperado(): string
    {
        return in_array($this->configuracion('environment'), ['production', 'live'], true) ? 'live' : 'sandbox';
    }

    /**
     * Indica si un identificador tiene formato UUID (se valida antes de usarlo en una URL).
     */
    public static function esUuid(mixed $valor): bool
    {
        return is_string($valor) && preg_match(self::PATRON_UUID, $valor) === 1;
    }

    /**
     * Crea una sesión de verificación (POST /v3/session/).
     * Si ya existe una sesión sin terminar para el mismo workflow y vendor_data, Didit devuelve
     * esa misma sesión (idempotencia documentada).
     *
     * @param string               $vendorData Identificador interno estable del usuario.
     * @param string               $callback   URL a la que Didit devuelve al usuario (solo informativa).
     * @param array<string,mixed>  $metadata   Datos que Didit devuelve en los webhooks (trazabilidad).
     * @return array{session_id:string,url:string,status:string,vendor_data:?string}
     *
     * @throws DiditException
     */
    public function crearSesion(string $vendorData, string $callback, array $metadata = []): array
    {
        $this->exigirConfiguracion();

        $cuerpo = [
            'workflow_id'     => $this->configuracion('workflow_id'),
            'vendor_data'     => $vendorData,
            'callback'        => $callback,
            'callback_method' => 'both',
            'language'        => 'es',
            'metadata'        => (object) $metadata,
        ];

        $respuesta = $this->enviar(fn (PendingRequest $http) => $http->post('/v3/session/', $cuerpo), 'crear_sesion');
        $datos     = $respuesta->json();

        $sessionId = is_array($datos) ? ($datos['session_id'] ?? null) : null;
        $url       = is_array($datos) ? ($datos['url'] ?? null) : null;

        if (!self::esUuid($sessionId) || !$this->esUrlHttpsValida($url)) {
            Log::error('KYC Didit: respuesta de creación de sesión con formato inesperado (HTTP ' . $respuesta->status() . ').');
            throw new DiditException('Respuesta de Didit con formato inesperado.', $respuesta->status(), false);
        }

        return [
            'session_id'  => strtolower($sessionId),
            'url'         => $url,
            'status'      => is_string($datos['status'] ?? null) ? $datos['status'] : 'Not Started',
            'vendor_data' => is_string($datos['vendor_data'] ?? null) ? $datos['vendor_data'] : null,
        ];
    }

    /**
     * Consulta la decisión de una sesión (GET /v3/session/{session_id}/decision/).
     *
     * @return array<string,mixed> Decisión completa devuelta por Didit (no persistir tal cual).
     *
     * @throws DiditException
     */
    public function obtenerDecision(string $sessionId): array
    {
        $this->exigirConfiguracion();

        if (!self::esUuid($sessionId)) {
            throw new DiditException('Identificador de sesión inválido.', 0, false);
        }

        $respuesta = $this->enviar(
            fn (PendingRequest $http) => $http->get('/v3/session/' . strtolower($sessionId) . '/decision/'),
            'obtener_decision'
        );
        $datos = $respuesta->json();

        if (!is_array($datos)
            || !is_string($datos['status'] ?? null)
            || strtolower((string) ($datos['session_id'] ?? '')) !== strtolower($sessionId)) {
            Log::error('KYC Didit: la decisión recibida no corresponde a la sesión consultada (HTTP ' . $respuesta->status() . ').');
            throw new DiditException('Respuesta de Didit con formato inesperado.', $respuesta->status(), false);
        }

        return $datos;
    }

    /**
     * Reduce una decisión de Didit a lo mínimo necesario para auditoría: sin imágenes, datos del
     * documento, biometría ni datos personales.
     *
     * @param  array<string,mixed> $decision
     * @return array<string,mixed>
     */
    public static function sanearDecision(array $decision): array
    {
        $caracteristicas = [];

        foreach (['id_verifications', 'liveness_checks', 'face_matches', 'nfc_verifications', 'poa_verifications',
                  'database_validations', 'aml_screenings', 'phone_verifications', 'email_verifications', 'ip_analyses'] as $clave) {
            if (!isset($decision[$clave]) || !is_array($decision[$clave])) {
                continue;
            }

            $estados = [];
            foreach ($decision[$clave] as $elemento) {
                if (is_array($elemento) && isset($elemento['status']) && is_string($elemento['status'])) {
                    $estados[] = mb_substr($elemento['status'], 0, 40);
                }
            }

            $caracteristicas[$clave] = $estados;
        }

        $traza = is_array($decision['decision_trace'] ?? null) ? $decision['decision_trace'] : [];

        return [
            'estado'          => is_string($decision['status'] ?? null) ? mb_substr($decision['status'], 0, 40) : null,
            'tipo_sesion'     => is_string($decision['session_kind'] ?? null) ? mb_substr($decision['session_kind'], 0, 20) : null,
            'decidido_por'    => is_string($traza['decided_by'] ?? null) ? mb_substr($traza['decided_by'], 0, 20) : null,
            'decidido_en'     => is_string($traza['decided_at'] ?? null) ? mb_substr($traza['decided_at'], 0, 40) : null,
            'advertencias'    => is_array($traza['warning_counts'] ?? null) && is_numeric($traza['warning_counts']['total'] ?? null)
                ? (int) $traza['warning_counts']['total'] : null,
            'caracteristicas' => $caracteristicas,
        ];
    }

    /**
     * Ejecuta una petición HTTP y traduce los fallos a DiditException con mensajes seguros.
     *
     * @param callable(PendingRequest): Response $peticion
     *
     * @throws DiditException
     */
    private function enviar(callable $peticion, string $operacion): Response
    {
        $http = Http::baseUrl($this->configuracion('api_url'))
            ->withHeaders(['x-api-key' => $this->configuracion('api_key')])
            ->acceptJson()
            ->asJson()
            ->timeout(15)
            ->connectTimeout(5)
            ->withOptions(['allow_redirects' => false]);

        try {
            $respuesta = $peticion($http);
        } catch (ConnectionException $e) {
            Log::warning("KYC Didit [{$operacion}]: error de conexión.");
            throw new DiditException('No fue posible conectar con Didit.', 0, true);
        } catch (Throwable $e) {
            Log::error("KYC Didit [{$operacion}]: error inesperado (" . get_class($e) . ').');
            throw new DiditException('Error inesperado al comunicarse con Didit.', 0, false);
        }

        if ($respuesta->successful()) {
            return $respuesta;
        }

        $codigo = $respuesta->status();

        if ($codigo === 401 || $codigo === 403) {
            Log::critical("KYC Didit [{$operacion}]: credenciales rechazadas (HTTP {$codigo}). Revise DIDIT_API_KEY y sus permisos.");
            throw new DiditException('Didit rechazó las credenciales configuradas.', $codigo, false);
        }

        $reintentable = $codigo === 429 || $codigo >= 500;
        Log::warning("KYC Didit [{$operacion}]: respuesta HTTP {$codigo}.");

        throw new DiditException(
            $codigo === 404 ? 'Sesión de Didit no encontrada.' : 'Didit no pudo procesar la solicitud.',
            $codigo,
            $reintentable
        );
    }

    /**
     * Valida que haya credenciales configuradas antes de llamar a Didit.
     *
     * @throws DiditException
     */
    private function exigirConfiguracion(): void
    {
        if (!$this->estaConfigurado()) {
            Log::error('KYC Didit: faltan credenciales en .env (DIDIT_API_KEY, DIDIT_WORKFLOW_ID, DIDIT_WEBHOOK_SECRET) o DIDIT_API_URL no es https.');
            throw new DiditException('La integración con Didit no está configurada.', 0, false);
        }
    }

    /**
     * Comprueba que la URL de verificación sea https y tenga host.
     */
    private function esUrlHttpsValida(mixed $url): bool
    {
        if (!is_string($url) || strlen($url) > 2048) {
            return false;
        }

        $partes = parse_url($url);

        return is_array($partes)
            && ($partes['scheme'] ?? '') === 'https'
            && !empty($partes['host'])
            && !isset($partes['user'], $partes['pass']);
    }

    /**
     * Lee un valor de config('services.didit') como texto.
     */
    private function configuracion(string $clave): string
    {
        return trim((string) config('services.didit.' . $clave, ''));
    }
}
