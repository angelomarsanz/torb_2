<?php

/** Identifica los mensajes creados expresamente desde las mediaciones. */

namespace Reda\RedaAlojamiento\Models\Disputa;

use Illuminate\Database\Eloquent\Model;

class MensajeDeMediacion extends Model
{
    protected $table = 'mensajes_de_mediacion';
    protected $fillable = ['mensaje_id'];
}
