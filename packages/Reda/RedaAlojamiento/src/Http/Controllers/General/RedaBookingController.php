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
     * Obtiene un mapa de datos de inmuebles y reservaciones válidas para el usuario autenticado.
     * Se filtran estrictamente las "Consultas" (sin pago) y las reservas vencidas.
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
                    'respuesta' => [
                        'properties' => (object)[],
                        'bookings' => [],
                        'codes' => []
                    ],
                    'code' => 200
                ], 200);
            }

            $userId = Auth::id();
            $today = date('Y-m-d');

            /**
             * Criterios de "Reservación Válida" (Lista Blanca):
             * 1. Dueño: El usuario autenticado.
             * 2. Vigencia: Fecha de fin >= hoy.
             * 3. No cancelada/rechazada/vencida (en DB).
             * 4. PAGO: Debe tener rastro de pago o estar en proceso de aceptación formal.
             */
            $bookings = Bookings::with('properties')
                ->where('user_id', $userId)
                ->where('end_date', '>=', $today)
                ->whereNotIn('status', ['Cancelled', 'Declined', 'Expired'])
                ->where(function($query) {
                    $query->where(function($q) {
                        $q->where('transaction_id', '!=', '')
                          ->where('transaction_id', '!=', ' ')
                          ->whereNotNull('transaction_id');
                    })
                    ->orWhere('payment_method_id', '>', 0)
                    ->orWhereIn('status', ['Accepted', 'Processing', 'processing']);
                })
                ->get();

            $propertyMap = [];
            $validBookingIds = [];
            $validBookingCodes = [];

            foreach ($bookings as $booking) {
                $validBookingIds[] = $booking->id;
                if ($booking->code) $validBookingCodes[] = $booking->code;
                
                if ($booking->properties) {
                    $propertyMap[$booking->property_id] = [
                        'name' => $booking->properties->name,
                        'slug' => $booking->properties->slug
                    ];
                }
            }

            $respuesta = [
                'success' => true,
                'message' => __('Listado de reservaciones válidas obtenido'),
                'mensaje_usuario' => '',
                'respuesta' => [
                    'properties' => (object)$propertyMap,
                    'bookings' => $validBookingIds,
                    'codes' => $validBookingCodes
                ],
                'code' => 200
            ];

            return response()->json($respuesta, 200);

        } catch (\Exception $e) {
            Log::error("REDA Booking Error (getActiveIds): " . $e->getMessage());
            $respuesta = [
                'success' => false,
                'message' => $e->getMessage(),
                'mensaje_usuario' => __('Error al verificar reservas activas'),
                'respuesta' => [
                    'properties' => (object)[],
                    'bookings' => [],
                    'codes' => []
                ],
                'code' => 500
            ];
            return response()->json($respuesta, $respuesta['code']);
        }
    }

    /**
     * Obtiene los detalles de una reserva específica para mostrar en el modal.
     * 
     * @param int $propertyId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBookingDetails($propertyId)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                    'mensaje_usuario' => __('Por favor, inicie sesión'),
                    'respuesta' => '',
                    'code' => 401
                ], 401);
            }

            $userId = Auth::id();

            // Buscamos la reserva más reciente para esta propiedad y este usuario
            $booking = Bookings::with(['properties', 'host', 'currency'])
                ->where('user_id', $userId)
                ->where('property_id', $propertyId)
                ->whereNotIn('status', ['Cancelled', 'Declined'])
                ->orderBy('id', 'desc')
                ->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found',
                    'mensaje_usuario' => __('No se encontró una reservación activa para esta propiedad'),
                    'respuesta' => '',
                    'code' => 404
                ], 404);
            }

            $respuesta = [
                'success' => true,
                'message' => __('Detalles de reserva obtenidos'),
                'mensaje_usuario' => '',
                'respuesta' => [
                    'propiedad_nombre' => optional($booking->properties)->name,
                    'propiedad_foto' => optional($booking->properties)->cover_photo,
                    'ubicacion' => [
                        'ciudad' => optional($booking->properties->property_address)->city,
                        'pais' => optional($booking->properties->property_address->countries)->name
                    ],
                    'estado' => __($booking->status),
                    'estado_label' => strtolower($booking->status),
                    'fecha_inicio' => date('M d, Y', strtotime($booking->start_date)),
                    'fecha_fin' => date('M d, Y', strtotime($booking->end_date)),
                    'huespedes' => $booking->guest,
                    'noches' => $booking->total_night,
                    'codigo' => $booking->code,
                    'simbolo_moneda' => optional($booking->currency)->symbol,
                    'total' => $booking->total
                ],
                'code' => 200
            ];

            return response()->json($respuesta, 200);

        } catch (\Exception $e) {
            Log::error("REDA Booking Error (getBookingDetails): " . $e->getMessage());
            $respuesta = [
                'success' => false,
                'message' => $e->getMessage(),
                'mensaje_usuario' => __('Error al obtener detalles de la reserva'),
                'respuesta' => '',
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
     * Obtiene los datos de las reservaciones que tienen algún pago registrado.
     * Se considera pagada si transaction_id no está vacío, payment_method_id > 0
     * o si el status es 'Accepted' o 'Processing'.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPaidBookingIds()
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User not authenticated',
                    'mensaje_usuario' => '',
                    'respuesta' => ['items' => []],
                    'code' => 200
                ], 200);
            }

            $userId = Auth::id();
            
            $bookings = Bookings::with('properties')
                ->where('user_id', $userId)
                ->where(function($query) {
                    $query->where(function($q) {
                        $q->where('transaction_id', '!=', '')
                          ->where('transaction_id', '!=', ' ')
                          ->whereNotNull('transaction_id');
                    })
                    ->orWhere('payment_method_id', '>', 0)
                    ->orWhereIn('status', ['Accepted', 'Processing']);
                })
                ->get();

            $elementos = $bookings->map(function($b) {
                return [
                    'id' => $b->id,
                    'code' => $b->code,
                    'property_name' => optional($b->properties)->name,
                    'start_date' => date('M d, Y', strtotime($b->start_date)),
                    'end_date' => date('M d, Y', strtotime($b->end_date))
                ];
            });

            $respuesta = [
                'success' => true,
                'message' => __('Listado de reservaciones pagadas obtenido'),
                'mensaje_usuario' => '',
                'respuesta' => [
                    'items' => $elementos
                ],
                'code' => 200
            ];

            return response()->json($respuesta, 200);

        } catch (\Exception $e) {
            Log::error("REDA Booking Error (getPaidBookingIds): " . $e->getMessage());
            $respuesta = [
                'success' => false,
                'message' => $e->getMessage(),
                'mensaje_usuario' => __('Error al obtener reservaciones pagadas'),
                'respuesta' => ['items' => []],
                'code' => 500
            ];
            return response()->json($respuesta, $respuesta['code']);
        }
    }
}
