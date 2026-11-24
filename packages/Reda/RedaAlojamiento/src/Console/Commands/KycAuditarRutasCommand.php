<?php

/**
 * Resumen: Comando "reda:kyc-auditar-rutas". Imprime la matriz de cobertura del bloqueo KYC (AuditorRutasKyc):
 * para cada ruta de los controladores vigilados indica si es RESTRINGIDA (y si el middleware está aplicado),
 * PERMITIDA (con su justificación) o SIN CLASIFICAR. Termina con código de error si hay rutas sin clasificar o
 * restringidas sin middleware, por lo que sirve como comprobación previa al despliegue. Opciones:
 * --formato=tabla|json y --solo-problemas.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Console\Commands
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Routing\Router;
use Reda\RedaAlojamiento\Services\Kyc\AuditorRutasKyc;

class KycAuditarRutasCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'reda:kyc-auditar-rutas {--formato=tabla : tabla o json} {--solo-problemas : Muestra únicamente las rutas con fallo}';

    /**
     * @var string
     */
    protected $description = 'Audita que todas las rutas de alojamientos, negocios y reservas estén clasificadas y protegidas por el KYC';

    /**
     * Ejecuta la auditoría y muestra la matriz.
     */
    public function handle(AuditorRutasKyc $auditor, Router $router): int
    {
        $r = $auditor->auditar($router);
        $filas = $this->option('solo-problemas') ? array_values(array_filter($r['filas'], static fn ($f) => $f['falla'])) : $r['filas'];

        if ($this->option('formato') === 'json') {
            $this->line(json_encode(['resumen' => [
                'rutas' => count($r['filas']), 'sin_clasificar' => $r['sin_clasificar'], 'sin_proteccion' => $r['sin_proteccion'],
                'manifiesto_sin_ruta' => $r['manifiesto_sin_ruta'],
            ], 'filas' => $filas], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        } else {
            $this->table(
                ['Método', 'URI', 'Acción', 'Clasificación', 'Protegida', 'Detalle'],
                array_map(static fn ($f) => [
                    $f['metodos'], $f['uri'], preg_replace('#^.*\\\\#', '', $f['accion']),
                    $f['clasificacion'] . ($f['falla'] ? '  <<< FALLA' : ''),
                    $f['clasificacion'] === AuditorRutasKyc::RESTRINGIDA ? ($f['protegida'] ? 'sí' : 'NO') : '-',
                    mb_strimwidth((string) $f['detalle'], 0, 60, '…'),
                ], $filas)
            );

            $this->info(count($r['filas']) . ' rutas vigiladas: ' . count(array_filter($r['filas'], static fn ($f) => $f['clasificacion'] === AuditorRutasKyc::RESTRINGIDA)) . ' restringidas, '
                . count(array_filter($r['filas'], static fn ($f) => $f['clasificacion'] === AuditorRutasKyc::PERMITIDA)) . ' permitidas.');

            foreach ($r['manifiesto_sin_ruta'] as $accion) {
                $this->warn('Aviso: la acción ' . $accion . ' figura en la configuración pero ninguna ruta la usa.');
            }
        }

        $falla = $r['sin_clasificar'] > 0 || $r['sin_proteccion'] > 0;

        // En formato json solo se imprime el JSON (apto para procesarlo); el código de salida indica el resultado.
        if ($this->option('formato') === 'json') {
            return $falla ? self::FAILURE : self::SUCCESS;
        }

        if ($falla) {
            $this->error("AUDITORÍA FALLIDA: {$r['sin_clasificar']} sin clasificar y {$r['sin_proteccion']} restringidas sin middleware.");

            return self::FAILURE;
        }

        $this->info('Auditoría correcta: todas las rutas vigiladas están clasificadas y las restringidas tienen el middleware.');

        return self::SUCCESS;
    }
}
