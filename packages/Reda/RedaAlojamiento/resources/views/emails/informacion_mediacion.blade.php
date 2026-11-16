{{-- Correo de solicitud o aviso de carga de información adicional de mediación. --}}
<p>{{ __('Información adicional de mediación') }} #{{ $disputa->id }}</p>
@if ($correo->tipo === 'solicitud')
    <p>{{ $solicitud->solicitud }}</p>
    <p>{{ __('Consulte el enlace para conocer el plazo vigente y cargar sus archivos.') }}</p>
    <p><a href="{{ $enlace }}">{{ __('Cargar archivos solicitados') }}</a></p>
@else
    <p>{{ __('El usuario :nombre envió archivos solicitados.', ['nombre' => trim(($usuario->first_name ?? '') . ' ' . ($usuario->last_name ?? ''))]) }}</p>
    <p><a href="{{ $enlace }}">{{ __('Ver alertas') }}</a></p>
@endif
