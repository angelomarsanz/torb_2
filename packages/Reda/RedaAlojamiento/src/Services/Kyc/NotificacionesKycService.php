<?php

/**
 * Resumen: Notificaciones del KYC (Fase 8). Escucha los eventos que emite KycService y avisa a las personas
 * correspondientes:
 *
 *  USUARIO (correo): recibimos tu solicitud manual, identidad verificada, solicitud rechazada, corrección solicitada,
 *  verificación de Didit no completada, verificación expirada y verificación que dejó de estar vigente,
 *  verificación eliminada por un administrador (puede volver a enviarla).
 *  ADMINISTRADORES activos con el permiso manage_kyc (alerta en alertas_admin con tipo "kyc" y correo): llegó una
 *  solicitud manual nueva, el usuario respondió una corrección, o Didit dejó una verificación en revisión.
 *
 * Reglas:
 *  - Un aviso nunca interrumpe el KYC: todo se ejecuta protegido (cualquier error se registra sin datos personales) y
 *    los correos se envían después de responder la petición (terminating), para no demorar al usuario ni al webhook
 *    de Didit con el servidor de correo.
 *  - Los correos no incluyen el texto libre del administrador (motivo del rechazo, corrección solicitada, motivo de la
 *    eliminación) ni datos del documento: solo avisan y enlazan a la pantalla de verificaciones, protegida por la sesión del usuario.
 *  - Fallos, expiraciones y pérdidas de verificación se avisan solo si provienen de una decisión de Didit. El error
 *    técnico al crear la sesión (origen "sistema") no se notifica: el usuario ya lo vio en pantalla.
 *  - Los avisos se pueden apagar con KYC_NOTIFICACIONES=false o por canal en config/kyc.php (notificaciones).
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Services\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Services\Kyc;

use App\Http\Helpers\UserPermission;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Reda\RedaAlojamiento\Enums\EstadoKyc;
use Reda\RedaAlojamiento\Events\CorreccionKycAtendida;
use Reda\RedaAlojamiento\Events\CorreccionKycSolicitada;
use Reda\RedaAlojamiento\Events\EstadoKycCambiado;
use Reda\RedaAlojamiento\Events\VerificacionKycEliminada;
use Reda\RedaAlojamiento\Models\Alerta\AlertaAdmin;
use Reda\RedaAlojamiento\Models\Kyc\EventoKyc;
use Reda\RedaAlojamiento\Policies\KycPolicy;
use Throwable;

class NotificacionesKycService
{
    /** Tipo con el que se guardan las alertas del KYC en alertas_admin. */
    public const TIPO_ALERTA = 'kyc';

    /** Plantillas de correo del plugin. */
    private const VISTA_USUARIO = 'reda-alojamiento::emails.kyc_notificacion_usuario';
    private const VISTA_ADMIN   = 'reda-alojamiento::emails.kyc_notificacion_admin';

    // ------------------------------------------------------------------
    // ESCUCHAS DE EVENTOS
    // ------------------------------------------------------------------

    /**
     * Avisa según el cambio de estado de la verificación de un usuario.
     */
    public function alCambiarEstado(EstadoKycCambiado $evento): void
    {
        if (!$this->habilitado()) {
            return;
        }

        $situacionUsuario = $this->situacionParaUsuario($evento);

        if ($situacionUsuario !== null) {
            $this->avisarUsuario($evento->usuarioId, $situacionUsuario);
        }

        $situacionAdministradores = $this->situacionParaAdministradores($evento);

        if ($situacionAdministradores !== null) {
            $this->avisarAdministradores($evento->usuarioId, $situacionAdministradores);
        }
    }

    /**
     * Avisa al usuario de que un administrador le pidió corregir su solicitud manual.
     */
    public function alSolicitarCorreccion(CorreccionKycSolicitada $evento): void
    {
        if ($this->habilitado()) {
            $this->avisarUsuario($evento->usuarioId, 'correccion');
        }
    }

    /**
     * Avisa al usuario de que un administrador eliminó su verificación y de que puede enviarla de nuevo.
     */
    public function alEliminarVerificacion(VerificacionKycEliminada $evento): void
    {
        if ($this->habilitado()) {
            $this->avisarUsuario($evento->usuarioId, 'eliminada');
        }
    }

    /**
     * Avisa a los administradores de que el usuario respondió la corrección y su solicitud espera nueva revisión.
     */
    public function alAtenderCorreccion(CorreccionKycAtendida $evento): void
    {
        if ($this->habilitado()) {
            $this->avisarAdministradores($evento->usuarioId, 'correccion_atendida');
        }
    }

    // ------------------------------------------------------------------
    // MATRIZ DE AVISOS
    // ------------------------------------------------------------------

    /**
     * Situación que se le comunica al usuario por un cambio de estado, o null si no se le avisa.
     *
     * @return string|null recibida|verificada|rechazada|no_completada|expirada|perdida
     */
    public function situacionParaUsuario(EstadoKycCambiado $evento): ?string
    {
        $decidioDidit = $this->decidioDidit($evento->origen);

        // Una verificación vigente que Didit revoca: se avisa siempre que la haya cambiado Didit.
        if ($evento->anterior === EstadoKyc::Verificado && $evento->nuevo !== EstadoKyc::Verificado) {
            return $decidioDidit ? 'perdida' : null;
        }

        return match ($evento->nuevo) {
            EstadoKyc::Verificado      => 'verificada',
            EstadoKyc::Rechazado       => 'rechazada',
            EstadoKyc::RevisionManual  => $evento->anterior !== EstadoKyc::RevisionManual ? 'recibida' : null,
            EstadoKyc::Fallido         => $decidioDidit ? 'no_completada' : null,
            EstadoKyc::Expirado        => $decidioDidit ? 'expirada' : null,
            default                    => null,
        };
    }

    /**
     * Situación que se comunica a los administradores por un cambio de estado, o null si no se les avisa.
     *
     * @return string|null nueva_manual|didit_en_revision
     */
    public function situacionParaAdministradores(EstadoKycCambiado $evento): ?string
    {
        if ($evento->nuevo === EstadoKyc::RevisionManual && $evento->anterior !== EstadoKyc::RevisionManual) {
            return 'nueva_manual';
        }

        if ($evento->nuevo === EstadoKyc::Pendiente && $evento->anterior !== EstadoKyc::Pendiente && $this->decidioDidit($evento->origen)) {
            return 'didit_en_revision';
        }

        return null;
    }

    // ------------------------------------------------------------------
    // AVISO AL USUARIO
    // ------------------------------------------------------------------

    /**
     * Envía al usuario el correo de la situación indicada (tras responder la petición).
     */
    private function avisarUsuario(int $usuarioId, string $situacion): void
    {
        $this->protegido('avisarUsuario:' . $situacion, function () use ($usuarioId, $situacion) {
            if (!config('kyc.notificaciones.correo_usuario', true)) {
                return;
            }

            $usuario = User::find($usuarioId);

            if (!$usuario || !$this->correoValido($usuario->email)) {
                return;
            }

            $textos = $this->textosUsuario($situacion);

            $this->enviarCorreo(
                (string) $usuario->email,
                $this->limpiarNombre(trim((string) $usuario->first_name . ' ' . (string) $usuario->last_name)),
                $textos['asunto'] . ' - ' . siteName(),
                self::VISTA_USUARIO,
                [
                    'nombreUsuario' => $this->limpiarNombre((string) $usuario->first_name),
                    'titulo'        => $textos['titulo'],
                    'parrafos'      => $textos['parrafos'],
                    'textoBoton'    => $textos['boton'],
                    'urlBoton'      => url((string) config('kyc.url_seccion', 'users/edit-verification')),
                    'color'         => $textos['color'],
                ],
                'usuario:' . $situacion . ':' . $usuarioId
            );
        });
    }

    /**
     * Textos del correo al usuario para cada situación (ya traducidos).
     *
     * @return array{asunto:string,titulo:string,parrafos:array<int,string>,boton:string,color:string}
     */
    public function textosUsuario(string $situacion): array
    {
        return match ($situacion) {
            'recibida' => [
                'asunto'   => __('Recibimos tu solicitud de verificación de identidad'),
                'titulo'   => __('Recibimos tu solicitud'),
                'parrafos' => [
                    __('Recibimos tus datos y documentos para verificar tu identidad. Una persona los revisará y te avisaremos cuando haya una decisión.'),
                    __('No necesitas hacer nada más por ahora.'),
                ],
                'boton'    => __('Ver el estado de mi verificación'),
                'color'    => '#1f78c1',
            ],
            'verificada' => [
                'asunto'   => __('Tu identidad fue verificada'),
                'titulo'   => __('¡Tu identidad está verificada!'),
                'parrafos' => [
                    __('Ya puedes publicar alojamientos y negocios, y hacer reservas en la plataforma.'),
                ],
                'boton'    => __('Ver mi verificación'),
                'color'    => '#1a9a50',
            ],
            'rechazada' => [
                'asunto'   => __('Tu solicitud de verificación no fue aprobada'),
                'titulo'   => __('Tu solicitud no fue aprobada'),
                'parrafos' => [
                    __('Revisamos tu solicitud de verificación de identidad y no pudimos aprobarla.'),
                    __('Inicia sesión para ver el motivo y volver a intentarlo cuando quieras.'),
                ],
                'boton'    => __('Ver el motivo y reintentar'),
                'color'    => '#d33a3a',
            ],
            'correccion' => [
                'asunto'   => __('Necesitamos que corrijas tu solicitud de verificación'),
                'titulo'   => __('Debes corregir tu solicitud'),
                'parrafos' => [
                    __('Un administrador revisó tu solicitud de verificación de identidad y necesita que corrijas algo para poder continuar.'),
                    __('Inicia sesión para ver qué debes corregir y enviar la corrección.'),
                ],
                'boton'    => __('Corregir mi solicitud'),
                'color'    => '#d68a00',
            ],
            'eliminada' => [
                'asunto'   => __('Tu verificación de identidad fue eliminada'),
                'titulo'   => __('Tu verificación fue eliminada'),
                'parrafos' => [
                    __('Un administrador eliminó tu verificación de identidad junto con los datos y documentos que enviaste.'),
                    __('Mientras no vuelvas a verificarte no podrás publicar ni reservar. Puedes enviar tu verificación de nuevo cuando quieras.'),
                ],
                'boton'    => __('Verificar mi identidad'),
                'color'    => '#d68a00',
            ],
            'no_completada' => [
                'asunto'   => __('No pudimos completar tu verificación de identidad'),
                'titulo'   => __('Tu verificación no se completó'),
                'parrafos' => [
                    __('La verificación automática con Didit no pudo completarse.'),
                    __('Puedes intentarlo de nuevo o enviar tus documentos para que una persona los revise.'),
                ],
                'boton'    => __('Elegir cómo verificarme'),
                'color'    => '#d33a3a',
            ],
            'expirada' => [
                'asunto'   => __('Tu verificación de identidad expiró'),
                'titulo'   => __('Tu verificación expiró'),
                'parrafos' => [
                    __('Tu verificación de identidad ya no está vigente.'),
                    __('Verifícate de nuevo para seguir publicando y reservando.'),
                ],
                'boton'    => __('Verificar mi identidad'),
                'color'    => '#d68a00',
            ],
            default => [ // perdida
                'asunto'   => __('Tu verificación de identidad ya no está vigente'),
                'titulo'   => __('Tu verificación ya no está vigente'),
                'parrafos' => [
                    __('El proveedor de verificación cambió el resultado de tu identidad y ya no figura como verificada.'),
                    __('Mientras tanto no podrás publicar ni reservar. Verifícate de nuevo para recuperar el acceso.'),
                ],
                'boton'    => __('Verificar de nuevo'),
                'color'    => '#d68a00',
            ],
        };
    }

    // ------------------------------------------------------------------
    // AVISO A LOS ADMINISTRADORES
    // ------------------------------------------------------------------

    /**
     * Crea la alerta y envía el correo a cada administrador activo con el permiso manage_kyc.
     */
    private function avisarAdministradores(int $usuarioId, string $situacion): void
    {
        $this->protegido('avisarAdministradores:' . $situacion, function () use ($usuarioId, $situacion) {
            $conAlerta = (bool) config('kyc.notificaciones.alerta_admin', true);
            $conCorreo = (bool) config('kyc.notificaciones.correo_admin', true);

            if (!$conAlerta && !$conCorreo) {
                return;
            }

            $usuario = User::find($usuarioId);

            if (!$usuario) {
                return;
            }

            $nombre  = $this->limpiarNombre(trim((string) $usuario->first_name . ' ' . (string) $usuario->last_name)) ?: (string) $usuario->email;
            $textos  = $this->textosAdministrador($situacion, $nombre, (string) $usuario->email);
            $destino = $this->administradoresDeKyc();

            if ($destino->isEmpty()) {
                Log::warning('KYC notificaciones: ningún administrador activo tiene el permiso manage_kyc; no hay a quién avisar.');

                return;
            }

            $urlRevision = url('admin/reda/kyc?grupo=pendientes&buscar=' . urlencode((string) $usuario->email));

            foreach ($destino as $administrador) {
                if ($conAlerta) {
                    $this->protegido('alerta_admin', fn () => AlertaAdmin::create([
                        'admin_id'      => $administrador->id,
                        'titulo'        => $textos['titulo'],
                        'mensaje'       => $textos['mensaje'],
                        'tipo'          => self::TIPO_ALERTA,
                        'user_id'       => $usuarioId,
                        'leido'         => 0,
                        'fecha_lectura' => null,
                    ]));
                }

                if ($conCorreo && $this->correoValido($administrador->email)) {
                    $this->enviarCorreo(
                        (string) $administrador->email,
                        $this->limpiarNombre((string) $administrador->username),
                        $textos['titulo'] . ' - ' . siteName(),
                        self::VISTA_ADMIN,
                        [
                            'titulo'       => $textos['titulo'],
                            'mensaje'      => $textos['mensaje'],
                            'nombreUsuario' => $nombre,
                            'correoUsuario' => (string) $usuario->email,
                            'urlBoton'     => $urlRevision,
                            'textoBoton'   => __('Revisar solicitud'),
                        ],
                        'admin:' . $situacion . ':' . $usuarioId
                    );
                }
            }
        });
    }

    /**
     * Textos de la alerta y del correo para los administradores (ya traducidos).
     *
     * @return array{titulo:string,mensaje:string}
     */
    public function textosAdministrador(string $situacion, string $nombre, string $correo): array
    {
        $variables = ['nombre' => $nombre, 'correo' => $correo];

        return match ($situacion) {
            'correccion_atendida' => [
                'titulo'  => __('Corrección recibida en una verificación'),
                'mensaje' => __(':nombre (:correo) respondió la corrección solicitada. La solicitud espera una nueva revisión.', $variables),
            ],
            'didit_en_revision' => [
                'titulo'  => __('Verificación con Didit en revisión'),
                'mensaje' => __(':nombre (:correo) tiene una verificación con Didit en revisión. Se resuelve en la consola de Didit y el resultado llegará solo.', $variables),
            ],
            default => [ // nueva_manual
                'titulo'  => __('Nueva solicitud de verificación de identidad'),
                'mensaje' => __(':nombre (:correo) envió sus documentos para revisión manual.', $variables),
            ],
        };
    }

    /**
     * Administradores activos cuyo rol tiene el permiso manage_kyc (el mismo criterio de KycPolicy).
     *
     * @return \Illuminate\Support\Collection<int,Admin>
     */
    private function administradoresDeKyc()
    {
        return Admin::query()
            ->where('status', 'Active')
            ->get()
            ->filter(static fn (Admin $administrador) => (int) UserPermission::has_permission($administrador->id, KycPolicy::PERMISO_ADMIN) > 0)
            ->values();
    }

    // ------------------------------------------------------------------
    // AUXILIARES
    // ------------------------------------------------------------------

    /**
     * Indica si los avisos están habilitados (interruptor general KYC_NOTIFICACIONES).
     */
    private function habilitado(): bool
    {
        return (bool) config('kyc.notificaciones.habilitadas', true);
    }

    /**
     * Indica si el cambio lo decidió Didit (webhook o consulta de respaldo).
     */
    private function decidioDidit(string $origen): bool
    {
        return in_array($origen, [EventoKyc::ORIGEN_DIDIT_WEBHOOK, EventoKyc::ORIGEN_DIDIT_CONSULTA], true);
    }

    /**
     * Envía un correo con la plantilla indicada después de responder la petición. Si el envío falla solo se
     * registra el tipo de error (sin direcciones ni nombres) y el KYC continúa con normalidad.
     *
     * @param array<string,mixed> $datos
     * @param string              $referencia Identificador sin datos personales para el registro.
     */
    private function enviarCorreo(string $destino, string $nombreDestino, string $asunto, string $vista, array $datos, string $referencia): void
    {
        app()->terminating(function () use ($destino, $nombreDestino, $asunto, $vista, $datos, $referencia) {
            try {
                Mail::send($vista, $datos, function ($mensaje) use ($destino, $nombreDestino, $asunto) {
                    $mensaje->to($destino, $nombreDestino !== '' ? $nombreDestino : null)->subject($asunto);
                });

                Log::info('KYC notificación enviada [' . $referencia . '].');
            } catch (Throwable $e) {
                Log::error('KYC notificación: no se pudo enviar el correo [' . $referencia . '] (' . get_class($e) . ' en ' . basename($e->getFile()) . ':' . $e->getLine() . ').');
            }
        });
    }

    /**
     * Ejecuta una acción sin permitir que un error salga hacia el flujo del KYC.
     */
    private function protegido(string $contexto, callable $accion): void
    {
        try {
            $accion();
        } catch (Throwable $e) {
            Log::error('KYC notificaciones [' . $contexto . '] error: ' . get_class($e) . ' en ' . basename($e->getFile()) . ':' . $e->getLine());
        }
    }

    /**
     * Indica si el texto es una dirección de correo válida.
     */
    private function correoValido(mixed $correo): bool
    {
        return is_string($correo) && $correo !== '' && filter_var($correo, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Nombre apto para usarse como nombre del destinatario: sin saltos de línea ni caracteres de control, que
     * permitirían inyectar cabeceras en el correo. El HTML no se elimina: las plantillas lo escapan con {{ }}.
     */
    private function limpiarNombre(string $nombre): string
    {
        return trim((string) preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $nombre));
    }
}
