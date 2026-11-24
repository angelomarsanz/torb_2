<?php

/**
 * Resumen: Crea la tabla documentos_kyc con el historial de archivos de identidad enviados en la
 * verificación manual. Los archivos NO se guardan aquí: solo sus metadatos y la ruta dentro del disco
 * privado (kyc_privado). Cuando un documento se reemplaza, el anterior pasa a estado "reemplazado"
 * y se conserva el historial. user_id se repite a propósito para validar la propiedad (anti-IDOR).
 * Todas las columnas nuevas aceptan valores nulos, excepto el ID principal.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de documentos KYC.
     */
    public function up(): void
    {
        if (Schema::hasTable('documentos_kyc')) {
            return;
        }

        Schema::create('documentos_kyc', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('solicitud_id')->nullable()->index();
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->string('tipo', 20)->nullable();
            $table->string('disco', 40)->nullable();
            $table->string('ruta', 255)->nullable();
            $table->string('nombre_original', 255)->nullable();
            $table->string('mime', 100)->nullable();
            $table->unsignedBigInteger('tamano_bytes')->nullable();
            $table->string('sha256', 64)->nullable();
            $table->boolean('cifrado')->nullable()->default(1);
            $table->string('estado', 20)->nullable()->default('vigente');
            $table->unsignedBigInteger('reemplazado_por_id')->nullable();
            $table->timestamp('eliminado_en')->nullable();
            $table->timestamps();

            $table->index(['solicitud_id', 'tipo', 'estado']);

            $table->foreign('solicitud_id')->references('id')->on('solicitudes_kyc')->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
        });
    }

    /**
     * Elimina la tabla de documentos KYC.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos_kyc');
    }
};
