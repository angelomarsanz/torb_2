{{--
    Resumen: Plantilla de correo electrónico dirigida a los administradores del sistema por primer aviso de mediaciones.
    Informa al equipo administrativo que un usuario ha alcanzado el umbral preventivo configurado de mediaciones,
    aportando los datos del usuario, el conteo actual y enlaces rápidos al panel de control de mediaciones.
--}}
@extends('emails.template')

@section('emails.main')
<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="border-bottom: 2px solid #ffc107; padding-bottom: 10px; margin-bottom: 20px;">
        <h2 style="color: #d39e00; margin: 0;">{{ __('Alerta Administrativa: Primer aviso de límite de mediaciones') }}</h2>
    </div>

    <p style="font-size: 15px;">
        {{ __('Hola Administrador,') }}
    </p>

    <p style="font-size: 15px;">
        {{ __('Se le notifica que el usuario') }} <strong>{{ $nombreUsuario }}</strong> ({{ $emailUsuario }}) {{ __('ha alcanzado el primer aviso preventivo con') }} <strong>{{ $conteoMediaciones }} {{ __('mediaciones acumuladas') }}</strong> {{ __('(umbral configurado:') }} {{ $limitePrimerAviso }}).
    </p>

    <div style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px; padding: 15px; margin: 20px 0;">
        <h4 style="margin-top: 0; margin-bottom: 10px; color: #495057; font-size: 15px;">{{ __('Detalles del Usuario:') }}</h4>
        <ul style="margin: 0; padding-left: 20px; font-size: 14px; color: #6c757d;">
            <li><strong>{{ __('ID Usuario:') }}</strong> #{{ $idUsuario }}</li>
            <li><strong>{{ __('Nombre completo:') }}</strong> {{ $nombreUsuario }}</li>
            <li><strong>{{ __('Correo electrónico:') }}</strong> {{ $emailUsuario }}</li>
            <li><strong>{{ __('Mediaciones acumuladas:') }}</strong> {{ $conteoMediaciones }}</li>
            <li><strong>{{ __('Límite para suspensión:') }}</strong> {{ $limiteSegundoAviso }}</li>
            @if (!empty($disputaId))
            <li><strong>{{ __('Última Mediación:') }}</strong> #{{ $disputaId }}</li>
            @endif
        </ul>
    </div>

    <p style="font-size: 14px; color: #555;">
        {{ __('Se ha enviado la notificación preventiva al buzón y correo del usuario. Puede consultar el detalle y gestionar los casos desde el panel administrativo.') }}
    </p>

    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ url('admin/reda/disputas') }}" style="background-color: #ffc107; color: #212529; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">
            {{ __('Ver Mediaciones en el Admin') }}
        </a>
    </div>

    <p style="font-size: 12px; color: #999; margin-top: 35px; border-top: 1px solid #eee; padding-top: 15px;">
        {{ __('Notificación automática para el equipo de administración de') }} {{ siteName() }}.
    </p>
</div>
@endsection
