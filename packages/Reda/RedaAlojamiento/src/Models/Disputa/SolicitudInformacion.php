<?php

/** Representa una solicitud de información adicional y su plazo vigente. */
namespace Reda\RedaAlojamiento\Models\Disputa;

use Illuminate\Database\Eloquent\Model;

class SolicitudInformacion extends Model
{
    protected $table = 'solicitudes_informacion_mediacion';
    protected $guarded = [];
    protected $casts = ['vence_en' => 'datetime'];
}
