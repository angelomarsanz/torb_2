<?php

/**
 * Reinicia datos inequívocos de mediación y conserva una cola durable de adjuntos pendientes.
 * Debe ejecutarse durante una ventana sin actividad de mediaciones ni de su chat.
 * Incluye solicitudes de información y archivos privados; conserva los fallos en la cola.
 */

namespace Reda\RedaAlojamiento\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Reda\RedaAlojamiento\Models\Disputa\AdjuntoMediacionPendiente;
use RuntimeException;
use Throwable;

class ReinicioMediacionesService
{
    /** Consulta cuentas inactivas, no eliminadas y con antecedentes de segundo aviso. */
    public function usuariosElegibles()
    {
        return DB::table('users')
            ->where('status', 'Inactive')->whereNull('deleted_at')
            ->whereExists(function ($consulta) {
                $consulta->selectRaw('1')->from('usuarios_avisos_mediaciones')
                    ->whereColumn('usuarios_avisos_mediaciones.user_id', 'users.id')
                    ->where(function ($condicion) {
                        $condicion->where('segundo_aviso_enviado', 1)->orWhere('cuenta_suspendida', 1);
                    });
            });
    }

    /**
     * Elimina datos en una transacción y procesa después la cola persistente de archivos.
     * El bloqueo de MySQL impide reinicios simultáneos. Los archivos pendientes sobreviven al commit.
     */
    public function reiniciar(array $usuarios): array
    {
        $bloqueo = DB::selectOne('SELECT GET_LOCK(?, 0) AS obtenido', ['reda_reinicio_mediaciones']);
        if (!$bloqueo || (int) $bloqueo->obtenido !== 1) {
            throw new RuntimeException('Ya hay un reinicio de mediaciones en ejecución.');
        }

        try {
            $resultado = DB::transaction(function () use ($usuarios) {
                $elegibles = $this->usuariosElegibles()->whereIn('users.id', $usuarios)
                    ->lockForUpdate()->pluck('users.id')->map(fn ($id) => (int) $id)->all();
                if (count($elegibles) !== count($usuarios)) {
                    throw new RuntimeException('Una o más cuentas seleccionadas ya no son elegibles. Actualice la página.');
                }

                $disputas = DB::table('disputas')->lockForUpdate()->get();
                $conservarAdjuntos = Schema::hasTable('disputas_respaldo');
                $rutas = [];
                foreach ($disputas as $disputa) {
                    foreach (['documentos_turista', 'documentos_anfitrion', 'documentos_agente'] as $columna) {
                        if (!$disputa->$columna) {
                            continue;
                        }
                        $documentos = json_decode($disputa->$columna, true, 512, JSON_THROW_ON_ERROR);
                        if (!is_array($documentos)) {
                            throw new RuntimeException('La lista de adjuntos de una mediación no es válida.');
                        }
                        foreach ($documentos as $ruta) {
                            if (!is_string($ruta)) {
                                throw new RuntimeException('La ruta de un adjunto no es válida.');
                            }
                            $ruta = $this->normalizarRuta($ruta);
                            if (!str_starts_with($ruta, 'images/disputas/' . $disputa->id . '/')) {
                                throw new RuntimeException('Un adjunto no corresponde al identificador de su mediación.');
                            }
                            $rutas[$ruta] = true;
                        }
                    }
                }

                $adjuntosConservados = $conservarAdjuntos ? count($rutas) : 0;
                if (!$conservarAdjuntos) {
                    foreach (array_keys($rutas) as $ruta) {
                        $this->encolarAdjunto($ruta);
                    }
                }

                $archivosPrivados = DB::table('archivos_informacion_mediacion')->lockForUpdate()->get(['solicitud_id', 'ruta']);
                foreach ($archivosPrivados as $archivo) {
                    if (!is_string($archivo->ruta) || !$archivo->solicitud_id
                        || !preg_match('#^reda/mediaciones/informacion/' . (int) $archivo->solicitud_id . '/[A-Za-z0-9_-]+\\.[A-Za-z0-9]+$#D', $archivo->ruta)) {
                        throw new RuntimeException('Se encontró una ruta privada de mediación no reconocida.');
                    }
                    $this->encolarAdjunto('privado:' . $archivo->ruta);
                }

                $mensajes = DB::table('mensajes_de_mediacion')->whereNotNull('mensaje_id')->pluck('mensaje_id')
                    ->merge(DB::table('destinatarios_informacion_mediacion')->whereNotNull('mensaje_id')->pluck('mensaje_id'))
                    ->unique()->values();
                DB::table('correos_informacion_mediacion')->delete();
                DB::table('archivos_informacion_mediacion')->delete();
                DB::table('destinatarios_informacion_mediacion')->delete();
                $solicitudes = DB::table('solicitudes_informacion_mediacion')->delete();
                DB::table('reda_mensajes_metadata')->whereIn('message_id', $mensajes)->delete();
                $cantidadMensajes = DB::table('messages')->whereIn('id', $mensajes)->delete();
                DB::table('mensajes_de_mediacion')->delete();
                $alertas = DB::table('alertas_admin')->whereIn('tipo', ['primer_aviso', 'suspension', 'archivos_informacion'])->delete();
                $reactivados = $usuarios ? $this->usuariosElegibles()->whereIn('users.id', $usuarios)
                    ->update(['status' => 'Active', 'updated_at' => now()]) : 0;
                if ($reactivados !== count($usuarios)) {
                    throw new RuntimeException('No se pudieron reactivar todas las cuentas seleccionadas.');
                }
                $avisos = DB::table('usuarios_avisos_mediaciones')->delete();
                $cantidadDisputas = DB::table('disputas')->delete();

                return compact('cantidadDisputas', 'cantidadMensajes', 'alertas', 'avisos', 'reactivados', 'adjuntosConservados', 'solicitudes');
            });

            $resultado['adjuntosPendientes'] = $this->procesarAdjuntos();
            return $resultado;
        } finally {
            DB::selectOne('SELECT RELEASE_LOCK(?) AS liberado', ['reda_reinicio_mediaciones']);
        }
    }

