{{--
    Resumen: Plantilla de correo para avisar al usuario sobre su verificación de identidad (KYC). Una sola plantilla
    sirve a todas las situaciones (solicitud recibida, verificada, rechazada, corrección solicitada, no completada,
    expirada o ya no vigente): el servicio NotificacionesKycService envía el título, los párrafos, el texto del botón
    y el color ya traducidos. No incluye texto libre del administrador ni datos del documento; solo enlaza a la
    pantalla de verificaciones, protegida por la sesión del usuario. Todo valor se escapa con {{ }}.
--}}
@extends('emails.template')

@section('emails.main')
<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="border-bottom: 3px solid {{ $color }}; padding-bottom: 10px; margin-bottom: 20px;">
        <h2 style="color: {{ $color }}; margin: 0;">{{ $titulo }}</h2>
    </div>

    <p style="font-size: 16px;">
        {{ __('Hola') }}@if (!empty($nombreUsuario)) <strong>{{ $nombreUsuario }}</strong>@endif,
    </p>

    @foreach ($parrafos as $parrafo)
        <p style="font-size: 15px;">{{ $parrafo }}</p>
    @endforeach

    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ $urlBoton }}" style="background-color: {{ $color }}; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">
            {{ $textoBoton }}
        </a>
    </div>

    <p style="font-size: 12px; color: #999; margin-top: 35px; border-top: 1px solid #eee; padding-top: 15px;">
        {{ __('Este es un mensaje automático del sistema de verificación de identidad de') }} {{ siteName() }}. {{ __('Por favor no responda a este correo.') }}
    </p>
</div>
@endsection
