<?php

/**
 * RedaPaymentController
 * 
 * Controlador extendido del sistema de pagos original para añadir funcionalidades
 * específicas del plugin REDA Alojamiento, como la persistencia de datos post-login
 * y verificaciones de seguridad de reservas.
 */

namespace Reda\RedaAlojamiento\Http\Controllers\General;

use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Auth, Session, Log;

class RedaPaymentController extends PaymentController
{
    /**
     * Sobrescribe el index original para capturar datos de reserva antes de la autenticación.
     * Esto soluciona el problema de pérdida de datos POST cuando un invitado intenta reservar.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 1. Log de entrada para trazabilidad técnica
        Log::info("REDA Payment: Petición entrante [" . $request->method() . "] - URI: " . $request->getRequestUri());

        // El ID puede venir de la ruta, del input, o de lo que guardamos previamente en sesión
        $idPropiedad = $request->id ?? $request->route('id') ?? Session::get('payment_property_id');

        // 2. Si hay datos en el request (POST inicial), los aseguramos en la sesión
        if ($request->has('checkin')) {
            Log::info("REDA Payment: Asegurando datos de reserva en sesión para ID: " . $idPropiedad);
            $datosReserva = $request->all();
            $datosReserva['id'] = $idPropiedad; 
            
            Session::put('reda_payment_data', $datosReserva);
            Session::put('payment_property_id', $idPropiedad);
            
            // Compatibilidad con el controlador padre
            Session::put([
                'payment_checkin'        => $request->checkin,
                'payment_checkout'       => $request->checkout,
                'payment_number_of_guests' => $request->number_of_guests,
                'payment_booking_type'   => $request->booking_type,
                'payment_booking_status' => $request->booking_status,
                'payment_booking_id'     => $request->booking_id,
            ]);
            Session::save();
        }

        // 3. Verificación de Autenticación manual
        if (!Auth::check()) {
            Log::info("REDA Payment: Usuario no autenticado para ID: " . $idPropiedad . ". Forzando redirección controlada.");
            
            $urlDestino = url("payments/book/{$idPropiedad}");
            Session::put('url.intended', $urlDestino);
            Session::put('reda.intended', $urlDestino); 
            
            return redirect()->guest('login');
        }

        // 4. Restauración Crítica post-login
        if (!$request->has('checkin') && Session::has('reda_payment_data')) {
            Log::info("REDA Payment: Detectado regreso de login. Restaurando datos.");
            $respaldo = Session::get('reda_payment_data');
            $request->merge($respaldo);
        }

        // 5. Verificación de Usuario Activo
        if (Auth::user()->status == 'Inactive') {
            Log::warning("REDA Payment: Usuario inactivo detectado: " . Auth::user()->email);
            Auth::logout();
            return redirect()->guest('login');
        }

        Log::info("REDA Payment: Entregando control al PaymentController original.");
        return parent::index($request);
    }

    /**
     * Redirige al login asegurando que el destino final sea la propiedad con el hash de reserva.
     * Si el usuario ya está autenticado y tiene una reserva activa para esa propiedad,
     * lo redirige directamente a sus viajes activos con una alerta personalizada.
     * 
     * @param string $slug El slug de la propiedad.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectReservar($slug)
    {
        $propiedad = \App\Models\Properties::where('slug', $slug)->first();
        
        if (!Auth::check()) {
            $urlDestino = url("properties/{$slug}#reservar");
            Session::put('url.intended', $urlDestino);
            return redirect()->guest('login');
        }

        // Si ya está autenticado, verificamos si tiene reserva activa para este inmueble
        if ($propiedad) {
            $hoy = date('Y-m-d');
            $reservaActiva = \App\Models\Bookings::where([
                ['user_id', '=', Auth::id()],
                ['property_id', '=', $propiedad->id],
                ['status', '=', 'Accepted'],
                ['end_date', '>=', $hoy]
            ])->exists();

            if ($reservaActiva) {
                // Redirigir a viajes activos con parámetro para mostrar modal en frontend
                return redirect('trips/active?reda_alert=active_booking');
            }
        }
        
        return redirect(url("properties/{$slug}#reservar"));
    }
}
