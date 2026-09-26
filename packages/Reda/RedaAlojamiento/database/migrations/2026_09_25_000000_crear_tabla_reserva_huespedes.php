<?php

/**
 * Migración: Crear tabla reserva_huespedes
 * 
 * Propósito: Crea la tabla auxiliar 'reserva_huespedes' para almacenar de forma independiente
 * y normalizada el desglose de huéspedes (Adultos y Niños) de cada reservación en el plugin REDA Alojamiento.
 * 
 * Cumplimiento de directrices REDA:
 * - Nombres de tabla y columnas en español.
 * - Todas las columnas nuevas aceptan valores nulos excepto el id.
 * - Vinculada a la tabla bookings mediante 'reserva_id'.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones.
     */
    public function up(): void
    {
        Schema::create('reserva_huespedes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reserva_id')->nullable();
            $table->integer('adultos')->nullable()->default(1);
            $table->integer('ninos')->nullable()->default(0);
            $table->timestamps();

            $table->index('reserva_id');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('reserva_huespedes');
    }
};
