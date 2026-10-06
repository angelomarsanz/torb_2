<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones para crear la tabla de alertas administrativas del plugin REDA.
     * Almacena las alertas y notificaciones destinadas a los usuarios administradores
     * sobre límites de mediaciones, suspensiones de cuentas y eventos críticos del sistema.
     * Todas las columnas nuevas aceptan valores nulos, excepto el ID principal.
     */
    public function up(): void
    {
        if (!Schema::hasTable('alertas_admin')) {
            Schema::create('alertas_admin', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('admin_id')->nullable()->index();
                $table->string('titulo', 255)->nullable();
                $table->text('mensaje')->nullable();
                $table->string('tipo', 100)->nullable()->index(); // 'primer_aviso', 'suspension', 'general'
                $table->unsignedBigInteger('disputa_id')->nullable()->index();
                $table->unsignedInteger('user_id')->nullable()->index();
                $table->boolean('leido')->nullable()->default(0);
                $table->timestamp('fecha_lectura')->nullable();
                $table->timestamps();

                // Llaves foráneas no restrictivas
                $table->foreign('admin_id')->references('id')->on('admin')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('alertas_admin');
    }
};
