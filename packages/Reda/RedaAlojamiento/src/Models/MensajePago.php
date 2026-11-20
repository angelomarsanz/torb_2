<?php

/** Vincula un mensaje completo de pago con su reserva y sus comprobantes originales. */
namespace Reda\RedaAlojamiento\Models;

use Illuminate\Database\Eloquent\Model;

class MensajePago extends Model
{
    protected $table = 'mensajes_pagos';
    protected $fillable = ['reserva_id', 'mensaje_id', 'texto', 'comprobantes'];
    protected $casts = ['comprobantes' => 'array'];
}
