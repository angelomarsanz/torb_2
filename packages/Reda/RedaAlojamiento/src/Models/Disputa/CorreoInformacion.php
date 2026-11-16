<?php

/** Registra los correos de información adicional pendientes o enviados. */
namespace Reda\RedaAlojamiento\Models\Disputa;

use Illuminate\Database\Eloquent\Model;

class CorreoInformacion extends Model
{
    protected $table = 'correos_informacion_mediacion';
    protected $guarded = [];
}
