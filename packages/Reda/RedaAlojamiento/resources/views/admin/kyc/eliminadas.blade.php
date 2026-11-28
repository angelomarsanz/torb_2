{{--
    Verificaciones de identidad (KYC) eliminadas por un administrador. Para cada eliminación muestra quién la hizo,
    cuándo, el estado que tenía el usuario, qué se eliminó y el motivo, con acceso al historial de auditoría que se
    conserva. Escritorio: tabla. Móvil: tarjetas. Paginación de 10 en 10 (los controles se muestran siempre).
    Todo el contenido se escapa con {{ }}; el motivo conserva sus saltos de línea con CSS, nunca como HTML.
--}}
@extends('admin.template')

@section('main')
@php
    use Reda\RedaAlojamiento\Enums\EstadoKyc;
    $estadoEtiqueta = static fn (?string $v) => $v && EstadoKyc::tryFrom($v) ? EstadoKyc::tryFrom($v)->etiqueta() : '—';
    $metodoEtiqueta = static fn (?string $v) => $v === 'didit' ? __('Automática (Didit)') : ($v === 'manual' ? __('Manual') : '—');
@endphp
<div class="content-wrapper" id="reda-kyc-eliminadas">
    <section class="content-header">
        <h1>{{ __('Verificación de identidad (KYC)') }}</h1>
    </section>
    <section class="content">
        @include('reda-alojamiento::admin.kyc.partials.pestanas')

        <p class="text-muted small">{{ __('Verificaciones eliminadas por un administrador. Los datos personales y los documentos se borraron; solo se conserva el historial de auditoría, que no contiene datos personales.') }}</p>

        {{-- Escritorio --}}
        <div class="card d-none d-lg-block">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 reda-kyc-tabla">
                    <thead>
                        <tr>
                            <th>{{ __('Fecha') }}</th>
                            <th>{{ __('Usuario') }}</th>
                            <th>{{ __('Administrador') }}</th>
                            <th>{{ __('Estado que tenía') }}</th>
                            <th>{{ __('Eliminado') }}</th>
                            <th>{{ __('Motivo') }}</th>
                            <th class="text-end">{{ __('Acciones') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($eliminaciones as $e)
                            @php $d = is_array($e->detalle) ? $e->detalle : []; @endphp
                            <tr>
                                <td>{{ $e->created_at?->format('d/m/Y H:i') }}</td>
                                <td>
                                    <strong>{{ $e->usuario_nombre }} {{ $e->usuario_apellido }}</strong><br>
                                    <small class="text-muted">{{ $e->usuario_correo }}</small>
                                </td>
                                <td>{{ $e->admin_nombre ?: '—' }}</td>
                                <td>{{ $estadoEtiqueta($e->estado_anterior) }}<br><small class="text-muted">{{ $metodoEtiqueta($d['metodo'] ?? null) }}</small></td>
                                <td><small>{{ (int) ($d['solicitudes'] ?? 0) }} {{ __('solicitud(es)') }}<br>{{ (int) ($d['documentos'] ?? 0) }} {{ __('documento(s)') }}</small></td>
                                <td><div class="reda-kyc-motivo">{{ $d['motivo'] ?? '—' }}</div></td>
                                <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('reda.admin.kyc.historial', $e->user_id) }}">{{ __('Ver historial') }}</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">{{ __('No se ha eliminado ninguna verificación.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Móvil y tableta: tarjetas --}}
        <div class="d-lg-none reda-kyc-tarjetas">
            @forelse ($eliminaciones as $e)
                @php $d = is_array($e->detalle) ? $e->detalle : []; @endphp
                <div class="card reda-kyc-tarjeta mb-2">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div>
                                <strong>{{ $e->usuario_nombre }} {{ $e->usuario_apellido }}</strong><br>
                                <small class="text-muted">{{ $e->usuario_correo }}</small>
                            </div>
                            <small class="text-muted text-end">{{ $e->created_at?->format('d/m/Y H:i') }}</small>
                        </div>
                        <dl class="row small mb-2 mt-2">
                            <dt class="col-5">{{ __('Administrador') }}</dt><dd class="col-7">{{ $e->admin_nombre ?: '—' }}</dd>
                            <dt class="col-5">{{ __('Estado que tenía') }}</dt><dd class="col-7">{{ $estadoEtiqueta($e->estado_anterior) }} · {{ $metodoEtiqueta($d['metodo'] ?? null) }}</dd>
                            <dt class="col-5">{{ __('Eliminado') }}</dt><dd class="col-7">{{ (int) ($d['solicitudes'] ?? 0) }} {{ __('solicitud(es)') }}, {{ (int) ($d['documentos'] ?? 0) }} {{ __('documento(s)') }}</dd>
                        </dl>
                        <div class="reda-kyc-motivo mb-2">{{ $d['motivo'] ?? '—' }}</div>
                        <a class="btn btn-sm btn-outline-primary w-100" href="{{ route('reda.admin.kyc.historial', $e->user_id) }}">{{ __('Ver historial') }}</a>
                    </div>
                </div>
            @empty
                <p class="text-center text-muted py-4">{{ __('No se ha eliminado ninguna verificación.') }}</p>
            @endforelse
        </div>

        {{ $eliminaciones->links('reda-alojamiento::admin.general.paginacion') }}
    </section>
</div>
@endsection
