{{--
    Resumen: Notifica las restricciones de acciones por alcanzar el límite de mediaciones.
    Explica los accesos afectados y la posibilidad de consultar el caso con soporte.
--}}
@extends('emails.template')

@section('emails.main')
<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="border-bottom: 2px solid #dc3545; padding-bottom: 10px; margin-bottom: 20px;">
        <h2 style="color: #dc3545; margin: 0;">{{ __('Notificación: Acciones restringidas por mediaciones') }}</h2>
    </div>

    <p style="font-size: 16px;">
        {{ __('Estimado/a') }} <strong>{{ $nombreUsuario }}</strong>,
    </p>

    <p style="font-size: 15px;">
        {{ __('Le comunicamos que se han restringido algunas acciones de su cuenta en') }} {{ siteName() }} {{ __('porque ha acumulado') }} <strong>{{ $conteoMediaciones }} {{ __('mediaciones') }}</strong>, {{ __('alcanzando el límite máximo estipulado en las políticas de la plataforma.') }}
    </p>

    <div style="background-color: #f8d7da; border-left: 4px solid #f5c6cb; padding: 15px; margin: 20px 0; border-radius: 4px;">
        <p style="margin: 0; color: #721c24; font-size: 14px;">
            <strong>{{ __('Acciones restringidas:') }}</strong> {{ __('Crear propiedades o negocios, hacer reservaciones, iniciar nuevas mediaciones y solicitar retiros de dinero. Podrá seguir accediendo a su cuenta.') }}
        </p>
    </div>

    <p style="font-size: 14px; color: #555;">
        {{ __('Si considera que esta medida corresponde a un error o desea solicitar una revisión de su expediente ante el equipo de mediaciones, puede comunicarse directamente con nuestro departamento de soporte técnico.') }}
    </p>

    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ url('contact-us') }}" style="background-color: #6c757d; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">
            {{ __('Contactar a Soporte') }}
        </a>
    </div>

    <p style="font-size: 12px; color: #999; margin-top: 35px; border-top: 1px solid #eee; padding-top: 15px;">
        {{ __('Este es un mensaje automático de control y seguridad emitido por') }} {{ siteName() }}.
    </p>
</div>
@endsection
