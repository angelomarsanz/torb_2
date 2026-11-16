<?php

/** Registra archivos privados de solicitudes y documentos entregados por participantes. */
namespace Reda\RedaAlojamiento\Models\Disputa;

use Illuminate\Database\Eloquent\Model;

class ArchivoInformacion extends Model
{
    protected $table = 'archivos_informacion_mediacion';
    protected $guarded = [];
}
