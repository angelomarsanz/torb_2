<?php

/**
 * Resumen: Verificación criptográfica de la firma de los webhooks de Didit. Clase pura (sin acceso a
 * base de datos ni a la petición de Laravel) para poder probarla de forma aislada.
 *
 * Acepta únicamente firmas HMAC-SHA256 que autentican TODO el cuerpo:
 *  - X-Signature-V2: JSON canónico (claves ordenadas, compacto, Unicode y barras sin escapar).
 *  - X-Signature: bytes exactos del cuerpo recibido.
 * No acepta X-Signature-Simple: Didit la declara obsoleta porque solo autentica el sobre y no la decisión.
 * También comprueba que X-Timestamp esté dentro de la tolerancia (300 s por defecto) y admite las
 * cabeceras "-Previous" que Didit envía durante 24 horas tras rotar el secreto.
 * Referencia: https://docs.didit.me/integration/webhooks (sección PHP / Laravel).
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Services\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Services\Kyc;

use stdClass;

class FirmaWebhookDidit
{
    /**
     * @param string $secreto    Secreto de firma del webhook (DIDIT_WEBHOOK_SECRET).
     * @param int    $tolerancia Diferencia máxima, en segundos, entre X-Timestamp y la hora actual.
     */
    public function __construct(
        private readonly string $secreto,
        private readonly int $tolerancia = 300,
    ) {
    }

    /**
     * Verifica la firma de un webhook.
     *
     * @param string        $cuerpoCrudo Cuerpo exacto recibido (antes de cualquier decodificación).
     * @param callable      $cabecera    fn(string $nombre): ?string que devuelve una cabecera HTTP.
     * @param int|null      $ahora       Hora actual (solo para pruebas).
     * @return bool                       true únicamente si la firma y la marca de tiempo son válidas.
     */
    public function verificar(string $cuerpoCrudo, callable $cabecera, ?int $ahora = null): bool
    {
        return $this->motivoDeRechazo($cuerpoCrudo, $cabecera, $ahora) === null;
    }

    /**
     * Devuelve el motivo del rechazo o null si la firma es válida. El motivo no contiene datos
     * del cuerpo y es seguro para registros.
     */
    public function motivoDeRechazo(string $cuerpoCrudo, callable $cabecera, ?int $ahora = null): ?string
    {
        if ($this->secreto === '') {
            return 'secreto_no_configurado';
        }

        $marca = $cabecera('x-timestamp');
        if (!is_string($marca) || !ctype_digit($marca)) {
            return 'timestamp_ausente_o_invalido';
        }

        if (abs(($ahora ?? time()) - (int) $marca) > $this->tolerancia) {
            return 'timestamp_fuera_de_tolerancia';
        }

        $firmasV2  = $this->firmasRecibidas($cabecera, 'x-signature-v2');
        $firmasRaw = $this->firmasRecibidas($cabecera, 'x-signature');

        if (empty($firmasV2) && empty($firmasRaw)) {
            return 'firma_ausente';
        }

        if (!empty($firmasV2)) {
            $esperadaV2 = $this->firmaV2($cuerpoCrudo);
            foreach ($firmasV2 as $firma) {
                if ($esperadaV2 !== null && hash_equals($esperadaV2, $firma)) {
                    return null;
                }
            }
        }

        if (!empty($firmasRaw)) {
            $esperadaRaw = hash_hmac('sha256', $cuerpoCrudo, $this->secreto);
            foreach ($firmasRaw as $firma) {
                if (hash_equals($esperadaRaw, $firma)) {
                    return null;
                }
            }
        }

        return 'firma_invalida';
    }

    /**
     * Calcula la firma V2 de un cuerpo (JSON canónico). Devuelve null si el cuerpo no es JSON válido.
     */
    public function firmaV2(string $cuerpoCrudo): ?string
    {
        // Decodificar como objetos: con arreglos asociativos {} pasaría a [] y cambiarían los bytes firmados.
        $decodificado = json_decode($cuerpoCrudo, false);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        // Si el servidor cambia serialize_precision, los decimales se codifican distinto y la firma fallaría.
        $precisionAnterior = ini_get('serialize_precision');
        ini_set('serialize_precision', '-1');

        try {
            $canonico = json_encode(
                $this->canonicalizar($decodificado),
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            );
        } finally {
            ini_set('serialize_precision', (string) $precisionAnterior);
        }

        if ($canonico === false) {
            return null;
        }

        return hash_hmac('sha256', $canonico, $this->secreto);
    }

    /**
     * Reconstruye la forma canónica de Didit: claves ordenadas recursivamente, objetos JSON
     * conservados como objetos y listas en su orden original.
     */
    private function canonicalizar(mixed $valor): mixed
    {
        if ($valor instanceof stdClass) {
            $propiedades = get_object_vars($valor);
            ksort($propiedades, SORT_STRING);

            $ordenado = new stdClass();
            foreach ($propiedades as $clave => $elemento) {
                $ordenado->{$clave} = $this->canonicalizar($elemento);
            }

            return $ordenado;
        }

        if (is_array($valor)) {
            return array_map(fn ($elemento) => $this->canonicalizar($elemento), $valor);
        }

        return $valor;
    }

    /**
     * Firmas presentes en la cabecera y en su variante "-previous" (hexadecimal en minúsculas).
     *
     * @return array<int, string>
     */
    private function firmasRecibidas(callable $cabecera, string $nombre): array
    {
        $firmas = [];

        foreach ([$nombre, $nombre . '-previous'] as $candidato) {
            $valor = $cabecera($candidato);
            if (is_string($valor) && $valor !== '') {
                $firmas[] = strtolower(trim($valor));
            }
        }

        return $firmas;
    }
}
