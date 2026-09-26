<?php

/**
 * Modelo ReservaHuesped
 * 
 * Propósito: Gestiona los registros de la tabla auxiliar 'reserva_huespedes',
 * que almacena de manera desglosada el número de adultos y niños para cada reservación.
 * 
 * Responsabilidades:
 * - Vincula cada registro con la reservación original de vRent mediante 'reserva_id'.
 * - Ofrece métodos de ayuda para obtener el total combinado de huéspedes.
 */

namespace Reda\RedaAlojamiento\Models\Reserva;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Bookings;

class ReservaHuesped extends Model
{
    use HasFactory;

    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'reserva_huespedes';

    /**
     * Atributos asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'reserva_id',
        'adultos',
        'ninos'
    ];

    /**
     * Relación con la reservación del core.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reserva()
    {
        return $this->belongsTo(Bookings::class, 'reserva_id', 'id');
    }

    /**
     * Obtiene el total de huéspedes sumando adultos y niños.
     *
     * @return int
     */
    public function getTotalHuespedesAttribute()
    {
        return (int) ($this->adultos ?? 0) + (int) ($this->ninos ?? 0);
    }
}
