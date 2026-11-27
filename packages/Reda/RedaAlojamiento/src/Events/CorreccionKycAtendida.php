<?php

/**
 * Resumen: Evento que se emite cuando el usuario responde una corrección que un administrador pidió en su
 * solicitud de verificación manual (reenvía datos y/o documentos). La solicitud sigue en revisión manual, por lo
 * que no cambia de estado y EstadoKycCambiado no se emite; este evento permite avisar a los administradores
 * de que la solicitud espera una nueva revisión. KycService lo emite solo cuando la transacción se confirma.
 * Solo transporta identificadores: quien lo escucha consulta los datos que necesite.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Events
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Events;

class CorreccionKycAtendida
{
    /**
     * @param int $usuarioId   Usuario que respondió la corrección.
     * @param int $solicitudId Solicitud manual que vuelve a quedar pendiente de revisión.
     */
    public function __construct(
        public readonly int $usuarioId,
        public readonly int $solicitudId,
    ) {
    }
}
