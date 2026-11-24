<?php

/**
 * Resumen: Construye la respuesta cuando se bloquea una operación por falta de verificación KYC.
 *  - Peticiones AJAX/JSON/API: estructura estándar del plugin con código 403, el mensaje de bloqueo y, en
 *    "respuesta", el código "kyc_requerido" y la URL de la sección KYC (la interfaz la usa para mostrar el modal).
 *  - Peticiones normales: mensaje flash de error y redirección a la sección KYC (kyc.url_seccion).
 * El mensaje es el aprobado para el usuario. Esta clase solo representa el bloqueo; la decisión de bloquear
 * la toman EnsureKycVerified y BloqueoKycObserver en el servidor.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Services\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Services\Kyc;

use App\Http\Helpers\Common;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RespuestaBloqueoKyc
{
    /**
     * Mensaje de bloqueo para el usuario (traducido).
     */
    public static function mensaje(): string
    {
        return __('Para publicar alojamientos, ofrecer servicios o realizar reservas, primero debes verificar tu identidad. Completa tu proceso de verificación KYC para habilitar estas funcionalidades.');
    }

    /**
     * URL absoluta de la sección KYC del usuario.
     */
    public static function urlSeccion(): string
    {
        return url((string) config('kyc.url_seccion', 'users/edit-verification'));
    }

    /**
     * Crea la respuesta de bloqueo adecuada al tipo de petición.
     */
    public static function crear(Request $peticion): Response
    {
        if ($peticion->expectsJson() || $peticion->ajax() || $peticion->wantsJson()) {
            return response()->json([
                'success'         => false,
                'message'         => __('Verificación de identidad requerida'),
                'mensaje_usuario' => self::mensaje(),
                'respuesta'       => ['codigo' => 'kyc_requerido', 'url' => self::urlSeccion()],
                'code'            => 403,
            ], 403);
        }

        Common::one_time_message('error', self::mensaje());

        return redirect(self::urlSeccion());
    }
}
