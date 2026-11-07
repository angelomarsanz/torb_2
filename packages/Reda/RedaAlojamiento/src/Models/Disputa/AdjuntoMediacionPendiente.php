<?php

/** Conserva los adjuntos cuya eliminación física está pendiente después del reinicio. */

namespace Reda\RedaAlojamiento\Models\Disputa;

use Illuminate\Database\Eloquent\Model;

class AdjuntoMediacionPendiente extends Model
{
    protected $table = 'adjuntos_mediacion_pendientes';
    protected $fillable = ['ruta', 'hash'];
}
