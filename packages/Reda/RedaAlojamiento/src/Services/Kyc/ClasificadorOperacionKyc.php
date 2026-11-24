<?php

/**
 * Resumen: Decide, para las rutas con reglas dinámicas, si una petición concreta es una operación restringida.
 * Se usa solo cuando el usuario NO está verificado. Etiquetas (ver kyc.operaciones_restringidas):
 *  - null/vacía     siempre restringida.
 *  - reserva_nueva  restringida salvo que pague una reserva EXISTENTE del propio usuario que espera el pago
 *                   (estado kyc.reservas.estados_pago_pendiente, por ejemplo Processing). Se validan en la base de
 *                   datos la propiedad y el estado; el booking_id de la petición o de la sesión nunca se acepta sin
 *                   comprobar. Si no hay reserva, o es de otro usuario, o está en otro estado, es una reserva nueva.
 *  - activacion     restringida al activar una publicación (el estado que envía la interfaz es el actual; activar
 *                   es todo lo que no sea "Listed"). Desactivar siempre se permite.
 *  - borrador       restringida al modificar un alojamiento propio aún no publicado (estado distinto de Listed).
 *  - edicion        restringida solo si kyc.bloquear_edicion_publicados está activo y la petición modifica datos.
 *  - desconocida    por seguridad se trata como restringida.
 * Es una capa de ruta pensada para dar un aviso temprano; la garantía final la dan los observadores de modelo.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Services\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Services\Kyc;

use App\Models\Bookings;
use App\Models\Properties;
use Illuminate\Http\Request;

class ClasificadorOperacionKyc
{
    /**
     * Indica si la petición es una operación restringida según su etiqueta.
     */
    public function esRestringida(?string $etiqueta, Request $peticion, int $usuarioId): bool
    {
        return match ($etiqueta) {
            null, ''        => true,
            'reserva_nueva' => $this->esReservaNueva($peticion, $usuarioId),
            'activacion'    => (string) $peticion->input('status') !== 'Listed',
            'borrador'      => $this->modificaAlojamientoNoPublicado($peticion, $usuarioId),
            'edicion'       => (bool) config('kyc.bloquear_edicion_publicados', false) && $this->modificaDatos($peticion),
            default         => true,
        };
    }

    /**
     * Es reserva nueva salvo que TODAS las referencias a una reserva (petición y sesión) correspondan a una
     * reserva existente del usuario que espera su pago.
     */
    private function esReservaNueva(Request $peticion, int $usuarioId): bool
    {
        $candidatos = [];

        foreach ([$peticion->input('booking_id'), $peticion->hasSession() ? $peticion->session()->get('payment_booking_id') : null] as $valor) {
            if ($valor === null || $valor === '' || $valor === false) {
                continue;
            }

            if (!is_scalar($valor) || !ctype_digit((string) $valor)) {
                return true;
            }

            $candidatos[] = (int) $valor;
        }

        if (empty($candidatos)) {
            return true;
        }

        $estadosValidos = array_map('strtolower', (array) config('kyc.reservas.estados_pago_pendiente', ['Processing']));

        foreach (array_unique($candidatos) as $id) {
            $reserva = Bookings::query()->where('id', $id)->where('user_id', $usuarioId)->first(['id', 'status']);

            if (!$reserva || !in_array(strtolower((string) $reserva->status), $estadosValidos, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Modificar (no consultar) un alojamiento propio que todavía no está publicado.
     */
    private function modificaAlojamientoNoPublicado(Request $peticion, int $usuarioId): bool
    {
        if (!$this->modificaDatos($peticion)) {
            return false;
        }

        if (config('kyc.bloquear_edicion_publicados', false)) {
            return true;
        }

        $propiedadId = $peticion->route('id');
        $estado = is_scalar($propiedadId)
            ? Properties::query()->where('id', $propiedadId)->where('host_id', $usuarioId)->value('status')
            : null;

        // Sin alojamiento propio identificable no se puede afirmar que esté publicado: se restringe.
        return $estado !== 'Listed';
    }

    /**
     * La petición modifica datos (cualquier método distinto de consulta).
     */
    private function modificaDatos(Request $peticion): bool
    {
        return !in_array($peticion->method(), ['GET', 'HEAD', 'OPTIONS'], true);
    }
}
