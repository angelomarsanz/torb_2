<?php

/**
 * Resumen: Excepción que se lanza cuando un usuario sin identidad verificada intenta una operación restringida
 * (publicar alojamientos o negocios, crear ofertas o reservas nuevas). La lanzan los observadores de modelo
 * (BloqueoKycObserver), que son la última barrera: protegen aunque exista una ruta nueva sin clasificar.
 * Al lanzarse dentro de una transacción la revierte, por lo que no queda ningún registro a medias.
 * Se convierte sola en respuesta HTTP (JSON 403 si es AJAX o API; redirección a la sección KYC en caso contrario)
 * mediante render(), sin tocar el manejador de excepciones del proyecto.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Exceptions
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Exceptions;

use Illuminate\Http\Request;
use Reda\RedaAlojamiento\Services\Kyc\RespuestaBloqueoKyc;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class KycNoVerificadoException extends RuntimeException
{
    /**
     * @param string $operacion Operación bloqueada (solo para registros internos).
     */
    public function __construct(public readonly string $operacion = '')
    {
        parent::__construct(RespuestaBloqueoKyc::mensaje());
    }

    /**
     * Respuesta HTTP que se envía al usuario.
     */
    public function render(Request $peticion): Response
    {
        return RespuestaBloqueoKyc::crear($peticion);
    }

    /**
     * Es un bloqueo esperado, no un error del sistema: no se reporta como excepción.
     */
    public function report(): bool
    {
        return false;
    }
}
