{{--
    Resumen: Vista de administración para el listado y gestión de Alertas del Sistema (Plugin REDA).
    Muestra las notificaciones generadas automáticamente por el módulo de mediaciones
    (primer aviso de umbrales alcanzados, suspensiones de cuentas de usuarios por exceso de disputas).
    Permite filtrar por estado (todas, no leídas, leídas), marcar como leída individualmente
    o en bloque, y paginar de 10 en 10 con controles interactivos y animación de espera.
--}}
@extends('admin.template')

@section('main')
<div class="content-wrapper">
	<section class="content-header">
		<h1>{{ __('Alertas del Sistema') }}</h1>
		@include('admin.common.breadcrumb')
	</section>

	<section class="content">
        <div id="index_alertas_admin" class="mt-2 reda-admin-alertas">
            <div class="row justify-content-center">
                <div class="col-xs-12 col-md-10 col-lg-9">
                    {{-- Barra de Herramientas y Filtros --}}
                    <div class="card border rounded-3 shadow-sm bg-white mb-4">
                        <div class="card-body p-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                            {{-- Filtros de estado --}}
                            <div class="btn-group" role="group" aria-label="Filtros de alertas">
                                <button type="button" class="btn btn-outline-primary btn-sm btn-filtro-alerta active" data-estado="todos">
                                    {{ __('Todas') }}
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm btn-filtro-alerta" data-estado="no_leidas">
                                    {{ __('No leídas') }} <span id="badge-conteo-filtro-no-leidas" class="badge bg-danger ms-1 d-none">0</span>
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm btn-filtro-alerta" data-estado="leidas">
                                    {{ __('Leídas') }}
                                </button>
                            </div>

                            {{-- Botón Marcar todas como leídas --}}
                            <div>
                                <button type="button" id="btn-marcar-todas-leidas" class="btn btn-outline-secondary btn-sm d-flex align-items-center">
                                    <i class="fa fa-check-double me-2"></i>
                                    <span>{{ __('Marcar todas como leídas') }}</span>
                                    <i class="fa fa-spinner fa-spin ms-2 d-none spinner-marcar-todas"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Contenedor del Listado de Alertas --}}
                    <div id="alertas-list-container" class="position-relative">
                        <div class="text-center py-5">
                            <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>
                            <p class="text-muted mt-2 f-14">{{ __('Cargando alertas...') }}</p>
                        </div>
                    </div>

                    {{-- Contenedor de Paginación --}}
                    <div id="alertas-pagination-container" class="mt-4 mb-5 d-flex justify-content-center">
                        {{-- Inyectado dinámicamente vía AJAX --}}
                    </div>
                </div>
            </div>
        </div>
	</section>
</div>
@stop

@section('validate_script')
    <script>
        window.RedaAlojamiento = @json(__('reda-alojamiento::messages'));
        window.RedaAlojamientoJson = @json(__('reda-alojamiento::es'));
    </script>
    <script type="text/javascript" src="{{ asset('public/js/reda/admin/vistas/alerta/indexAlertas.min.js') }}?v={{ time() }}"></script>
@endsection
