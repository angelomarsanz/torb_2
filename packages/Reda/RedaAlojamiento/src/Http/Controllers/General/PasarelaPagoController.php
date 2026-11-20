<?php

/** Conserva el flujo original de pasarelas y vincula los mensajes de transferencia sin perder el contexto de pago. */
namespace Reda\RedaAlojamiento\Http\Controllers\General;

use App\Models\Bookings;
use App\Models\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Gateway\Http\Controllers\GatewayController;
use Reda\RedaAlojamiento\Models\MensajePago;
use Reda\RedaAlojamiento\Models\Disputa\MensajeMetadata;
use RuntimeException;
use Throwable;

class PasarelaPagoController extends GatewayController
{
    /** Valida la nota antes de que el procesador original mueva los archivos. */
    public function makePayment(Request $solicitud)
    {
        if (session('payment_method') === 'directbanktransfer') {
            $solicitud->validate([
                'note' => ['required', 'string', 'max:10000'],
                'attachment' => ['required', 'array', 'min:1'],
                'attachment.*' => ['required', 'image'],
            ]);
        }
        return parent::makePayment($solicitud);
    }

    /**
     * Conserva el contexto que el núcleo elimina, registra el mensaje y restaura la sesión si falla la transacción.
     * En el éxito mantiene la limpieza de sesión del núcleo y no altera otras pasarelas.
     */
    public function getAllData($respuesta)
    {
        if (($respuesta->payment_method->alias ?? null) !== 'directbanktransfer') {
            return parent::getAllData($respuesta);
        }

        $texto = trim((string) $respuesta->note);
        $comprobantes = is_string($respuesta->attachments) ? json_decode($respuesta->attachments, true) : null;
        if ($texto === '' || !is_array($comprobantes) || count($comprobantes) === 0) {
            throw new RuntimeException(__('No se pudo registrar el mensaje o el comprobante del pago.'));
        }

        $datosPago = session()->only([
            'payment_property_id',
            'payment_checkin',
            'payment_checkout',
            'payment_number_of_guests',
            'payment_booking_type',
            'payment_booking_status',
            'payment_booking_id',
        ]);
        $idPropiedad = $datosPago['payment_property_id'] ?? null;
        $idReserva = $datosPago['payment_booking_id'] ?? null;

        try {
            return DB::transaction(function () use ($respuesta, $texto, $comprobantes, $idPropiedad, $idReserva) {
                // La columna original solo admite 191 caracteres; el mensaje completo vive en el plugin.
                $respuesta->note = mb_substr($texto, 0, 191);
                $codigo = parent::getAllData($respuesta);
                $consulta = Bookings::where('code', $codigo)
                    ->where('user_id', Auth::id())
                    ->where('property_id', $idPropiedad);
                if ($idReserva) {
                    $consulta->whereKey($idReserva);
                }
                $reserva = $consulta->sole();

                $mensaje = new Messages();
                $mensaje->property_id = $reserva->property_id;
                $mensaje->booking_id = $reserva->id;
                $mensaje->sender_id = $reserva->user_id;
                $mensaje->receiver_id = $reserva->host_id;
                $mensaje->message = $texto;
                $mensaje->type_id = 4;
                $mensaje->read = 0;
                $mensaje->save();
                MensajeMetadata::where('message_id', $mensaje->id)->update(['sender_type' => 'user']);

                MensajePago::create([
                    'reserva_id' => $reserva->id,
                    'mensaje_id' => $mensaje->id,
                    'texto' => $texto,
                    'comprobantes' => $comprobantes,
                ]);
                return $codigo;
            });
        } catch (Throwable $error) {
            session()->put($datosPago);
            Log::error('Error al registrar el mensaje de pago: ' . $error->getMessage());
            throw $error;
        }
    }
}
