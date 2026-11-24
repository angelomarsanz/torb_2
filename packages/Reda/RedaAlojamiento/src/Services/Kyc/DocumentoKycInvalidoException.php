<?php

/**
 * Resumen: Excepción lanzada por KycDocumentoService cuando un archivo de identidad no es válido
 * (formato, tipo MIME falso, tamaño, contenido peligroso o ilegible) o no está disponible para su lectura.
 * Lleva el campo del formulario afectado y un mensaje seguro y traducido para el usuario.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Services\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Services\Kyc;

use RuntimeException;

class DocumentoKycInvalidoException extends RuntimeException
{
    /**
     * @param string $campo   Campo del formulario (por ejemplo documento_frente).
     * @param string $mensaje Mensaje seguro, traducido, apto para mostrar al usuario.
     */
    public function __construct(public readonly string $campo, string $mensaje)
    {
        parent::__construct($mensaje);
    }
}
