{{-- 
    Resumen: Vista de administración para la configuración de mediaciones permitidas.
    Permite a los administradores con Rol 1 definir los umbrales máximos de mediaciones:
    cantidad para primer aviso y cantidad para segundo aviso y suspensión de cuenta.
    Los valores se almacenan en la tabla settings del sistema con type "Mediaciones".
--}}
@extends('admin.template')

@section('main')
<div class="content-wrapper">
	<section class="content-header">
		<h1>{{ __('Configuración de mediaciones') }}</h1>
		@include('admin.common.breadcrumb')
	</section>

	<section class="content">
        <div id="configuracion_mediaciones_admin" class="mt-2 reda-admin-disputas-config">
            <div class="row">
                <div class="col-xs-12 col-md-8 col-lg-6">
                    <div class="card border rounded-3 shadow-sm bg-white box box-solid">
                        <div class="card-header box-header with-border border-bottom bg-transparent py-3">
                            <h3 class="card-title box-title mb-0 fw-bold text-dark">
                                {{ __('Cantidad de mediaciones permitidas') }}
                            </h3>
                        </div>
                        <div class="card-body box-body p-4">
                            <form id="form-configuracion-mediaciones" method="POST" action="{{ route('reda.admin.disputas.configuracion.store') }}">
                                @csrf
                                
                                {{-- Input 1: Cantidad de mediaciones para primer aviso --}}
                                <div class="form-group mb-4">
                                    <label for="input-primer-aviso" class="form-label fw-semibold text-dark">
                                        {{ __('Cantidad de mediaciones para primer aviso') }} <span class="text-danger">*</span>
                                    </label>
                                    <input 
                                        type="number" 
                                        class="form-control f-14" 
                                        id="input-primer-aviso" 
                                        name="primer_aviso" 
                                        min="0" 
                                        step="1"
                                        value="{{ $primerAviso }}" 
                                        placeholder="0"
                                        required
                                    >
                                    <small class="form-text text-muted">
                                        {{ __('Número de mediaciones acumuladas por el usuario para disparar el primer aviso preventivo.') }}
                                    </small>
                                </div>

                                {{-- Input 2: Cantidad de mediaciones para segundo aviso y suspensión de cuenta --}}
                                <div class="form-group mb-4">
                                    <label for="input-segundo-aviso" class="form-label fw-semibold text-dark">
                                        {{ __('Cantidad de mediaciones para segundo aviso y suspensión de cuenta') }} <span class="text-danger">*</span>
                                    </label>
                                    <input 
                                        type="number" 
                                        class="form-control f-14" 
                                        id="input-segundo-aviso" 
                                        name="segundo_aviso" 
                                        min="0" 
                                        step="1"
                                        value="{{ $segundoAviso }}" 
                                        placeholder="0"
                                        required
                                    >
                                    <small class="form-text text-muted">
                                        {{ __('Número de mediaciones acumuladas que activará el segundo aviso y la suspensión de la cuenta.') }}
                                    </small>
                                </div>

                                {{-- Botón de Guardar --}}
                                <div class="d-flex justify-content-end align-items-center mt-4">
                                    <button type="submit" id="btn-guardar-config-mediaciones" class="btn btn-primary btn-flat px-4 py-2">
                                        <span class="btn-text">{{ __('Guardar') }}</span>
                                        <i class="fa fa-spinner fa-spin ms-2 d-none spinner-guardar"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
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
    <script type="text/javascript" src="{{ asset('public/js/reda/admin/vistas/disputa/configuracionDisputas.min.js') }}?v={{ time() }}"></script>
@endsection
