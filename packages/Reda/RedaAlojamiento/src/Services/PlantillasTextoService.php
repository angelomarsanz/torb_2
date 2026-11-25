<?php

/** Convierte plantillas a texto plano y resuelve variables de reserva y pago, incluidas fechas visibles. */
namespace Reda\RedaAlojamiento\Services;

use App\Models\Properties;
use App\Models\Bookings;
use App\Models\Country;
use DateTimeImmutable;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use Modules\Gateway\Entities\Gateway;
use Reda\RedaAlojamiento\Models\PlantillaTexto;

class PlantillasTextoService
{
    /**
     * Resuelve la reserva, formatea sus fechas para el mensaje inicial y selecciona sus plantillas.
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
            '{llegada}' => $usarDatosPago ? (string) session('payment_checkin') : $this->formatearFechaReserva((string) session('payment_checkin')),
            '{salida}' => $usarDatosPago ? (string) session('payment_checkout') : $this->formatearFechaReserva((string) session('payment_checkout')),
            '{adultos}' => (string) $adultos,
            '{ninos}' => (string) $ninos,
            '{total_personas}' => (string) ((int) $adultos + (int) $ninos),
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

        $contexto = $usarDatosPago ? PlantillaTexto::CONTEXTO_PAGO : PlantillaTexto::CONTEXTO_RESERVA;
        return PlantillaTexto::where('contexto', $contexto)
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

    /** Convierte fechas ISO o regionales a día/mes/año sin cambiar el día calendario. */
    private function formatearFechaReserva(string $fecha): string
    {
        if ($fecha === '') {
            return '';
        }

        foreach (array_unique(['Y-m-d', setDateForFront(), 'd-m-Y', 'd/m/Y']) as $formato) {
            $fechaInterpretada = DateTimeImmutable::createFromFormat('!' . $formato, $fecha);
            if ($fechaInterpretada && $fechaInterpretada->format($formato) === $fecha) {
                return $fechaInterpretada->format('d/m/Y');
            }
        }

        throw new InvalidArgumentException('Formato de fecha de reserva no reconocido.');
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
