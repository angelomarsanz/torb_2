{{-- Editor de opciones independientes para reserva y pago; los mensajes se envían en texto plano. --}}
@extends('admin.template')

@section('main')
<div class="content-wrapper" id="plantillas-textos">
    <section class="content-header">
        <h1>{{ __('Plantillas de textos') }}</h1>
    </section>
    <section class="content">
        @if (session('estado_plantilla'))
            <div class="alert alert-success" role="status">{{ session('estado_plantilla') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        <div class="reda-plantillas-disposicion">
            <aside class="card reda-plantillas-lista">
                <div class="card-body">
                    <h2 class="h5">{{ __('Mensaje al anfitrión') }}</h2>
                    <div class="list-group mb-3">
                        <a class="list-group-item list-group-item-action {{ $contexto === \Reda\RedaAlojamiento\Models\PlantillaTexto::CONTEXTO_RESERVA ? 'active' : '' }}" href="{{ route('reda.admin.plantillas.textos', ['contexto' => \Reda\RedaAlojamiento\Models\PlantillaTexto::CONTEXTO_RESERVA]) }}">{{ __('Reserva inicial') }}</a>
                        <a class="list-group-item list-group-item-action {{ $contexto === \Reda\RedaAlojamiento\Models\PlantillaTexto::CONTEXTO_PAGO ? 'active' : '' }}" href="{{ route('reda.admin.plantillas.textos', ['contexto' => \Reda\RedaAlojamiento\Models\PlantillaTexto::CONTEXTO_PAGO]) }}">{{ __('Confirmación de pago') }}</a>
                    </div>
                    <h3 class="h6">{{ $contexto === \Reda\RedaAlojamiento\Models\PlantillaTexto::CONTEXTO_RESERVA ? __('Reserva inicial') : __('Confirmación de pago') }}</h3>
                    <a class="btn btn-outline-primary btn-sm mb-3" href="{{ route('reda.admin.plantillas.textos', ['contexto' => $contexto]) }}">{{ __('Nueva opción') }}</a>
                    <div class="list-group">
                        @forelse ($plantillas as $plantilla)
                            <a class="list-group-item list-group-item-action {{ $seleccionada && $seleccionada->id === $plantilla->id ? 'active' : '' }}"
                               href="{{ route('reda.admin.plantillas.textos', ['contexto' => $contexto, 'editar' => $plantilla->id, 'page' => $plantillas->currentPage()]) }}">
                                {{ $plantilla->nombre }} <small>({{ $plantilla->orden }})</small>
                            </a>
                        @empty
                            <p>{{ __('Todavía no hay opciones de texto.') }}</p>
                        @endforelse
                    </div>
                    {{ $plantillas->links('reda-alojamiento::admin.general.paginacion') }}
                </div>
            </aside>
            <main class="card reda-plantillas-editor">
                <div class="card-body">
                    <h2 class="h5">{{ $seleccionada ? __('Editar opción') : __('Nueva opción') }}</h2>
                    <p class="text-muted">{{ __('El huésped recibirá texto plano; los estilos no se enviarán.') }}</p>
                    <form id="form-plantilla-texto" action="{{ route('reda.admin.plantillas.guardar') }}" method="POST">
                        @csrf
                        <input type="hidden" name="contexto" value="{{ $contexto }}">
                        <input type="hidden" name="id" value="{{ old('id', $seleccionada->id ?? '') }}">
                        <label class="form-label" for="nombre-plantilla">{{ __('Nombre de la opción') }}</label>
                        <input class="form-control mb-3" id="nombre-plantilla" name="nombre" maxlength="191" required value="{{ old('nombre', $seleccionada->nombre ?? '') }}">
                        <label class="form-label" for="orden-plantilla">{{ __('Orden') }}</label>
                        <input class="form-control mb-3" id="orden-plantilla" name="orden" type="number" min="0" max="999999" required value="{{ old('orden', $seleccionada->orden ?? 0) }}">
                        <p class="text-muted small">{{ __('La opción con el menor orden aparece primero. Si hay empate, se usa el orden de creación.') }}</p>
                        <label class="form-label" for="contenido-plantilla">{{ __('Texto') }}</label>
                        <textarea class="form-control" id="contenido-plantilla" name="contenido" rows="12">{{ old('contenido', $seleccionada->contenido ?? '') }}</textarea>
                        <button class="btn btn-primary mt-3" type="submit">{{ __('Guardar opción') }}</button>
                    </form>
                    @if ($seleccionada)
                        <form class="mt-2 reda-eliminar-plantilla" action="{{ route('reda.admin.plantillas.eliminar', $seleccionada->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <input type="hidden" name="contexto" value="{{ $contexto }}">
                            <button class="btn btn-outline-danger" type="submit">{{ __('Eliminar opción') }}</button>
                        </form>
                    @endif
                </div>
            </main>
            <aside class="card reda-plantillas-variables">
                <div class="card-body">
                    <h2 class="h5">{{ __('Variables disponibles') }}</h2>
                    <p class="text-muted small">{{ __('Copie las variables en el texto; se sustituirán con los datos de la reserva.') }}</p>
                    <ul class="list-unstyled mb-0">
                        <li><code>{anfitrion}</code> — {{ __('Nombre del anfitrión') }}</li>
                        <li><code>{huesped}</code> — {{ __('Nombre del huésped') }}</li>
                        <li><code>{alojamiento}</code> — {{ __('Alojamiento') }}</li>
                        <li><code>{llegada}</code> — {{ __('Fecha de llegada') }}</li>
                        <li><code>{salida}</code> — {{ __('Fecha de salida') }}</li>
                        <li><code>{adultos}</code> — {{ __('Adultos') }}</li>
                        <li><code>{ninos}</code> — {{ __('Niños') }}</li>
                        <li><code>{total_personas}</code> — {{ __('Total de personas') }}</li>
                        <li><code>{pais}</code> — {{ __('País') }}</li>
                        <li><code>{metodo_pago}</code> — {{ __('Método de pago') }}</li>
                    </ul>
                    <p class="text-muted small mt-3">{{ __('El país y el método de pago se muestran como no seleccionados cuando no están disponibles.') }}</p>
                </div>
            </aside>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="{{ asset('public/backend/plugins/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('public/js/reda/admin/vistas/plantillas/textos.min.js') }}?v={{ time() }}"></script>
@endpush
