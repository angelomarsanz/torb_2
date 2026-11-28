<?php

/**
 * Resumen: Evento que se emite cuando un administrador elimina la verificación de identidad de un usuario
 * (acción "Eliminar verificación" del panel KYC). KycService lo emite solo cuando la transacción se confirma.
 * Solo transporta identificadores: el motivo que escribió el administrador no viaja en el evento, porque no debe
 * llegar a quien lo escucha (por ejemplo, al correo que se le envía al usuario).
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Events
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Events;

class VerificacionKycEliminada
{
    /**
     * @param int $usuarioId Usuario cuya verificación se eliminó.
     * @param int $adminId   Administrador que la eliminó.
     */
    public function __construct(
        public readonly int $usuarioId,
        public readonly int $adminId,
    ) {
    }
}
