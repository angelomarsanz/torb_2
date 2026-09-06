<?php

/**
 * RedaBookingController
 * 
 * Controlador para gestionar consultas relacionadas con las reservas 
 * desde el plugin REDA Alojamiento.
 */

namespace Reda\RedaAlojamiento\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bookings;
use Auth;

class RedaBookingController extends Controller
{
    /**
     * Obtiene los IDs de inmuebles con reservas activas o vigentes para el usuario autenticado.
     * Se considera "Vigente" una reserva que esté en estado 'Accepted' y cuya fecha 
     * de finalización sea hoy o en el futuro.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveBookingPropertyIds()
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Usuario no autenticado'),
                    'mensaje_usuario' => '',
                    'respuesta' => [],
                    'code' => 200
                ], 200);
            }

            $userId = Auth::id();
            $today = date('Y-m-d');

            // Buscamos reservas aceptadas que no hayan terminado
            $activePropertyIds = Bookings::where('user_id', $userId)
                ->where('status', 'Accepted')
                ->where('end_date', '>=', $today)
                ->pluck('property_id')
                ->unique()
                ->toArray();

            $respuesta = [
                'success' => true,
                'message' => __('IDs de propiedades con reservas activas obtenidos correctamente'),
                'mensaje_usuario' => '',
                'respuesta' => array_values($activePropertyIds),
                'code' => 200
            ];

            return response()->json($respuesta, $respuesta['code']);

        } catch (\Exception $e) {
            $respuesta = [
                'success' => false,
                'message' => __('Error al obtener reservas activas'),
                'mensaje_usuario' => __('Hubo un problema al verificar sus reservas.'),
                'respuesta' => $e->getMessage(),
                'code' => 500
            ];
            return response()->json($respuesta, $respuesta['code']);
        }
    }
}
