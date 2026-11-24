<?php

/**
 * Resumen: Última barrera del bloqueo KYC, a nivel de MODELO. Impide crear propiedades, reservas reales, negocios
 * y ofertas, y activar publicaciones o convertir una consulta en reserva, cuando el usuario autenticado no está
 * verificado. Funciona aunque aparezca una ruta, un endpoint AJAX o un módulo nuevo sin clasificar, porque
 * intercepta la escritura en el modelo (si falla dentro de una transacción, esta se revierte).
 *
 *  - Properties: creating, y updating cuando el estado cambia a Listed (activar).
 *  - Experiencia y ActividadExperiencia: creating (crear negocio u oferta).
 *  - Bookings: creating solo si la reserva es REAL. El chat crea una "consulta" (Bookings con estado "Inquiry" o,
 *    en versiones anteriores, vacío) y no se bloquea; updating cuando una consulta pasa a reserva real
 *    (Pending, Accepted, Processing...).
 *    Las transiciones de reservas ya existentes (aceptar, pagar, cancelar) no se tocan.
 * Se registra junto a los observadores existentes del plugin; no modifica los modelos del núcleo.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Observers
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Observers;

use App\Models\Bookings;
use App\Models\Properties;
use Illuminate\Database\Eloquent\Model;
use Reda\RedaAlojamiento\Models\Experiencia\ActividadExperiencia;
use Reda\RedaAlojamiento\Models\Experiencia\Experiencia;
use Reda\RedaAlojamiento\Services\Kyc\BloqueoKyc;

class BloqueoKycObserver
{
    public function __construct(private readonly BloqueoKyc $bloqueo)
    {
    }

    /**
     * Antes de crear un registro restringido.
     */
    public function creating(Model $modelo): void
    {
        if ($modelo instanceof Bookings) {
            if (!$this->esConsulta($modelo->status)) {
                $this->bloqueo->exigir('crear una reserva', true);
            }

            return;
        }

        if ($modelo instanceof Properties) {
            $this->bloqueo->exigir('crear un alojamiento');
        } elseif ($modelo instanceof Experiencia) {
            $this->bloqueo->exigir('crear un negocio');
        } elseif ($modelo instanceof ActividadExperiencia) {
            $this->bloqueo->exigir('crear un producto o servicio');
        }
    }

    /**
     * Antes de actualizar un registro restringido.
     */
    public function updating(Model $modelo): void
    {
        if ($modelo instanceof Properties) {
            if ($modelo->isDirty('status') && $modelo->status === 'Listed' && $modelo->getOriginal('status') !== 'Listed') {
                $this->bloqueo->exigir('activar un alojamiento');
            }

            return;
        }

        if ($modelo instanceof Bookings
            && $modelo->isDirty('status')
            && $this->esConsulta($modelo->getOriginal('status'))
            && !$this->esConsulta($modelo->status)) {
            $this->bloqueo->exigir('convertir una consulta en reserva', true);
        }
    }

    /**
     * Indica si el estado corresponde a una consulta del chat (no es una reserva): "Inquiry" o vacío (versión anterior).
     */
    private function esConsulta(mixed $estado): bool
    {
        $texto = strtolower(trim((string) $estado));

        return $texto === '' || $texto === 'inquiry';
    }
}
