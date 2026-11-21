<?php

/** Administra por pantalla las opciones de texto con el permiso de plantillas de correo. */
namespace Reda\RedaAlojamiento\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Reda\RedaAlojamiento\Models\PlantillaTexto;
use Reda\RedaAlojamiento\Services\PlantillasTextoService;

class PlantillaTextoController extends Controller
{
    /** Exige el mismo permiso usado por el menú original de plantillas de correo. */
    private function autorizar(): void
    {
        $admin = Auth::guard('admin')->user();
        abort_unless($admin && \Helpers::has_permission($admin->id, 'manage_email_template'), 403);
    }

    /** Presenta las opciones paginadas y la opción seleccionada del contexto elegido. */
    public function index(Request $solicitud)
    {
        $this->autorizar();
        $solicitud->validate(['contexto' => ['sometimes', 'required', 'in:' . PlantillaTexto::CONTEXTO_PAGO . ',' . PlantillaTexto::CONTEXTO_RESERVA]]);
        $contexto = $solicitud->query('contexto', PlantillaTexto::CONTEXTO_PAGO);
        $plantillas = PlantillaTexto::where('contexto', $contexto)
            ->orderBy('orden')->orderBy('id')->paginate(10)->withQueryString();
        $seleccionada = $solicitud->filled('editar')
            ? PlantillaTexto::where('contexto', $contexto)->findOrFail($solicitud->query('editar'))
            : null;
        return view('reda-alojamiento::admin.plantillas.textos', compact('plantillas', 'seleccionada', 'contexto'));
    }

    /** Valida y guarda una opción en su contexto, conservando su orden explícito. */
    public function guardar(Request $solicitud, PlantillasTextoService $servicio)
    {
        $this->autorizar();
        $datos = $solicitud->validate([
            'id' => ['nullable', 'integer', 'min:1'],
            'contexto' => ['required', 'in:' . PlantillaTexto::CONTEXTO_PAGO . ',' . PlantillaTexto::CONTEXTO_RESERVA],
            'nombre' => ['required', 'string', 'max:191'],
            'contenido' => ['required', 'string', 'max:100000'],
            'orden' => ['required', 'integer', 'min:0', 'max:999999'],
        ]);
        if ($servicio->textoPlano($datos['contenido']) === '') {
            return back()->withErrors(['contenido' => __('Escriba el texto de la plantilla.')])->withInput();
        }
        $plantilla = !empty($datos['id'])
            ? PlantillaTexto::where('contexto', $datos['contexto'])->findOrFail($datos['id'])
            : new PlantillaTexto();
        $plantilla->fill($datos);
        $plantilla->save();
        return redirect()->route('reda.admin.plantillas.textos', ['contexto' => $plantilla->contexto, 'editar' => $plantilla->id])
            ->with('estado_plantilla', __('Plantilla guardada correctamente.'));
    }

    /** Elimina una opción del contexto indicado sin modificar mensajes ya enviados. */
    public function eliminar(Request $solicitud, int $id)
    {
        $this->autorizar();
        $datos = $solicitud->validate([
            'contexto' => ['required', 'in:' . PlantillaTexto::CONTEXTO_PAGO . ',' . PlantillaTexto::CONTEXTO_RESERVA],
        ]);
        PlantillaTexto::where('contexto', $datos['contexto'])->findOrFail($id)->delete();
        return redirect()->route('reda.admin.plantillas.textos', ['contexto' => $datos['contexto']])
            ->with('estado_plantilla', __('Plantilla eliminada correctamente.'));
    }
}
