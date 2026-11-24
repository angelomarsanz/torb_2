<?php

/**
 * Resumen: Crea la tabla usuarios_kyc, que representa el estado efectivo de autorización KYC de
 * cada usuario (una fila por usuario). Es la ÚNICA tabla que consulta el bloqueo de operaciones.
 * Solo KycService escribe en ella, dentro de una transacción con bloqueo de fila. Un usuario sin
 * fila equivale al estado not_started. La columna version permite detectar escrituras concurrentes.
 * Todas las columnas nuevas aceptan valores nulos, excepto el ID principal.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de estado KYC efectivo por usuario.
     */
    public function up(): void
    {
        if (Schema::hasTable('usuarios_kyc')) {
            return;
        }

        Schema::create('usuarios_kyc', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable()->unique();
            $table->string('estado', 20)->nullable()->default('not_started')->index();
            $table->unsignedBigInteger('solicitud_activa_id')->nullable()->index();
            $table->string('metodo_verificacion', 20)->nullable();
            $table->timestamp('verificado_en')->nullable();
            $table->unsignedInteger('verificado_por_admin_id')->nullable()->index();
            $table->timestamp('expira_en')->nullable();
            $table->unsignedInteger('version')->nullable()->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('solicitud_activa_id')->references('id')->on('solicitudes_kyc')->nullOnDelete();
            $table->foreign('verificado_por_admin_id')->references('id')->on('admin')->nullOnDelete();
        });
    }

    /**
     * Elimina la tabla de estado KYC efectivo.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios_kyc');
    }
};
