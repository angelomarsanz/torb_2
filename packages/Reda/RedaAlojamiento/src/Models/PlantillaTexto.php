<?php

/** Modelo y contextos de las opciones editables de texto predefinido del plugin. */
namespace Reda\RedaAlojamiento\Models;

use Illuminate\Database\Eloquent\Model;

class PlantillaTexto extends Model
{
    public const CONTEXTO_PAGO = 'mensaje_anfitrion';
    public const CONTEXTO_RESERVA = 'mensaje_reserva';

    protected $table = 'plantillas_textos';
    protected $fillable = ['contexto', 'nombre', 'contenido', 'orden'];
}
