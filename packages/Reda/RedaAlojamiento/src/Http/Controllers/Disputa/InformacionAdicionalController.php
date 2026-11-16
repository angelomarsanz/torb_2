<?php

/** Gestiona solicitudes de información, archivos privados, avisos y correos reintentables. */
namespace Reda\RedaAlojamiento\Http\Controllers\Disputa;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Messages;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Reda\RedaAlojamiento\Models\Alerta\AlertaAdmin;
use Reda\RedaAlojamiento\Models\Disputa\ArchivoInformacion;
use Reda\RedaAlojamiento\Models\Disputa\CorreoInformacion;
use Reda\RedaAlojamiento\Models\Disputa\DestinatarioInformacion;
use Reda\RedaAlojamiento\Models\Disputa\Disputa;
use Reda\RedaAlojamiento\Models\Disputa\MensajeDeMediacion;
use Reda\RedaAlojamiento\Models\Disputa\MensajeMetadata;
use Reda\RedaAlojamiento\Models\Disputa\SolicitudInformacion;
use Throwable;

class InformacionAdicionalController extends Controller
{
    /** Devuelve la estructura de respuesta común del plugin. */
    private function responder(bool $exito, string $mensaje, $datos = '', int $codigo = 200)
    {
        return response()->json(['success' => $exito, 'message' => $mensaje, 'mensaje_usuario' => $mensaje,
            'respuesta' => $datos, 'code' => $codigo], $codigo);
    }

    /** Comprueba el rol y, para agentes, la asignación del expediente. */
    private function autorizarAdmin(Disputa $disputa, bool $soloAsignado = false): void
    {
        $id = Auth::guard('admin')->id();
        $roles = DB::table('role_admin')->where('admin_id', $id)->whereIn('role_id', [1, 2])->pluck('role_id');
        abort_unless($id && $roles->isNotEmpty(), 403);
        if ($soloAsignado) {
            abort_unless($disputa->id_usuario_agente_asignado && (int) $disputa->id_usuario_agente_asignado === (int) $id, 403);
        } elseif (!$roles->contains(1)) {
            abort_unless(!$disputa->id_usuario_agente_asignado || (int) $disputa->id_usuario_agente_asignado === (int) $id, 403);
        }
    }

    /** Comprueba que el usuario sea destinatario de la solicitud. */
    private function autorizarDestinatario(SolicitudInformacion $solicitud): void
    {
        abort_unless(Auth::id() && DestinatarioInformacion::where('solicitud_id', $solicitud->id)
            ->where('usuario_id', Auth::id())->exists(), 403);
    }

    /** Guarda un documento fuera del directorio público y registra su ruta. */
    private function guardarArchivo($archivo, SolicitudInformacion $solicitud, string $tipo, ?int $usuarioId, array &$rutas): void
    {
        $ruta = $archivo->store('reda/mediaciones/informacion/' . $solicitud->id, ['disk' => 'local', 'visibility' => 'private']);
        if (!$ruta) {
            throw new \RuntimeException('No se pudo guardar el archivo privado.');
        }
        $rutas[] = $ruta;
        ArchivoInformacion::create([
            'solicitud_id' => $solicitud->id, 'usuario_id' => $usuarioId, 'tipo' => $tipo,
            'ruta' => $ruta, 'nombre' => basename(str_replace('\\', '/', $archivo->getClientOriginalName())),
            'mime' => $archivo->getMimeType(),
        ]);
    }

