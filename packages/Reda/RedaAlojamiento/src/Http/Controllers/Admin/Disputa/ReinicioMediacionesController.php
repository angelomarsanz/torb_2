<?php

/** Expone la herramienta de reinicio exclusivamente a administradores autorizados en desarrollo. */

namespace Reda\RedaAlojamiento\Http\Controllers\Admin\Disputa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Reda\RedaAlojamiento\Services\ReinicioMediacionesService;
use Throwable;

class ReinicioMediacionesController extends Controller
{
    /** Verifica la habilitación explícita y el rol 1 en cada petición. */
    private function autorizar(): void
    {
        abort_unless(config('reda-alojamiento.reinicio_mediaciones_habilitado') === true
            && app()->environment(['local', 'development', 'dev']), 404);
        $admin = Auth::guard('admin')->user();
        abort_unless($admin && DB::table('role_admin')->where('admin_id', $admin->id)->where('role_id', 1)->exists(), 403);
    }

    /** Presenta la herramienta y las cuentas elegibles para selección voluntaria. */
    public function index(ReinicioMediacionesService $servicio)
    {
        $this->autorizar();
        $usuarios = $servicio->usuariosElegibles()->select('users.id', 'users.first_name', 'users.last_name', 'users.email')
            ->orderBy('users.id')->get();
        return view('reda-alojamiento::admin.disputa.reinicio_mediaciones', compact('usuarios'));
    }

    /** Valida la confirmación y ejecuta el reinicio, comunicando fallos y archivos pendientes. */
    public function reiniciar(Request $solicitud, ReinicioMediacionesService $servicio)
    {
        $this->autorizar();
        $validador = Validator::make($solicitud->all(), [
            'confirmacion' => ['required', 'string', 'in:Estoy seguro de reiniciar las mediaciones'],
            'usuarios' => ['nullable', 'array'],
            'usuarios.*' => ['required', 'integer', 'distinct', 'min:1'],
        ]);
        $datosOriginales = [];
        if (str_starts_with((string) $solicitud->header('Content-Type'), 'application/x-www-form-urlencoded')) {
            parse_str($solicitud->getContent(), $datosOriginales);
        }
        if ($validador->fails() || ($datosOriginales['confirmacion'] ?? null) !== 'Estoy seguro de reiniciar las mediaciones') {
            return response()->json([
                'success' => false,
                'message' => __('Error de validación'),
                'mensaje_usuario' => __('Escriba la frase de confirmación exactamente.'),
                'respuesta' => $validador->errors(),
                'code' => 422,
            ], 422);
        }
        $datos = $validador->validated();
        try {
            $resultado = $servicio->reiniciar(array_map('intval', $datos['usuarios'] ?? []));
            $pendientes = $resultado['adjuntosPendientes'];
            Log::warning('Reinicio de mediaciones ejecutado por administrador ID ' . Auth::guard('admin')->id() . ': ' . print_r($resultado, true));
            return response()->json([
                'success' => $pendientes === 0,
                'message' => $pendientes ? __('Hay adjuntos pendientes') : __('Reinicio completado'),
                'mensaje_usuario' => $pendientes
                    ? __('Los datos se reiniciaron, pero hay adjuntos pendientes. Revise el registro y repita el reinicio durante mantenimiento.')
                    : __('El reinicio terminó. Los mensajes históricos ambiguos se conservaron.'),
                'respuesta' => $resultado,
                'code' => $pendientes ? 500 : 200,
            ], $pendientes ? 500 : 200);
        } catch (Throwable $error) {
            Log::error('Error en reinicio de mediaciones: ' . $error->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Error al reiniciar mediaciones'),
                'mensaje_usuario' => __('No se pudo completar el reinicio. Revise el registro y actualice la página antes de volver a intentarlo; los datos podrían haberse reiniciado parcialmente.'),
                'respuesta' => $error->getMessage(),
                'code' => 500,
            ], 500);
        }
    }
}
