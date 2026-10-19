<?php

/**
 * Resumen: Asigna el permiso de gestión de mensajes al rol 2 de atención al usuario.
 * Permite que dichos administradores usen la opción "Mensajes" del submenú
 * "Mensajes y Alertas" y accedan a las rutas nativas protegidas por manage_messages.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Agrega manage_messages al rol 2 si existen el rol y el permiso.
     */
    public function up(): void
    {
        $permisoMensajesId = DB::table('permissions')
            ->where('name', 'manage_messages')
            ->value('id');

        if (!$permisoMensajesId || !DB::table('roles')->where('id', 2)->exists()) {
            return;
        }

        DB::table('permission_role')->insertOrIgnore([
            'permission_id' => $permisoMensajesId,
            'role_id' => 2,
        ]);
    }

    /**
     * Revierte la asignación de manage_messages al rol 2.
     */
    public function down(): void
    {
        $permisoMensajesId = DB::table('permissions')
            ->where('name', 'manage_messages')
            ->value('id');

        if ($permisoMensajesId) {
            DB::table('permission_role')
                ->where('permission_id', $permisoMensajesId)
                ->where('role_id', 2)
                ->delete();
        }
    }
};
