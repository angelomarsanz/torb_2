{{-- Archivo maestro para inyectar recursos al final del <body> del Usuario --}}

{{-- 1. Traducciones de Laravel a JS --}}
<script>
    // Silenciador de errores de terceros (evita bloqueos por scripts originales)
    window.dateRangeBtn = window.dateRangeBtn || null;

    window.AuthCheck = {{ Auth::check() ? 'true' : 'false' }};
    window.RedaAlojamiento = @json(__('reda-alojamiento::messages'));
    window.RedaAlojamientoJson = @json(__('reda-alojamiento::es'));
    // Refuerzo para asegurar que sea un objeto
    if (typeof window.RedaAlojamientoJson !== 'object' || window.RedaAlojamientoJson === null) {
        window.RedaAlojamientoJson = {};
    }
</script>

{{-- 2. Modales de uso general --}}
@include('reda-alojamiento::general.modal_notificaciones')
@include('reda-alojamiento::general.modal_confirmacion')
@include('reda-alojamiento::general.modal_crop')
@include('reda-alojamiento::general.modal_reservar')

{{-- 3. Scripts de uso general del plugin --}}
<script src="{{ asset('public/js/reda/general/reda-general-main.min.js') }}?v={{ time() }}"></script>
<script src="{{ asset('public/js/reda/vistas/pago/frontend/pagos.min.js') }}?v={{ time() }}"></script>

@if(Route::currentRouteName() == 'property.single')
    <script src="{{ asset('public/js/reda/vistas/frontend/propiedad_detalle.min.js') }}?v={{ time() }}"></script>
@endif
