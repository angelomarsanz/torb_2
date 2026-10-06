{{--
    Resumen: Plantilla de correo electrónico dirigida a los administradores del sistema por suspensión de cuenta de usuario.
    Alerta con carácter de prioridad al equipo de administración sobre la suspensión automática de una cuenta
    tras alcanzar o superar el límite máximo de mediaciones estipulado en la configuración del plugin.
--}}
@extends('emails.template')

@section('emails.main')
<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="border-bottom: 2px solid #dc3545; padding-bottom: 10px; margin-bottom: 20px;">
        <h2 style="color: #dc3545; margin: 0;">{{ __('Alerta Crítica: Cuenta de usuario suspendida por mediaciones') }}</h2>
    </div>

    <p style="font-size: 15px;">
        {{ __('Hola Administrador,') }}
    </p>

    <p style="font-size: 15px;">
        {{ __('Le informamos que la cuenta del usuario') }} <strong>{{ $nombreUsuario }}</strong> ({{ $emailUsuario }}) {{ __('ha sido') }} <strong style="color: #dc3545;">{{ __('SUSPENDIDA AUTOMÁTICAMENTE') }}</strong> {{ __('al acumular') }} <strong>{{ $conteoMediaciones }} {{ __('mediaciones') }}</strong>, {{ __('alcanzando el límite máximo permitido (:limite).', ['limite' => $limiteSegundoAviso]) }}
    </p>

    <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 6px; padding: 15px; margin: 20px 0;">
        <h4 style="margin-top: 0; margin-bottom: 10px; color: #721c24; font-size: 15px;">{{ __('Datos de la cuenta suspendida:') }}</h4>
        <ul style="margin: 0; padding-left: 20px; font-size: 14px; color: #721c24;">
            <li><strong>{{ __('ID Usuario:') }}</strong> #{{ $idUsuario }}</li>
            <li><strong>{{ __('Nombre completo:') }}</strong> {{ $nombreUsuario }}</li>
            <li><strong>{{ __('Correo electrónico:') }}</strong> {{ $emailUsuario }}</li>
            <li><strong>{{ __('Total de mediaciones registradas:') }}</strong> {{ $conteoMediaciones }}</li>
            <li><strong>{{ __('Límite de suspensión configurado:') }}</strong> {{ $limiteSegundoAviso }}</li>
            <li><strong>{{ __('Estatus asignado:') }}</strong> Inactive (Suspendido)</li>
            @if (!empty($disputaId))
            <li><strong>{{ __('Disputa detonante:') }}</strong> #{{ $disputaId }}</li>
            @endif
        </ul>
    </div>

    <p style="font-size: 14px; color: #555;">
        {{ __('El usuario ya ha sido notificado vía correo y buzón de mensajes. Su sesión y capacidades operativas en la plataforma han quedado restringidas. Puede auditar la alerta y el expediente completo en el panel administrativo.') }}
    </p>

    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ url('admin/reda/alertas') }}" style="background-color: #dc3545; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">
            {{ __('Ver Alertas en el Admin') }}
        </a>
    </div>

    <p style="font-size: 12px; color: #999; margin-top: 35px; border-top: 1px solid #eee; padding-top: 15px;">
        {{ __('Alerta crítica del sistema de mediaciones de') }} {{ siteName() }}.
    </p>
</div>
@endsection