    /** Acepta únicamente rutas relativas de archivos dentro de images/disputas. */
    private function normalizarRuta(string $ruta): string
    {
        $ruta = preg_replace('#^public/#', '', $ruta);
        if (!preg_match('#^images/disputas/[0-9]+/(?:documentos_turista|documentos_anfitrion|documentos_agente)/[0-9]+/[a-zA-Z0-9_.-]+$#D', $ruta)
            || str_contains($ruta, '..')) {
            throw new RuntimeException('Se encontró una ruta de adjunto no reconocida; no se realizó el reinicio.');
        }
        return $ruta;
    }

    /** Registra la ruta aunque no se pueda calcular su huella por falta de acceso. */
    private function encolarAdjunto(string $ruta): void
    {
        $hash = null;
        try {
            $archivo = $this->archivoSeguro($ruta);
            if ($archivo !== null) {
                $hash = hash_file('sha256', $archivo);
                if ($hash === false) {
                    throw new RuntimeException('No se pudo calcular la huella del adjunto.');
                }
            }
        } catch (Throwable $error) {
            Log::warning('No se pudo verificar un adjunto de mediación; queda pendiente: ' . $error->getMessage());
        }
        AdjuntoMediacionPendiente::create(['ruta' => $ruta, 'hash' => $hash]);
    }

    /** Rechaza rutas fuera de las áreas previstas y enlaces simbólicos en sus componentes. */
    private function archivoSeguro(string $ruta): ?string
    {
        if (str_starts_with($ruta, 'privado:')) {
            $ruta = substr($ruta, strlen('privado:'));
            if (!preg_match('#^reda/mediaciones/informacion/[0-9]+/[A-Za-z0-9_-]+\\.[A-Za-z0-9]+$#D', $ruta)) {
                throw new RuntimeException('Ruta privada de adjunto no válida.');
            }
            $actual = rtrim(Storage::disk('local')->path(''), DIRECTORY_SEPARATOR);
        } else {
            $ruta = $this->normalizarRuta($ruta);
            $actual = rtrim(public_path(), DIRECTORY_SEPARATOR);
        }
        foreach (explode('/', $ruta) as $parte) {
            if (is_link($actual)) {
                throw new RuntimeException('La ruta del adjunto contiene un enlace simbólico.');
            }
            if (is_dir($actual) && !is_executable($actual)) {
                throw new RuntimeException('No hay permisos para inspeccionar el directorio de un adjunto.');
            }
            $actual .= DIRECTORY_SEPARATOR . $parte;
            if (is_link($actual)) {
                throw new RuntimeException('La ruta del adjunto contiene un enlace simbólico.');
            }
        }
        if (!file_exists($actual)) {
            return null;
        }
        if (!is_file($actual)) {
            throw new RuntimeException('La ruta de un adjunto no corresponde a un archivo.');
        }
        return $actual;
    }

    /** Reintenta cada adjunto sin borrar archivos sustituidos ni datos de respaldos históricos. */
    private function procesarAdjuntos(): int
    {
        foreach (AdjuntoMediacionPendiente::orderBy('id')->get() as $pendiente) {
            try {
                if (!str_starts_with($pendiente->ruta, 'privado:') && Schema::hasTable('disputas_respaldo')) {
                    throw new RuntimeException('Existe disputas_respaldo; se conservan los adjuntos por precaución.');
                }
                $archivo = $this->archivoSeguro($pendiente->ruta);
                if ($archivo !== null) {
                    if (!$pendiente->hash) {
                        throw new RuntimeException('No hay huella verificable para eliminar el adjunto con seguridad.');
                    }
                    if (hash_file('sha256', $archivo) !== $pendiente->hash) {
                        throw new RuntimeException('El adjunto cambió desde que se programó su eliminación.');
                    }
                    if (!unlink($archivo)) {
                        throw new RuntimeException('No se pudo eliminar el adjunto.');
                    }
                }
                $pendiente->delete();
            } catch (Throwable $error) {
                Log::error('Reinicio de mediaciones: adjunto pendiente ID ' . $pendiente->id . ': ' . $error->getMessage());
            }
        }
        return AdjuntoMediacionPendiente::count();
    }
}