    /** Envía los correos pendientes, conservando el error para reintentos explícitos. */
    private function enviarCorreos(int $disputaId): int
    {
        $ids = CorreoInformacion::join('solicitudes_informacion_mediacion as s', 's.id', '=', 'correos_informacion_mediacion.solicitud_id')
            ->where('s.disputa_id', $disputaId)->where('correos_informacion_mediacion.estado', 'pendiente')
            ->pluck('correos_informacion_mediacion.id');
        foreach ($ids as $id) {
            DB::transaction(function () use ($id) {
                $correo = CorreoInformacion::whereKey($id)->lockForUpdate()->first();
                if (!$correo || $correo->estado !== 'pendiente') {
                    return;
                }
                try {
                    $solicitud = SolicitudInformacion::findOrFail($correo->solicitud_id);
                    $disputa = Disputa::findOrFail($solicitud->disputa_id);
                    $destino = $correo->tipo === 'solicitud' ? User::findOrFail($correo->destinatario_id) : Admin::findOrFail($correo->destinatario_id);
                    if (!filter_var($destino->email, FILTER_VALIDATE_EMAIL)) {
                        throw new \RuntimeException('El destinatario no tiene un correo válido.');
                    }
                    $usuario = $correo->usuario_origen_id ? User::find($correo->usuario_origen_id) : null;
                    $enlace = $correo->tipo === 'solicitud'
                        ? route('reda.informacion.acceso', ['id' => $solicitud->id])
                        : url('admin/reda/alertas');
                    Mail::send('reda-alojamiento::emails.informacion_mediacion', compact('solicitud', 'disputa', 'correo', 'usuario', 'enlace'), function ($mensaje) use ($destino) {
                        $mensaje->to($destino->email)->subject(__('Información adicional de mediación'));
                    });
                    $correo->estado = 'enviado';
                    $correo->detalle = null;
                } catch (Throwable $error) {
                    Log::error('Error al enviar correo de información adicional ID ' . $id . ': ' . $error->getMessage());
                    $correo->detalle = $error->getMessage();
                }
                $correo->save();
            });
        }
        return CorreoInformacion::join('solicitudes_informacion_mediacion as s', 's.id', '=', 'correos_informacion_mediacion.solicitud_id')
            ->where('s.disputa_id', $disputaId)->where('correos_informacion_mediacion.estado', 'pendiente')->count();
    }

    /** Crea la solicitud, renueva únicamente plazos vigentes y entrega mensajes privados. */
    public function crear(Request $peticion)
    {
        $maximoHoras = (int) now()->diffInHours(now()->copy()->setDate(9999, 12, 31)->setTime(23, 59, 59));
        $validador = Validator::make($peticion->all(), [
            'disputa_id' => 'required|integer|min:1', 'destino' => 'required|in:demandante,demandado,ambos',
            'solicitud' => 'required|string|max:10000', 'horas' => 'required|integer|min:1|max:' . $maximoHoras,
            'archivo' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
        ]);
        if ($validador->fails()) {
            return $this->responder(false, __('Revise los campos y el archivo de la solicitud.'), $validador->errors(), 422);
        }
        $rutas = [];
        try {
            $resultado = DB::transaction(function () use ($peticion, &$rutas) {
                $disputa = Disputa::whereKey($peticion->disputa_id)->lockForUpdate()->firstOrFail();
                $this->autorizarAdmin($disputa, true);
                $participantes = [(int) $disputa->id_usuario_turista, (int) $disputa->id_usuario_anfitrion];
                $iniciador = (int) $disputa->id_usuario_inicial;
                if (!$disputa->booking || !$iniciador || !in_array($iniciador, $participantes, true)
                    || !$participantes[0] || !$participantes[1] || $participantes[0] === $participantes[1]) {
                    throw new \RuntimeException('La mediación no tiene participantes o demandante válidos.');
                }
                $demandado = $participantes[0] === $iniciador ? $participantes[1] : $participantes[0];
                $destinos = $peticion->destino === 'ambos' ? $participantes : [$peticion->destino === 'demandante' ? $iniciador : $demandado];
                $ahora = now();
                $vence = $ahora->copy()->addHours((int) $peticion->horas);
                $renovadas = SolicitudInformacion::where('disputa_id', $disputa->id)->where('vence_en', '>', $ahora)
                    ->update(['vence_en' => $vence, 'updated_at' => $ahora]);
                $solicitud = SolicitudInformacion::create([
                    'disputa_id' => $disputa->id, 'agente_id' => Auth::guard('admin')->id(),
                    'solicitud' => $peticion->solicitud, 'vence_en' => $vence,
                ]);
                if ($peticion->hasFile('archivo')) {
                    $this->guardarArchivo($peticion->file('archivo'), $solicitud, 'solicitud', null, $rutas);
                }
                foreach ($destinos as $usuarioId) {
                    User::findOrFail($usuarioId);
                    $mensaje = new Messages();
                    $mensaje->property_id = $disputa->booking->property_id;
                    $mensaje->booking_id = $disputa->booking_id;
                    $mensaje->sender_id = Auth::guard('admin')->id();
                    $mensaje->receiver_id = $usuarioId;
                    $mensaje->message = $peticion->solicitud;
                    $mensaje->type_id = 1;
                    $mensaje->read = 0;
                    $mensaje->save();
                    MensajeMetadata::updateOrCreate(['message_id' => $mensaje->id], ['sender_type' => 'admin']);
                    MensajeDeMediacion::create(['mensaje_id' => $mensaje->id]);
                    DestinatarioInformacion::create(['solicitud_id' => $solicitud->id, 'usuario_id' => $usuarioId, 'mensaje_id' => $mensaje->id]);
                    CorreoInformacion::create(['solicitud_id' => $solicitud->id, 'destinatario_id' => $usuarioId, 'tipo' => 'solicitud', 'estado' => 'pendiente']);
                }
                $disputa->estado = 'Esperando respuesta';
                $disputa->paso_actual = 'Solicitud de información adicional';
                $disputa->save();
                return ['disputa_id' => $disputa->id, 'renovadas' => $renovadas];
            });
        } catch (Throwable $error) {
            foreach ($rutas as $ruta) {
                if (!Storage::disk('local')->delete($ruta)) {
                    Log::error('No se pudo eliminar un archivo privado tras revertir: ' . $ruta);
                }
            }
            if ($error instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                return $this->responder(false, __('Solo el agente asignado puede solicitar información adicional.'), '', $error->getStatusCode());
            }
            Log::error('Error al crear solicitud de información: ' . $error->getMessage());
            return $this->responder(false, __('No se pudo crear la solicitud de información.'), '', 500);
        }
        try {
            $resultado['correos_pendientes'] = $this->enviarCorreos($resultado['disputa_id']);
        } catch (Throwable $error) {
            Log::error('La solicitud fue guardada, pero falló el procesamiento de correos: ' . $error->getMessage());
            $resultado['correos_pendientes'] = 1;
        }
        return $this->responder(true, __('La solicitud de información ha sido enviada.'), $resultado);
    }

