<?php

/**
 * Resumen: Método con el que se verifica la identidad de un usuario en una solicitud KYC.
 * "didit" es la verificación automática mediante la API de Didit y "manual" es el envío
 * de datos y documentos que revisa un administrador autorizado.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Enums
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Enums;

enum MetodoKyc: string
{
    case Didit  = 'didit';
    case Manual = 'manual';

    /**
     * Texto legible para el usuario, traducido con el archivo JSON del plugin.
     */
    public function etiqueta(): string
    {
        return match ($this) {
            self::Didit  => __('Verificación automática (Didit)'),
            self::Manual => __('Verificación manual'),
        };
    }
}
