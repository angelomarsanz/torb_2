<?php

/**
 * Resumen: Evento de Laravel que KycService emite DESPUÉS de confirmar la transacción cuando cambia el
 * estado KYC de un usuario. Es el punto de extensión para las notificaciones (correo y avisos internos)
 * sin acoplarlas al servicio. Solo transporta identificadores y estados, nunca datos personales.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Events
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Events;

use Reda\RedaAlojamiento\Enums\EstadoKyc;
use Reda\RedaAlojamiento\Enums\MetodoKyc;

class EstadoKycCambiado
{
    /**
     * @param int          $usuarioId   Usuario al que pertenece la verificación.
     * @param int|null     $solicitudId Solicitud que originó el cambio.
     * @param EstadoKyc    $anterior    Estado efectivo previo.
     * @param EstadoKyc    $nuevo       Estado efectivo nuevo.
     * @param MetodoKyc|null $metodo    Método de la solicitud.
     * @param string       $origen      Origen del cambio (didit_webhook, didit_consulta, usuario, admin, sistema).
     */
    public function __construct(
        public readonly int $usuarioId,
        public readonly ?int $solicitudId,
        public readonly EstadoKyc $anterior,
        public readonly EstadoKyc $nuevo,
        public readonly ?MetodoKyc $metodo,
        public readonly string $origen,
    ) {
    }
}
