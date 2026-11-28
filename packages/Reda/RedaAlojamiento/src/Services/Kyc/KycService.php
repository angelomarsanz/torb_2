<?php

/**
 * Resumen: Servicio central del KYC y ÚNICO responsable de cambiar el estado de la verificación de identidad.
 *
 * Modelo de datos:
 *  - usuarios_kyc     = estado efectivo de autorización (lo único que consulta el bloqueo de operaciones).
 *  - solicitudes_kyc  = historial de intentos; la fila activa de cada usuario es usuarios_kyc.solicitud_activa_id.
 *  - eventos_kyc      = auditoría inmutable e idempotencia de eventos de Didit.
 *
 * Garantías:
 *  - Todo cambio ocurre en una transacción que bloquea (FOR UPDATE) la fila del usuario en usuarios_kyc,
 *    de modo que dos peticiones simultáneas se serializan; solicitud, estado efectivo y evento se escriben juntos.
 *  - Una sola solicitud abierta por usuario (índice UNIQUE sobre solicitudes_kyc.clave_abierta).
 *  - Nunca se llama a Didit dentro de una transacción con bloqueo.
 *  - Solo se procesan eventos de la solicitud activa; los de sesiones anteriores o más antiguos que el último
 *    evento aplicado se registran y se ignoran.
 *  - Un resultado "Approved" solo se acepta tras comprobar sesión, usuario (vendor_data), entorno y, por defecto,
 *    confirmar la decisión directamente con la API de Didit. La redirección del navegador nunca aprueba.
 *  - El estado solo se escribe a través de la ventana GuardiaEscrituraKyc.
 *  - Verificación manual: los archivos se validan y se escriben cifrados FUERA de la transacción y, si la operación
 *    no se completa (validación, conflicto o error), se borran para no dejar huérfanos. La solicitud, sus documentos
 *    y el evento se crean juntos. Una verificación Didit sin terminar se reemplaza por la manual (queda como failed
 *    en el historial y no genera un aviso intermedio). Las correcciones solicitadas por un administrador se atienden
 *    sin cambiar de estado y conservando los documentos reemplazados como historial.
 *  - Avisos: tras confirmar la transacción emite EstadoKycCambiado (cambio de estado), CorreccionKycSolicitada (el
 *    administrador pide una corrección), CorreccionKycAtendida (el usuario la responde) y VerificacionKycEliminada
 *    (un administrador elimina la verificación). NotificacionesKycService los escucha; este servicio no envía
 *    correos ni alertas.
 *  - Eliminación (eliminarVerificacion): un administrador con manage_kyc puede borrar por completo la verificación
 *    de un usuario (solicitudes con sus datos personales, documentos y archivos) y dejarlo en not_started para que
 *    vuelva a verificarse. Se conserva la auditoría sin datos personales y un evento "kyc_eliminado" con el motivo.
 *
 * Todas las operaciones públicas devuelven la estructura estándar del plugin:
 * ['success','message','mensaje_usuario','respuesta','code'].
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Services\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Services\Kyc;

use App\Models\Admin;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Reda\RedaAlojamiento\Enums\EstadoKyc;
use Reda\RedaAlojamiento\Enums\MetodoKyc;
use Reda\RedaAlojamiento\Events\CorreccionKycAtendida;
use Reda\RedaAlojamiento\Events\CorreccionKycSolicitada;
use Reda\RedaAlojamiento\Events\EstadoKycCambiado;
use Reda\RedaAlojamiento\Events\VerificacionKycEliminada;
use Reda\RedaAlojamiento\Models\Kyc\DocumentoKyc;
use Reda\RedaAlojamiento\Models\Kyc\EventoKyc;
use Reda\RedaAlojamiento\Models\Kyc\SolicitudKyc;
use Reda\RedaAlojamiento\Models\Kyc\UsuarioKyc;
use Throwable;

class KycService
{
    /**
     * Caché en memoria del resultado de estaVerificado(): [verificado, momento de lectura]. Se invalida en cada
     * cambio de estado hecho por este servicio y caduca a los pocos segundos para no quedar obsoleta en procesos
     * de larga duración (colas, Octane) ni ante cambios hechos por otro proceso.
     *
     * @var array<int,array{0:bool,1:int}>
     */
    private array $cacheVerificado = [];

    private const VIGENCIA_CACHE_SEGUNDOS = 5;

    public function __construct(
        private readonly DiditService $didit,
        private readonly KycDocumentoService $documentos,
        private readonly ValidadorKycManual $validador,
    ) {
    }

    // ------------------------------------------------------------------
    // LECTURA
    // ------------------------------------------------------------------

    /**
     * Indica si el usuario tiene la identidad verificada y vigente. Es la única consulta que debe usar
     * el bloqueo de operaciones. Un usuario sin registro en usuarios_kyc no está verificado.
     */
    public function estaVerificado(?int $usuarioId): bool
    {
        if (!$usuarioId) {
            return false;
        }

        $guardado = $this->cacheVerificado[$usuarioId] ?? null;

        if ($guardado === null || (time() - $guardado[1]) >= self::VIGENCIA_CACHE_SEGUNDOS) {
            $registro = UsuarioKyc::where('user_id', $usuarioId)->first();
            $guardado = $this->cacheVerificado[$usuarioId] = [$registro?->esVerificado() ?? false, time()];
        }

        return $guardado[0];
    }

    /**
     * Descarta la caché en memoria de estaVerificado() (de un usuario o de todos).
     */
    public function olvidarCache(?int $usuarioId = null): void
    {
        if ($usuarioId === null) {
            $this->cacheVerificado = [];
        } else {
            unset($this->cacheVerificado[$usuarioId]);
        }
    }

    /**
     * Estado efectivo del usuario (not_started si no tiene registro).
     */
    public function estadoDe(int $usuarioId): EstadoKyc
    {
        return UsuarioKyc::where('user_id', $usuarioId)->first()?->estadoEfectivo() ?? EstadoKyc::SinIniciar;
    }

    /**
     * Última solicitud del usuario (la que originó su estado actual).
     */
    public function solicitudActiva(int $usuarioId): ?SolicitudKyc
    {
        $registro = UsuarioKyc::where('user_id', $usuarioId)->first();

        return $registro?->solicitud_activa_id ? SolicitudKyc::find($registro->solicitud_activa_id) : null;
    }

    /**
     * Resumen del estado para la interfaz del usuario. No incluye datos personales.
     *
     * @return array<string,mixed>
     */
    public function resumenParaUsuario(int $usuarioId): array
    {
        $registro  = UsuarioKyc::where('user_id', $usuarioId)->first();
        $estado    = $registro?->estadoEfectivo() ?? EstadoKyc::SinIniciar;
        $solicitud = $registro?->solicitud_activa_id ? SolicitudKyc::find($registro->solicitud_activa_id) : null;

        $documentos = [];
        if ($solicitud && $solicitud->metodo === MetodoKyc::Manual) {
            $documentos = $solicitud->documentosVigentes()->orderBy('id')->get(['id', 'tipo'])
                ->map(static fn ($d) => ['id' => (int) $d->id, 'tipo' => $d->tipo])->all();
        }

        return [
            'estado'                 => $estado->value,
            'etiqueta'               => $estado->etiqueta(),
            'verificado'             => $registro?->esVerificado() ?? false,
            'metodo'                 => $solicitud?->metodo?->value,
            'solicitud_abierta'      => $estado->esAbierto(),
            'puede_iniciar'          => in_array($estado, EstadoKyc::permitenNuevoIntento(), true)
                || ($estado === EstadoKyc::Verificado && !($registro?->esVerificado() ?? false)),
            'puede_solicitar_manual' => $this->bloqueoManual($registro, $solicitud) === null,
            'correccion_pendiente'   => $solicitud?->tieneCorreccionPendiente() ?? false,
            'motivo_rechazo'         => $estado === EstadoKyc::Rechazado ? $solicitud?->motivo_rechazo : null,
            'correccion_solicitada'  => $solicitud?->tieneCorreccionPendiente() ? $solicitud->correccion_solicitada : null,
            'documentos'             => $documentos,
        ];
    }

    // ------------------------------------------------------------------
    // VERIFICACIÓN AUTOMÁTICA (DIDIT)
    // ------------------------------------------------------------------

    /**
     * Inicia (o retoma) la verificación automática del usuario y devuelve la URL de Didit.
     *
     * Paso 1: en una transacción corta reserva la solicitud (estado in_progress).
     * Paso 2: fuera de cualquier transacción crea la sesión en Didit.
     * Paso 3: guarda el session_id. Si Didit falla, la solicitud nueva se cierra como "failed".
     *
     * @return array{success:bool,message:string,mensaje_usuario:string,respuesta:mixed,code:int}
     */
    public function iniciarDidit(int $usuarioId, ?string $ip = null): array
    {
        try {
            if (!$this->didit->estaConfigurado()) {
                Log::error('KYC: se intentó iniciar Didit sin credenciales configuradas.');

                return $this->respuesta(false, __('Didit no configurado'),
                    __('La verificación automática no está disponible en este momento. Puedes solicitar la revisión manual.'),
                    'didit_no_configurado', 503);
            }

            $reserva = $this->transaccion(fn () => $this->reservarSolicitudDidit($usuarioId, $ip));

            if (isset($reserva['error'])) {
                return $reserva['error'];
            }

            $solicitudId = $reserva['solicitud_id'];
            $esNueva     = $reserva['nueva'];
            $solicitud   = SolicitudKyc::findOrFail($solicitudId);

            try {
                $sesion = $this->didit->crearSesion(
                    (string) $solicitud->didit_vendor_data,
                    url('kyc/retorno'),
                    [
                        'solicitud_id' => $solicitud->id,
                        'usuario_id'   => $usuarioId,
                        'intento'      => $solicitud->numero_intento,
                    ]
                );
            } catch (DiditException $e) {
                if ($esNueva) {
                    $this->cerrarComoFallidaPorError($solicitudId, $ip);
                }

                return $this->respuesta(false, __('Error creando la sesión de Didit'),
                    __('No fue posible iniciar la verificación automática. Inténtalo de nuevo en unos minutos o solicita la revisión manual.'),
                    $e->reintentable ? 'didit_no_disponible' : 'didit_error', $e->reintentable ? 503 : 502);
            }

            $this->transaccion(function () use ($usuarioId, $solicitudId, $sesion, $ip) {
                $this->bloquearUsuario($usuarioId);
                $solicitud = SolicitudKyc::where('id', $solicitudId)->lockForUpdate()->firstOrFail();

                if ($solicitud->didit_session_id !== $sesion['session_id']) {
                    $solicitud->didit_session_id      = $sesion['session_id'];
                    $solicitud->didit_estado_original = mb_substr($sesion['status'], 0, 40);
                    $solicitud->save();
                }

                $this->registrarEvento([
                    'user_id'          => $usuarioId,
                    'solicitud_id'     => $solicitudId,
                    'origen'           => EventoKyc::ORIGEN_USUARIO,
                    'tipo'             => 'sesion_didit_creada',
                    'didit_session_id' => $sesion['session_id'],
                    'didit_estado'     => mb_substr($sesion['status'], 0, 40),
                    'resultado'        => EventoKyc::RESULTADO_PROCESADO,
                    'ip'               => $ip,
                ]);
            });

            return $this->respuesta(true, __('Sesión de Didit creada'),
                __('Te redirigiremos a Didit para completar la verificación de tu identidad.'),
                ['url' => $sesion['url']], 200);
        } catch (UniqueConstraintViolationException $e) {
            Log::warning('KYC: conflicto de unicidad al iniciar Didit para el usuario ' . $usuarioId . '.');

            return $this->respuesta(false, __('Conflicto al iniciar la verificación'),
                __('Ya existe una verificación en curso. Actualiza la página e inténtalo de nuevo.'), 'conflicto', 409);
        } catch (Throwable $e) {
            return $this->errorInterno($e, 'iniciarDidit');
        }
    }

    /**
     * Procesa un webhook de Didit YA AUTENTICADO (la firma debe verificarse antes, en el controlador).
     *
     * @param  array<string,mixed> $payload     Cuerpo decodificado como arreglo asociativo.
     * @param  string              $cuerpoCrudo Cuerpo exacto recibido (solo para calcular su hash de auditoría).
     * @return array{success:bool,message:string,mensaje_usuario:string,respuesta:mixed,code:int}
     */
    public function procesarWebhookDidit(array $payload, string $cuerpoCrudo, ?string $ip = null): array
    {
        try {
            $tipo = $payload['webhook_type'] ?? null;

            // Solo interesan los cambios de sesión KYC; el resto de eventos del destino se confirman sin procesar.
            if (!in_array($tipo, ['status.updated', 'data.updated'], true)
                || ($payload['session_kind'] ?? 'user') === 'business'
                || isset($payload['business_session_id'])) {
                return $this->respuesta(true, __('Evento ignorado'), '', 'tipo_no_procesable', 200);
            }

            $sessionId  = is_string($payload['session_id'] ?? null) ? strtolower($payload['session_id']) : '';
            $estadoDidit = $payload['status'] ?? null;

            if (!DiditService::esUuid($sessionId) || !is_string($estadoDidit) || $estadoDidit === '') {
                return $this->respuesta(true, __('Evento ignorado'), '', 'payload_incompleto', 200);
            }

            $eventId = is_string($payload['event_id'] ?? null) && $payload['event_id'] !== ''
                ? mb_substr($payload['event_id'], 0, 100) : null;

            $marca = is_numeric($payload['created_at'] ?? null) ? (int) $payload['created_at']
                : (is_numeric($payload['timestamp'] ?? null) ? (int) $payload['timestamp'] : time());

            $clave = $eventId
                ? 'didit:' . $eventId
                : 'didit:' . hash('sha256', implode('|', [$sessionId, $estadoDidit, $tipo, $payload['timestamp'] ?? '']));

            return $this->procesarEstadoDidit([
                'origen'            => EventoKyc::ORIGEN_DIDIT_WEBHOOK,
                'tipo'              => 'webhook:' . $tipo,
                'session_id'        => $sessionId,
                'estado_didit'      => mb_substr($estadoDidit, 0, 40),
                'vendor_data'       => is_string($payload['vendor_data'] ?? null) ? $payload['vendor_data'] : null,
                'entorno'           => is_string($payload['environment'] ?? null) ? $payload['environment'] : null,
                'orden'             => $marca,
                'clave'             => mb_substr($clave, 0, 191),
                'event_id'          => $eventId,
                'payload_hash'      => hash('sha256', $cuerpoCrudo),
                'ip'                => $ip,
                'resultado_saneado' => is_array($payload['decision'] ?? null) ? DiditService::sanearDecision($payload['decision']) : null,
                'detalle'           => array_filter([
                    'webhook_type' => $tipo,
                    'entorno'      => is_string($payload['environment'] ?? null) ? $payload['environment'] : null,
                    'trigger'      => is_string($payload['trigger'] ?? null) ? mb_substr($payload['trigger'], 0, 60) : null,
                    'creado_en'    => $marca,
                ], static fn ($v) => $v !== null),
            ]);
        } catch (Throwable $e) {
            return $this->errorInterno($e, 'procesarWebhookDidit');
        }
    }

    /**
     * Consulta de respaldo: pregunta a Didit el estado de la sesión cuando el webhook tarda en llegar.
     * Usa el mismo camino de transición que el webhook. Respeta un intervalo mínimo entre consultas.
     *
     * @return array{success:bool,message:string,mensaje_usuario:string,respuesta:mixed,code:int}
     */
    public function sincronizarConDidit(SolicitudKyc|int $solicitud, bool $forzar = false): array
    {
        try {
            $solicitud = $solicitud instanceof SolicitudKyc ? $solicitud->fresh() : SolicitudKyc::find($solicitud);

            if (!$solicitud
                || $solicitud->metodo !== MetodoKyc::Didit
                || !$solicitud->didit_session_id
                || !in_array($solicitud->estado, [EstadoKyc::EnProceso, EstadoKyc::Pendiente], true)) {
                return $this->respuesta(true, __('Consulta omitida'), '', 'no_aplica', 200);
            }

            $intervalo = (int) config('kyc.didit.intervalo_consulta_respaldo_segundos', 30);
            $ultima    = $solicitud->didit_ultima_consulta_en;

            if (!$forzar && $ultima && $ultima->diffInSeconds(now(), true) < $intervalo) {
                return $this->respuesta(true, __('Consulta omitida'), '', 'intervalo_no_cumplido', 200);
            }

            // Se marca antes de consultar para que un fallo de Didit tampoco provoque consultas en ráfaga.
            $this->transaccion(function () use ($solicitud) {
                $fila = SolicitudKyc::where('id', $solicitud->id)->lockForUpdate()->first();
                if ($fila) {
                    $fila->didit_ultima_consulta_en = now();
                    $fila->save();
                }
            });

            try {
                $decision = $this->didit->obtenerDecision($solicitud->didit_session_id);
            } catch (DiditException $e) {
                return $this->respuesta(false, __('Error consultando a Didit'), '',
                    $e->reintentable ? 'didit_no_disponible' : 'didit_error', $e->reintentable ? 503 : 502);
            }

            return $this->procesarEstadoDidit([
                'origen'            => EventoKyc::ORIGEN_DIDIT_CONSULTA,
                'tipo'              => 'consulta_decision',
                'session_id'        => strtolower($solicitud->didit_session_id),
                'estado_didit'      => mb_substr($decision['status'], 0, 40),
                'vendor_data'       => is_string($decision['vendor_data'] ?? null) ? $decision['vendor_data'] : null,
                'entorno'           => null,
                'orden'             => time(),
                'clave'             => null,
                'event_id'          => null,
                'payload_hash'      => null,
                'ip'                => null,
                'resultado_saneado' => DiditService::sanearDecision($decision),
                'detalle'           => [],
            ]);
        } catch (Throwable $e) {
            return $this->errorInterno($e, 'sincronizarConDidit');
        }
    }

    // ------------------------------------------------------------------
    // VERIFICACIÓN MANUAL
    // ------------------------------------------------------------------

    /**
     * Envía una solicitud de verificación manual con datos personales y documentos.
     *
     * Valida datos y archivos, guarda los archivos cifrados (fuera de la transacción) y, en una transacción
     * con bloqueo de la fila del usuario, crea la solicitud en estado manual_review. Si el usuario tenía una
     * verificación con Didit en proceso, esa solicitud se cierra como fallida (la manual la reemplaza).
     * Impide duplicados: no se admite una solicitud manual o en revisión mientras haya otra abierta.
     *
     * @param  array<string,mixed> $datos    Campos del formulario.
     * @param  array<string,mixed> $archivos Archivos por tipo: frente, reverso, selfie.
     * @return array{success:bool,message:string,mensaje_usuario:string,respuesta:mixed,code:int}
     */
    public function enviarManual(int $usuarioId, array $datos, array $archivos, ?string $ip = null): array
    {
        $rutas = [];

        try {
            $validacion = $this->validador->validarEnvio($datos, $archivos, $this->correoDe($usuarioId));

            if (!$validacion['ok']) {
                return $this->respuestaValidacion($validacion['errores']);
            }

            $campos = $validacion['datos'];

            $registro = UsuarioKyc::where('user_id', $usuarioId)->first();
            $activa   = $registro?->solicitud_activa_id ? SolicitudKyc::find($registro->solicitud_activa_id) : null;

            if ($error = $this->bloqueoManual($registro, $activa)) {
                return $error;
            }

            // Solo se conservan los documentos que exige el tipo elegido (minimización de datos).
            $obligatorios = $this->validador->tiposObligatorios($campos['tipo_documento']);
            $preparados   = $this->documentos->prepararArchivos(array_intersect_key($archivos, array_flip($obligatorios)));

            foreach ($preparados as $tipo => $preparado) {
                $rutas[$tipo] = $this->documentos->guardarCifrado($preparado);
            }

            $coincidencias = $this->contarCoincidenciasDocumento($usuarioId, $campos['numero_documento']);

            $resultado = $this->transaccion(function () use ($usuarioId, $campos, $preparados, $rutas, $coincidencias, $ip) {
                $registro = $this->bloquearUsuario($usuarioId);
                $activa   = $registro->solicitud_activa_id
                    ? SolicitudKyc::where('id', $registro->solicitud_activa_id)->lockForUpdate()->first() : null;

                if ($error = $this->bloqueoManual($registro, $activa)) {
                    return ['error' => $error];
                }

                $limite = (int) config('kyc.limites.envios_manuales_por_dia', 3);
                $enviosHoy = SolicitudKyc::where('user_id', $usuarioId)
                    ->where('metodo', MetodoKyc::Manual->value)
                    ->where('created_at', '>=', now()->subDay())
                    ->count();

                if ($limite > 0 && $enviosHoy >= $limite) {
                    return ['error' => $this->respuesta(false, __('Límite de envíos alcanzado'),
                        __('Has alcanzado el límite de solicitudes de verificación de hoy. Inténtalo de nuevo mañana.'), 'limite_envios', 429)];
                }

                // La verificación automática sin terminar se reemplaza por la manual (el usuario eligió la alternativa).
                if ($registro->estadoEfectivo() === EstadoKyc::EnProceso && $activa) {
                    $this->aplicarCambio($registro, $activa, EstadoKyc::Fallido, [
                        'origen'    => EventoKyc::ORIGEN_USUARIO,
                        'tipo'      => 'didit_reemplazada_por_manual',
                        'ip'        => $ip,
                        'sin_aviso' => true,
                    ]);
                }

                $solicitud = SolicitudKyc::create([
                    'user_id'        => $usuarioId,
                    'metodo'         => MetodoKyc::Manual,
                    'estado'         => EstadoKyc::SinIniciar,
                    'numero_intento' => ((int) SolicitudKyc::where('user_id', $usuarioId)->max('numero_intento')) + 1,
                    'iniciada_en'    => now(),
                    'enviada_en'     => now(),
                ]);

                foreach ($preparados as $tipo => $preparado) {
                    $this->documentos->registrarDocumento($solicitud, $preparado, $rutas[$tipo]);
                }

                $this->aplicarCambio($registro, $solicitud, EstadoKyc::RevisionManual, [
                    'origen'    => EventoKyc::ORIGEN_USUARIO,
                    'tipo'      => 'envio_manual',
                    'ip'        => $ip,
                    'atributos' => $campos,
                    'detalle'   => [
                        'consentimiento_tratamiento_datos' => true,
                        'tipo_documento'                   => $campos['tipo_documento'],
                        'documentos'                       => array_keys($preparados),
                        'documento_coincide_otro_usuario'  => $coincidencias > 0,
                    ],
                ]);

                return ['ok' => true];
            });

            if (isset($resultado['error'])) {
                $this->descartarArchivos($rutas);

                return $resultado['error'];
            }

            return $this->respuesta(true, __('Solicitud manual recibida'),
                __('Recibimos tu solicitud de verificación. Un administrador la revisará y te avisaremos del resultado.'),
                ['estado' => EstadoKyc::RevisionManual->value], 200);
        } catch (DocumentoKycInvalidoException $e) {
            $this->descartarArchivos($rutas);

            return $this->respuestaValidacion([$e->campo => [$e->getMessage()]]);
        } catch (UniqueConstraintViolationException $e) {
            $this->descartarArchivos($rutas);

            return $this->respuesta(false, __('Conflicto al enviar la solicitud'),
                __('Ya existe una solicitud de verificación en curso. Actualiza la página e inténtalo de nuevo.'), 'conflicto', 409);
        } catch (Throwable $e) {
            $this->descartarArchivos($rutas);

            return $this->errorInterno($e, 'enviarManual');
        }
    }

    /**
     * Reenvía datos y/o documentos cuando un administrador solicitó una corrección. La solicitud sigue en
     * manual_review; los documentos reemplazados se conservan como historial y se registra la atención de la corrección.
     * Al confirmarse la transacción emite CorreccionKycAtendida para avisar a los administradores.
     *
     * @param  array<string,mixed> $datos
     * @param  array<string,mixed> $archivos Solo los documentos que se reemplazan.
     * @return array{success:bool,message:string,mensaje_usuario:string,respuesta:mixed,code:int}
     */
    public function reenviarCorreccion(int $usuarioId, array $datos, array $archivos, ?string $ip = null): array
    {
        $rutas = [];

        try {
            $validacion = $this->validador->validarCorreccion($datos, $this->correoDe($usuarioId));

            if (!$validacion['ok']) {
                return $this->respuestaValidacion($validacion['errores']);
            }

            $campos = $validacion['datos'];

            $registro = UsuarioKyc::where('user_id', $usuarioId)->first();
            $activa   = $registro?->solicitud_activa_id ? SolicitudKyc::find($registro->solicitud_activa_id) : null;

            if (!$this->hayCorreccionPendiente($registro, $activa)) {
                return $this->respuesta(false, __('Sin corrección pendiente'),
                    __('No tienes ninguna corrección pendiente en tu solicitud.'), 'sin_correccion', 409);
            }

            $preparados = $this->documentos->prepararArchivos($archivos);

            foreach ($preparados as $tipo => $preparado) {
                $rutas[$tipo] = $this->documentos->guardarCifrado($preparado);
            }

            $resultado = $this->transaccion(function () use ($usuarioId, $campos, $preparados, $rutas, $ip) {
                $registro  = $this->bloquearUsuario($usuarioId);
                $solicitud = $registro->solicitud_activa_id
                    ? SolicitudKyc::where('id', $registro->solicitud_activa_id)->lockForUpdate()->first() : null;

                if (!$this->hayCorreccionPendiente($registro, $solicitud)) {
                    return ['error' => $this->respuesta(false, __('Sin corrección pendiente'),
                        __('No tienes ninguna corrección pendiente en tu solicitud.'), 'sin_correccion', 409)];
                }

                $limite = (int) config('kyc.limites.reenvios_correccion_por_dia', 5);
                $reenviosHoy = EventoKyc::where('user_id', $usuarioId)->where('tipo', 'correccion_atendida')
                    ->where('created_at', '>=', now()->subDay())->count();

                if ($limite > 0 && $reenviosHoy >= $limite) {
                    return ['error' => $this->respuesta(false, __('Límite de reenvíos alcanzado'),
                        __('Has alcanzado el límite de correcciones de hoy. Inténtalo de nuevo mañana.'), 'limite_reenvios', 429)];
                }

                $modificados = [];
                foreach (ValidadorKycManual::CAMPOS as $campo) {
                    $actual = $campo === 'numero_documento'
                        ? SolicitudKyc::normalizarDocumento($solicitud->numero_documento)
                        : (string) $solicitud->{$campo};
                    $nuevo = $campo === 'numero_documento'
                        ? SolicitudKyc::normalizarDocumento($campos[$campo])
                        : (string) $campos[$campo];

                    if ($actual !== $nuevo) {
                        $modificados[] = $campo;
                    }
                }

                if (empty($modificados) && empty($preparados)) {
                    return ['error' => $this->respuesta(false, __('Sin cambios'),
                        __('No detectamos cambios en tus datos ni nuevos documentos. Corrige lo indicado y vuelve a enviar.'), 'sin_cambios', 422)];
                }

                $vigentes = $solicitud->documentosVigentes()->pluck('tipo')->all();
                $faltantes = $this->validador->documentosFaltantes($campos['tipo_documento'], array_merge($vigentes, array_keys($preparados)));

                if (!empty($faltantes)) {
                    return ['error' => $this->respuestaValidacion($faltantes)];
                }

                foreach ($preparados as $tipo => $preparado) {
                    $this->documentos->registrarDocumento($solicitud, $preparado, $rutas[$tipo]);
                }

                $solicitud->fill($campos);
                $solicitud->correccion_atendida_en = now();
                $solicitud->save();

                $this->registrarEvento([
                    'user_id'         => $usuarioId,
                    'solicitud_id'    => $solicitud->id,
                    'origen'          => EventoKyc::ORIGEN_USUARIO,
                    'tipo'            => 'correccion_atendida',
                    'estado_anterior' => EstadoKyc::RevisionManual->value,
                    'estado_nuevo'    => EstadoKyc::RevisionManual->value,
                    'resultado'       => EventoKyc::RESULTADO_PROCESADO,
                    'detalle'         => ['campos_modificados' => $modificados, 'documentos' => array_keys($preparados)],
                    'ip'              => $ip,
                ]);

                // Avisa a los administradores solo si la transacción se confirma.
                $idSolicitudAtendida = (int) $solicitud->id;
                DB::afterCommit(static fn () => event(new CorreccionKycAtendida($usuarioId, $idSolicitudAtendida)));

                return ['ok' => true];
            });

            if (isset($resultado['error'])) {
                $this->descartarArchivos($rutas);

                return $resultado['error'];
            }

            return $this->respuesta(true, __('Corrección recibida'),
                __('Recibimos tu corrección. Un administrador volverá a revisar tu solicitud.'),
                ['estado' => EstadoKyc::RevisionManual->value], 200);
        } catch (DocumentoKycInvalidoException $e) {
            $this->descartarArchivos($rutas);

            return $this->respuestaValidacion([$e->campo => [$e->getMessage()]]);
        } catch (Throwable $e) {
            $this->descartarArchivos($rutas);

            return $this->errorInterno($e, 'reenviarCorreccion');
        }
    }

    /**
     * Datos que el usuario ve para completar una corrección pendiente (solo mientras exista una).
     * Es información del propio usuario; el controlador la envía sin caché.
     *
     * @return array<string,mixed>|null
     */
    public function solicitudParaCorreccion(int $usuarioId): ?array
    {
        $registro  = UsuarioKyc::where('user_id', $usuarioId)->first();
        $solicitud = $registro?->solicitud_activa_id ? SolicitudKyc::find($registro->solicitud_activa_id) : null;

        if (!$this->hayCorreccionPendiente($registro, $solicitud)) {
            return null;
        }

        return [
            'datos' => [
                'nombres'              => $solicitud->nombres,
                'apellidos'            => $solicitud->apellidos,
                'tipo_documento'       => $solicitud->tipo_documento,
                'numero_documento'     => $solicitud->numero_documento,
                'pais_expedicion'      => $solicitud->pais_expedicion,
                'fecha_nacimiento'     => $solicitud->fecha_nacimiento,
                'pais_residencia'      => $solicitud->pais_residencia,
                'ciudad_residencia'    => $solicitud->ciudad_residencia,
                'direccion_residencia' => $solicitud->direccion_residencia,
                'correo_contacto'      => $solicitud->correo_contacto,
            ],
            'correccion_solicitada' => $solicitud->correccion_solicitada,
            'documentos'            => $solicitud->documentosVigentes()->orderBy('id')->get(['id', 'tipo'])
                ->map(static fn ($d) => ['id' => (int) $d->id, 'tipo' => $d->tipo])->all(),
        ];
    }

    /**
     * Cantidad de OTROS usuarios con el mismo número de documento (verificados o con una solicitud abierta).
     * Es una señal para el revisor, no un motivo de rechazo automático (puede haber errores de digitación).
     */
    public function coincidenciasDocumento(SolicitudKyc $solicitud): int
    {
        return $solicitud->numero_documento === null
            ? 0
            : $this->contarCoincidenciasDocumento((int) $solicitud->user_id, $solicitud->numero_documento);
    }

    /**
     * Decide si el usuario puede abrir una solicitud manual. Devuelve la respuesta de error o null si puede.
     */
    private function bloqueoManual(?UsuarioKyc $registro, ?SolicitudKyc $activa): ?array
    {
        $estado = $registro?->estadoEfectivo() ?? EstadoKyc::SinIniciar;

        return match (true) {
            $estado === EstadoKyc::Verificado => $this->respuesta(false, __('Usuario ya verificado'),
                __('Ya tienes tu identidad verificada.'), 'ya_verificado', 409),
            $estado === EstadoKyc::RevisionManual => $this->respuesta(false, __('Solicitud manual en revisión'),
                __('Ya tienes una solicitud de verificación manual en revisión.'), 'manual_en_revision', 409),
            $estado === EstadoKyc::Pendiente => $this->respuesta(false, __('Verificación pendiente de revisión'),
                __('Tu verificación está siendo revisada. Te avisaremos cuando haya una decisión.'), 'pendiente', 409),
            $estado === EstadoKyc::EnProceso && !($activa && $activa->metodo === MetodoKyc::Didit) => $this->respuesta(false, __('Solicitud en curso'),
                __('Ya tienes una solicitud de verificación en curso.'), 'solicitud_abierta', 409),
            default => null,
        };
    }

    /**
     * Indica si la solicitud activa es manual, está en revisión y tiene una corrección por atender.
     */
    private function hayCorreccionPendiente(?UsuarioKyc $registro, ?SolicitudKyc $solicitud): bool
    {
        return $registro !== null
            && $solicitud !== null
            && $registro->estadoEfectivo() === EstadoKyc::RevisionManual
            && $solicitud->metodo === MetodoKyc::Manual
            && $solicitud->tieneCorreccionPendiente();
    }

    /**
     * Cuenta otros usuarios con el mismo documento (tolera la letra V/E de la cédula).
     */
    private function contarCoincidenciasDocumento(int $usuarioId, string $numero): int
    {
        $hashes = SolicitudKyc::hashesCandidatosDocumento($numero);

        if (empty($hashes)) {
            return 0;
        }

        return SolicitudKyc::whereIn('numero_documento_hash', $hashes)
            ->where('user_id', '!=', $usuarioId)
            ->whereIn('estado', [
                EstadoKyc::Verificado->value, EstadoKyc::EnProceso->value, EstadoKyc::Pendiente->value, EstadoKyc::RevisionManual->value,
            ])
            ->distinct()
            ->count('user_id');
    }

    /**
     * Correo del usuario autenticado (valor predeterminado del correo de contacto).
     */
    private function correoDe(int $usuarioId): ?string
    {
        $correo = DB::table('users')->where('id', $usuarioId)->value('email');

        return is_string($correo) && $correo !== '' ? $correo : null;
    }

    /**
     * Borra los archivos ya escritos cuando la operación no se completó.
     *
     * @param array<string,string> $rutas
     */
    private function descartarArchivos(array $rutas): void
    {
        foreach ($rutas as $ruta) {
            $this->documentos->eliminarArchivoFisico($ruta);
        }
    }

    /**
     * Respuesta 422 con los errores por campo; el primer mensaje se muestra al usuario.
     *
     * @param array<string,array<int,string>> $errores
     * @return array{success:bool,message:string,mensaje_usuario:string,respuesta:mixed,code:int}
     */
    private function respuestaValidacion(array $errores): array
    {
        $primero = reset($errores);
        $mensaje = is_array($primero) ? (string) reset($primero) : __('Revisa los datos enviados.');

        return $this->respuesta(false, __('Datos de verificación inválidos'), $mensaje, $errores, 422);
    }

    // ------------------------------------------------------------------
    // ACCIONES ADMINISTRATIVAS (verificación manual)
    // ------------------------------------------------------------------

    /**
     * Aprueba una solicitud manual. El permiso manage_kyc se verifica aquí, en el servidor, además de en la ruta.
     * Exige que la solicitud sea la activa del usuario y esté en manual_review, sin una corrección pendiente de
     * atender, con sus datos y los documentos obligatorios vigentes. Registra al administrador responsable y la fecha.
     *
     * @return array{success:bool,message:string,mensaje_usuario:string,respuesta:mixed,code:int}
     */
    public function aprobarManual(int $adminId, int $solicitudId, ?string $observacion = null, ?string $ip = null): array
    {
        try {
            if ($error = $this->exigirAdministrador($adminId)) {
                return $error;
            }

            $observacion = $this->limpiarTexto($observacion);

            if ($observacion !== null && mb_strlen($observacion) > 2000) {
                return $this->respuestaValidacion(['observacion' => [__('La observación no puede superar los 2000 caracteres.')]]);
            }

            return $this->transaccion(function () use ($adminId, $solicitudId, $observacion, $ip) {
                [$registro, $solicitud, $error] = $this->cargarSolicitudManualAbierta($solicitudId);

                if ($error) {
                    return $error;
                }

                if ($solicitud->tieneCorreccionPendiente()) {
                    return $this->respuesta(false, __('Corrección pendiente'),
                        __('Hay una corrección solicitada que el usuario aún no atiende. Espera su respuesta antes de decidir.'), 'correccion_pendiente', 409);
                }

                $faltantes = $this->validador->documentosFaltantes(
                    (string) $solicitud->tipo_documento,
                    $solicitud->documentosVigentes()->pluck('tipo')->all()
                );

                if (!empty($faltantes) || !$solicitud->nombres || !$solicitud->apellidos || !$solicitud->numero_documento) {
                    return $this->respuesta(false, __('Solicitud incompleta'),
                        __('La solicitud no tiene todos los datos y documentos obligatorios; pide una corrección.'), 'solicitud_incompleta', 422);
                }

                $atributos = ['motivo_rechazo' => null, 'mensaje_usuario' => null];

                if ($observacion !== null) {
                    $atributos['observaciones_internas'] = $this->agregarObservacionAlTexto($solicitud, $adminId, $observacion);
                }

                $this->aplicarCambio($registro, $solicitud, EstadoKyc::Verificado, [
                    'origen'    => EventoKyc::ORIGEN_ADMIN,
                    'tipo'      => 'aprobacion_manual',
                    'admin_id'  => $adminId,
                    'ip'        => $ip,
                    'atributos' => $atributos,
                ]);

                return $this->respuesta(true, __('Solicitud aprobada'), __('La identidad del usuario fue verificada.'),
                    ['estado' => EstadoKyc::Verificado->value], 200);
            });
        } catch (\LengthException $e) {
            return $this->respuestaValidacion(['observacion' => [__('El historial de observaciones alcanzó su límite.')]]);
        } catch (Throwable $e) {
            return $this->errorInterno($e, 'aprobarManual');
        }
    }

    /**
     * Rechaza una solicitud manual. El motivo es obligatorio y se comunica al usuario.
     *
     * @return array{success:bool,message:string,mensaje_usuario:string,respuesta:mixed,code:int}
     */
    public function rechazarManual(int $adminId, int $solicitudId, ?string $motivo, ?string $observacion = null, ?string $ip = null): array
    {
        try {
            if ($error = $this->exigirAdministrador($adminId)) {
                return $error;
            }

            $motivo      = $this->limpiarTexto($motivo);
            $observacion = $this->limpiarTexto($observacion);

            if ($motivo === null || mb_strlen($motivo) < 5 || mb_strlen($motivo) > 1000) {
                return $this->respuestaValidacion(['motivo' => [__('El motivo del rechazo es obligatorio (entre 5 y 1000 caracteres).')]]);
            }

            if ($observacion !== null && mb_strlen($observacion) > 2000) {
                return $this->respuestaValidacion(['observacion' => [__('La observación no puede superar los 2000 caracteres.')]]);
            }

            return $this->transaccion(function () use ($adminId, $solicitudId, $motivo, $observacion, $ip) {
                [$registro, $solicitud, $error] = $this->cargarSolicitudManualAbierta($solicitudId);

                if ($error) {
                    return $error;
                }

                $atributos = ['motivo_rechazo' => $motivo, 'mensaje_usuario' => null];

                if ($observacion !== null) {
                    $atributos['observaciones_internas'] = $this->agregarObservacionAlTexto($solicitud, $adminId, $observacion);
                }

                $this->aplicarCambio($registro, $solicitud, EstadoKyc::Rechazado, [
                    'origen'    => EventoKyc::ORIGEN_ADMIN,
                    'tipo'      => 'rechazo_manual',
                    'admin_id'  => $adminId,
                    'ip'        => $ip,
                    'atributos' => $atributos,
                ]);

                return $this->respuesta(true, __('Solicitud rechazada'), __('La solicitud fue rechazada y se registró el motivo.'),
                    ['estado' => EstadoKyc::Rechazado->value], 200);
            });
        } catch (\LengthException $e) {
            return $this->respuestaValidacion(['observacion' => [__('El historial de observaciones alcanzó su límite.')]]);
        } catch (Throwable $e) {
            return $this->errorInterno($e, 'rechazarManual');
        }
    }

    /**
     * Pide al usuario que corrija datos o documentos. La solicitud sigue en manual_review y el usuario puede
     * reenviar (reenviarCorreccion). Si ya había una corrección pendiente, se reemplaza por la nueva.
     *
     * @return array{success:bool,message:string,mensaje_usuario:string,respuesta:mixed,code:int}
     */
    public function solicitarCorreccion(int $adminId, int $solicitudId, ?string $texto, ?string $ip = null): array
    {
        try {
            if ($error = $this->exigirAdministrador($adminId)) {
                return $error;
            }

            $texto = $this->limpiarTexto($texto);

            if ($texto === null || mb_strlen($texto) < 5 || mb_strlen($texto) > 1000) {
                return $this->respuestaValidacion(['correccion' => [__('Indica qué debe corregir el usuario (entre 5 y 1000 caracteres).')]]);
            }

            return $this->transaccion(function () use ($adminId, $solicitudId, $texto, $ip) {
                [$registro, $solicitud, $error] = $this->cargarSolicitudManualAbierta($solicitudId);

                if ($error) {
                    return $error;
                }

                $solicitud->correccion_solicitada    = $texto;
                $solicitud->correccion_solicitada_en = now();
                $solicitud->correccion_atendida_en   = null;
                $solicitud->revisado_por_admin_id    = $adminId;
                $solicitud->save();

                $this->registrarEvento([
                    'user_id'         => $solicitud->user_id,
                    'solicitud_id'    => $solicitud->id,
                    'origen'          => EventoKyc::ORIGEN_ADMIN,
                    'tipo'            => 'correccion_solicitada',
                    'estado_anterior' => EstadoKyc::RevisionManual->value,
                    'estado_nuevo'    => EstadoKyc::RevisionManual->value,
                    'resultado'       => EventoKyc::RESULTADO_PROCESADO,
                    'admin_id'        => $adminId,
                    'ip'              => $ip,
                ]);

                $usuarioId = (int) $solicitud->user_id;
                $idSolicitud = (int) $solicitud->id;
                DB::afterCommit(static fn () => event(new CorreccionKycSolicitada($usuarioId, $idSolicitud, $adminId)));

                return $this->respuesta(true, __('Corrección solicitada'), __('Se pidió la corrección al usuario.'),
                    ['estado' => EstadoKyc::RevisionManual->value], 200);
            });
        } catch (Throwable $e) {
            return $this->errorInterno($e, 'solicitarCorreccion');
        }
    }

    /**
     * Agrega una observación interna (nunca visible para el usuario) a cualquier solicitud, incluso cerrada.
     *
     * @return array{success:bool,message:string,mensaje_usuario:string,respuesta:mixed,code:int}
     */
    public function agregarObservacion(int $adminId, int $solicitudId, ?string $texto, ?string $ip = null): array
    {
        try {
            if ($error = $this->exigirAdministrador($adminId)) {
                return $error;
            }

            $texto = $this->limpiarTexto($texto);

            if ($texto === null || mb_strlen($texto) < 2 || mb_strlen($texto) > 2000) {
                return $this->respuestaValidacion(['observacion' => [__('Escribe la observación (entre 2 y 2000 caracteres).')]]);
            }

            return $this->transaccion(function () use ($adminId, $solicitudId, $texto, $ip) {
                [$solicitud, $error] = $this->cargarSolicitudBloqueada($solicitudId);

                if ($error) {
                    return $error;
                }

                $nuevo = $this->agregarObservacionAlTexto($solicitud, $adminId, $texto);
                $solicitud->observaciones_internas = $nuevo;
                $solicitud->save();

                $this->registrarEvento([
                    'user_id'      => $solicitud->user_id,
                    'solicitud_id' => $solicitud->id,
                    'origen'       => EventoKyc::ORIGEN_ADMIN,
                    'tipo'         => 'observacion_admin',
                    'resultado'    => EventoKyc::RESULTADO_PROCESADO,
                    'admin_id'     => $adminId,
                    'ip'           => $ip,
                ]);

                return $this->respuesta(true, __('Observación guardada'), __('La observación interna fue registrada.'),
                    ['observaciones' => $nuevo], 200);
            });
        } catch (\LengthException $e) {
            return $this->respuestaValidacion(['observacion' => [__('El historial de observaciones alcanzó su límite.')]]);
        } catch (Throwable $e) {
            return $this->errorInterno($e, 'agregarObservacion');
        }
    }

    /**
     * Suspende o reanuda la eliminación automática de los documentos de una solicitud (retención legal).
     * No modifica el estado de verificación.
     *
     * @return array{success:bool,message:string,mensaje_usuario:string,respuesta:mixed,code:int}
     */
    public function suspenderRetencion(int $adminId, int $solicitudId, bool $suspender, ?string $ip = null): array
    {
        try {
            if ($error = $this->exigirAdministrador($adminId)) {
                return $error;
            }

            return $this->transaccion(function () use ($adminId, $solicitudId, $suspender, $ip) {
                [$solicitud, $error] = $this->cargarSolicitudBloqueada($solicitudId);

                if ($error) {
                    return $error;
                }

                $solicitud->retencion_suspendida = $suspender;
                $solicitud->save();

                $this->registrarEvento([
                    'user_id'      => $solicitud->user_id,
                    'solicitud_id' => $solicitud->id,
                    'origen'       => EventoKyc::ORIGEN_ADMIN,
                    'tipo'         => $suspender ? 'retencion_suspendida' : 'retencion_reanudada',
                    'resultado'    => EventoKyc::RESULTADO_PROCESADO,
                    'admin_id'     => $adminId,
                    'ip'           => $ip,
                ]);

                return $this->respuesta(true, $suspender ? __('Retención suspendida') : __('Retención reanudada'),
                    $suspender ? __('Los documentos no se eliminarán automáticamente.') : __('Los documentos volverán a seguir la política de retención.'),
                    ['retencion_suspendida' => $suspender], 200);
            });
        } catch (Throwable $e) {
            return $this->errorInterno($e, 'suspenderRetencion');
        }
    }

    /**
     * Devuelve el número de documento completo a un administrador autorizado (por defecto se muestra enmascarado).
     * Cada revelación queda en la auditoría con el administrador y la IP.
     *
     * @return array{success:bool,message:string,mensaje_usuario:string,respuesta:mixed,code:int}
     */
    public function revelarNumeroDocumento(int $adminId, int $solicitudId, ?string $ip = null): array
    {
        try {
            if ($error = $this->exigirAdministrador($adminId)) {
                return $error;
            }

            return $this->transaccion(function () use ($adminId, $solicitudId, $ip) {
                [$solicitud, $error] = $this->cargarSolicitudBloqueada($solicitudId);

                if ($error) {
                    return $error;
                }

                if (!$solicitud->numero_documento) {
                    return $this->respuesta(false, __('Sin número de documento'), __('La solicitud no tiene número de documento.'), 'sin_documento', 404);
                }

                $this->registrarEvento([
                    'user_id'      => $solicitud->user_id,
                    'solicitud_id' => $solicitud->id,
                    'origen'       => EventoKyc::ORIGEN_ADMIN,
                    'tipo'         => 'numero_documento_revelado',
                    'resultado'    => EventoKyc::RESULTADO_PROCESADO,
                    'admin_id'     => $adminId,
                    'ip'           => $ip,
                ]);

                return $this->respuesta(true, __('Número de documento'), '', ['numero_documento' => $solicitud->numero_documento], 200);
            });
        } catch (Throwable $e) {
            return $this->errorInterno($e, 'revelarNumeroDocumento');
        }
    }

    /**
     * Registra que un administrador abrió el detalle de una solicitud (acceso a datos personales). Para no llenar la
     * auditoría, no repite el registro del mismo administrador y solicitud dentro de los 10 minutos siguientes.
     */
    public function registrarConsultaAdmin(int $adminId, int $solicitudId, ?string $ip = null): void
    {
        try {
            $reciente = EventoKyc::where('solicitud_id', $solicitudId)->where('admin_id', $adminId)->where('tipo', 'solicitud_consultada')
                ->where('created_at', '>=', now()->subMinutes(10))->exists();

            if ($reciente) {
                return;
            }

            $solicitud = SolicitudKyc::find($solicitudId);

            $this->transaccion(fn () => $this->registrarEvento([
                'user_id'      => $solicitud?->user_id,
                'solicitud_id' => $solicitud?->id,
                'origen'       => EventoKyc::ORIGEN_ADMIN,
                'tipo'         => 'solicitud_consultada',
                'resultado'    => EventoKyc::RESULTADO_PROCESADO,
                'admin_id'     => $adminId,
                'ip'           => $ip,
            ]));
        } catch (Throwable $e) {
            Log::warning('KYC: no se pudo registrar la consulta de la solicitud ' . $solicitudId . ' (' . get_class($e) . ').');
        }
    }

    /**
     * Elimina por completo la verificación de identidad del usuario dueño de la solicitud indicada, para que pueda
     * volver a enviarla (por ejemplo, si un administrador aprobó por error). Exige el permiso manage_kyc en el
     * servidor y un motivo obligatorio (de 5 a 500 caracteres).
     *
     * En una sola transacción, con la fila del usuario bloqueada (serializa a dos administradores y al propio usuario):
     *  - borra los documentos y las solicitudes del usuario, con todos sus datos personales;
     *  - deja usuarios_kyc en not_started, sin solicitud activa ni verificación vigente;
     *  - conserva los eventos de auditoría (no contienen datos personales; pierden el vínculo con la solicitud) y
     *    agrega el evento "kyc_eliminado" con el administrador, la IP, el motivo y las cantidades eliminadas.
     * Los archivos físicos se borran DESPUÉS de confirmar la transacción: si algo falla, no se pierde ni un archivo.
     * Si un archivo no se puede borrar queda huérfano pero cifrado y sin vínculos; se informa en la respuesta.
     * Una verificación de Didit abierta no se cancela en Didit: sus webhooks posteriores se ignoran (sesión
     * desconocida) y el usuario sigue sin verificar.
     * Al confirmarse emite VerificacionKycEliminada para avisar al usuario.
     *
     * @return array{success:bool,message:string,mensaje_usuario:string,respuesta:mixed,code:int}
     */
    public function eliminarVerificacion(int $adminId, int $solicitudId, ?string $motivo, ?string $ip = null): array
    {
        try {
            if ($error = $this->exigirAdministrador($adminId)) {
                return $error;
            }

            $motivo = $this->limpiarTexto($motivo);

            if ($motivo === null || mb_strlen($motivo) < 5 || mb_strlen($motivo) > 500) {
                return $this->respuestaValidacion(['motivo' => [__('El motivo de la eliminación es obligatorio (entre 5 y 500 caracteres).')]]);
            }

            $resultado = $this->transaccion(function () use ($adminId, $solicitudId, $motivo, $ip) {
                $existente = SolicitudKyc::find($solicitudId);

                if (!$existente) {
                    return ['error' => $this->respuesta(false, __('Verificación no encontrada'),
                        __('La verificación ya no existe (otro administrador pudo haberla eliminado).'), 'no_encontrada', 404)];
                }

                $usuarioId   = (int) $existente->user_id;
                $registro    = $this->bloquearUsuario($usuarioId);
                $solicitudes = SolicitudKyc::where('user_id', $usuarioId)->lockForUpdate()->get();

                // Otro administrador pudo eliminarla mientras esta petición esperaba el bloqueo.
                if ($solicitudes->isEmpty()) {
                    return ['error' => $this->respuesta(false, __('Verificación no encontrada'),
                        __('La verificación ya no existe (otro administrador pudo haberla eliminado).'), 'no_encontrada', 404)];
                }

                $documentos = DocumentoKyc::where('user_id', $usuarioId)->get();
                $rutas      = $documentos->where('estado', '!=', DocumentoKyc::ESTADO_ELIMINADO)->pluck('ruta')->filter()->values()->all();
                $anterior   = $registro->estadoEfectivo();
                $metodo     = $registro->metodo_verificacion?->value;

                foreach ($documentos as $documento) {
                    $documento->delete();
                }

                // Antes de borrar las solicitudes: usuarios_kyc.solicitud_activa_id apunta a una de ellas.
                $registro->estado                  = EstadoKyc::SinIniciar;
                $registro->solicitud_activa_id     = null;
                $registro->metodo_verificacion     = null;
                $registro->verificado_en           = null;
                $registro->verificado_por_admin_id = null;
                $registro->expira_en               = null;
                $registro->version                 = ((int) $registro->version) + 1;
                $registro->save();

                foreach ($solicitudes as $solicitud) {
                    $solicitud->delete();
                }

                $this->registrarEvento([
                    'user_id'         => $usuarioId,
                    'solicitud_id'    => null,
                    'origen'          => EventoKyc::ORIGEN_ADMIN,
                    'tipo'            => 'kyc_eliminado',
                    'estado_anterior' => $anterior->value,
                    'estado_nuevo'    => EstadoKyc::SinIniciar->value,
                    'resultado'       => EventoKyc::RESULTADO_PROCESADO,
                    'detalle'         => array_filter([
                        'motivo'      => $motivo,
                        'metodo'      => $metodo,
                        'solicitudes' => $solicitudes->count(),
                        'documentos'  => $documentos->count(),
                    ], static fn ($valor) => $valor !== null),
                    'admin_id'        => $adminId,
                    'ip'              => $ip,
                ]);

                unset($this->cacheVerificado[$usuarioId]);

                DB::afterCommit(function () use ($usuarioId, $adminId) {
                    unset($this->cacheVerificado[$usuarioId]);
                    event(new VerificacionKycEliminada($usuarioId, $adminId));
                });

                return [
                    'usuario_id'  => $usuarioId,
                    'solicitudes' => $solicitudes->count(),
                    'documentos'  => $documentos->count(),
                    'rutas'       => $rutas,
                ];
            });

            if (isset($resultado['error'])) {
                return $resultado['error'];
            }

            $pendientes = $this->documentos->eliminarArchivosFisicos($resultado['rutas']);

            if ($pendientes > 0) {
                Log::warning('KYC: la verificación del usuario ' . $resultado['usuario_id'] . ' se eliminó, pero ' . $pendientes . ' archivo(s) físico(s) no se pudieron borrar y deben retirarse manualmente.');
            }

            return $this->respuesta(true, __('Verificación eliminada'),
                $pendientes > 0
                    ? __('La verificación se eliminó de la base de datos, pero :n archivo(s) no se pudieron borrar del disco. Quedaron cifrados y sin vínculos; avisa a soporte técnico para retirarlos.', ['n' => $pendientes])
                    : __('Se eliminaron los datos y documentos de verificación del usuario. Podrá volver a enviar su verificación de identidad.'),
                [
                    'usuario_id'          => $resultado['usuario_id'],
                    'solicitudes'         => $resultado['solicitudes'],
                    'documentos'          => $resultado['documentos'],
                    'archivos_pendientes' => $pendientes,
                ], 200);
        } catch (Throwable $e) {
            return $this->errorInterno($e, 'eliminarVerificacion');
        }
    }

    /**
     * Verifica en el servidor que el administrador exista, esté activo y tenga el permiso manage_kyc (KycPolicy).
     * Devuelve la respuesta 403 o null si está autorizado.
     */
    private function exigirAdministrador(int $adminId): ?array
    {
        $admin = Admin::find($adminId);

        if (!$admin || !Gate::forUser($admin)->allows('gestionar', SolicitudKyc::class)) {
            return $this->respuesta(false, __('Acción no autorizada'),
                __('No tienes permiso para gestionar verificaciones de identidad.'), 'no_autorizado', 403);
        }

        return null;
    }

    /**
     * Carga y bloquea una solicitud (y la fila de su usuario). Dentro de transaccion().
     *
     * @return array{0:?SolicitudKyc,1:?array}
     */
    private function cargarSolicitudBloqueada(int $solicitudId): array
    {
        $solicitud = SolicitudKyc::find($solicitudId);

        if (!$solicitud) {
            return [null, $this->respuesta(false, __('Solicitud no encontrada'), __('La solicitud no existe.'), 'no_encontrada', 404)];
        }

        $this->bloquearUsuario((int) $solicitud->user_id);

        return [SolicitudKyc::where('id', $solicitudId)->lockForUpdate()->firstOrFail(), null];
    }

    /**
     * Carga la solicitud y comprueba que sea una solicitud manual activa y en revisión. Dentro de transaccion().
     *
     * @return array{0:?UsuarioKyc,1:?SolicitudKyc,2:?array}
     */
    private function cargarSolicitudManualAbierta(int $solicitudId): array
    {
        $existente = SolicitudKyc::find($solicitudId);

        if (!$existente) {
            return [null, null, $this->respuesta(false, __('Solicitud no encontrada'), __('La solicitud no existe.'), 'no_encontrada', 404)];
        }

        $registro  = $this->bloquearUsuario((int) $existente->user_id);
        $solicitud = SolicitudKyc::where('id', $solicitudId)->lockForUpdate()->firstOrFail();

        if ($solicitud->metodo !== MetodoKyc::Manual
            || $solicitud->estado !== EstadoKyc::RevisionManual
            || $registro->estadoEfectivo() !== EstadoKyc::RevisionManual
            || (int) $registro->solicitud_activa_id !== (int) $solicitud->id) {
            return [$registro, $solicitud, $this->respuesta(false, __('Estado no válido'),
                __('La solicitud ya no está pendiente de revisión manual (otro administrador pudo haberla resuelto).'), 'estado_no_permite', 409)];
        }

        return [$registro, $solicitud, null];
    }

    /**
     * Agrega al historial de observaciones internas una línea con fecha y administrador.
     *
     * @throws \LengthException Si el historial superaría los 20000 caracteres.
     */
    private function agregarObservacionAlTexto(SolicitudKyc $solicitud, int $adminId, string $texto): string
    {
        $nombre = (string) (DB::table('admin')->where('id', $adminId)->value('username') ?? ('admin ' . $adminId));
        $linea  = '[' . now()->format('Y-m-d H:i') . '] ' . $nombre . ': ' . $texto;
        $nuevo  = trim((string) $solicitud->observaciones_internas) === '' ? $linea : $solicitud->observaciones_internas . "\n" . $linea;

        if (mb_strlen($nuevo) > 20000) {
            throw new \LengthException('El historial de observaciones superaría el límite.');
        }

        return $nuevo;
    }

    /**
     * Normaliza un texto libre: recorta, unifica saltos de línea y elimina caracteres de control. Vacío = null.
     */
    private function limpiarTexto(?string $texto): ?string
    {
        if ($texto === null) {
            return null;
        }

        $texto = str_replace(["\r\n", "\r"], "\n", $texto);
        $texto = (string) preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $texto);
        $texto = trim($texto);

        return $texto === '' ? null : $texto;
    }

    // ------------------------------------------------------------------
    // INTERNOS: DIDIT
    // ------------------------------------------------------------------

    /**
     * Reserva (o localiza) la solicitud de verificación automática dentro de una transacción con bloqueo.
     *
     * @return array{solicitud_id?:int,nueva?:bool,error?:array}
     */
    private function reservarSolicitudDidit(int $usuarioId, ?string $ip): array
    {
        $registro = $this->bloquearUsuario($usuarioId);
        $estado   = $registro->estadoEfectivo();

        if ($estado === EstadoKyc::Verificado && $registro->esVerificado()) {
            return ['error' => $this->respuesta(false, __('Usuario ya verificado'),
                __('Ya tienes tu identidad verificada.'), 'ya_verificado', 409)];
        }

        if ($estado->esAbierto()) {
            $activa = $registro->solicitud_activa_id
                ? SolicitudKyc::where('id', $registro->solicitud_activa_id)->lockForUpdate()->first() : null;

            if ($estado === EstadoKyc::RevisionManual) {
                return ['error' => $this->respuesta(false, __('Solicitud manual en revisión'),
                    __('Ya tienes una solicitud de verificación manual en revisión.'), 'manual_en_revision', 409)];
            }

            if ($estado === EstadoKyc::Pendiente) {
                return ['error' => $this->respuesta(false, __('Verificación pendiente de revisión'),
                    __('Tu verificación está siendo revisada. Te avisaremos cuando haya una decisión.'), 'pendiente', 409)];
            }

            // En proceso: se retoma la misma solicitud (Didit devuelve la sesión sin terminar).
            if ($activa && $activa->metodo === MetodoKyc::Didit && $activa->estado === EstadoKyc::EnProceso) {
                return ['solicitud_id' => $activa->id, 'nueva' => false];
            }

            return ['error' => $this->respuesta(false, __('Solicitud en curso'),
                __('Ya tienes una solicitud de verificación en curso.'), 'solicitud_abierta', 409)];
        }

        $limite = (int) config('kyc.limites.intentos_didit_por_dia', 5);
        $intentosHoy = SolicitudKyc::where('user_id', $usuarioId)
            ->where('metodo', MetodoKyc::Didit->value)
            ->whereNotNull('didit_session_id')
            ->where('created_at', '>=', now()->subDay())
            ->count();

        if ($limite > 0 && $intentosHoy >= $limite) {
            return ['error' => $this->respuesta(false, __('Límite de intentos alcanzado'),
                __('Has alcanzado el límite de intentos de verificación de hoy. Inténtalo de nuevo mañana o solicita la revisión manual.'),
                'limite_intentos', 429)];
        }

        $numeroIntento = ((int) SolicitudKyc::where('user_id', $usuarioId)->max('numero_intento')) + 1;

        $solicitud = SolicitudKyc::create([
            'user_id'        => $usuarioId,
            'metodo'         => MetodoKyc::Didit,
            'estado'         => EstadoKyc::SinIniciar,
            'numero_intento' => $numeroIntento,
            'iniciada_en'    => now(),
        ]);

        $solicitud->didit_vendor_data = config('kyc.didit.vendor_data_por_intento', false)
            ? "kyc-{$usuarioId}-{$solicitud->id}"
            : "kyc-{$usuarioId}";

        $this->aplicarCambio($registro, $solicitud, EstadoKyc::EnProceso, [
            'origen' => EventoKyc::ORIGEN_USUARIO,
            'tipo'   => 'inicio_verificacion_didit',
            'ip'     => $ip,
        ]);

        return ['solicitud_id' => $solicitud->id, 'nueva' => true];
    }

    /**
     * Cierra como "failed" una solicitud recién creada cuando Didit no pudo crear la sesión.
     * Así no queda una solicitud abierta sin sesión y el usuario puede reintentar de inmediato.
     */
    private function cerrarComoFallidaPorError(int $solicitudId, ?string $ip): void
    {
        try {
            $this->transaccion(function () use ($solicitudId, $ip) {
                $solicitud = SolicitudKyc::where('id', $solicitudId)->lockForUpdate()->first();
                if (!$solicitud) {
                    return;
                }

                $registro = $this->bloquearUsuario($solicitud->user_id);

                if ($solicitud->estado === EstadoKyc::EnProceso && !$solicitud->didit_session_id
                    && $registro->solicitud_activa_id === $solicitud->id) {
                    $this->aplicarCambio($registro, $solicitud, EstadoKyc::Fallido, [
                        'origen'    => EventoKyc::ORIGEN_SISTEMA,
                        'tipo'      => 'error_creacion_sesion_didit',
                        'ip'        => $ip,
                        'atributos' => ['didit_estado_original' => 'error_creacion_sesion'],
                    ]);
                }
            });
        } catch (Throwable $e) {
            Log::error('KYC: no se pudo cerrar la solicitud ' . $solicitudId . ' tras el error de Didit (' . get_class($e) . ').');
        }
    }

    /**
     * Camino único para aplicar un estado informado por Didit (webhook o consulta de respaldo).
     *
     * @param  array<string,mixed> $ev Datos normalizados del evento.
     * @return array{success:bool,message:string,mensaje_usuario:string,respuesta:mixed,code:int}
     */
    private function procesarEstadoDidit(array $ev): array
    {
        $esWebhook = $ev['origen'] === EventoKyc::ORIGEN_DIDIT_WEBHOOK;
        $solicitud = SolicitudKyc::where('didit_session_id', $ev['session_id'])->first();

        if (!$solicitud) {
            if ($esWebhook) {
                $this->registrarEventoAislado([
                    'origen' => $ev['origen'], 'tipo' => $ev['tipo'], 'clave_idempotencia' => $ev['clave'],
                    'didit_event_id' => $ev['event_id'], 'didit_session_id' => $ev['session_id'],
                    'didit_estado' => $ev['estado_didit'], 'resultado' => EventoKyc::RESULTADO_IGNORADO_SESION_DESCONOCIDA,
                    'payload_hash' => $ev['payload_hash'], 'ip' => $ev['ip'], 'detalle' => $ev['detalle'],
                ]);
                Log::info('KYC Didit: evento de una sesión desconocida (' . $ev['estado_didit'] . '); se ignora.');
            }

            return $this->respuesta(true, __('Evento ignorado'), '', EventoKyc::RESULTADO_IGNORADO_SESION_DESCONOCIDA, 200);
        }

        $base = [
            'user_id' => $solicitud->user_id, 'solicitud_id' => $solicitud->id, 'origen' => $ev['origen'],
            'tipo' => $ev['tipo'], 'clave_idempotencia' => $ev['clave'], 'didit_event_id' => $ev['event_id'],
            'didit_session_id' => $ev['session_id'], 'didit_estado' => $ev['estado_didit'],
            'payload_hash' => $ev['payload_hash'], 'ip' => $ev['ip'], 'detalle' => $ev['detalle'],
        ];

        // El vendor_data firmado debe corresponder al usuario/intento dueño de la sesión.
        if (!is_string($ev['vendor_data']) || !hash_equals((string) $solicitud->didit_vendor_data, $ev['vendor_data'])) {
            Log::warning('KYC Didit: vendor_data no coincide con la solicitud ' . $solicitud->id . '; evento ignorado.');
            $this->registrarEventoAislado($base + ['resultado' => EventoKyc::RESULTADO_RECHAZADO_VENDOR]);

            return $this->respuesta(true, __('Evento ignorado'), '', EventoKyc::RESULTADO_RECHAZADO_VENDOR, 200);
        }

        // Un evento sandbox nunca debe modificar datos de producción (ni al revés).
        if ($ev['entorno'] !== null && $ev['entorno'] !== $this->didit->entornoEsperado()) {
            Log::warning('KYC Didit: evento del entorno "' . mb_substr($ev['entorno'], 0, 20) . '" distinto del configurado; ignorado.');
            $this->registrarEventoAislado($base + ['resultado' => EventoKyc::RESULTADO_IGNORADO_ENTORNO]);

            return $this->respuesta(true, __('Evento ignorado'), '', EventoKyc::RESULTADO_IGNORADO_ENTORNO, 200);
        }

        $mapa  = (array) config('kyc.didit.mapa_estados', []);
        $nuevo = array_key_exists($ev['estado_didit'], $mapa) ? EstadoKyc::tryFrom($mapa[$ev['estado_didit']]) : null;

        if (!$nuevo) {
            Log::warning('KYC Didit: estado desconocido "' . $ev['estado_didit'] . '" en la solicitud ' . $solicitud->id . '.');
            $this->registrarEventoAislado($base + ['resultado' => EventoKyc::RESULTADO_IGNORADO_ESTADO]);

            return $this->respuesta(true, __('Evento ignorado'), '', EventoKyc::RESULTADO_IGNORADO_ESTADO, 200);
        }

        $resultadoSaneado = $ev['resultado_saneado'];

        // Una aprobación comunicada por webhook se confirma con la API antes de autorizar al usuario.
        if ($esWebhook && $nuevo === EstadoKyc::Verificado && config('kyc.didit.confirmar_aprobacion_por_api', true)) {
            try {
                $decision = $this->didit->obtenerDecision($ev['session_id']);
            } catch (DiditException $e) {
                // Sin cambios: Didit reintentará el webhook y la consulta de respaldo lo resolverá.
                return $this->respuesta(false, __('Confirmación con Didit no disponible'), '', 'confirmacion_no_disponible', 503);
            }

            $coincide = $decision['status'] === 'Approved'
                && is_string($decision['vendor_data'] ?? null)
                && hash_equals((string) $solicitud->didit_vendor_data, $decision['vendor_data']);

            if (!$coincide) {
                Log::warning('KYC Didit: la API no confirma la aprobación de la solicitud ' . $solicitud->id . '; no se autoriza.');
                $this->registrarEventoAislado($base + ['resultado' => EventoKyc::RESULTADO_CONFIRMACION_FALLIDA]);

                return $this->respuesta(true, __('Evento ignorado'), '', EventoKyc::RESULTADO_CONFIRMACION_FALLIDA, 200);
            }

            $resultadoSaneado = DiditService::sanearDecision($decision);
        }

        return $this->transaccion(function () use ($ev, $solicitud, $nuevo, $base, $resultadoSaneado) {
            $registro = $this->bloquearUsuario($solicitud->user_id);
            $fila     = SolicitudKyc::where('id', $solicitud->id)->lockForUpdate()->firstOrFail();

            // Idempotencia: el mismo evento no se procesa dos veces (se comprueba ya con el bloqueo tomado).
            if ($ev['clave'] !== null && EventoKyc::where('clave_idempotencia', $ev['clave'])->exists()) {
                return $this->respuesta(true, __('Evento duplicado'), '', EventoKyc::RESULTADO_DUPLICADO, 200);
            }

            $anteriorUsuario = $registro->estadoEfectivo();
            $parcial = $base + ['estado_anterior' => $anteriorUsuario->value];

            if ($registro->solicitud_activa_id !== $fila->id || $fila->metodo !== MetodoKyc::Didit) {
                $this->registrarEvento($parcial + ['resultado' => EventoKyc::RESULTADO_IGNORADO_SESION_ANTIGUA]);

                return $this->respuesta(true, __('Evento ignorado'), '', EventoKyc::RESULTADO_IGNORADO_SESION_ANTIGUA, 200);
            }

            if ($fila->didit_ultimo_timestamp !== null && $ev['orden'] < (int) $fila->didit_ultimo_timestamp) {
                $this->registrarEvento($parcial + ['resultado' => EventoKyc::RESULTADO_IGNORADO_EVENTO_ANTIGUO]);

                return $this->respuesta(true, __('Evento ignorado'), '', EventoKyc::RESULTADO_IGNORADO_EVENTO_ANTIGUO, 200);
            }

            $atributosDidit = [
                'didit_estado_original'  => $ev['estado_didit'],
                'didit_resultado'        => $resultadoSaneado,
                'didit_ultimo_timestamp' => max($ev['orden'], (int) $fila->didit_ultimo_timestamp),
            ];

            if ($fila->estado === $nuevo) {
                $fila->fill($atributosDidit)->save();

                if ($ev['origen'] === EventoKyc::ORIGEN_DIDIT_WEBHOOK) {
                    $this->registrarEvento($parcial + [
                        'estado_nuevo' => $nuevo->value, 'resultado' => EventoKyc::RESULTADO_IGNORADO_SIN_CAMBIO,
                    ]);
                }

                return $this->respuesta(true, __('Evento procesado'), '', EventoKyc::RESULTADO_IGNORADO_SIN_CAMBIO, 200);
            }

            if (!$fila->estado->puedePasarA($nuevo)) {
                $this->registrarEvento($parcial + [
                    'estado_nuevo' => $nuevo->value, 'resultado' => EventoKyc::RESULTADO_IGNORADO_TRANSICION,
                ]);
                Log::warning('KYC Didit: transición no permitida ' . $fila->estado->value . ' -> ' . $nuevo->value . ' en la solicitud ' . $fila->id . '.');

                return $this->respuesta(true, __('Evento ignorado'), '', EventoKyc::RESULTADO_IGNORADO_TRANSICION, 200);
            }

            $this->aplicarCambio($registro, $fila, $nuevo, [
                'origen'             => $ev['origen'],
                'tipo'               => $ev['tipo'],
                'atributos'          => $atributosDidit,
                'clave_idempotencia' => $ev['clave'],
                'didit_event_id'     => $ev['event_id'],
                'didit_session_id'   => $ev['session_id'],
                'didit_estado'       => $ev['estado_didit'],
                'payload_hash'       => $ev['payload_hash'],
                'ip'                 => $ev['ip'],
                'detalle'            => $ev['detalle'],
            ]);

            return $this->respuesta(true, __('Evento procesado'), '', EventoKyc::RESULTADO_PROCESADO, 200);
        });
    }

    // ------------------------------------------------------------------
    // INTERNOS: ESCRITURA CENTRALIZADA
    // ------------------------------------------------------------------

    /**
     * Cambia el estado de una solicitud y el estado efectivo del usuario de forma atómica y registra
     * el evento de auditoría. Debe llamarse dentro de transaccion() y con la fila de usuarios_kyc bloqueada.
     *
     * Contexto admitido: origen, tipo, atributos (campos de la solicitud), admin_id, ip, detalle,
     * clave_idempotencia, didit_event_id, didit_session_id, didit_estado, payload_hash y sin_aviso
     * (true omite el evento EstadoKycCambiado en cambios intermedios que el usuario no debe ver).
     *
     * @param array<string,mixed> $contexto
     */
    private function aplicarCambio(UsuarioKyc $registro, SolicitudKyc $solicitud, EstadoKyc $nuevo, array $contexto): void
    {
        $anteriorUsuario   = $registro->estadoEfectivo();
        $anteriorSolicitud = $solicitud->estado;
        $ahora             = now();

        $solicitud->fill($contexto['atributos'] ?? []);
        $solicitud->estado        = $nuevo;
        $solicitud->clave_abierta = $nuevo->esAbierto() ? $solicitud->user_id : null;
        $solicitud->cerrada_en    = $nuevo->esAbierto() ? null : $ahora;

        if ($nuevo === EstadoKyc::Verificado) {
            $solicitud->aprobada_en = $ahora;
        } elseif ($nuevo === EstadoKyc::Rechazado) {
            $solicitud->rechazada_en = $ahora;
        }

        if (isset($contexto['admin_id'])) {
            $solicitud->revisado_por_admin_id = $contexto['admin_id'];
        }

        $solicitud->save();

        $registro->estado              = $nuevo;
        $registro->solicitud_activa_id = $solicitud->id;
        $registro->metodo_verificacion = $solicitud->metodo;
        $registro->version             = ((int) $registro->version) + 1;
        $registro->expira_en           = null;

        if ($nuevo === EstadoKyc::Verificado) {
            $registro->verificado_en            = $ahora;
            $registro->verificado_por_admin_id  = $contexto['admin_id'] ?? null;
        } else {
            $registro->verificado_en            = null;
            $registro->verificado_por_admin_id  = null;
        }

        $registro->save();

        $this->registrarEvento([
            'user_id'            => $solicitud->user_id,
            'solicitud_id'       => $solicitud->id,
            'origen'             => $contexto['origen'] ?? EventoKyc::ORIGEN_SISTEMA,
            'tipo'               => $contexto['tipo'] ?? 'cambio_estado',
            'clave_idempotencia' => $contexto['clave_idempotencia'] ?? null,
            'didit_event_id'     => $contexto['didit_event_id'] ?? null,
            'didit_session_id'   => $contexto['didit_session_id'] ?? null,
            'didit_estado'       => $contexto['didit_estado'] ?? null,
            'estado_anterior'    => $anteriorUsuario->value,
            'estado_nuevo'       => $nuevo->value,
            'resultado'          => EventoKyc::RESULTADO_PROCESADO,
            'detalle'            => ($contexto['detalle'] ?? []) + ['estado_solicitud_anterior' => $anteriorSolicitud?->value],
            'payload_hash'       => $contexto['payload_hash'] ?? null,
            'admin_id'           => $contexto['admin_id'] ?? null,
            'ip'                 => $contexto['ip'] ?? null,
        ]);

        $usuarioId = (int) $solicitud->user_id;
        unset($this->cacheVerificado[$usuarioId]);

        if ($anteriorUsuario !== $nuevo && empty($contexto['sin_aviso'])) {
            $solicitudId = (int) $solicitud->id;
            $metodo      = $solicitud->metodo;
            $origen      = (string) ($contexto['origen'] ?? EventoKyc::ORIGEN_SISTEMA);

            // Los avisos (correo, notificaciones) salen solo si la transacción se confirma.
            DB::afterCommit(function () use ($usuarioId, $solicitudId, $anteriorUsuario, $nuevo, $metodo, $origen) {
                unset($this->cacheVerificado[$usuarioId]);
                event(new EstadoKycCambiado($usuarioId, $solicitudId, $anteriorUsuario, $nuevo, $metodo, $origen));
            });
        }
    }

    /**
     * Asegura que exista la fila de usuarios_kyc y la devuelve bloqueada (FOR UPDATE).
     * La creación previa con INSERT IGNORE evita bloqueos de hueco y errores de duplicado por concurrencia.
     * Debe llamarse dentro de transaccion().
     */
    private function bloquearUsuario(int $usuarioId): UsuarioKyc
    {
        DB::table('usuarios_kyc')->insertOrIgnore([
            'user_id'    => $usuarioId,
            'estado'     => EstadoKyc::SinIniciar->value,
            'version'    => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return UsuarioKyc::where('user_id', $usuarioId)->lockForUpdate()->firstOrFail();
    }

    /**
     * Ejecuta un bloque en una transacción (con reintento ante bloqueos mutuos) y con la escritura KYC autorizada.
     *
     * @template T
     * @param  callable(): T $bloque
     * @return T
     */
    private function transaccion(callable $bloque): mixed
    {
        return DB::transaction(fn () => GuardiaEscrituraKyc::ejecutar($bloque), 3);
    }

    /**
     * Registra un evento de auditoría dentro de una transacción ya abierta.
     *
     * @param array<string,mixed> $datos
     */
    private function registrarEvento(array $datos): EventoKyc
    {
        return EventoKyc::create($datos);
    }

    /**
     * Registra un evento de auditoría fuera de una transacción (descartes sin cambio de estado).
     * Si la clave de idempotencia ya existe, el evento es un duplicado y no se vuelve a registrar.
     *
     * @param array<string,mixed> $datos
     */
    private function registrarEventoAislado(array $datos): void
    {
        try {
            $this->transaccion(fn () => $this->registrarEvento($datos));
        } catch (UniqueConstraintViolationException $e) {
            // Evento ya registrado por una entrega anterior del mismo webhook.
        }
    }

    // ------------------------------------------------------------------
    // UTILIDADES
    // ------------------------------------------------------------------

    /**
     * Construye la respuesta estándar del plugin.
     *
     * @return array{success:bool,message:string,mensaje_usuario:string,respuesta:mixed,code:int}
     */
    private function respuesta(bool $exito, string $mensaje, string $mensajeUsuario, mixed $respuesta, int $codigo): array
    {
        return [
            'success'         => $exito,
            'message'         => $mensaje,
            'mensaje_usuario' => $mensajeUsuario,
            'respuesta'       => $respuesta,
            'code'            => $codigo,
        ];
    }

    /**
     * Registra un error inesperado sin datos sensibles y devuelve una respuesta 500 segura.
     * El detalle técnico queda en el log de Laravel; el atributo "respuesta" solo lleva un código.
     *
     * @return array{success:bool,message:string,mensaje_usuario:string,respuesta:mixed,code:int}
     */
    private function errorInterno(Throwable $e, string $operacion): array
    {
        Log::error('KYC [' . $operacion . '] error interno: ' . get_class($e) . ' en ' . basename($e->getFile()) . ':' . $e->getLine());

        return $this->respuesta(false, __('Error interno del KYC'),
            __('No fue posible completar la operación. Inténtalo de nuevo en unos minutos.'), 'error_interno', 500);
    }
}
