<?php

/** Modelo de las opciones editables de texto predefinido del plugin. */
namespace Reda\RedaAlojamiento\Models;

use Illuminate\Database\Eloquent\Model;

class PlantillaTexto extends Model
{
    protected $table = 'plantillas_textos';
    protected $fillable = ['contexto', 'nombre', 'contenido', 'orden'];
}
