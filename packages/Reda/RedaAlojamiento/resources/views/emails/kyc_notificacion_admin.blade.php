{{--
    Resumen: Plantilla de correo para avisar a los administradores con permiso manage_kyc de que una verificación de
    identidad requiere su atención (solicitud manual nueva, corrección respondida o verificación de Didit en
    revisión). NotificacionesKycService envía el título y el mensaje ya traducidos. Muestra solo el nombre y el correo
    de la cuenta del usuario (nunca datos del documento) y enlaza al panel, donde se exige el permiso. Todo valor
    se escapa con {{ }}.
--}}
@extends('emails.template')

@section('emails.main')
<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="border-bottom: 3px solid #1f78c1; padding-bottom: 10px; margin-bottom: 20px;">
        <h2 style="color: #1f78c1; margin: 0;">{{ $titulo }}</h2>
    </div>

    <p style="font-size: 15px;">{{ $mensaje }}</p>

    <div style="background-color: #f4f8fc; border-left: 4px solid #1f78c1; padding: 12px 15px; margin: 20px 0; border-radius: 4px;">
        <p style="margin: 0; font-size: 14px;">
            <strong>{{ __('Usuario') }}:</strong> {{ $nombreUsuario }}<br>
            <strong>{{ __('Correo') }}:</strong> {{ $correoUsuario }}
        </p>
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ $urlBoton }}" style="background-color: #1f78c1; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">
            {{ $textoBoton }}
        </a>
    </div>

    <p style="font-size: 12px; color: #999; margin-top: 35px; border-top: 1px solid #eee; padding-top: 15px;">
        {{ __('Este es un mensaje automático del sistema de verificación de identidad de') }} {{ siteName() }}. {{ __('Por favor no responda a este correo.') }}
    </p>
</div>
@endsection
