<?php

/**
 * Resumen: Modelo Eloquent para la tabla auxiliar alertas_admin del plugin REDA.
 * Gestiona el almacenamiento, consulta y estado de lectura de las alertas y notificaciones
 * emitidas hacia los usuarios administradores (primeros avisos por mediaciones,
 * suspensiones de cuentas de usuarios y eventos administrativos críticos).
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Models\Alerta
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Models\Alerta;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;
use App\Models\User;
use Reda\RedaAlojamiento\Models\Disputa\Disputa;

class AlertaAdmin extends Model
{
    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'alertas_admin';

    /**
     * Atributos asignables de forma masiva.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id',
        'titulo',
        'mensaje',
        'tipo',
        'disputa_id',
        'user_id',
        'leido',
        'fecha_lectura',
    ];

    /**
     * Conversión de atributos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'leido' => 'boolean',
        'fecha_lectura' => 'datetime',
    ];

    /**
     * Relación con el administrador destinatario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    /**
     * Relación con el usuario (turista o anfitrión) involucrado en la alerta.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con la mediación o disputa de origen.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function disputa()
    {
        return $this->belongsTo(Disputa::class, 'disputa_id');
    }
}
