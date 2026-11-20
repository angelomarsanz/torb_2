<?php

/**
 * Crea almacenamiento auxiliar para plantillas y mensajes de pago sin alterar tablas originales.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /** Crea las tablas auxiliares del plugin. */
    public function up(): void
    {
        Schema::create('plantillas_textos', function (Blueprint $tabla) {
            $tabla->id();
            $tabla->string('contexto')->nullable()->index();
            $tabla->string('nombre')->nullable();
            $tabla->longText('contenido')->nullable();
            $tabla->unsignedInteger('orden')->nullable();
            $tabla->timestamps();
        });

        Schema::create('mensajes_pagos', function (Blueprint $tabla) {
            $tabla->id();
            $tabla->unsignedBigInteger('reserva_id')->nullable()->index();
            $tabla->unsignedBigInteger('mensaje_id')->nullable()->unique();
            $tabla->longText('texto')->nullable();
            $tabla->longText('comprobantes')->nullable();
            $tabla->timestamps();
        });
    }

    /** Solo revierte tablas vacías para no perder mensajes ni plantillas existentes. */
    public function down(): void
    {
        foreach (['mensajes_pagos', 'plantillas_textos'] as $tabla) {
            if (Schema::hasTable($tabla) && DB::table($tabla)->exists()) {
                throw new RuntimeException('No se puede revertir la migración: hay datos en ' . $tabla . '.');
            }
        }
        Schema::dropIfExists('mensajes_pagos');
        Schema::dropIfExists('plantillas_textos');
    }
};
