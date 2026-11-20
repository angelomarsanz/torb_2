<?php

/** Convierte plantillas administrativas a texto plano y resuelve variables de reserva y pago. */
namespace Reda\RedaAlojamiento\Services;

use App\Models\Properties;
use App\Models\Bookings;
use App\Models\Country;
use Illuminate\Support\Facades\Auth;
use Modules\Gateway\Entities\Gateway;
use Reda\RedaAlojamiento\Models\PlantillaTexto;

class PlantillasTextoService
{
    /**
     * Resuelve la reserva y, en confirmación, los datos de pago de sesión.
     * También entrega el texto con variables de pago para actualizarlo en la pantalla inicial.
     */
    public function opcionesPago(bool $usarDatosPago = false): array
    {
        $propiedad = Properties::with('users')->findOrFail(session('payment_property_id'));
        $anfitrion = $propiedad->users;
        $huesped = Auth::user();
        $adultos = session('payment_adultos', session('payment_number_of_guests'));
        $ninos = session('payment_ninos', 0);
        if (session('payment_booking_id')) {
            $reserva = Bookings::where('user_id', $huesped->id)
                ->where('property_id', $propiedad->id)
                ->findOrFail(session('payment_booking_id'));
            $desglose = reda_obtener_desglose_huespedes($reserva);
            $adultos = $desglose['adultos'];
            $ninos = $desglose['ninos'];
        }
        $variables = [
            '{anfitrion}' => trim(($anfitrion->first_name ?? '') . ' ' . ($anfitrion->last_name ?? '')),
            '{huesped}' => trim(($huesped->first_name ?? '') . ' ' . ($huesped->last_name ?? '')),
            '{alojamiento}' => $propiedad->name,
            '{llegada}' => (string) session('payment_checkin'),
            '{salida}' => (string) session('payment_checkout'),
            '{adultos}' => (string) $adultos,
            '{ninos}' => (string) $ninos,
        ];

        $pais = __('No seleccionado');
        $metodoPago = __('No seleccionado');
        if ($usarDatosPago && session('payment_country')) {
            $pais = Country::where('short_name', session('payment_country'))->firstOrFail()->name;
        }
        if ($usarDatosPago && session('payment_method')) {
            $metodoPago = __(Gateway::where('alias', session('payment_method'))->firstOrFail()->name);
        }
        $variablesPago = ['{pais}' => $pais, '{metodo_pago}' => $metodoPago];

        return PlantillaTexto::where('contexto', 'mensaje_anfitrion')
            ->orderBy('orden')->orderBy('id')->get()
            ->map(function (PlantillaTexto $plantilla) use ($variables, $variablesPago) {
                $texto = strtr($this->textoPlano($plantilla->contenido), $variables);
                return [
                    'id' => $plantilla->id,
                    'nombre' => $plantilla->nombre,
                    'texto' => strtr($texto, $variablesPago),
                    'texto_con_variables_pago' => $texto,
                    'pais' => $variablesPago['{pais}'],
                    'metodo_pago' => $variablesPago['{metodo_pago}'],
                ];
            })->all();
    }

    /** Conserva saltos y viñetas al convertir el HTML del editor a texto sin etiquetas. */
    public function textoPlano(?string $contenido): string
    {
        $contenido = preg_replace('/<\s*br\s*\/?\s*>/i', "\n", $contenido ?? '');
        $contenido = preg_replace('/<\s*li(?:\s[^>]*)?>/i', "\n• ", $contenido);
        $contenido = preg_replace('/<\/\s*(?:p|div|li|ul|ol|h[1-6])\s*>/i', "\n", $contenido);
        $texto = html_entity_decode(strip_tags($contenido), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return trim(preg_replace("/\n{3,}/", "\n\n", str_replace("\r", '', $texto)));
    }
}
