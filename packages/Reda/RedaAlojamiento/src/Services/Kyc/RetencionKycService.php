<?php

/**
 * Resumen: Política de retención de documentos de identidad del KYC. Está SEPARADA de la lógica de aprobación:
 * eliminar o conservar un archivo nunca cambia el estado de verificación de un usuario.
 *
 * Para cada solicitud ya cerrada (verified, rejected, failed o expired) con archivos aún guardados calcula la
 * fecha de eliminación = fecha de cierre + plazo del estado (config kyc.retencion, en días; null = no eliminar).
 * Los plazos se leen siempre de la configuración, de modo que un cambio se aplica en la siguiente ejecución.
 *
 * NO elimina si hay un proceso pendiente asociado al usuario (kyc.retencion.respetar_procesos_pendientes):
 *  - una mediación/disputa activa en la que participe (estado distinto de Cerrado/Cerrada), o
 *  - una cuenta con acciones restringidas por mediaciones, o
 *  - una retención suspendida manualmente en la solicitud (retencion_suspendida).
 * Elimina solo los archivos y conserva las filas de documentos y la auditoría. Lo ejecuta el comando
 * reda:kyc-depurar (diario) y admite una simulación sin cambios.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Services\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Services\Kyc;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Reda\RedaAlojamiento\Enums\EstadoKyc;
use Reda\RedaAlojamiento\Models\Kyc\DocumentoKyc;
use Reda\RedaAlojamiento\Models\Kyc\SolicitudKyc;
use Throwable;

class RetencionKycService
{
    public function __construct(private readonly KycDocumentoService $documentos)
    {
    }

    /**
     * Plazo de retención en días para el estado de una solicitud cerrada (null = sin eliminación automática).
     */
    public function plazoEnDias(EstadoKyc $estado): ?int
    {
        $clave = match ($estado) {
            EstadoKyc::Verificado => 'dias_tras_aprobacion',
            EstadoKyc::Rechazado  => 'dias_tras_rechazo',
            EstadoKyc::Fallido    => 'dias_tras_fallo',
            EstadoKyc::Expirado   => 'dias_tras_expiracion',
            default               => null,
        };

        $dias = $clave ? config('kyc.retencion.' . $clave) : null;

        return is_numeric($dias) && (int) $dias >= 0 ? (int) $dias : null;
    }

    /**
     * Fecha a partir de la cual pueden eliminarse los archivos de una solicitud cerrada (null si no aplica).
     */
    public function fechaLimite(SolicitudKyc $solicitud): ?CarbonInterface
    {
        if (!$solicitud->estado || $solicitud->estado->esAbierto()) {
            return null;
        }

        $dias = $this->plazoEnDias($solicitud->estado);
        $base = $solicitud->cerrada_en ?? $solicitud->aprobada_en ?? $solicitud->rechazada_en ?? $solicitud->updated_at;

        return $dias === null || $base === null ? null : $base->copy()->addDays($dias);
    }

    /**
     * Motivos por los que NO deben eliminarse los documentos de un usuario (lista vacía si puede eliminarse).
     *
     * @return array<int,string>
     */
    public function procesosPendientes(int $usuarioId): array
    {
        $motivos = [];

        if (Schema::hasTable('disputas')) {
            $cerrados = array_values(array_unique(['Cerrado', 'Cerrada', __('Cerrado'), __('Cerrada')]));

            $activa = DB::table('disputas')
                ->where(function ($q) use ($usuarioId) {
                    $q->where('id_usuario_turista', $usuarioId)
                        ->orWhere('id_usuario_anfitrion', $usuarioId)
                        ->orWhere('id_usuario_inicial', $usuarioId);
                })
                ->where(function ($q) use ($cerrados) {
                    $q->whereNull('estado')->orWhereNotIn('estado', $cerrados);
                })
                ->exists();

            if ($activa) {
                $motivos[] = 'mediacion_activa';
            }
        }

        if (Schema::hasTable('usuarios_avisos_mediaciones')
            && DB::table('usuarios_avisos_mediaciones')->where('user_id', $usuarioId)->where('cuenta_suspendida', 1)->exists()) {
            $motivos[] = 'cuenta_restringida_por_mediaciones';
        }

        return $motivos;
    }

    /**
     * Programa y ejecuta la eliminación de documentos vencidos.
     *
     * @param  bool $simular Si es true solo cuenta, sin modificar archivos ni base de datos.
     * @return array{habilitada:bool,evaluadas:int,programadas:int,eliminadas:int,documentos:int,bloqueadas:int,errores:int}
     */
    public function depurar(bool $simular = false): array
    {
        $resumen = [
            'habilitada' => (bool) config('kyc.retencion.automatica_habilitada', true),
            'evaluadas' => 0, 'programadas' => 0, 'eliminadas' => 0, 'documentos' => 0, 'bloqueadas' => 0, 'errores' => 0,
        ];

        if (!$resumen['habilitada']) {
            return $resumen;
        }

        $cerrados = [EstadoKyc::Verificado->value, EstadoKyc::Rechazado->value, EstadoKyc::Fallido->value, EstadoKyc::Expirado->value];

        SolicitudKyc::query()
            ->whereIn('estado', $cerrados)
            ->whereNull('documentos_eliminados_en')
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))->from('documentos_kyc')
                    ->whereColumn('documentos_kyc.solicitud_id', 'solicitudes_kyc.id')
                    ->where('documentos_kyc.estado', '!=', DocumentoKyc::ESTADO_ELIMINADO);
            })
            ->chunkById(100, function ($lote) use (&$resumen, $simular) {
                foreach ($lote as $solicitud) {
                    $resumen['evaluadas']++;

                    try {
                        $this->evaluar($solicitud, $simular, $resumen);
                    } catch (Throwable $e) {
                        $resumen['errores']++;
                        Log::error('KYC retención: error con la solicitud ' . $solicitud->id . ' (' . get_class($e) . ').');
                    }
                }
            });

        return $resumen;
    }

    /**
     * Evalúa una solicitud: actualiza su fecha de eliminación y, si venció y nada lo impide, elimina sus archivos.
     *
     * @param array<string,mixed> $resumen
     */
    private function evaluar(SolicitudKyc $solicitud, bool $simular, array &$resumen): void
    {
        $limite = $this->fechaLimite($solicitud);

        if ($limite === null) {
            return;
        }

        if (!$simular && (!$solicitud->eliminar_documentos_en || !$solicitud->eliminar_documentos_en->equalTo($limite))) {
            $this->documentos->programarEliminacion($solicitud, $limite);
            $resumen['programadas']++;
        }

        if ($limite->isFuture()) {
            return;
        }

        if ($solicitud->retencion_suspendida
            || (config('kyc.retencion.respetar_procesos_pendientes', true) && !empty($this->procesosPendientes((int) $solicitud->user_id)))) {
            $resumen['bloqueadas']++;

            return;
        }

        $resumen['eliminadas']++;

        if ($simular) {
            $resumen['documentos'] += DocumentoKyc::where('solicitud_id', $solicitud->id)
                ->where('estado', '!=', DocumentoKyc::ESTADO_ELIMINADO)->count();

            return;
        }

        $resumen['documentos'] += $this->documentos->eliminarDocumentosDeSolicitud($solicitud, 'retencion_vencida');
    }
}
