<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones para crear la tabla de control de avisos y suspensiones por mediaciones.
     * Registra el historial de primer aviso y suspensión de cada usuario para prevenir duplicidad
     * de correos, mensajes en buzón o sanciones repetitivas.
     * Todas las columnas nuevas aceptan valores nulos, excepto el ID principal.
     */
    public function up(): void
    {
        if (!Schema::hasTable('usuarios_avisos_mediaciones')) {
            Schema::create('usuarios_avisos_mediaciones', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id')->nullable()->index();
                $table->integer('conteo_mediaciones')->nullable()->default(0);
                $table->boolean('primer_aviso_enviado')->nullable()->default(0);
                $table->timestamp('fecha_primer_aviso')->nullable();
                $table->boolean('segundo_aviso_enviado')->nullable()->default(0);
                $table->timestamp('fecha_segundo_aviso')->nullable();
                $table->boolean('cuenta_suspendida')->nullable()->default(0);
                $table->timestamp('fecha_suspension')->nullable();
                $table->text('motivo')->nullable();
                $table->timestamps();

                // Llave foránea hacia la tabla de usuarios del sistema
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios_avisos_mediaciones');
    }
};
