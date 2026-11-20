<?php

/** Entrega las opciones del mensaje al anfitrión para la sesión de pago autenticada. */
namespace Reda\RedaAlojamiento\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Reda\RedaAlojamiento\Services\PlantillasTextoService;
use Throwable;

class PlantillaPagoController extends Controller
{
    /** Devuelve plantillas y datos del pago en confirmación, o un error explícito. */
    public function opciones(PlantillasTextoService $servicio)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => __('Plantillas cargadas'),
                'mensaje_usuario' => '',
                'respuesta' => $servicio->opcionesPago(request()->query('pantalla') === 'confirmacion'),
                'code' => 200,
            ])->header('Cache-Control', 'private, no-store');
        } catch (Throwable $error) {
            Log::error('Error al cargar plantillas de pago: ' . $error->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Error al cargar plantillas'),
                'mensaje_usuario' => __('No se pudieron cargar los textos predefinidos. Puede escribir su mensaje manualmente.'),
                'respuesta' => '',
                'code' => 500,
            ], 500);
        }
    }
}
