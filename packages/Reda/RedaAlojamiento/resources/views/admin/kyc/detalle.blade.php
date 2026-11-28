{{--
    Detalle administrativo de una solicitud KYC: datos personales (el número de documento se muestra enmascarado y se
    revela bajo demanda, con auditoría), documentos con su historial, resultado de Didit, observaciones, acciones
    (incluida "Eliminar verificación", con motivo obligatorio y confirmación) y el historial de cambios. Las acciones
    se envían por AJAX (detalleKyc.js). Todo el contenido se escapa con {{ }}.
--}}
@extends('admin.template')

@section('main')
@php
    use Reda\RedaAlojamiento\Enums\EstadoKyc;
    use Reda\RedaAlojamiento\Support\EtiquetasKyc;

    $pais = static function (?string $codigo) {
        if (!$codigo) { return '—'; }
        $nombre = class_exists('Locale') ? \Locale::getDisplayRegion('-' . $codigo, app()->getLocale()) : null;
        return $nombre && $nombre !== $codigo ? $nombre . ' (' . $codigo . ')' : $codigo;
    };
    try { $nacimiento = $solicitud->fecha_nacimiento ? \Carbon\Carbon::createFromFormat('Y-m-d', $solicitud->fecha_nacimiento) : null; } catch (\Throwable $e) { $nacimiento = null; }
    $estadoEtiqueta = static fn (?string $v) => $v && EstadoKyc::tryFrom($v) ? EstadoKyc::tryFrom($v)->etiqueta() : '—';
    $fecha = static fn ($f) => $f ? $f->format('d/m/Y H:i') : '—';
    $correccionPendiente = $solicitud->tieneCorreccionPendiente();
    $correccionAtendida = $solicitud->correccion_atendida_en && !$correccionPendiente;
    $tieneArchivos = $documentos->contains(fn ($d) => $d->estado !== 'eliminado');
