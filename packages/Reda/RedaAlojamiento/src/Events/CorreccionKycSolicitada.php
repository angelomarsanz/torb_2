<?php

/**
 * Resumen: Evento de Laravel que KycService emite DESPUÉS de confirmar la transacción cuando un administrador
 * solicita una corrección en una verificación manual. El estado KYC no cambia (sigue manual_review), por lo que no
 * se usa EstadoKycCambiado. Es el punto de enganche de la notificación al usuario ("se solicitó una corrección").
 * Solo transporta identificadores; el texto de la corrección se lee de la solicitud al notificar.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Events
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Events;

class CorreccionKycSolicitada
{
    /**
     * @param int $usuarioId   Usuario al que se le pide la corrección.
     * @param int $solicitudId Solicitud manual en revisión.
     * @param int $adminId     Administrador que la solicitó.
     */
    public function __construct(
        public readonly int $usuarioId,
        public readonly int $solicitudId,
        public readonly int $adminId,
    ) {
    }
}
