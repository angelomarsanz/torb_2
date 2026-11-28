{{--
    Pestañas del panel administrativo KYC: los grupos de estado del listado y la pestaña "Eliminadas" (verificaciones
    eliminadas por un administrador). Se comparte entre el listado, la pantalla de eliminadas y el historial de un
    usuario. Espera $filtros (grupo, metodo, buscar) y $contadores. Todo el contenido se escapa con {{ }}.
--}}
@php
    $pestanas = [
        'pendientes'  => __('Pendientes de revisión'),
        'en_proceso'  => __('En proceso'),
        'verificados' => __('Verificados'),
        'rechazados'  => __('Rechazados'),
        'fallidos'    => __('Fallidos'),
        'expirados'   => __('Expirados'),
        'todos'       => __('Todos'),
    ];
@endphp
<ul class="nav nav-pills reda-kyc-pestanas mb-3" role="tablist">
    @foreach ($pestanas as $clave => $texto)
        <li class="nav-item">
            <a class="nav-link {{ $filtros['grupo'] === $clave ? 'active' : '' }}"
               href="{{ route('reda.admin.kyc.index', array_filter(['grupo' => $clave, 'metodo' => $filtros['metodo'], 'buscar' => $filtros['buscar']])) }}">
                {{ $texto }} <span class="badge bg-light text-dark">{{ $contadores[$clave] ?? 0 }}</span>
            </a>
        </li>
    @endforeach
    <li class="nav-item">
        <a class="nav-link {{ $filtros['grupo'] === 'eliminadas' ? 'active' : '' }}" href="{{ route('reda.admin.kyc.eliminadas') }}">
            {{ __('Eliminadas') }} <span class="badge bg-light text-dark">{{ $contadores['eliminadas'] ?? 0 }}</span>
        </a>
    </li>
</ul>
