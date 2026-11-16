<?php

/** Crea las tablas auxiliares para solicitudes, destinatarios, archivos y correos de mediación. */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /** Crea las tablas sin alterar las tablas originales. */
    public function up(): void
    {
        Schema::create('solicitudes_informacion_mediacion', function (Blueprint $tabla) {
            $tabla->id();
            $tabla->unsignedBigInteger('disputa_id')->nullable()->index();
            $tabla->unsignedInteger('agente_id')->nullable();
            $tabla->text('solicitud')->nullable();
            $tabla->dateTime('vence_en')->nullable()->index();
            $tabla->timestamps();
        });
        Schema::create('destinatarios_informacion_mediacion', function (Blueprint $tabla) {
            $tabla->id();
            $tabla->unsignedBigInteger('solicitud_id')->nullable()->index();
            $tabla->unsignedInteger('usuario_id')->nullable()->index();
            $tabla->unsignedInteger('mensaje_id')->nullable()->index();
            $tabla->timestamps();
            $tabla->unique(['solicitud_id', 'usuario_id'], 'destinatario_solicitud_unico');
        });
        Schema::create('archivos_informacion_mediacion', function (Blueprint $tabla) {
            $tabla->id();
            $tabla->unsignedBigInteger('solicitud_id')->nullable()->index();
            $tabla->unsignedInteger('usuario_id')->nullable()->index();
            $tabla->string('tipo')->nullable();
            $tabla->string('ruta')->nullable();
            $tabla->string('nombre')->nullable();
            $tabla->string('mime')->nullable();
            $tabla->timestamps();
        });
        Schema::create('correos_informacion_mediacion', function (Blueprint $tabla) {
            $tabla->id();
            $tabla->unsignedBigInteger('solicitud_id')->nullable()->index();
            $tabla->unsignedInteger('destinatario_id')->nullable();
            $tabla->unsignedInteger('usuario_origen_id')->nullable();
            $tabla->string('tipo')->nullable();
            $tabla->string('estado')->nullable()->index();
            $tabla->text('detalle')->nullable();
            $tabla->timestamps();
        });
    }

    /** Impide perder solicitudes o referencias de archivos y elimina solo tablas vacías. */
    public function down(): void
    {
        foreach (['solicitudes_informacion_mediacion', 'destinatarios_informacion_mediacion',
            'archivos_informacion_mediacion', 'correos_informacion_mediacion'] as $nombre) {
            if (Schema::hasTable($nombre) && DB::table($nombre)->exists()) {
                throw new \RuntimeException('No se puede revertir la migración: existen datos de información adicional.');
            }
        }
        Schema::dropIfExists('correos_informacion_mediacion');
        Schema::dropIfExists('archivos_informacion_mediacion');
        Schema::dropIfExists('destinatarios_informacion_mediacion');
        Schema::dropIfExists('solicitudes_informacion_mediacion');
    }
};
