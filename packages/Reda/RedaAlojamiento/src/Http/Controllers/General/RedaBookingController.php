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
use Illuminate\Support\Facades\Log;

class RedaBookingController extends Controller
{
    /**
     * Obtiene un mapa de IDs y nombres de inmuebles con reservas activas o vigentes para el usuario autenticado.
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
                    'message' => 'User not authenticated',
                    'mensaje_usuario' => '',
                    'respuesta' => (object)[],
                    'code' => 200
                ], 200);
            }

            $userId = Auth::id();
            $today = date('Y-m-d');

            // Buscamos todas las reservas activas (Accepted futuras/hoy, Pending, processing)
            $bookings = Bookings::with('properties')
                ->where('user_id', $userId)
                ->where(function($query) use ($today) {
                    $query->where(function($q) use ($today) {
                        $q->where('status', 'Accepted')
                          ->where('end_date', '>=', $today);
                    })
                    ->orWhereIn('status', ['Pending', 'processing']);
                })
                ->get();

            $map = [];
            foreach ($bookings as $booking) {
                if ($booking->properties) {
                    $map[$booking->property_id] = $booking->properties->name;
                }
            }

            $respuesta = [
                'success' => true,
                'message' => __('Listado de propiedades con reservas activas obtenido'),
                'mensaje_usuario' => '',
                'respuesta' => (object)$map,
                'code' => 200
            ];

            return response()->json($respuesta, 200);

        } catch (\Exception $e) {
            Log::error("REDA Booking Error (getActiveIds): " . $e->getMessage());
            $respuesta = [
                'success' => false,
                'message' => $e->getMessage(),
                'mensaje_usuario' => __('Error al verificar reservas activas'),
                'respuesta' => (object)[],
                'code' => 500
            ];
            return response()->json($respuesta, $respuesta['code']);
        }
    }

    /**
     * Obtiene el conteo de reservaciones activas para el usuario autenticado.
     * Considera las categorías:
     * 1. Actual (Status: Accepted y en curso)
     * 2. Próximamente (Status: Accepted y futura)
     * 3. Pendiente (Status: Pending o processing)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCountActiveBookings()
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User not authenticated',
                    'mensaje_usuario' => '',
                    'respuesta' => 0,
                    'code' => 200
                ], 200);
            }

            $userId = Auth::id();
            $today = date('Y-m-d');

            // Lógica refinada para coincidir con las vistas de "Mis Viajes":
            // - 'Accepted' con end_date >= hoy cubre tanto "Actual" como "Próximamente".
            // - 'Pending' y 'processing' cubren "Pendiente".
            $count = Bookings::where('user_id', $userId)
                ->where(function($query) use ($today) {
                    $query->where(function($q) use ($today) {
                        $q->where('status', 'Accepted')
                          ->where('end_date', '>=', $today);
                    })
                    ->orWhereIn('status', ['Pending', 'processing']);
                })
                ->count();

            $respuesta = [
                'success' => true,
                'message' => __('Conteo de reservaciones (Actual, Próximamente, Pendiente) obtenido correctamente'),
                'mensaje_usuario' => '',
                'respuesta' => $count,
                'code' => 200
            ];

            return response()->json($respuesta, 200);

        } catch (\Exception $e) {
            Log::error("REDA Booking Error (getCount): " . $e->getMessage());
            $respuesta = [
                'success' => false,
                'message' => $e->getMessage(),
                'mensaje_usuario' => __('Error al obtener el conteo de viajes'),
                'respuesta' => 0,
                'code' => 500
            ];
            return response()->json($respuesta, $respuesta['code']);
        }
    }
}
