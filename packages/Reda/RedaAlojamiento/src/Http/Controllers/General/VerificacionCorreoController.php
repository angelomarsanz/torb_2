<?php

/**
 * Controlador para la Verificación y Corrección de Correo Electrónico
 *
 * Este controlador gestiona la actualización de direcciones de correo no verificadas
 * y el reenvío de correos de confirmación en el flujo de registro e inicio de sesión
 * del plugin Reda Alojamiento.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Http\Controllers\General
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EmailController;
use App\Models\PasswordResets;
use App\Models\User;
use App\Models\UsersVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class VerificacionCorreoController extends Controller
{
    /**
     * Actualiza la dirección de correo electrónico no confirmada de un usuario
     * y le reenvía el correo de verificación con un nuevo token.
     *
     * @param  \Illuminate\Http\Request      $request
     * @param  \App\Http\Controllers\EmailController $emailController
     * @return \Illuminate\Http\JsonResponse
     */
    public function actualizarCorreoYReenviar(Request $request, EmailController $emailController)
    {
        try {
            $reglas = [
                'correo_actual' => 'required|email|max:255',
                'nuevo_correo'  => 'required|email|max:255',
            ];

            $mensajes = [
                'correo_actual.required' => __('El correo actual es obligatorio.'),
                'correo_actual.email'    => __('El correo actual no tiene un formato válido.'),
                'nuevo_correo.required'  => __('El nuevo correo electrónico es obligatorio.'),
                'nuevo_correo.email'     => __('El nuevo correo no tiene un formato válido.'),
            ];

            $validador = Validator::make($request->all(), $reglas, $mensajes);

            if ($validador->fails()) {
                $respuesta = [
                    'success' => false,
                    'message' => __('Datos de validación inválidos'),
                    'mensaje_usuario' => $validador->errors()->first(),
                    'respuesta' => $validador->errors(),
                    'code' => 422
                ];
                return response()->json($respuesta, 422);
            }

            $correoActual = trim($request->correo_actual);
            $nuevoCorreo  = trim($request->nuevo_correo);

            // Buscar usuario por su correo actual
            $usuario = User::where('email', $correoActual)->first();

            if (!$usuario) {
                $respuesta = [
                    'success' => false,
                    'message' => __('Usuario no encontrado'),
                    'mensaje_usuario' => __('No se encontró ninguna cuenta asociada a la dirección de correo proporcionada.'),
                    'respuesta' => '',
                    'code' => 404
                ];
                return response()->json($respuesta, 404);
            }

            // Comprobar si el usuario ya está verificado en users_verification
            $verificacion = UsersVerification::where('user_id', $usuario->id)->first();
            if ($verificacion && strtolower($verificacion->email) === 'yes') {
                $respuesta = [
                    'success' => false,
                    'message' => __('Usuario ya verificado'),
                    'mensaje_usuario' => __('Esta cuenta de correo ya se encuentra confirmada. Por favor inicie sesión con sus credenciales.'),
                    'respuesta' => '',
                    'code' => 400
                ];
                return response()->json($respuesta, 400);
            }

            // Si el correo nuevo es diferente al actual, validar que no esté ocupado por otro usuario
            if (strtolower($nuevoCorreo) !== strtolower($correoActual)) {
                $correoEnUso = User::where('email', $nuevoCorreo)->where('id', '!=', $usuario->id)->exists();
                if ($correoEnUso) {
                    $respuesta = [
                        'success' => false,
                        'message' => __('Correo en uso'),
                        'mensaje_usuario' => __('La nueva dirección de correo ya se encuentra registrada por otro usuario.'),
                        'respuesta' => '',
                        'code' => 422
                    ];
                    return response()->json($respuesta, 422);
                }

                // Actualizar correo en la tabla users
                $usuario->email = $nuevoCorreo;
                $usuario->save();
            }

            // Limpiar tokens anteriores en password_resets asociados a ambos correos
            PasswordResets::where('email', $correoActual)->orWhere('email', $usuario->email)->delete();

            // Reenviar correo de confirmación
            try {
                $emailController->welcome_email($usuario);
                Log::info("Correo de verificación reenviado a {$usuario->email} para el usuario ID {$usuario->id}");
            } catch (\Exception $e) {
                Log::error("Fallo al reenviar correo de confirmación a {$usuario->email}: " . $e->getMessage());
                $respuesta = [
                    'success' => false,
                    'message' => __('Error al enviar correo'),
                    'mensaje_usuario' => __('No fue posible enviar el correo de verificación en este momento. Por favor intente nuevamente en unos instantes.'),
                    'respuesta' => $e->getMessage(),
                    'code' => 500
                ];
                return response()->json($respuesta, 500);
            }

            $respuesta = [
                'success' => true,
                'message' => __('Correo actualizado y verificación reenviada'),
                'mensaje_usuario' => __('Se ha enviado un nuevo enlace de confirmación a la dirección :email. Por favor ingrese a su bandeja de entrada y confirme su cuenta.', ['email' => $usuario->email]),
                'respuesta' => [
                    'nuevo_correo' => $usuario->email
                ],
                'code' => 200
            ];
            return response()->json($respuesta, 200);

        } catch (\Exception $ex) {
            Log::error("Error general en VerificacionCorreoController@actualizarCorreoYReenviar: " . $ex->getMessage());
            $respuesta = [
                'success' => false,
                'message' => __('Error interno del servidor'),
                'mensaje_usuario' => __('Ocurrió un error inesperado al procesar su solicitud. Inténtelo nuevamente.'),
                'respuesta' => $ex->getMessage(),
                'code' => 500
            ];
            return response()->json($respuesta, 500);
        }
    }
}