    /** Reintenta los correos pendientes de una mediación autorizada. */
    public function reintentar(int $id)
    {
        $disputa = Disputa::findOrFail($id);
        $this->autorizarAdmin($disputa, true);
        try {
            $pendientes = $this->enviarCorreos($id);
        } catch (Throwable $error) {
            Log::error('Error al reintentar correos de mediación: ' . $error->getMessage());
            return $this->responder(false, __('No se pudieron procesar los correos pendientes.'), '', 500);
        }
        return $this->responder($pendientes === 0, $pendientes ? __('Hay correos pendientes de envío.') : __('Los correos pendientes fueron enviados.'), ['pendientes' => $pendientes], $pendientes ? 503 : 200);
    }

    /** Redirige desde el correo al inbox conservando el destino después del inicio de sesión. */
    public function acceso(int $id)
    {
        $url = route('reda.informacion.acceso', ['id' => $id]);
        if (!Auth::check()) {
            session(['url.intended' => $url]);
            return redirect()->guest('login');
        }
        $solicitud = SolicitudInformacion::findOrFail($id);
        $this->autorizarDestinatario($solicitud);
        $disputa = Disputa::findOrFail($solicitud->disputa_id);
        return redirect(url('inbox') . '?' . http_build_query(['id' => $disputa->booking_id, 'solicitud_informacion' => $id]));
    }

    /** Comprueba destinatario y plazo antes de mostrar el formulario de carga. */
    public function consultar(int $id)
    {
        $solicitud = SolicitudInformacion::findOrFail($id);
        $this->autorizarDestinatario($solicitud);
        if (!$solicitud->vence_en || !$solicitud->vence_en->isFuture()) {
            return $this->responder(false, __('El plazo para cargar los archivos ha vencido.'), '', 410);
        }
        $archivos = ArchivoInformacion::where('solicitud_id', $id)->where('tipo', 'solicitud')->get(['id', 'nombre']);
        return $this->responder(true, __('Solicitud disponible.'), [
            'solicitud' => $solicitud->solicitud, 'vence_en' => $solicitud->vence_en->format('d/m/Y H:i'),
            'archivos' => $archivos,
        ]);
    }

