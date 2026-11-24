<?php

/**
 * Resumen: Crea la tabla solicitudes_kyc, que guarda un registro por cada intento de verificación
 * de identidad (automático con Didit o manual). Es el historial de intentos: nunca se borra una
 * solicitud al iniciar otra. Incluye datos de Didit, datos personales (los sensibles se almacenan
 * cifrados), la decisión administrativa y el control de retención de documentos.
 * La columna clave_abierta es user_id mientras la solicitud está abierta y NULL cuando se cierra;
 * su índice UNIQUE impide dos solicitudes abiertas del mismo usuario (compatible con MySQL/MariaDB).
 * Todas las columnas nuevas aceptan valores nulos, excepto el ID principal.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de solicitudes KYC.
     */
    public function up(): void
    {
        if (Schema::hasTable('solicitudes_kyc')) {
            return;
        }

        Schema::create('solicitudes_kyc', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->string('metodo', 20)->nullable();
            $table->string('estado', 20)->nullable();
            $table->unsignedInteger('numero_intento')->nullable();
            $table->unsignedInteger('clave_abierta')->nullable()->unique();

            // Datos de Didit (sin credenciales ni imágenes)
            $table->string('didit_session_id', 64)->nullable()->unique();
            $table->string('didit_vendor_data', 100)->nullable();
            $table->string('didit_estado_original', 40)->nullable();
            $table->json('didit_resultado')->nullable();
            $table->unsignedBigInteger('didit_ultimo_timestamp')->nullable();
            $table->timestamp('didit_ultima_consulta_en')->nullable();

            // Datos personales (nombres y apellidos tal como figuran en el documento)
            $table->string('nombres', 150)->nullable();
            $table->string('apellidos', 150)->nullable();
            $table->string('tipo_documento', 40)->nullable();
            $table->text('numero_documento')->nullable();
            $table->string('numero_documento_hash', 64)->nullable()->index();
            $table->string('numero_documento_ultimos4', 4)->nullable();
            $table->string('pais_expedicion', 2)->nullable();
            $table->text('fecha_nacimiento')->nullable();
            $table->string('pais_residencia', 2)->nullable();
            $table->string('ciudad_residencia', 120)->nullable();
            $table->text('direccion_residencia')->nullable();
            $table->string('correo_contacto', 190)->nullable();

            // Fechas del proceso
            $table->timestamp('iniciada_en')->nullable();
            $table->timestamp('enviada_en')->nullable();
            $table->timestamp('aprobada_en')->nullable();
            $table->timestamp('rechazada_en')->nullable();
            $table->timestamp('cerrada_en')->nullable();

            // Decisión administrativa
            $table->unsignedInteger('revisado_por_admin_id')->nullable()->index();
            $table->text('motivo_rechazo')->nullable();
            $table->text('mensaje_usuario')->nullable();
            $table->text('observaciones_internas')->nullable();
            $table->text('correccion_solicitada')->nullable();
            $table->timestamp('correccion_solicitada_en')->nullable();
            $table->timestamp('correccion_atendida_en')->nullable();

            // Retención de documentos (independiente de la aprobación)
            $table->timestamp('eliminar_documentos_en')->nullable()->index();
            $table->timestamp('documentos_eliminados_en')->nullable();
            $table->boolean('retencion_suspendida')->nullable()->default(0);

            $table->timestamps();

            $table->index(['user_id', 'estado']);

            // Los usuarios se eliminan de forma lógica; el historial KYC no debe borrarse en cascada
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('revisado_por_admin_id')->references('id')->on('admin')->nullOnDelete();
        });
    }

    /**
     * Elimina la tabla de solicitudes KYC.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes_kyc');
    }
};
