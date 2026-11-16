<?php

/** Relaciona cada solicitud con sus destinatarios y el mensaje privado enviado. */
namespace Reda\RedaAlojamiento\Models\Disputa;

use Illuminate\Database\Eloquent\Model;

class DestinatarioInformacion extends Model
{
    protected $table = 'destinatarios_informacion_mediacion';
    protected $guarded = [];
}
