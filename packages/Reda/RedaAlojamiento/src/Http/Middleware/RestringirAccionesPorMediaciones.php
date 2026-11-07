<?php

/**
 * Restringe acciones nuevas de usuarios que alcanzaron el límite de mediaciones.
 * Conserva el acceso a la cuenta, los datos existentes y las confirmaciones de pagos en curso.
 */

namespace Reda\RedaAlojamiento\Http\Middleware;

use App\Http\Helpers\Common;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Reda\RedaAlojamiento\Models\Disputa\UsuarioAvisoMediacion;
use Symfony\Component\HttpFoundation\Response;

class RestringirAccionesPorMediaciones
{
    /**
     * Impide las operaciones restringidas antes de que el controlador procese la petición.
     */
    public function handle(Request $peticion, Closure $siguiente): Response
    {
        $ruta = $peticion->route();
        $usuario = Auth::user();

        if (!$ruta) {
            return $siguiente($peticion);
        }

        $uri = $ruta->uri();
        $esPasarela = in_array($uri, ['gateway/payment/{gateway}/pay', 'gateway/pay/{gateway}/complete'], true);
        if (!$usuario && $esPasarela && $peticion->session()->has('payment_property_id')) {
            return redirect()->guest('login');
        }

        if (!$usuario) {
            return $siguiente($peticion);
        }

        $esAccionRestringida = in_array($uri, [
            'property/create',
            'payments/book/{id?}',
            'reda/payments/book/{id?}',
            'payments/create_booking',
            'gateway/payment/{gateway}/pay',
            'gateway/pay/{gateway}/complete',
            'users/payout/success',
            'reda/negocios/crear-experiencia',
            'reda/disputas/store',
        ], true);

        if (!$esAccionRestringida) {
            return $siguiente($peticion);
        }

        $restringido = UsuarioAvisoMediacion::where('user_id', $usuario->id)
            ->where('cuenta_suspendida', 1)->exists();

        if (!$restringido) {
            $respuesta = $siguiente($peticion);
            if ($uri === 'payments/create_booking' && $peticion->input('payment_method')
                && $respuesta->isRedirect() && $peticion->session()->has('payment_property_id')) {
                $peticion->session()->put('reda_pago_iniciado', $this->identificarPago($peticion, $usuario->id));
            }
            return $respuesta;
        }

        if ($esPasarela
            && $peticion->session()->has('payment_property_id')
            && $peticion->session()->get('reda_pago_iniciado') === $this->identificarPago($peticion, $usuario->id)) {
            return $siguiente($peticion);
        }

        $mensaje = __('Su cuenta tiene restricciones por el límite de mediaciones. No puede crear propiedades o negocios, hacer reservaciones, iniciar mediaciones ni solicitar retiros. Comuníquese con soporte para revisar su caso.');

        if ($peticion->expectsJson() || $peticion->ajax()) {
            return response()->json([
                'success' => false,
                'message' => __('Acción restringida por mediaciones'),
                'mensaje_usuario' => $mensaje,
                'respuesta' => '',
                'code' => 403,
            ], 403);
        }

        Common::one_time_message('error', $mensaje);

        return redirect($uri === 'users/payout/success' ? 'users/payout-list' : 'dashboard');
    }

    /**
     * Identifica el pago preparado antes de la restricción, vinculado a la sesión y al usuario.
     */
    private function identificarPago(Request $peticion, $idUsuario): array
    {
        return [
            'usuario' => $idUsuario,
            'propiedad' => $peticion->session()->get('payment_property_id'),
            'llegada' => $peticion->session()->get('payment_checkin'),
            'salida' => $peticion->session()->get('payment_checkout'),
            'metodo' => $peticion->session()->get('payment_method'),
        ];
    }
}
