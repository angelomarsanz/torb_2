<?php

/**
 * Observador ReservaObserver
 * 
 * Propósito: Intercepta la creación de reservaciones en el modelo original Bookings
 * para persistir el desglose de huéspedes (Adultos y Niños) sin alterar los flujos
 * de pago del core ni requerir modificaciones invasivas en el controlador original.
 * 
 * Estrategia de Persistencia Dual:
 * 1. Almacena en 'booking_details' las claves 'adultos' y 'ninos' mediante atributos explícitos.
 * 2. Si la tabla auxiliar 'reserva_huespedes' ha sido migrada, guarda también el registro allí.
 */

namespace Reda\RedaAlojamiento\Observers;

use App\Models\Bookings;
use App\Models\BookingDetails;
use Reda\RedaAlojamiento\Models\Reserva\ReservaHuesped;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class ReservaObserver
{
    /**
     * Persiste los huéspedes tras crear una reserva, sin asignación masiva en BookingDetails.
     * 
     * @param Bookings $booking Instancia recién creada de la reservación
     * @return void
     */
    public function created(Bookings $booking)
    {
        try {
            // 1. Obtener valores de adultos y niños asegurados en sesión o request
            $adultos = Session::get('payment_adultos')
                ?? request('adultos')
                ?? Session::get('reda_payment_data.adultos')
                ?? $booking->guest;

            $ninos = Session::get('payment_ninos')
                ?? request('ninos')
                ?? Session::get('reda_payment_data.ninos')
                ?? 0;

            $adultos = (int) $adultos;
            $ninos = (int) $ninos;

            if ($adultos <= 0) {
                $adultos = max(1, (int) $booking->guest - $ninos);
            }

            Log::info("REDA ReservaObserver: Registrando desglose para reserva #{$booking->id} (Código: {$booking->code}) - Adultos: {$adultos}, Niños: {$ninos}");

            // 2. Persistencia en tabla booking_details (App\Models\BookingDetails)
            foreach (['adultos' => $adultos, 'ninos' => $ninos] as $campo => $cantidad) {
                $detalle = BookingDetails::where('booking_id', $booking->id)
                    ->where('field', $campo)
                    ->first() ?? new BookingDetails;
                $detalle->booking_id = $booking->id;
                $detalle->field = $campo;
                $detalle->value = (string) $cantidad;
                $detalle->save();
            }

            // 3. Persistencia en tabla auxiliar 'reserva_huespedes' si existe
            if (Schema::hasTable('reserva_huespedes')) {
                ReservaHuesped::updateOrCreate(
                    ['reserva_id' => $booking->id],
                    ['adultos' => $adultos, 'ninos' => $ninos]
                );
            }

        } catch (\Exception $e) {
            Log::error("REDA ReservaObserver Error al guardar desglose de huéspedes: " . $e->getMessage());
        }
    }
}
