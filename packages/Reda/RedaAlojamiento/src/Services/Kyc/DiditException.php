<?php

/**
 * Resumen: Excepción de la integración con Didit. Lleva un mensaje seguro (sin credenciales ni datos
 * personales), el código HTTP recibido de Didit (si lo hubo) y si el error es reintentable
 * (fallo de red, 429 o 5xx) para que el webhook responda con un código que provoque un reintento.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Services\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Services\Kyc;

use RuntimeException;

class DiditException extends RuntimeException
{
    /**
     * @param string $mensaje      Mensaje seguro para registros.
     * @param int    $codigoHttp   Código HTTP de Didit (0 si no hubo respuesta).
     * @param bool   $reintentable Indica si repetir la operación más tarde puede funcionar.
     */
    public function __construct(
        string $mensaje,
        public readonly int $codigoHttp = 0,
        public readonly bool $reintentable = false,
    ) {
        parent::__construct($mensaje);
    }
}
