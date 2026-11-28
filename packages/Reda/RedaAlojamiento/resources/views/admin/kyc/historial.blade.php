{{--
    Historial de auditoría KYC de un usuario: todos los eventos que se conservan (envíos, decisiones, consultas de
    administradores y eliminaciones de la verificación), del más reciente al más antiguo, de 10 en 10. Los eventos no
    contienen datos personales y tras una eliminación ya no se vinculan a ninguna solicitud. Escritorio: tabla.
    Móvil: tarjetas. Todo el contenido se escapa con {{ }}.
--}}
@extends('admin.template')

@section('main')
@php
    use Reda\RedaAlojamiento\Enums\EstadoKyc;
    use Reda\RedaAlojamiento\Support\EtiquetasKyc;
    $estadoEtiqueta = static fn (?string $v) => $v && EstadoKyc::tryFrom($v) ? EstadoKyc::tryFrom($v)->etiqueta() : '—';
    $transicion = static fn ($e) => ($e->estado_anterior || $e->estado_nuevo) ? $estadoEtiqueta($e->estado_anterior) . ' → ' . $estadoEtiqueta($e->estado_nuevo) : '';
@endphp
<div class="content-wrapper" id="reda-kyc-historial">
    <section class="content-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h1 class="mb-0">{{ __('Historial de verificación') }}</h1>
        <a class="btn btn-outline-secondary" href="{{ route('reda.admin.kyc.eliminadas') }}">{{ __('Volver a eliminadas') }}</a>
    </section>
    <section class="content">
        @include('reda-alojamiento::admin.kyc.partials.pestanas')

        <div class="card mb-3">
            <div class="card-body">
                <strong>{{ $usuario->first_name }} {{ $usuario->last_name }}</strong>
                @if ($usuario->deleted_at)<span class="badge bg-secondary">{{ __('Cuenta eliminada') }}</span>@endif<br>
                <small class="text-muted">{{ $usuario->email }}</small>
                <div class="small mt-2">{{ __('Verificaciones eliminadas') }}: {{ $eliminaciones }}</div>
            </div>
        </div>

        <p class="text-muted small">{{ __('Estos eventos no contienen datos personales ni documentos. Tras una eliminación ya no están vinculados a ninguna solicitud.') }}</p>

        {{-- Escritorio --}}
        <div class="card d-none d-lg-block">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 reda-kyc-tabla">
                    <thead>
                        <tr>
                            <th>{{ __('Fecha') }}</th>
                            <th>{{ __('Evento') }}</th>
                            <th>{{ __('Origen') }}</th>
                            <th>{{ __('Estado') }}</th>
                            <th>{{ __('Resultado') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($eventos as $e)
                            <tr>
                                <td>{{ $e->created_at?->format('d/m/Y H:i') }}</td>
                                <td>
                                    {{ EtiquetasKyc::evento($e->tipo) }}
                                    @if ($e->tipo === 'kyc_eliminado' && !empty($e->detalle['motivo']))
                                        <div class="reda-kyc-motivo mt-1">{{ $e->detalle['motivo'] }}</div>
                                    @endif
                                </td>
                                <td>{{ EtiquetasKyc::origen($e->origen) }}@if ($e->admin_nombre) · {{ $e->admin_nombre }}@endif</td>
                                <td>{{ $transicion($e) ?: '—' }}</td>
                                <td>{{ $e->resultado ? EtiquetasKyc::resultado($e->resultado) : '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">{{ __('Sin movimientos registrados.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Móvil y tableta: tarjetas --}}
        <div class="d-lg-none reda-kyc-tarjetas">
            @forelse ($eventos as $e)
                <div class="card reda-kyc-tarjeta mb-2">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <strong>{{ EtiquetasKyc::evento($e->tipo) }}</strong>
                            <small class="text-muted text-end">{{ $e->created_at?->format('d/m/Y H:i') }}</small>
                        </div>
                        <div class="small text-muted">{{ EtiquetasKyc::origen($e->origen) }}@if ($e->admin_nombre) · {{ $e->admin_nombre }}@endif</div>
                        @if ($transicion($e))<div class="small">{{ $transicion($e) }}</div>@endif
                        @if ($e->resultado && $e->resultado !== 'procesado')<div class="small">{{ EtiquetasKyc::resultado($e->resultado) }}</div>@endif
                        @if ($e->tipo === 'kyc_eliminado' && !empty($e->detalle['motivo']))
                            <div class="reda-kyc-motivo mt-1">{{ $e->detalle['motivo'] }}</div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-center text-muted py-4">{{ __('Sin movimientos registrados.') }}</p>
            @endforelse
        </div>

        {{ $eventos->links('reda-alojamiento::admin.general.paginacion') }}
    </section>
</div>
@endsection
