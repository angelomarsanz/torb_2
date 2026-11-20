<?php

/** Administra las opciones de texto predefinido respetando el permiso de plantillas de correo. */
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

    /** Presenta las opciones paginadas y la opción seleccionada para edición. */
    public function index(Request $solicitud)
    {
        $this->autorizar();
        $plantillas = PlantillaTexto::where('contexto', 'mensaje_anfitrion')
            ->orderBy('orden')->orderBy('id')->paginate(10)->withQueryString();
        $seleccionada = $solicitud->filled('editar')
            ? PlantillaTexto::where('contexto', 'mensaje_anfitrion')->findOrFail($solicitud->query('editar'))
            : null;
        return view('reda-alojamiento::admin.plantillas.textos', compact('plantillas', 'seleccionada'));
    }

    /** Valida y guarda una opción nueva o existente, conservando su orden explícito. */
    public function guardar(Request $solicitud, PlantillasTextoService $servicio)
    {
        $this->autorizar();
        $datos = $solicitud->validate([
            'id' => ['nullable', 'integer', 'min:1'],
            'nombre' => ['required', 'string', 'max:191'],
            'contenido' => ['required', 'string', 'max:100000'],
            'orden' => ['required', 'integer', 'min:0', 'max:999999'],
        ]);
        if ($servicio->textoPlano($datos['contenido']) === '') {
            return back()->withErrors(['contenido' => __('Escriba el texto de la plantilla.')])->withInput();
        }
        $plantilla = !empty($datos['id'])
            ? PlantillaTexto::where('contexto', 'mensaje_anfitrion')->findOrFail($datos['id'])
            : new PlantillaTexto();
        $plantilla->fill($datos);
        $plantilla->contexto = 'mensaje_anfitrion';
        $plantilla->save();
        return redirect()->route('reda.admin.plantillas.textos', ['editar' => $plantilla->id])
            ->with('estado_plantilla', __('Plantilla guardada correctamente.'));
    }

    /** Elimina una opción sin modificar los mensajes ya enviados. */
    public function eliminar(int $id)
    {
        $this->autorizar();
        PlantillaTexto::where('contexto', 'mensaje_anfitrion')->findOrFail($id)->delete();
        return redirect()->route('reda.admin.plantillas.textos')
            ->with('estado_plantilla', __('Plantilla eliminada correctamente.'));
    }
}
