{{--
    Resumen: Plantilla de correo electrónico para el primer aviso preventivo al usuario por límite de mediaciones.
    Notifica cordialmente al usuario sobre la cantidad de mediaciones acumuladas y le advierte sobre
    el límite que activará la suspensión de su cuenta si continúa acumulando disputas.
--}}
@extends('emails.template')

@section('emails.main')
<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="border-bottom: 2px solid #f0ad4e; padding-bottom: 10px; margin-bottom: 20px;">
        <h2 style="color: #d9534f; margin: 0;">{{ __('Aviso preventivo: Límite de mediaciones permitidas') }}</h2>
    </div>

    <p style="font-size: 16px;">
        {{ __('Estimado/a') }} <strong>{{ $nombreUsuario }}</strong>,
    </p>

    <p style="font-size: 15px;">
        {{ __('Le informamos que ha acumulado') }} <strong>{{ $conteoMediaciones }} {{ __('mediaciones') }}</strong> {{ __('en la plataforma, alcanzando el umbral del primer aviso de advertencia.') }}
    </p>

    <div style="background-color: #fff3cd; border-left: 4px solid #ffeeba; padding: 15px; margin: 20px 0; border-radius: 4px;">
        <p style="margin: 0; color: #856404; font-size: 14px;">
            <strong>{{ __('Importante:') }}</strong> {{ __('El límite máximo permitido en el sistema es de') }} <strong>{{ $limiteSegundoAviso }} {{ __('mediaciones') }}</strong>. 
            {{ __('En caso de alcanzar dicho límite, su cuenta será suspendida de manera automática, restringiendo su acceso a reservas, publicaciones y contrataciones.') }}
        </p>
    </div>

    <p style="font-size: 14px; color: #555;">
        {{ __('Le recomendamos revisar el historial de sus estancias y comunicarse con nuestro equipo de atención o su contraparte para resolver cualquier diferencia de manera amigable.') }}
    </p>

    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ url('inbox') }}" style="background-color: #0d6efd; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">
            {{ __('Ir a mi buzón de mensajes') }}
        </a>
    </div>

    <p style="font-size: 12px; color: #999; margin-top: 35px; border-top: 1px solid #eee; padding-top: 15px;">
        {{ __('Este es un mensaje automático emitido por el sistema de mediaciones de') }} {{ siteName() }}. {{ __('Por favor no responda a este correo.') }}
    </p>
</div>
@endsection
