{{-- 
    Archivo maestro para inyectar recursos al final del <body> del Administrador.
    Carga modales globales, scripts del plugin y expone datos de traducción, rol y disponibilidad del reinicio.
--}}

@php
    $adminActual = Auth::guard('admin')->user();
    $roleIdActual = null;
    $roleNombreActual = null;
    $tieneAccesoMediaciones = false;
    $tieneAccesoKyc = false;
    $kycPendientes = 0;

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

        // KYC: el menú solo se ofrece a quien tiene manage_kyc (las rutas lo vuelven a exigir en el servidor)
        try {
            $tieneAccesoKyc = \Illuminate\Support\Facades\Gate::forUser($adminActual)->allows('gestionar', \Reda\RedaAlojamiento\Models\Kyc\SolicitudKyc::class);
            if ($tieneAccesoKyc && \Illuminate\Support\Facades\Schema::hasTable('solicitudes_kyc')) {
                $kycPendientes = \Reda\RedaAlojamiento\Models\Kyc\SolicitudKyc::whereIn('estado', ['manual_review', 'pending'])->count();
            }
        } catch (\Throwable $e) {
            $tieneAccesoKyc = false;
            \Illuminate\Support\Facades\Log::warning('KYC: no se pudo calcular el acceso al menú KYC: ' . $e->getMessage());
        }
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
        tieneAccesoKyc: {{ $tieneAccesoKyc ? 'true' : 'false' }},
        kycPendientes: {{ (int) $kycPendientes }},
        esAdminTotal: {{ ($roleIdActual === 1 || in_array($roleNombreNorm, ['admin'])) ? 'true' : 'false' }},
        reinicioMediacionesHabilitado: {{ (config('reda-alojamiento.reinicio_mediaciones_habilitado') === true && app()->environment(['local', 'development', 'dev']) && $adminActual && \DB::table('role_admin')->where('admin_id', $adminActual->id)->where('role_id', 1)->exists()) ? 'true' : 'false' }},
        rutaReinicioMediaciones: @json(route('reda.admin.pruebas.mediaciones'))
    };
</script>

{{-- 2. Modales de uso general --}}
@include('reda-alojamiento::admin.general.modal_notificaciones')
@include('reda-alojamiento::admin.general.modal_confirmacion')

{{-- 3. Scripts de uso general del plugin --}}
<script type="text/javascript" src="{{  asset('public/js/reda/admin/general/reda-admin-general-main.min.js') }}?v={{ time() }}"></script>
<script type="text/javascript" src="{{  asset('public/js/reda/vistas/frontend/desgloseHuespedes.min.js') }}?v={{ time() }}"></script>
