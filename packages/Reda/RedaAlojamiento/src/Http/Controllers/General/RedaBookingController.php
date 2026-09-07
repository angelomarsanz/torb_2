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
     * Se considera "Vigente" una reserva que esté:
     * 1. En estado 'Accepted' y cuya fecha de finalización sea hoy o en el futuro.
     * 2. En estado 'Pending' o 'processing' (reservas en curso de aprobación o pago).
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

            // Buscamos reservas aceptadas vigentes O reservas pendientes/en proceso
            $activePropertyIds = Bookings::where('user_id', $userId)
                ->where(function($query) use ($today) {
                    $query->where(function($q) use ($today) {
                        $q->where('status', 'Accepted')
                          ->where('end_date', '>=', $today);
                    })
                    ->orWhereIn('status', ['Pending', 'processing']);
                })
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
