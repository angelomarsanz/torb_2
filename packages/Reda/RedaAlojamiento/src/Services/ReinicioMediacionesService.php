<?php

/**
 * Reinicia datos inequívocos de mediación y conserva una cola durable de adjuntos pendientes.
 * Debe ejecutarse durante una ventana sin actividad de mediaciones ni de su chat.
 */

namespace Reda\RedaAlojamiento\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
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
     * El bloqueo de MySQL impide que dos administradores ejecuten el reinicio simultáneamente.
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
                        $archivo = $this->archivoSeguro($ruta);
                        if ($archivo !== null) {
                            $hash = hash_file('sha256', $archivo);
                            if ($hash === false) {
                                throw new RuntimeException('No se pudo verificar un adjunto antes del reinicio.');
                            }
                            AdjuntoMediacionPendiente::create(['ruta' => $ruta, 'hash' => $hash]);
                        }
                    }
                }

                $mensajes = DB::table('mensajes_de_mediacion')->whereNotNull('mensaje_id')->pluck('mensaje_id');
                DB::table('reda_mensajes_metadata')->whereIn('message_id', $mensajes)->delete();
                $cantidadMensajes = DB::table('messages')->whereIn('id', $mensajes)->delete();
                DB::table('mensajes_de_mediacion')->delete();
                $alertas = DB::table('alertas_admin')->whereIn('tipo', ['primer_aviso', 'suspension'])->delete();
                $reactivados = $usuarios ? $this->usuariosElegibles()->whereIn('users.id', $usuarios)
                    ->update(['status' => 'Active', 'updated_at' => now()]) : 0;
                if ($reactivados !== count($usuarios)) {
                    throw new RuntimeException('No se pudieron reactivar todas las cuentas seleccionadas.');
                }
                $avisos = DB::table('usuarios_avisos_mediaciones')->delete();
                $cantidadDisputas = DB::table('disputas')->delete();

                return compact('cantidadDisputas', 'cantidadMensajes', 'alertas', 'avisos', 'reactivados', 'adjuntosConservados');
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

    /** Rechaza enlaces simbólicos en cada componente y devuelve null si el archivo no existe. */
    private function archivoSeguro(string $ruta): ?string
    {
        $ruta = $this->normalizarRuta($ruta);
        $actual = rtrim(public_path(), DIRECTORY_SEPARATOR);
        foreach (explode('/', $ruta) as $parte) {
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
                if (Schema::hasTable('disputas_respaldo')) {
                    throw new RuntimeException('Existe disputas_respaldo; se conservan los adjuntos por precaución.');
                }
                $archivo = $this->archivoSeguro($pendiente->ruta);
                if ($archivo !== null) {
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
