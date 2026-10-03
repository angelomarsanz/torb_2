{{-- 
    Archivo maestro para inyectar recursos al final del <body> del Administrador.
    Carga modales globales, scripts del plugin y expone datos de traducción y rol de sesión.
--}}

@php
    $adminActual = Auth::guard('admin')->user();
    $roleIdActual = null;
    $roleNombreActual = null;
    $tieneAccesoMediaciones = false;

    if ($adminActual) {
        $rolData = \DB::table('role_admin')
            ->leftJoin('roles', 'role_admin.role_id', '=', 'roles.id')
            ->where('role_admin.admin_id', $adminActual->id)
            ->select('roles.id as role_id', 'roles.name as role_name', 'roles.display_name')
            ->first();

        if ($rolData) {
            $roleIdActual = (int) $rolData->role_id;
            $roleNombreActual = $rolData->role_name;
            $roleDisplayNameActual = $rolData->display_name;
        }

        // Tienen acceso a la gestión de Mediaciones los roles 1 (Admin) y 2 (Atención al usuario)
        $roleNombreNorm = strtolower(trim($roleNombreActual ?? ''));
        $roleDisplayNorm = strtolower(trim($roleDisplayNameActual ?? ''));
        $tieneAccesoMediaciones = in_array($roleIdActual, [1, 2])
            || in_array($roleNombreNorm, ['admin', 'atención al usuario', 'atencion al usuario'])
            || in_array($roleDisplayNorm, ['admin', 'atención al usuario', 'atencion al usuario']);
    }
@endphp

{{-- 1. Traducciones y datos de sesión de Laravel a JS --}}
<script>
    window.RedaAlojamiento = @json(__('reda-alojamiento::messages'));
    window.RedaAlojamientoJson = @json(__('reda-alojamiento::es'));
    window.RedaAdminUser = {
        id: {{ $adminActual ? $adminActual->id : 'null' }},
        roleId: {{ $roleIdActual !== null ? $roleIdActual : 'null' }},
        roleName: {!! json_encode($roleNombreActual) !!},
        tieneAccesoMediaciones: {{ $tieneAccesoMediaciones ? 'true' : 'false' }},
        esAdminTotal: {{ ($roleIdActual === 1 || in_array($roleNombreNorm, ['admin'])) ? 'true' : 'false' }}
    };
</script>

{{-- 2. Modales de uso general --}}
@include('reda-alojamiento::admin.general.modal_notificaciones')
@include('reda-alojamiento::admin.general.modal_confirmacion')

{{-- 3. Scripts de uso general del plugin --}}
<script type="text/javascript" src="{{  asset('public/js/reda/admin/general/reda-admin-general-main.min.js') }}?v={{ time() }}"></script>
<script type="text/javascript" src="{{  asset('public/js/reda/vistas/frontend/desgloseHuespedes.min.js') }}?v={{ time() }}"></script>
