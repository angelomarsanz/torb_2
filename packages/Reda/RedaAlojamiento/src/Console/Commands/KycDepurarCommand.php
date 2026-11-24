<?php

/**
 * Resumen: Comando "reda:kyc-depurar". Aplica la política de retención de documentos de identidad
 * (RetencionKycService): elimina los archivos de solicitudes cerradas cuyo plazo venció, salvo que exista una
 * mediación activa, una cuenta restringida o una retención suspendida. No cambia el estado de verificación y
 * conserva la auditoría. La opción --simular muestra qué se eliminaría sin modificar nada.
 * Se programa a diario desde RedaAlojamientoServiceProvider (requiere "schedule:run" en el cron).
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Console\Commands
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Console\Commands;

use Illuminate\Console\Command;
use Reda\RedaAlojamiento\Services\Kyc\RetencionKycService;

class KycDepurarCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'reda:kyc-depurar {--simular : Muestra lo que se eliminaría sin modificar archivos ni base de datos}';

    /**
     * @var string
     */
    protected $description = 'Elimina los documentos de identidad del KYC cuyo plazo de retención venció';

    /**
     * Ejecuta la depuración y muestra el resumen.
     */
    public function handle(RetencionKycService $retencion): int
    {
        $simular = (bool) $this->option('simular');
        $r = $retencion->depurar($simular);

        if (!$r['habilitada']) {
            $this->warn('La eliminación automática está deshabilitada (kyc.retencion.automatica_habilitada). No se hizo nada.');

            return self::SUCCESS;
        }

        $prefijo = $simular ? 'SIMULACIÓN. ' : '';
        $this->info("{$prefijo}KYC retención: {$r['evaluadas']} solicitudes evaluadas, {$r['eliminadas']} con documentos "
            . ($simular ? 'a eliminar' : 'eliminados') . " ({$r['documentos']} archivos), {$r['bloqueadas']} protegidas por procesos pendientes, "
            . "{$r['programadas']} con fecha reprogramada, {$r['errores']} con error.");

        return $r['errores'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}
