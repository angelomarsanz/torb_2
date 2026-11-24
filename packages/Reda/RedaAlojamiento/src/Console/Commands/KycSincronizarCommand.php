<?php

/**
 * Resumen: Comando "reda:kyc-sincronizar". Consulta de respaldo programada: pregunta a Didit el estado de
 * las sesiones abiertas (in_progress o pending) cuya solicitud lleva más de N minutos
 * (kyc.didit.minutos_para_consulta_programada) sin resolverse, por si el webhook no llegó. Usa el mismo
 * camino que el webhook (KycService::sincronizarConDidit), por lo que respeta la idempotencia y el orden de eventos.
 * Se programa cada 10 minutos desde RedaAlojamientoServiceProvider (requiere que "schedule:run" esté en el cron).
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Console\Commands
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Console\Commands;

use Illuminate\Console\Command;
use Reda\RedaAlojamiento\Enums\EstadoKyc;
use Reda\RedaAlojamiento\Enums\MetodoKyc;
use Reda\RedaAlojamiento\Models\Kyc\SolicitudKyc;
use Reda\RedaAlojamiento\Services\Kyc\DiditService;
use Reda\RedaAlojamiento\Services\Kyc\KycService;

class KycSincronizarCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'reda:kyc-sincronizar {--forzar : Ignora el intervalo mínimo entre consultas de cada solicitud}';

    /**
     * @var string
     */
    protected $description = 'Consulta a Didit el estado de las verificaciones automáticas abiertas (respaldo del webhook)';

    /**
     * Recorre las solicitudes abiertas y las sincroniza con Didit.
     */
    public function handle(KycService $kyc, DiditService $didit): int
    {
        if (!$didit->estaConfigurado()) {
            $this->warn('Didit no está configurado (DIDIT_API_KEY, DIDIT_WORKFLOW_ID, DIDIT_WEBHOOK_SECRET). No se consultó nada.');

            return self::SUCCESS;
        }

        $minutos = (int) config('kyc.didit.minutos_para_consulta_programada', 5);
        $forzar  = (bool) $this->option('forzar');
        $totales = ['consultadas' => 0, 'omitidas' => 0, 'errores' => 0];

        SolicitudKyc::query()
            ->where('metodo', MetodoKyc::Didit->value)
            ->whereIn('estado', [EstadoKyc::EnProceso->value, EstadoKyc::Pendiente->value])
            ->whereNotNull('didit_session_id')
            ->where('iniciada_en', '<=', now()->subMinutes($minutos))
            ->chunkById(50, function ($lote) use ($kyc, $forzar, &$totales) {
                foreach ($lote as $solicitud) {
                    $resultado = $kyc->sincronizarConDidit($solicitud, $forzar);

                    if (!$resultado['success']) {
                        $totales['errores']++;
                    } elseif (in_array($resultado['respuesta'], ['no_aplica', 'intervalo_no_cumplido'], true)) {
                        $totales['omitidas']++;
                    } else {
                        $totales['consultadas']++;
                    }
                }
            });

        $this->info("KYC sincronizado: {$totales['consultadas']} consultadas, {$totales['omitidas']} omitidas, {$totales['errores']} con error.");

        return self::SUCCESS;
    }
}
