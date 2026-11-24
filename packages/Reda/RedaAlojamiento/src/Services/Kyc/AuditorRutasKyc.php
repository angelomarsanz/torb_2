<?php

/**
 * Resumen: Auditor de cobertura del bloqueo KYC. Recorre todas las rutas registradas de los controladores vigilados
 * (kyc.controladores_vigilados) y verifica que cada una esté CLASIFICADA:
 *  - RESTRINGIDA: figura en kyc.operaciones_restringidas y realmente tiene el middleware kyc.verificado.
 *  - PERMITIDA: figura en kyc.rutas_permitidas con su justificación.
 *  - SIN CLASIFICAR: no figura en ninguna lista (una ruta nueva olvidada). Es un fallo.
 * También detecta rutas restringidas a las que no llegó el middleware (fallo) y entradas de los manifiestos que ya
 * no corresponden a ninguna ruta (aviso). Su resultado es la matriz de cobertura que permite comprobar que ninguna
 * operación restringida puede ejecutarse sin KYC aprobado. Lo usa el comando reda:kyc-auditar-rutas y las pruebas.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Services\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Services\Kyc;

use Illuminate\Routing\Router;

class AuditorRutasKyc
{
    public const RESTRINGIDA     = 'RESTRINGIDA';
    public const PERMITIDA       = 'PERMITIDA';
    public const SIN_CLASIFICAR  = 'SIN CLASIFICAR';

    /**
     * Genera la matriz de cobertura.
     *
     * @return array{filas:array<int,array<string,mixed>>,sin_clasificar:int,sin_proteccion:int,manifiesto_sin_ruta:array<int,string>}
     */
    public function auditar(Router $router): array
    {
        $vigilados   = array_map(static fn ($c) => ltrim((string) $c, '\\'), (array) config('kyc.controladores_vigilados', []));
        $restringidas = (array) config('kyc.operaciones_restringidas', []);
        $permitidas   = (array) config('kyc.rutas_permitidas', []);

        $filas = [];
        $accionesVistas = [];

        foreach ($router->getRoutes()->getRoutes() as $ruta) {
            $accion = AplicadorMiddlewareKyc::accionDe($ruta);

            if ($accion === null || AplicadorMiddlewareKyc::esRutaAdministrativa($ruta)) {
                continue;
            }

            [$controlador, $metodo] = explode('@', $accion, 2);

            if (!in_array(ltrim($controlador, '\\'), $vigilados, true)) {
                continue;
            }

            $accionesVistas[$accion] = true;
            $middleware = array_map(static fn ($m) => is_string($m) ? $m : 'closure', $ruta->middleware());
            $protegida  = (bool) array_filter($middleware, static fn ($m) => $m === AplicadorMiddlewareKyc::ALIAS || str_starts_with($m, AplicadorMiddlewareKyc::ALIAS . ':'));

            if (array_key_exists($accion, $restringidas)) {
                $clasificacion = self::RESTRINGIDA;
                $detalle = $restringidas[$accion] ?: 'siempre';
            } elseif (array_key_exists($accion, $permitidas)) {
                $clasificacion = self::PERMITIDA;
                $detalle = $permitidas[$accion];
            } else {
                $clasificacion = self::SIN_CLASIFICAR;
                $detalle = 'Agregue la acción a kyc.operaciones_restringidas o a kyc.rutas_permitidas.';
            }

            $filas[] = [
                'metodos'        => implode('|', array_diff($ruta->methods(), ['HEAD'])),
                'uri'            => $ruta->uri(),
                'accion'         => $accion,
                'clasificacion'  => $clasificacion,
                'protegida'      => $protegida,
                'detalle'        => $detalle,
                'metodo_existe'  => class_exists($controlador) ? method_exists($controlador, $metodo) : null,
                'falla'          => $clasificacion === self::SIN_CLASIFICAR || ($clasificacion === self::RESTRINGIDA && !$protegida),
            ];
        }

        usort($filas, static fn ($a, $b) => [$a['accion'], $a['uri']] <=> [$b['accion'], $b['uri']]);

        $huerfanas = [];
        $alternativas = array_flip((array) config('kyc.acciones_alternativas', []));
        foreach (array_keys($restringidas + $permitidas) as $accion) {
            if (!isset($accionesVistas[$accion]) && !isset($alternativas[$accion])) {
                $huerfanas[] = $accion;
            }
        }

        return [
            'filas'               => $filas,
            'sin_clasificar'      => count(array_filter($filas, static fn ($f) => $f['clasificacion'] === self::SIN_CLASIFICAR)),
            'sin_proteccion'      => count(array_filter($filas, static fn ($f) => $f['clasificacion'] === self::RESTRINGIDA && !$f['protegida'])),
            'manifiesto_sin_ruta' => $huerfanas,
        ];
    }
}
