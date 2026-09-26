{{-- Archivo maestro para inyectar recursos al final del <body> del Usuario --}}

{{-- 1. Traducciones y Variables de Sesión de Laravel a JS --}}
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

    // Datos de huéspedes en sesión para la página de pago
    window.RedaSessionHuespedes = {
        adultos: {{ (int) (Session::get('payment_adultos') ?? (Session::get('reda_payment_data.adultos') ?? (Session::get('payment_number_of_guests') ?? 1))) }},
        ninos: {{ (int) (Session::get('payment_ninos') ?? (Session::get('reda_payment_data.ninos') ?? 0)) }}
    };
</script>

{{-- 2. Modales de uso general --}}
@include('reda-alojamiento::general.modal_notificaciones')
@include('reda-alojamiento::general.modal_confirmacion')
@include('reda-alojamiento::general.modal_crop')
@include('reda-alojamiento::general.modal_reservar')
@include('reda-alojamiento::general.modal_verificacion_correo')

{{-- 3. Scripts de uso general del plugin --}}
<script src="{{ asset('public/js/reda/general/reda-general-main.min.js') }}?v={{ time() }}"></script>
<script src="{{ asset('public/js/reda/vistas/pago/frontend/pagos.min.js') }}?v={{ time() }}"></script>
<script src="{{ asset('public/js/reda/vistas/frontend/verDetalleReservaModal.min.js') }}?v={{ time() }}"></script>
<script src="{{ asset('public/js/reda/vistas/frontend/busquedaPropiedades.min.js') }}?v={{ time() }}"></script>
<script src="{{ asset('public/js/reda/vistas/frontend/desgloseHuespedes.min.js') }}?v={{ time() }}"></script>
<script src="{{ asset('public/js/reda/vistas/frontend/verificacionCorreo.min.js') }}?v={{ time() }}"></script>

@if(Route::currentRouteName() == 'property.single')
    <script src="{{ asset('public/js/reda/vistas/frontend/propiedad_detalle.min.js') }}?v={{ time() }}"></script>
@endif
