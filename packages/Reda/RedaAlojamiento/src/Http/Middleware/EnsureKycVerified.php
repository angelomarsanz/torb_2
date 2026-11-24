<?php

/**
 * Resumen: Middleware "kyc.verificado" (EnsureKycVerified). Bloquea en el servidor las rutas que ofrecen o
 * contratan algo (publicar alojamientos y negocios, crear ofertas, reservar) cuando el usuario autenticado no
 * tiene la identidad verificada. No depende del frontend ni de JavaScript.
 * Se añade automáticamente a las rutas listadas en kyc.operaciones_restringidas (por ACCIÓN, no por URL) y recibe
 * como parámetro la etiqueta de la regla (ver ClasificadorOperacionKyc), por ejemplo kyc.verificado:reserva_nueva.
 * Si el usuario está verificado la petición continúa; si la ruta es una reserva nueva se abre además la ventana de
 * reserva. Sin usuario autenticado, en consola o en el panel administrativo no interviene. Si bloquea, responde
 * con RespuestaBloqueoKyc (JSON 403 para AJAX/API o redirección a la sección KYC).
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Http\Middleware
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Reda\RedaAlojamiento\Services\Kyc\BloqueoKyc;
use Reda\RedaAlojamiento\Services\Kyc\ClasificadorOperacionKyc;
use Reda\RedaAlojamiento\Services\Kyc\RespuestaBloqueoKyc;
use Symfony\Component\HttpFoundation\Response;

class EnsureKycVerified
{
    public function __construct(
        private readonly BloqueoKyc $bloqueo,
        private readonly ClasificadorOperacionKyc $clasificador,
    ) {
    }

    /**
     * @param string|null $etiqueta Regla dinámica de la ruta (null = siempre restringida).
     */
    public function handle(Request $peticion, Closure $siguiente, ?string $etiqueta = null): Response
    {
        $usuarioId = $this->bloqueo->usuarioSujeto();

        if ($usuarioId === null) {
            return $siguiente($peticion);
        }

        $restringida = $this->clasificador->esRestringida($etiqueta, $peticion, $usuarioId);

        if ($this->bloqueo->estaAutorizado($usuarioId)) {
            if ($restringida && $etiqueta === 'reserva_nueva') {
                $this->bloqueo->abrirVentanaReserva($usuarioId);
            }

            return $siguiente($peticion);
        }

        if (!$restringida) {
            return $siguiente($peticion);
        }

        Log::info('KYC bloqueo: el usuario ' . $usuarioId . ' fue detenido en ' . $peticion->method() . ' /' . $peticion->path() . ' (' . ($etiqueta ?: 'siempre') . ').');

        return RespuestaBloqueoKyc::crear($peticion);
    }
}
