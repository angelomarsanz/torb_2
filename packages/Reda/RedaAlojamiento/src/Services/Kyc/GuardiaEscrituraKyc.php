<?php

/**
 * Resumen: Guardián de escritura del sistema KYC. Garantiza que los modelos KYC solo puedan crearse,
 * modificarse o eliminarse desde los servicios autorizados (KycService, KycDocumentoService). Los
 * servicios envuelven cada escritura en GuardiaEscrituraKyc::ejecutar(); cualquier intento fuera de
 * esa ventana (un controlador, un observer o una consola) lanza una excepción. Así KycService es el
 * único responsable de cambiar el estado de la verificación.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Services\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Services\Kyc;

use LogicException;

class GuardiaEscrituraKyc
{
    /**
     * Profundidad de anidamiento de las ventanas de escritura autorizadas.
     */
    private static int $profundidad = 0;

    /**
     * Ejecuta un bloque con la escritura de modelos KYC autorizada.
     * La ventana se cierra siempre, incluso si el bloque lanza una excepción.
     *
     * @template T
     * @param  callable(): T $bloque
     * @return T
     */
    public static function ejecutar(callable $bloque): mixed
    {
        self::$profundidad++;

        try {
            return $bloque();
        } finally {
            self::$profundidad--;
        }
    }

    /**
     * Indica si hay una ventana de escritura autorizada abierta.
     */
    public static function activa(): bool
    {
        return self::$profundidad > 0;
    }

    /**
     * Lanza una excepción si se intenta escribir fuera de una ventana autorizada.
     *
     * @throws LogicException
     */
    public static function exigirActiva(string $modelo, string $operacion): void
    {
        if (!self::activa()) {
            throw new LogicException(
                "Escritura no autorizada en {$modelo} ({$operacion}). Use KycService o KycDocumentoService."
            );
        }
    }
}
