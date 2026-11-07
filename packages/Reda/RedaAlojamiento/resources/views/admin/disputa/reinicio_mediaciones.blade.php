{{--
    Herramienta de pruebas para reiniciar mediaciones con confirmación y selección de cuentas.
    La autorización, la validación y la eliminación se ejecutan en el servidor.
--}}
@extends('admin.template')

@section('main')
<div class="content-wrapper" id="reinicio-mediaciones-admin">
    <section class="content-header">
        <h1>{{ __('Reiniciar mediaciones') }}</h1>
        @include('admin.common.breadcrumb')
    </section>
    <section class="content">
        <div class="card box box-solid">
            <div class="card-body box-body">
                <div class="alert alert-danger">
                    {{ __('Esta operación es destructiva e irreversible. Haga un respaldo actualizado y realícela únicamente durante una ventana sin usuarios ni procesos de mediación o chat.') }}
                </div>
                <p>{{ __('Se eliminarán mediaciones, avisos, alertas y mensajes identificados con certeza. Se conservarán la configuración, las reservas y los mensajes históricos de origen ambiguo.') }}</p>
                <p>{{ __('Los correos ya enviados no pueden retirarse. Si existe una tabla histórica de respaldo, sus registros y los adjuntos se conservarán por precaución.') }}</p>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modal-reinicio-mediaciones">{{ __('Reiniciar mediaciones') }}</button>
                <div id="resultado-reinicio" class="mt-3" role="status" aria-live="polite"></div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modal-reinicio-mediaciones" tabindex="-1" aria-labelledby="titulo-reinicio-mediaciones" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form id="form-reinicio-mediaciones" action="{{ route('reda.admin.pruebas.mediaciones.reiniciar') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h2 class="modal-title fs-5" id="titulo-reinicio-mediaciones">{{ __('Confirmar reinicio de mediaciones') }}</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Cerrar') }}"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-danger fw-bold">{{ __('Esta acción borrará información y no se puede deshacer. Asegúrese de tener un respaldo y de que no haya actividad de mediaciones o chat.') }}</p>
                        <p>{{ __('Seleccione únicamente las cuentas que desea reactivar. Un aviso por mediaciones no demuestra que esa haya sido la única causa de su inactividad; verifique cada caso.') }}</p>
                        @forelse ($usuarios as $usuario)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="usuarios[]" value="{{ $usuario->id }}" id="reactivar-usuario-{{ $usuario->id }}">
                                <label class="form-check-label" for="reactivar-usuario-{{ $usuario->id }}">{{ $usuario->first_name }} {{ $usuario->last_name }} ({{ $usuario->email }}) — ID {{ $usuario->id }}</label>
                            </div>
                        @empty
                            <p>{{ __('No hay cuentas inactivas elegibles para reactivar.') }}</p>
                        @endforelse
                        <label class="form-label mt-3" for="confirmacion-reinicio">{{ __('Escriba exactamente: Estoy seguro de reiniciar las mediaciones') }}</label>
                        <input class="form-control" id="confirmacion-reinicio" name="confirmacion" type="text" autocomplete="off" required>
                        <div id="estado-reinicio" class="mt-3" role="status" aria-live="polite"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">{{ __('Cancelar') }}</button>
                        <button type="submit" class="btn btn-danger" id="confirmar-reinicio">{{ __('Confirmar reinicio') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
