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

    /**
     * Obtiene los detalles de la reserva activa de un usuario para una propiedad específica.
     * 
     * @param int $property_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBookingDetails($property_id)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                    'mensaje_usuario' => __('Debes iniciar sesión para ver los detalles'),
                    'respuesta' => '',
                    'code' => 401
                ], 401);
            }

            $userId = Auth::id();
            $today = date('Y-m-d');

            // Buscamos la reserva más reciente que esté activa
            $booking = Bookings::with(['properties.property_address', 'properties.property_photos'])
                ->where('user_id', $userId)
                ->where('property_id', $property_id)
                ->where(function($query) use ($today) {
                    $query->where(function($q) use ($today) {
                        $q->where('status', 'Accepted')
                          ->where('end_date', '>=', $today);
                    })
                    ->orWhereIn('status', ['Pending', 'processing']);
                })
                ->orderBy('id', 'desc')
                ->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found',
                    'mensaje_usuario' => __('No se encontró una reserva activa para este inmueble'),
                    'respuesta' => '',
                    'code' => 404
                ], 404);
            }

            // Preparar los datos para el modal
            $datos = [
                'id' => $booking->id,
                'codigo' => $booking->code,
                'propiedad_nombre' => $booking->properties->name,
                'propiedad_id' => $booking->properties->id,
                'propiedad_foto' => $booking->properties->cover_photo,
                'ubicacion' => [
                    'ciudad' => $booking->properties->property_address->city ?? '',
                    'estado' => $booking->properties->property_address->state ?? '',
                    'pais' => $booking->properties->property_address->countries->name ?? '',
                ],
                'fecha_inicio' => date('d/m/Y', strtotime($booking->start_date)),
                'fecha_fin' => date('d/m/Y', strtotime($booking->end_date)),
                'huespedes' => $booking->guest,
                'noches' => $booking->total_night,
                'total' => $booking->total,
                'simbolo_moneda' => $booking->currency->symbol ?? '$',
                'estado' => $booking->status,
                'estado_label' => $booking->label_color
            ];

            $respuesta = [
                'success' => true,
                'message' => __('Detalles de la reserva obtenidos correctamente'),
                'mensaje_usuario' => '',
                'respuesta' => $datos,
                'code' => 200
            ];

            return response()->json($respuesta, 200);

        } catch (\Exception $e) {
            Log::error("REDA Booking Error (getDetails): " . $e->getMessage());
            $respuesta = [
                'success' => false,
                'message' => $e->getMessage(),
                'mensaje_usuario' => __('Error al obtener los detalles de la reserva'),
                'respuesta' => '',
                'code' => 500
            ];
            return response()->json($respuesta, $respuesta['code']);
        }
    }
}
