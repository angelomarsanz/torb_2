<?php

/**
 * Resumen: Crea la tabla eventos_kyc, el registro de auditoría de todo lo que ocurre en el KYC:
 * webhooks y consultas de Didit, acciones del usuario, decisiones administrativas y accesos a
 * documentos. clave_idempotencia (UNIQUE) evita procesar dos veces el mismo evento de Didit.
 * Es un registro inmutable: no se actualiza ni se elimina. El detalle se guarda saneado (sin
 * números de documento completos, imágenes ni datos biométricos).
 * Todas las columnas nuevas aceptan valores nulos, excepto el ID principal.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de eventos de auditoría KYC.
     */
    public function up(): void
    {
        if (Schema::hasTable('eventos_kyc')) {
            return;
        }

        Schema::create('eventos_kyc', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('solicitud_id')->nullable()->index();
            $table->string('origen', 30)->nullable();
            $table->string('tipo', 60)->nullable();
            $table->string('clave_idempotencia', 191)->nullable()->unique();
            $table->string('didit_event_id', 100)->nullable()->index();
            $table->string('didit_session_id', 64)->nullable()->index();
            $table->string('didit_estado', 40)->nullable();
            $table->string('estado_anterior', 20)->nullable();
            $table->string('estado_nuevo', 20)->nullable();
            $table->string('resultado', 40)->nullable();
            $table->json('detalle')->nullable();
            $table->string('payload_hash', 64)->nullable();
            $table->unsignedInteger('admin_id')->nullable()->index();
            $table->string('ip', 45)->nullable();
            $table->timestamps();

            $table->foreign('solicitud_id')->references('id')->on('solicitudes_kyc')->nullOnDelete();
        });
    }

    /**
     * Elimina la tabla de eventos KYC.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos_kyc');
    }
};
