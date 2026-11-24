<?php

/**
 * Resumen: Crea el permiso administrativo manage_kyc y lo asigna únicamente a los roles administradores.
 * Es idempotente y no destructiva: no usa truncate, no modifica PermissionsTableSeeder y puede ejecutarse
 * varias veces. No asume que el rol con id 1 es el administrador: asigna el permiso solo al rol llamado
 * "admin" que ya posee el permiso manage_admin (exclusivo del administrador autorizado). Si no encuentra
 * un rol que cumpla ambas condiciones, no asigna nada y registra una advertencia; el permiso se podrá
 * asignar entonces desde Roles en el panel. Limpia la caché de permisos que usa el sistema.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const PERMISO = 'manage_kyc';

    /**
     * Crea el permiso y lo asigna a los roles administradores verificados.
     */
    public function up(): void
    {
        if (!Schema::hasTable('permissions') || !Schema::hasTable('permission_role') || !Schema::hasTable('roles')) {
            return;
        }

        $permisoId = DB::table('permissions')->where('name', self::PERMISO)->value('id');

        if (!$permisoId) {
            $permisoId = DB::table('permissions')->insertGetId([
                'name'         => self::PERMISO,
                'display_name' => 'Manage KYC',
                'description'  => 'Manage identity verification (KYC) requests and documents',
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        $permisoAdminId = DB::table('permissions')->where('name', 'manage_admin')->value('id');

        $rolesAdministradores = [];
        if ($permisoAdminId) {
            $rolesAdministradores = DB::table('roles')
                ->join('permission_role', 'permission_role.role_id', '=', 'roles.id')
                ->whereRaw('LOWER(roles.name) = ?', ['admin'])
                ->where('permission_role.permission_id', $permisoAdminId)
                ->pluck('roles.id')
                ->all();
        }

        if (empty($rolesAdministradores)) {
            Log::warning('KYC: no se asignó manage_kyc a ningún rol porque no se encontró un rol "admin" con el permiso manage_admin. Asígnelo desde Roles en el panel.');
        }

        foreach ($rolesAdministradores as $rolId) {
            DB::table('permission_role')->insertOrIgnore([
                'permission_id' => $permisoId,
                'role_id'       => $rolId,
            ]);
        }

        $this->limpiarCachePermisos();
    }

    /**
     * Retira el permiso manage_kyc y sus asignaciones.
     */
    public function down(): void
    {
        if (!Schema::hasTable('permissions') || !Schema::hasTable('permission_role')) {
            return;
        }

        $permisoId = DB::table('permissions')->where('name', self::PERMISO)->value('id');

        if ($permisoId) {
            DB::table('permission_role')->where('permission_id', $permisoId)->delete();
            DB::table('permissions')->where('id', $permisoId)->delete();
        }

        $this->limpiarCachePermisos();
    }

    /**
     * Borra la caché permanente que usa Permissions::getAll().
     */
    private function limpiarCachePermisos(): void
    {
        try {
            Cache::forget(config('cache.prefix') . '.permissions');
        } catch (\Throwable $e) {
            Log::warning('KYC: no se pudo limpiar la caché de permisos: ' . $e->getMessage());
        }
    }
};