@endphp
<div class="content-wrapper" id="reda-kyc-detalle"
     data-url-aprobar="{{ route('reda.admin.kyc.aprobar', $solicitud->id) }}"
     data-url-rechazar="{{ route('reda.admin.kyc.rechazar', $solicitud->id) }}"
     data-url-correccion="{{ route('reda.admin.kyc.correccion', $solicitud->id) }}"
     data-url-observacion="{{ route('reda.admin.kyc.observacion', $solicitud->id) }}"
     data-url-retencion="{{ route('reda.admin.kyc.retencion', $solicitud->id) }}"
     data-url-revelar="{{ route('reda.admin.kyc.revelar', $solicitud->id) }}"
     data-url-eliminar="{{ route('reda.admin.kyc.eliminar', $solicitud->id) }}"
     data-url-eliminadas="{{ route('reda.admin.kyc.eliminadas') }}"
     data-url-listado="{{ route('reda.admin.kyc.index') }}">
    <section class="content-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h1 class="mb-0">{{ __('Solicitud de verificación') }} #{{ $solicitud->id }}
            <span class="badge {{ EtiquetasKyc::claseEstado($solicitud->estado) }} ms-2">{{ $solicitud->estado?->etiqueta() }}</span></h1>
        <a class="btn btn-outline-secondary btn-sm" href="{{ route('reda.admin.kyc.index') }}">&larr; {{ __('Volver al listado') }}</a>
    </section>

    <section class="content">
        @if ($coincidencias > 0)
            <div class="alert alert-warning" role="alert">
                {{ __('El número de documento coincide con :n usuario(s) ya verificado(s) o con una solicitud abierta. Revisa si se trata de una duplicidad.', ['n' => $coincidencias]) }}
            </div>
        @endif
        @if ($correccionPendiente)
            <div class="alert alert-info" role="status">{{ __('Se solicitó una corrección y el usuario aún no responde. No se puede decidir hasta que la atienda.') }}</div>
        @elseif ($correccionAtendida && $es_manual_abierta)
            <div class="alert alert-success" role="status">{{ __('El usuario atendió la corrección el :f. Revisa los datos y documentos actualizados.', ['f' => $fecha($solicitud->correccion_atendida_en)]) }}</div>
        @endif
        @if ($solicitud->estado === EstadoKyc::Rechazado && $solicitud->motivo_rechazo)
            <div class="alert alert-danger" role="alert"><strong>{{ __('Motivo del rechazo (comunicado al usuario)') }}:</strong> {{ $solicitud->motivo_rechazo }}</div>
        @endif

        <div class="row g-3">
            <div class="col-12 col-xl-8">
                {{-- Usuario y datos personales --}}
                <div class="card mb-3">
                    <div class="card-header"><h2 class="h5 mb-0">{{ __('Usuario y datos enviados') }}</h2></div>
                    <div class="card-body">
                        <dl class="row mb-0 reda-kyc-datos">
                            <dt class="col-sm-4">{{ __('Cuenta') }}</dt>
                            <dd class="col-sm-8">
                                @if ($usuario)
                                    {{ $usuario->first_name }} {{ $usuario->last_name }} · {{ $usuario->email }}
                                    · <a href="{{ url('admin/edit-customer/' . $usuario->id) }}">{{ __('Ver cliente') }}</a>
                                    @if ($usuario->deleted_at)<span class="badge bg-secondary">{{ __('Cuenta eliminada') }}</span>@endif
                                @else — @endif
                            </dd>
                            <dt class="col-sm-4">{{ __('Método') }}</dt><dd class="col-sm-8">{{ $solicitud->metodo?->etiqueta() }} · {{ __('Intento') }} {{ $solicitud->numero_intento }}</dd>
                            <dt class="col-sm-4">{{ __('Nombres (como en el documento)') }}</dt><dd class="col-sm-8">{{ $solicitud->nombres ?: '—' }}</dd>
                            <dt class="col-sm-4">{{ __('Apellidos (como en el documento)') }}</dt><dd class="col-sm-8">{{ $solicitud->apellidos ?: '—' }}</dd>
                            <dt class="col-sm-4">{{ __('Tipo de documento') }}</dt><dd class="col-sm-8">{{ EtiquetasKyc::tipoDocumento($solicitud->tipo_documento) ?: '—' }}</dd>
                            <dt class="col-sm-4">{{ __('Número de documento') }}</dt>
                            <dd class="col-sm-8">
                                <span id="kyc-numero-documento">{{ $solicitud->numero_documento_enmascarado ?: '—' }}</span>
                                @if ($solicitud->numero_documento_enmascarado)
                                    <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="btn-revelar-documento">{{ __('Mostrar completo') }}</button>
                                    <small class="text-muted d-block">{{ __('Cada vez que se muestra queda registrado.') }}</small>
                                @endif
                            </dd>
                            <dt class="col-sm-4">{{ __('País de expedición') }}</dt><dd class="col-sm-8">{{ $pais($solicitud->pais_expedicion) }}</dd>
                            <dt class="col-sm-4">{{ __('Fecha de nacimiento') }}</dt>
                            <dd class="col-sm-8">{{ $nacimiento ? $nacimiento->format('d/m/Y') . ' (' . $nacimiento->age . ' ' . __('años') . ')' : ($solicitud->fecha_nacimiento ?: '—') }}</dd>
                            <dt class="col-sm-4">{{ __('Residencia') }}</dt>
                            <dd class="col-sm-8">{{ $pais($solicitud->pais_residencia) }} · {{ $solicitud->ciudad_residencia ?: '—' }}<br>{{ $solicitud->direccion_residencia ?: '' }}</dd>
                            <dt class="col-sm-4">{{ __('Correo de contacto') }}</dt><dd class="col-sm-8">{{ $solicitud->correo_contacto ?: '—' }}</dd>
                            <dt class="col-sm-4">{{ __('Enviada') }}</dt><dd class="col-sm-8">{{ $fecha($solicitud->enviada_en ?? $solicitud->iniciada_en) }}</dd>
                            <dt class="col-sm-4">{{ __('Decisión') }}</dt>
                            <dd class="col-sm-8">
                                @if ($solicitud->aprobada_en){{ __('Aprobada') }}: {{ $fecha($solicitud->aprobada_en) }}
                                @elseif ($solicitud->rechazada_en){{ __('Rechazada') }}: {{ $fecha($solicitud->rechazada_en) }}
                                @else — @endif
                                @if ($administrador) · {{ __('Administrador') }}: {{ $administrador }}@endif
                            </dd>
                        </dl>
                    </div>
                </div>

                {{-- Documentos --}}
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">{{ __('Documentos') }}</h2>
                        @if ($tieneArchivos)
                            <button type="button" class="btn btn-sm {{ $solicitud->retencion_suspendida ? 'btn-warning' : 'btn-outline-secondary' }}" id="btn-retencion-kyc"
                                    data-suspender="{{ $solicitud->retencion_suspendida ? '0' : '1' }}">
                                {{ $solicitud->retencion_suspendida ? __('Reanudar eliminación automática') : __('Suspender eliminación automática') }}
                            </button>
                        @endif
                    </div>
                    <div class="card-body">
                        @if ($solicitud->retencion_suspendida)
                            <p class="text-warning small">{{ __('La eliminación automática de estos documentos está suspendida.') }}</p>
                        @elseif ($solicitud->eliminar_documentos_en && $tieneArchivos)
                            <p class="text-muted small">{{ __('Se eliminarán automáticamente a partir del :f.', ['f' => $solicitud->eliminar_documentos_en->format('d/m/Y')]) }}</p>
                        @endif
                        <div class="row g-3 reda-kyc-documentos">
                            @forelse ($documentos as $d)
                                <div class="col-12 col-md-6 col-xxl-4">
                                    <div class="card h-100 reda-kyc-documento {{ $d->estado !== 'vigente' ? 'reda-kyc-documento--inactivo' : '' }}">
                                        <div class="card-body p-2">
                                            <div class="d-flex justify-content-between small mb-1">
                                                <strong>{{ EtiquetasKyc::tipoArchivo($d->tipo) }}</strong>
                                                <span class="badge {{ $d->estado === 'vigente' ? 'bg-success' : 'bg-secondary' }}">{{ EtiquetasKyc::estadoArchivo($d->estado) }}</span>
                                            </div>
                                            @if ($d->estado === 'eliminado')
                                                <p class="text-muted small mb-0">{{ __('Archivo eliminado') }} {{ $d->eliminado_en?->format('d/m/Y') }}</p>
                                            @elseif ($d->mime === 'application/pdf')
                                                <a class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener" href="{{ route('reda.admin.kyc.documento', $d->id) }}">{{ __('Descargar PDF') }}</a>
                                            @else
                                                <a href="{{ route('reda.admin.kyc.documento', $d->id) }}" target="_blank" rel="noopener">
                                                    <img class="img-fluid rounded reda-kyc-miniatura" loading="lazy" alt="{{ EtiquetasKyc::tipoArchivo($d->tipo) }}" src="{{ route('reda.admin.kyc.documento', $d->id) }}">
                                                </a>
                                            @endif
                                            <div class="text-muted small mt-1">{{ $d->created_at?->format('d/m/Y H:i') }}@if ($d->tamano_bytes) · {{ number_format($d->tamano_bytes / 1024, 0, ',', '.') }} KB @endif</div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">{{ __('Esta solicitud no tiene documentos.') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Resultado de Didit --}}
                @if ($solicitud->didit_session_id || $solicitud->didit_estado_original)
                    <div class="card mb-3">
                        <div class="card-header"><h2 class="h5 mb-0">{{ __('Resultado de Didit') }}</h2></div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-4">{{ __('Estado informado por Didit') }}</dt><dd class="col-sm-8">{{ $solicitud->didit_estado_original ?: '—' }}</dd>
                                <dt class="col-sm-4">{{ __('Sesión') }}</dt><dd class="col-sm-8">{{ $solicitud->didit_session_id ? '…' . substr($solicitud->didit_session_id, -8) : '—' }}</dd>
                                @php $r = is_array($solicitud->didit_resultado) ? $solicitud->didit_resultado : []; @endphp
                                @if (!empty($r['decidido_por']))<dt class="col-sm-4">{{ __('Decidido por') }}</dt><dd class="col-sm-8">{{ $r['decidido_por'] }} {{ !empty($r['decidido_en']) ? '· ' . $r['decidido_en'] : '' }}</dd>@endif
                                @if (isset($r['advertencias']))<dt class="col-sm-4">{{ __('Advertencias') }}</dt><dd class="col-sm-8">{{ $r['advertencias'] }}</dd>@endif
                                @foreach (($r['caracteristicas'] ?? []) as $nombre => $estados)
                                    <dt class="col-sm-4">{{ $nombre }}</dt><dd class="col-sm-8">{{ empty($estados) ? '—' : implode(', ', $estados) }}</dd>
                                @endforeach
                            </dl>
                            <small class="text-muted">{{ __('Solo se guarda el resumen del resultado, sin imágenes ni datos biométricos. El detalle completo está en la consola de Didit.') }}</small>
                        </div>
                    </div>
                @endif

                {{-- Historial de cambios --}}
                <div class="card mb-3">
                    <div class="card-header"><h2 class="h5 mb-0">{{ __('Historial de cambios') }}</h2></div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush reda-kyc-historial">
                            @forelse ($eventos as $e)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between flex-wrap gap-1">
                                        <strong>{{ EtiquetasKyc::evento($e->tipo) }}</strong>
                                        <small class="text-muted">{{ $e->created_at?->format('d/m/Y H:i:s') }}</small>
                                    </div>
                                    <small class="text-muted">
                                        {{ EtiquetasKyc::origen($e->origen) }}@if ($e->admin_nombre) · {{ $e->admin_nombre }}@endif
                                        @if ($e->estado_anterior || $e->estado_nuevo) · {{ $estadoEtiqueta($e->estado_anterior) }} → {{ $estadoEtiqueta($e->estado_nuevo) }}@endif
                                        @if ($e->resultado && $e->resultado !== 'procesado') · {{ EtiquetasKyc::resultado($e->resultado) }}@endif
                                    </small>
                                </li>
                            @empty
                                <li class="list-group-item text-muted">{{ __('Sin movimientos registrados.') }}</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                {{-- Acciones --}}
                <div class="card mb-3">
                    <div class="card-header"><h2 class="h5 mb-0">{{ __('Decisión') }}</h2></div>
                    <div class="card-body d-grid gap-2">
                        @if ($es_manual_abierta)
                            <button type="button" class="btn btn-success" id="btn-aprobar-kyc" {{ $correccionPendiente ? 'disabled' : '' }}>{{ __('Aprobar solicitud') }}</button>
                            <button type="button" class="btn btn-danger" id="btn-rechazar-kyc" data-bs-toggle="modal" data-bs-target="#modal-rechazar-kyc">{{ __('Rechazar solicitud') }}</button>
                            <button type="button" class="btn btn-outline-primary" id="btn-correccion-kyc" data-bs-toggle="modal" data-bs-target="#modal-correccion-kyc">{{ __('Solicitar corrección') }}</button>
                        @else
                            <p class="text-muted mb-0">{{ __('Esta solicitud no admite decisiones manuales (solo las solicitudes manuales en revisión).') }}</p>
                        @endif
                    </div>
                </div>

                {{-- Eliminar la verificación (permite al usuario volver a verificarse) --}}
                <div class="card mb-3 border-danger">
                    <div class="card-header"><h2 class="h5 mb-0 text-danger">{{ __('Eliminar verificación') }}</h2></div>
                    <div class="card-body d-grid gap-2">
                        <p class="small text-muted mb-0">{{ __('Elimina los datos personales, los documentos y las solicitudes de este usuario y lo deja sin verificar para que pueda enviar su verificación de nuevo. Úsalo, por ejemplo, si una verificación se aprobó por error.') }}</p>
                        <button type="button" class="btn btn-outline-danger" id="btn-eliminar-kyc" data-bs-toggle="modal" data-bs-target="#modal-eliminar-kyc">{{ __('Eliminar verificación') }}</button>
                    </div>
                </div>

                {{-- Observaciones --}}
                <div class="card mb-3">
                    <div class="card-header"><h2 class="h5 mb-0">{{ __('Observaciones internas') }}</h2></div>
                    <div class="card-body">
                        @if ($correccionPendiente || $solicitud->correccion_solicitada)
                            <p class="small mb-2"><strong>{{ __('Última corrección solicitada') }}:</strong> {{ $solicitud->correccion_solicitada }}</p>
                        @endif
                        <pre class="reda-kyc-observaciones" id="kyc-observaciones">{{ $solicitud->observaciones_internas ?: __('Sin observaciones.') }}</pre>
                        <label class="form-label" for="kyc-nueva-observacion">{{ __('Agregar observación') }}</label>
                        <textarea class="form-control mb-2" id="kyc-nueva-observacion" rows="3" maxlength="2000"></textarea>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-observacion-kyc">{{ __('Guardar observación') }}</button>
                        <small class="text-muted d-block mt-1">{{ __('El usuario nunca ve estas observaciones.') }}</small>
                    </div>
                </div>

                {{-- Historial de verificaciones del usuario --}}
                <div class="card mb-3">
                    <div class="card-header"><h2 class="h5 mb-0">{{ __('Verificaciones del usuario') }}</h2></div>
                    <ul class="list-group list-group-flush">
                        @foreach ($historial as $h)
                            <li class="list-group-item {{ $h->id === $solicitud->id ? 'active' : '' }}">
                                <a class="{{ $h->id === $solicitud->id ? 'text-white' : '' }}" href="{{ route('reda.admin.kyc.detalle', $h->id) }}">#{{ $h->id }} · {{ $h->metodo?->etiqueta() }}</a><br>
                                <small>{{ $h->estado?->etiqueta() }} · {{ ($h->enviada_en ?? $h->iniciada_en ?? $h->created_at)?->format('d/m/Y') }}@if ($h->admin_nombre) · {{ $h->admin_nombre }}@endif</small>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Modal: rechazar --}}
    <div class="modal fade" id="modal-rechazar-kyc" tabindex="-1" aria-labelledby="titulo-rechazar-kyc" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title" id="titulo-rechazar-kyc">{{ __('Rechazar solicitud') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Cerrar') }}"></button></div>
                <div class="modal-body">
                    <label class="form-label" for="kyc-motivo-rechazo">{{ __('Motivo del rechazo') }} <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="kyc-motivo-rechazo" rows="4" maxlength="1000" required></textarea>
                    <small class="text-muted">{{ __('Este motivo se comunica al usuario. Explica qué debe cambiar para una nueva solicitud.') }}</small>
                    <div class="invalid-feedback d-block" id="error-motivo-rechazo"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancelar') }}</button>
                    <button type="button" class="btn btn-danger" id="btn-confirmar-rechazo-kyc">{{ __('Rechazar') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: eliminar la verificación --}}
    <div class="modal fade" id="modal-eliminar-kyc" tabindex="-1" aria-labelledby="titulo-eliminar-kyc" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title text-danger" id="titulo-eliminar-kyc">{{ __('Eliminar verificación') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Cerrar') }}"></button></div>
                <div class="modal-body">
                    <div class="alert alert-danger small">{{ __('Esta acción no se puede deshacer. Se borrarán definitivamente los datos personales, los documentos y las solicitudes de este usuario, y quedará sin verificar. Se le avisará por correo y podrá enviar su verificación de nuevo.') }}</div>
                    <label class="form-label" for="kyc-motivo-eliminacion">{{ __('Motivo de la eliminación') }} <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="kyc-motivo-eliminacion" rows="3" maxlength="500" required></textarea>
                    <small class="text-muted">{{ __('Queda registrado junto con tu nombre y la fecha. No se envía al usuario.') }}</small>
                    <div class="invalid-feedback d-block" id="error-motivo-eliminacion"></div>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="kyc-confirmar-eliminacion">
                        <label class="form-check-label small" for="kyc-confirmar-eliminacion">{{ __('Entiendo que se eliminarán definitivamente los datos y documentos y que el usuario deberá verificarse de nuevo.') }}</label>
                    </div>
                    <div class="invalid-feedback d-block" id="error-confirmar-eliminacion"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancelar') }}</button>
                    <button type="button" class="btn btn-danger" id="btn-confirmar-eliminacion-kyc">{{ __('Eliminar verificación') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: solicitar corrección --}}
    <div class="modal fade" id="modal-correccion-kyc" tabindex="-1" aria-labelledby="titulo-correccion-kyc" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title" id="titulo-correccion-kyc">{{ __('Solicitar corrección') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Cerrar') }}"></button></div>
                <div class="modal-body">
                    <label class="form-label" for="kyc-texto-correccion">{{ __('Indica qué información debe corregir el usuario') }} <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="kyc-texto-correccion" rows="4" maxlength="1000" required>{{ $correccionPendiente ? $solicitud->correccion_solicitada : '' }}</textarea>
                    <div class="invalid-feedback d-block" id="error-texto-correccion"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancelar') }}</button>
                    <button type="button" class="btn btn-primary" id="btn-confirmar-correccion-kyc">{{ __('Enviar solicitud de corrección') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('public/js/reda/admin/vistas/kyc/detalleKyc.min.js') }}?v={{ time() }}"></script>
@endpush
