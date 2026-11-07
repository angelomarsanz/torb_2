<?php

/**
 * Crea registros para identificar mensajes de mediación y recuperar la limpieza de adjuntos.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /** Crea las tablas auxiliares del reinicio. */
    public function up(): void
    {
        Schema::create('mensajes_de_mediacion', function (Blueprint $tabla) {
            $tabla->id();
            $tabla->unsignedInteger('mensaje_id')->nullable()->unique();
            $tabla->timestamps();
        });
        Schema::create('adjuntos_mediacion_pendientes', function (Blueprint $tabla) {
            $tabla->id();
            $tabla->text('ruta')->nullable();
            $tabla->string('hash', 64)->nullable();
            $tabla->timestamps();
        });
    }

    /** Evita perder la cola o los marcadores al revertir la migración. */
    public function down(): void
    {
        if (DB::table('adjuntos_mediacion_pendientes')->exists() || DB::table('mensajes_de_mediacion')->exists()) {
            throw new \RuntimeException('No se puede revertir la migración mientras existan adjuntos pendientes o mensajes identificados.');
        }
        Schema::dropIfExists('adjuntos_mediacion_pendientes');
        Schema::dropIfExists('mensajes_de_mediacion');
    }
};
