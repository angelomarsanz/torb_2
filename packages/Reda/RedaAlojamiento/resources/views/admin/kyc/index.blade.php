{{--
    Listado administrativo de verificaciones de identidad (KYC).
    Escritorio: tabla. Móvil: tarjetas. Filtros por estado y método, búsqueda y paginación de 10 en 10
    (los controles se muestran siempre, aunque haya una sola página). Todo el contenido se escapa con {{ }}.
--}}
@extends('admin.template')

@section('main')
@php
    use Reda\RedaAlojamiento\Support\EtiquetasKyc;
@endphp
<div class="content-wrapper" id="reda-kyc-index">
    <section class="content-header">
        <h1>{{ __('Verificación de identidad (KYC)') }}</h1>
    </section>
    <section class="content">
        @include('reda-alojamiento::admin.kyc.partials.pestanas')

        <form class="row g-2 align-items-end mb-3 reda-kyc-filtros" method="GET" action="{{ route('reda.admin.kyc.index') }}">
            <input type="hidden" name="grupo" value="{{ $filtros['grupo'] }}">
            <div class="col-12 col-md-5">
                <label class="form-label" for="kyc-buscar">{{ __('Buscar') }}</label>
                <input class="form-control" id="kyc-buscar" name="buscar" maxlength="100" value="{{ $filtros['buscar'] }}"
                       placeholder="{{ __('Nombre, correo electrónico o número de documento') }}">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label" for="kyc-metodo">{{ __('Método de verificación') }}</label>
                <select class="form-select" id="kyc-metodo" name="metodo">
                    <option value="">{{ __('Todos') }}</option>
                    <option value="didit" {{ $filtros['metodo'] === 'didit' ? 'selected' : '' }}>{{ __('Automática (Didit)') }}</option>
                    <option value="manual" {{ $filtros['metodo'] === 'manual' ? 'selected' : '' }}>{{ __('Manual') }}</option>
                </select>
            </div>
            <div class="col-6 col-md-4 d-flex gap-2">
                <button class="btn btn-primary" type="submit">{{ __('Filtrar') }}</button>
                <a class="btn btn-outline-secondary" href="{{ route('reda.admin.kyc.index') }}">{{ __('Limpiar') }}</a>
            </div>
        </form>

        {{-- Escritorio --}}
        <div class="card d-none d-lg-block">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 reda-kyc-tabla">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Usuario') }}</th>
                            <th>{{ __('Documento') }}</th>
                            <th>{{ __('Método') }}</th>
                            <th>{{ __('Estado') }}</th>
                            <th>{{ __('Solicitud') }}</th>
                            <th>{{ __('Actualización') }}</th>
                            <th>{{ __('Administrador') }}</th>
                            <th class="text-end">{{ __('Acciones') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($solicitudes as $s)
                            <tr>
                                <td>{{ $s->id }}</td>
                                <td>
                                    <strong>{{ $s->usuario_nombre }} {{ $s->usuario_apellido }}</strong><br>
                                    <small class="text-muted">{{ $s->usuario_correo }}</small>
                                </td>
                                <td>
                                    @if ($s->numero_documento_enmascarado)
                                        {{ $s->numero_documento_enmascarado }}<br>
                                        <small class="text-muted">{{ EtiquetasKyc::tipoDocumento($s->tipo_documento) }}</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $s->metodo?->etiqueta() }}</td>
                                <td><span class="badge {{ EtiquetasKyc::claseEstado($s->estado) }}">{{ $s->estado?->etiqueta() }}</span>
                                    @if ($s->tieneCorreccionPendiente())<br><small class="text-warning">{{ __('Corrección solicitada') }}</small>
                                    @elseif ($s->correccion_atendida_en && $s->estado?->value === 'manual_review')<br><small class="text-success">{{ __('Corrección recibida: revisar') }}</small>@endif
                                </td>
                                <td>{{ ($s->enviada_en ?? $s->iniciada_en ?? $s->created_at)?->format('d/m/Y H:i') }}</td>
                                <td>{{ $s->updated_at?->format('d/m/Y H:i') }}</td>
                                <td>{{ $s->admin_nombre ?: '—' }}</td>
                                <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('reda.admin.kyc.detalle', $s->id) }}">{{ __('Ver detalle') }}</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center text-muted py-4">{{ __('No hay solicitudes con estos filtros.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Móvil y tableta: tarjetas --}}
        <div class="d-lg-none reda-kyc-tarjetas">
            @forelse ($solicitudes as $s)
                <a class="card reda-kyc-tarjeta mb-2 text-decoration-none" href="{{ route('reda.admin.kyc.detalle', $s->id) }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div>
                                <strong class="text-dark">{{ $s->usuario_nombre }} {{ $s->usuario_apellido }}</strong><br>
                                <small class="text-muted">{{ $s->usuario_correo }}</small>
                            </div>
                            <span class="badge {{ EtiquetasKyc::claseEstado($s->estado) }}">{{ $s->estado?->etiqueta() }}</span>
                        </div>
                        <dl class="row small mb-0 mt-2 text-dark">
                            <dt class="col-5">{{ __('Solicitud') }}</dt><dd class="col-7">#{{ $s->id }} · {{ $s->metodo?->etiqueta() }}</dd>
                            <dt class="col-5">{{ __('Documento') }}</dt><dd class="col-7">{{ $s->numero_documento_enmascarado ?: '—' }}</dd>
                            <dt class="col-5">{{ __('Enviada') }}</dt><dd class="col-7">{{ ($s->enviada_en ?? $s->iniciada_en ?? $s->created_at)?->format('d/m/Y H:i') }}</dd>
                            <dt class="col-5">{{ __('Administrador') }}</dt><dd class="col-7">{{ $s->admin_nombre ?: '—' }}</dd>
                        </dl>
                        @if ($s->tieneCorreccionPendiente())<small class="text-warning">{{ __('Corrección solicitada') }}</small>
                        @elseif ($s->correccion_atendida_en && $s->estado?->value === 'manual_review')<small class="text-success">{{ __('Corrección recibida: revisar') }}</small>@endif
                    </div>
                </a>
            @empty
                <p class="text-center text-muted py-4">{{ __('No hay solicitudes con estos filtros.') }}</p>
            @endforelse
        </div>

        {{ $solicitudes->links('reda-alojamiento::admin.general.paginacion') }}
    </section>
</div>
@endsection

@push('scripts')
<script src="{{ asset('public/js/reda/admin/vistas/kyc/indexKyc.min.js') }}?v={{ time() }}"></script>
@endpush
