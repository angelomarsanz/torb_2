<?php

/**
 * Resumen: Aplica el middleware "kyc.verificado" a todas las rutas registradas cuya ACCIÓN
 * ("Controlador@método" final, después de los overrides del plugin) figure en kyc.operaciones_restringidas.
 * Al trabajar sobre las rutas ya registradas protege por igual las URL duplicadas por módulos, las renombradas y las
 * sobrescritas, sin editar routes/web.php ni los controladores. Se ejecuta en el callback "booted" del
 * ServiceProvider, DESPUÉS de las sobrescrituras de rutas del plugin. Es idempotente (no duplica el middleware).
 * Las rutas del panel administrativo no se tocan.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Services\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Services\Kyc;

use Illuminate\Routing\Route;
use Illuminate\Routing\Router;

class AplicadorMiddlewareKyc
{
    public const ALIAS = 'kyc.verificado';

    /**
     * Añade el middleware a las rutas que corresponden a operaciones restringidas.
     *
     * @return int Cantidad de rutas protegidas.
     */
    public function aplicar(Router $router): int
    {
        $mapa = (array) config('kyc.operaciones_restringidas', []);
        $protegidas = 0;

        foreach ($router->getRoutes()->getRoutes() as $ruta) {
            $accion = self::accionDe($ruta);

            if ($accion === null || !array_key_exists($accion, $mapa) || self::esRutaAdministrativa($ruta)) {
                continue;
            }

            $middleware = self::middlewareDe($mapa[$accion]);

            if (!in_array($middleware, $ruta->middleware(), true)) {
                $ruta->middleware($middleware);
            }

            $protegidas++;
        }

        return $protegidas;
    }

    /**
     * Middleware con su parámetro: "kyc.verificado" o "kyc.verificado:etiqueta".
     */
    public static function middlewareDe(?string $etiqueta): string
    {
        return $etiqueta ? self::ALIAS . ':' . $etiqueta : self::ALIAS;
    }

    /**
     * Acción "Controlador@método" de una ruta, o null si es una función anónima.
     */
    public static function accionDe(Route $ruta): ?string
    {
        $accion = $ruta->getActionName();

        return $accion !== 'Closure' && str_contains($accion, '@') ? $accion : null;
    }

    /**
     * Las rutas bajo /admin tienen su propio control de permisos y no pasan por el bloqueo de usuarios.
     */
    public static function esRutaAdministrativa(Route $ruta): bool
    {
        return $ruta->uri() === 'admin' || str_starts_with($ruta->uri(), 'admin/');
    }
}
