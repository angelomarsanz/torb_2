<?php

/** Sirve comprobantes asociados a mensajes nuevos solo a participantes de la reserva. */
namespace Reda\RedaAlojamiento\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Models\Bookings;
use App\Models\Messages;
use Illuminate\Support\Facades\Auth;
use Reda\RedaAlojamiento\Models\MensajePago;

class ComprobantePagoController extends Controller
{
    /** Comprueba mensaje, reserva, participante y ruta antes de servir el archivo. */
    public function mostrar(int $idMensaje, int $indice)
    {
        $pago = MensajePago::where('mensaje_id', $idMensaje)->firstOrFail();
        $mensaje = Messages::whereKey($idMensaje)->where('booking_id', $pago->reserva_id)->firstOrFail();
        $reserva = Bookings::findOrFail($pago->reserva_id);
        abort_unless(in_array((int) Auth::id(), [(int) $reserva->user_id, (int) $reserva->host_id], true), 403);
        abort_unless((int) $mensaje->sender_id === (int) $reserva->user_id, 404);
        $nombre = $pago->comprobantes[$indice] ?? null;
        abort_unless(is_string($nombre) && $nombre !== '' && basename($nombre) === $nombre, 404);
        $directorio = realpath(public_path('uploads/booking'));
        $archivo = $directorio ? realpath($directorio . DIRECTORY_SEPARATOR . $nombre) : false;
        abort_unless($archivo && str_starts_with($archivo, $directorio . DIRECTORY_SEPARATOR) && is_file($archivo), 404);
        return response()->file($archivo, [
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
