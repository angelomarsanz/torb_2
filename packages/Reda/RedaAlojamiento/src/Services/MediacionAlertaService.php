<?php

/**
 * Resumen: Servicio de Alertas, Notificaciones y Suspensiones por Límites de Mediaciones.
 * Evalúa los umbrales configurados en la tabla settings ('Cantidad mediaciones permitidas primer aviso'
 * y 'Cantidad mediaciones segundo aviso y suspensión') cada vez que se genera una nueva mediación.
 * Si se alcanza el umbral de primer aviso, envía correo preventivo, mensaje al buzón /inbox del usuario,
 * correo a administradores y alerta administrativa en el panel.
 * Si se alcanza el límite de suspensión, coloca el estatus del usuario en 'Inactive' (Suspendido),
 * notifica por correo y buzón, y alerta a todos los administradores activos.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Services
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Services;

use App\Models\Admin;
use App\Models\Bookings;
use App\Models\Messages;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Reda\RedaAlojamiento\Models\Alerta\AlertaAdmin;
use Reda\RedaAlojamiento\Models\Disputa\Disputa;
use Reda\RedaAlojamiento\Models\Disputa\MensajeMetadata;
use Reda\RedaAlojamiento\Models\Disputa\UsuarioAvisoMediacion;

class MediacionAlertaService
{
    /**
     * Evalúa los límites de mediaciones configurados para los usuarios involucrados en la disputa.
     * Dispara avisos preventivos, suspensiones de cuenta y notificaciones a administradores.
     *
     * @param Disputa $disputa
     * @return void
     */
    public static function verificarLimites(Disputa $disputa)
    {
        try {
            // 1. Obtener valores de umbral desde la tabla settings
            $settingPrimerAviso = DB::table('settings')
                ->where('name', 'Cantidad mediaciones permitidas primer aviso')
                ->first();

            $settingSegundoAviso = DB::table('settings')
                ->where('name', 'Cantidad mediaciones segundo aviso y suspensión')
                ->first();

            $primerAviso = $settingPrimerAviso ? (int) $settingPrimerAviso->value : 0;
            $segundoAviso = $settingSegundoAviso ? (int) $settingSegundoAviso->value : 0;

            // Si ambos límites están en 0 o no existen, no se ejecutan acciones
            if ($primerAviso <= 0 && $segundoAviso <= 0) {
                return;
            }

            // 2. Determinar usuarios a evaluar: contraparte e iniciador
            $idTurista = (int) $disputa->id_usuario_turista;
            $idAnfitrion = (int) $disputa->id_usuario_anfitrion;
            $idIniciador = (int) $disputa->id_usuario_inicial;

            // La contraparte denunciada es el usuario principal evaluado
            $idContraparte = ($idIniciador === $idTurista) ? $idAnfitrion : $idTurista;

            $usuariosAEvaluar = array_unique(array_filter([$idContraparte, $idIniciador]));

            foreach ($usuariosAEvaluar as $userId) {
                self::evaluarUsuarioIndividual($userId, $disputa, $primerAviso, $segundoAviso);
            }

        } catch (Exception $e) {
            Log::error("Error en MediacionAlertaService::verificarLimites: " . $e->getMessage() . " en " . $e->getFile() . ":" . $e->getLine());
        }
    }

    /**
     * Evalúa a un usuario individual contra los umbrales y ejecuta las alertas correspondientes.
     *
     * @param int $userId
     * @param Disputa $disputa
     * @param int $primerAviso
     * @param int $segundoAviso
     * @return void
     */
    protected static function evaluarUsuarioIndividual($userId, Disputa $disputa, $primerAviso, $segundoAviso)
    {
        $user = User::find($userId);
        if (!$user) {
            return;
        }

        // Conteo total de mediaciones acumuladas en las que participa el usuario
        $conteo = Disputa::where(function($q) use ($userId) {
            $q->where('id_usuario_turista', $userId)
              ->orWhere('id_usuario_anfitrion', $userId);
        })->count();

        // Registro de control para evitar duplicidad de avisos
        $registroAviso = UsuarioAvisoMediacion::firstOrCreate(
            ['user_id' => $userId],
            [
                'conteo_mediaciones' => $conteo,
                'primer_aviso_enviado' => 0,
                'segundo_aviso_enviado' => 0,
                'cuenta_suspendida' => 0,
            ]
        );

        $registroAviso->conteo_mediaciones = $conteo;
        $registroAviso->save();

        $nombreCompleto = trim($user->first_name . ' ' . $user->last_name);

        // --- CASO 1: SUPERACIÓN DE UMBRAL DE SUSPENSIÓN (SEGUNDO AVISO) ---
        if ($segundoAviso > 0 && $conteo >= $segundoAviso) {
            if (!$registroAviso->cuenta_suspendida) {
                // A. Suspender la cuenta del usuario cambiando su estatus nativo a Inactive
                $user->status = 'Inactive';
                $user->save();

                // B. Actualizar registro de control
                $registroAviso->cuenta_suspendida = 1;
                $registroAviso->segundo_aviso_enviado = 1;
                $registroAviso->fecha_suspension = Carbon::now();
                $registroAviso->fecha_segundo_aviso = Carbon::now();
                $registroAviso->motivo = __('Cuenta suspendida automáticamente por acumular :conteo mediaciones (límite: :limite).', [
                    'conteo' => $conteo,
                    'limite' => $segundoAviso
                ]);
                $registroAviso->save();

                Log::warning("Cuenta suspendida por mediaciones para usuario ID: {$user->id} ({$user->email}) - Conteo: {$conteo}");

                // C. Enviar correo al usuario
                self::enviarEmail(
                    $user->email,
                    $nombreCompleto,
                    __('Notificación de suspensión de cuenta por mediaciones - ') . siteName(),
                    'reda-alojamiento::emails.suspension_usuario',
                    [
                        'nombreUsuario' => $nombreCompleto,
                        'emailUsuario' => $user->email,
                        'conteoMediaciones' => $conteo,
                        'limiteSegundoAviso' => $segundoAviso,
                        'disputaId' => $disputa->id,
                    ]
                );

                // D. Enviar mensaje a su buzón /inbox
                $textoBuzonSuspension = __('Aviso del sistema: Su cuenta ha sido suspendida automáticamente al acumular :conteo mediaciones, alcanzando el límite máximo permitido (:limite). Comuníquese con soporte si requiere asistencia.', [
                    'conteo' => $conteo,
                    'limite' => $segundoAviso
                ]);
                self::enviarMensajeBuzon($user, $disputa, $textoBuzonSuspension);

                // E. Enviar correo a todos los administradores activos
                self::notificarAdminsPorEmail(
                    __('Alerta Crítica: Cuenta de usuario suspendida por exceso de mediaciones'),
                    'reda-alojamiento::emails.suspension_admin',
                    [
                        'idUsuario' => $user->id,
                        'nombreUsuario' => $nombreCompleto,
                        'emailUsuario' => $user->email,
                        'conteoMediaciones' => $conteo,
                        'limiteSegundoAviso' => $segundoAviso,
                        'disputaId' => $disputa->id,
                    ]
                );

                // F. Crear alerta para cada administrador en alertas_admin
                self::crearAlertaAdmins(
                    __('Cuenta suspendida: Límite de mediaciones excedido'),
                    __('El usuario :nombre (:email) ha acumulado :conteo mediaciones (límite: :limite). Su cuenta fue suspendida automáticamente.', [
                        'nombre' => $nombreCompleto,
                        'email' => $user->email,
                        'conteo' => $conteo,
                        'limite' => $segundoAviso
                    ]),
                    'suspension',
                    $disputa->id,
                    $user->id
                );
            }

            return; // Si ya se procesó suspensión, no se evalúa primer aviso
        }

        // --- CASO 2: SUPERACIÓN DE UMBRAL DE PRIMER AVISO PREVENTIVO ---
        if ($primerAviso > 0 && $conteo >= $primerAviso) {
            if (!$registroAviso->primer_aviso_enviado && !$registroAviso->cuenta_suspendida) {
                // A. Registrar envío de primer aviso
                $registroAviso->primer_aviso_enviado = 1;
                $registroAviso->fecha_primer_aviso = Carbon::now();
                $registroAviso->save();

                Log::info("Primer aviso preventivo emitido para usuario ID: {$user->id} ({$user->email}) - Conteo: {$conteo}");

                // B. Enviar correo al usuario
                self::enviarEmail(
                    $user->email,
                    $nombreCompleto,
                    __('Aviso preventivo: Límite de mediaciones permitidas - ') . siteName(),
                    'reda-alojamiento::emails.primer_aviso_usuario',
                    [
                        'nombreUsuario' => $nombreCompleto,
                        'emailUsuario' => $user->email,
                        'conteoMediaciones' => $conteo,
                        'limitePrimerAviso' => $primerAviso,
                        'limiteSegundoAviso' => $segundoAviso,
                        'disputaId' => $disputa->id,
                    ]
                );

                // C. Enviar mensaje a su buzón /inbox
                $textoBuzonPrimerAviso = __('Aviso preventivo: Le informamos que ha acumulado :conteo mediaciones en la plataforma, alcanzando el umbral de primer aviso (:limite). Le recordamos que al acumular :limite_suspension mediaciones su cuenta será suspendida.', [
                    'conteo' => $conteo,
                    'limite' => $primerAviso,
                    'limite_suspension' => $segundoAviso
                ]);
                self::enviarMensajeBuzon($user, $disputa, $textoBuzonPrimerAviso);

                // D. Enviar correo a todos los administradores activos
                self::notificarAdminsPorEmail(
                    __('Alerta Administrativa: Primer aviso de mediaciones alcanzado'),
                    'reda-alojamiento::emails.primer_aviso_admin',
                    [
                        'idUsuario' => $user->id,
                        'nombreUsuario' => $nombreCompleto,
                        'emailUsuario' => $user->email,
                        'conteoMediaciones' => $conteo,
                        'limitePrimerAviso' => $primerAviso,
                        'limiteSegundoAviso' => $segundoAviso,
                        'disputaId' => $disputa->id,
                    ]
                );

                // E. Crear alerta para cada administrador en alertas_admin
                self::crearAlertaAdmins(
                    __('Primer aviso: Usuario alcanzando límite de mediaciones'),
                    __('El usuario :nombre (:email) ha alcanzado :conteo mediaciones acumuladas (umbral: :limite).', [
                        'nombre' => $nombreCompleto,
                        'email' => $user->email,
                        'conteo' => $conteo,
                        'limite' => $primerAviso
                    ]),
                    'primer_aviso',
                    $disputa->id,
                    $user->id
                );
            }
        }
    }

    /**
     * Envía un mensaje informativo al buzón /inbox del usuario vinculado a la disputa.
     * Utiliza la tabla Messages del core combinada con reda_mensajes_metadata para identificarlo como admin.
     *
     * @param User $user
     * @param Disputa $disputa
     * @param string $textoMensaje
     * @return void
     */
    protected static function enviarMensajeBuzon(User $user, Disputa $disputa, $textoMensaje)
    {
        try {
            $booking = Bookings::find($disputa->booking_id);
            if (!$booking) {
                return;
            }

            // Identificar un ID de administrador para el emisor
            $admin = Admin::where('status', 'active')->first();
            $adminId = $admin ? $admin->id : 1;

            // Tipo de mensaje 'disputas' o tipo chat predeterminado
            $tipoDisputas = DB::table('message_type')->where('name', 'disputas')->first();
            $typeId = $tipoDisputas ? $tipoDisputas->id : 1;

            $message = new Messages();
            $message->property_id = $booking->property_id;
            $message->booking_id  = $booking->id;
            $message->receiver_id = $user->id;
            $message->sender_id   = $adminId;
            $message->message     = $textoMensaje;
            $message->type_id     = $typeId;
            $message->read        = 0;
            $message->save();

            // Evitar conflictos con accessors de PHP 8.2
            $message->makeHidden(['host_user', 'guest_user']);

            // Metadato del plugin indicando que el remitente es Administrador
            MensajeMetadata::updateOrCreate(
                ['message_id' => $message->id],
                ['sender_type' => 'admin']
            );

            Log::info("Mensaje en buzón enviado exitosamente a usuario {$user->id} para booking {$booking->id}.");

        } catch (Exception $e) {
            Log::error("Error al enviar mensaje a buzón: " . $e->getMessage());
        }
    }

    /**
     * Crea un registro en la tabla alertas_admin para cada administrador activo del sistema.
     *
     * @param string $titulo
     * @param string $mensaje
     * @param string $tipo
     * @param int|null $disputaId
     * @param int|null $userId
     * @return void
     */
    protected static function crearAlertaAdmins($titulo, $mensaje, $tipo, $disputaId = null, $userId = null)
    {
        try {
            $admins = Admin::where('status', 'active')->get();

            foreach ($admins as $admin) {
                AlertaAdmin::create([
                    'admin_id'      => $admin->id,
                    'titulo'        => $titulo,
                    'mensaje'       => $mensaje,
                    'tipo'          => $tipo,
                    'disputa_id'    => $disputaId,
                    'user_id'       => $userId,
                    'leido'         => 0,
                    'fecha_lectura' => null,
                ]);
            }

            Log::info("Alertas administrativas creadas para " . count($admins) . " administradores.");

        } catch (Exception $e) {
            Log::error("Error al crear alertas en alertas_admin: " . $e->getMessage());
        }
    }

    /**
     * Envía un correo electrónico a todos los administradores activos.
     *
     * @param string $asunto
     * @param string $vistaBlade
     * @param array $datos
     * @return void
     */
    protected static function notificarAdminsPorEmail($asunto, $vistaBlade, array $datos)
    {
        try {
            $admins = Admin::where('status', 'active')->whereNotNull('email')->get();

            foreach ($admins as $admin) {
                if (filter_var($admin->email, FILTER_VALIDATE_EMAIL)) {
                    self::enviarEmail(
                        $admin->email,
                        $admin->username ?: __('Administrador'),
                        $asunto . ' - ' . siteName(),
                        $vistaBlade,
                        $datos
                    );
                }
            }

        } catch (Exception $e) {
            Log::error("Error en notificarAdminsPorEmail: " . $e->getMessage());
        }
    }

    /**
     * Realiza el envío seguro de correo electrónico con gestión de excepciones.
     *
     * @param string $emailDestino
     * @param string $nombreDestino
     * @param string $asunto
     * @param string $vistaBlade
     * @param array $datos
     * @return bool
     */
    protected static function enviarEmail($emailDestino, $nombreDestino, $asunto, $vistaBlade, array $datos)
    {
        if (empty($emailDestino) || !filter_var($emailDestino, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        try {
            Mail::send($vistaBlade, $datos, function ($m) use ($emailDestino, $nombreDestino, $asunto) {
                $m->to($emailDestino, $nombreDestino)->subject($asunto);
            });

            Log::info("Correo enviado exitosamente a: {$emailDestino} con asunto: {$asunto}");
            return true;

        } catch (Exception $e) {
            Log::error("Fallo al enviar correo a {$emailDestino}: " . $e->getMessage());
            return false;
        }
    }
}