    /** Recibe archivos privados bajo el plazo bloqueado y notifica al agente asignado. */
    public function cargar(Request $peticion, int $id)
    {
        $validador = Validator::make($peticion->all(), [
            'archivos' => 'required|array|min:1|max:5',
            'archivos.*' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
        ]);
        if ($validador->fails()) {
            return $this->responder(false, __('Seleccione hasta 5 archivos JPG, PNG, WebP o PDF de máximo 10 MB cada uno.'), $validador->errors(), 422);
        }
        $rutas = [];
        try {
            $disputaId = DB::transaction(function () use ($id, $peticion, &$rutas) {
                $inicial = SolicitudInformacion::findOrFail($id);
                $disputa = Disputa::whereKey($inicial->disputa_id)->lockForUpdate()->firstOrFail();
                $solicitud = SolicitudInformacion::whereKey($id)->lockForUpdate()->firstOrFail();
                $this->autorizarDestinatario($solicitud);
                abort_if(!$solicitud->vence_en || !$solicitud->vence_en->isFuture(), 410, __('El plazo para cargar los archivos ha vencido.'));
                $agente = Admin::find($disputa->id_usuario_agente_asignado);
                if (!$agente) {
                    throw new \RuntimeException('La mediación no tiene un agente asignado para recibir el aviso.');
                }
                foreach ($peticion->file('archivos') as $archivo) {
                    $this->guardarArchivo($archivo, $solicitud, 'respuesta', (int) Auth::id(), $rutas);
                }
                $usuario = Auth::user();
                $nombre = trim($usuario->first_name . ' ' . $usuario->last_name);
                AlertaAdmin::create([
                    'admin_id' => $agente->id, 'titulo' => __('Archivos recibidos en mediación'),
                    'mensaje' => __('El usuario :nombre envió archivos solicitados.', ['nombre' => $nombre]),
                    'tipo' => 'archivos_informacion', 'disputa_id' => $disputa->id, 'user_id' => $usuario->id, 'leido' => false,
                ]);
                CorreoInformacion::create(['solicitud_id' => $id, 'destinatario_id' => $agente->id,
                    'usuario_origen_id' => $usuario->id, 'tipo' => 'carga', 'estado' => 'pendiente']);
                return $disputa->id;
            });
        } catch (Throwable $error) {
            foreach ($rutas as $ruta) {
                if (!Storage::disk('local')->delete($ruta)) {
                    Log::error('No se pudo eliminar un archivo privado tras revertir: ' . $ruta);
                }
            }
            if ($error instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                return $this->responder(false, $error->getStatusCode() === 410 ? __('El plazo para cargar los archivos ha vencido.') : __('No tiene acceso a esta solicitud.'), '', $error->getStatusCode());
            }
            Log::error('Error al cargar información adicional: ' . $error->getMessage());
            return $this->responder(false, __('No se pudieron cargar los archivos. Contacte al equipo de soporte.'), '', 500);
        }
        try {
            $pendientes = $this->enviarCorreos($disputaId);
        } catch (Throwable $error) {
            Log::error('Los archivos fueron guardados, pero falló el procesamiento de correos: ' . $error->getMessage());
            $pendientes = 1;
        }
        return $this->responder(true, __('Los archivos fueron cargados.'), ['correos_pendientes' => $pendientes]);
    }

    /** Entrega un archivo privado únicamente al propietario, destinatario del complemento o soporte autorizado. */
    public function archivo(int $id, bool $administrador = false)
    {
        $archivo = ArchivoInformacion::findOrFail($id);
        $solicitud = SolicitudInformacion::findOrFail($archivo->solicitud_id);
        if ($administrador) {
            $this->autorizarAdmin(Disputa::findOrFail($solicitud->disputa_id));
        } else {
            $this->autorizarDestinatario($solicitud);
            abort_unless($archivo->tipo === 'solicitud' || (int) $archivo->usuario_id === (int) Auth::id(), 403);
        }
        abort_unless($archivo->ruta && Storage::disk('local')->exists($archivo->ruta), 404);
        return Storage::disk('local')->response($archivo->ruta, $archivo->nombre, [
            'Content-Type' => $archivo->mime, 'X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store',
        ]);
    }

    /** Adapta la descarga para el guard administrativo. */
    public function archivoAdmin(int $id)
    {
        return $this->archivo($id, true);
    }
}
