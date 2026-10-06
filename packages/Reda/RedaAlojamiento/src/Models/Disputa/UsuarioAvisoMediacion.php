<?php

/**
 * Resumen: Modelo Eloquent para la tabla auxiliar usuarios_avisos_mediaciones del plugin REDA.
 * Mantiene el registro de estado, fechas de primer aviso preventivo y suspensión de cuentas
 * de usuarios por exceso de mediaciones acumuladas, previniendo duplicidades operativas.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Models\Disputa
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Models\Disputa;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UsuarioAvisoMediacion extends Model
{
    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'usuarios_avisos_mediaciones';

    /**
     * Atributos asignables de forma masiva.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'conteo_mediaciones',
        'primer_aviso_enviado',
        'fecha_primer_aviso',
        'segundo_aviso_enviado',
        'fecha_segundo_aviso',
        'cuenta_suspendida',
        'fecha_suspension',
        'motivo',
    ];

    /**
     * Conversión de atributos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'primer_aviso_enviado'  => 'boolean',
        'segundo_aviso_enviado' => 'boolean',
        'cuenta_suspendida'     => 'boolean',
        'fecha_primer_aviso'    => 'datetime',
        'fecha_segundo_aviso'   => 'datetime',
        'fecha_suspension'      => 'datetime',
    ];

    /**
     * Relación con el usuario del sistema.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
