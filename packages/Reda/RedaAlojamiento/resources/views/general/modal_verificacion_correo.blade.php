{{--
    Vista: modal_verificacion_correo.blade.php
    Descripción: Modales dinámicos (Bootstrap 4.5) para el flujo de verificación y corrección de correo electrónico:
        1. Modal para confirmar/corregir email antes de enviar el formulario de registro (Signup).
        2. Modal que informa que el correo no ha sido verificado al intentar iniciar sesión (Login) y permite corregir el correo.
        3. Modal de éxito que confirma que el correo ha sido verificado tras hacer clic en el enlace del email y da acceso a Login.
    Paquete: Reda\RedaAlojamiento
--}}

{{-- 1. Variables de contexto de verificación transferidas a JavaScript --}}
<script>
    window.RedaVerificacionData = {
        correoNoVerificado: @json(Session::get('correo_no_verificado') ?? null),
        correoConfirmadoExitoso: @json(Session::get('correo_confirmado_exitoso') ?? false),
        emailConfirmado: @json(Session::get('email_confirmado') ?? null),
        correoRegistradoPendiente: @json(Session::get('correo_registrado_pendiente') ?? null),
        rutaActualizarCorreo: @json(url('reda/usuarios/actualizar-correo-verificacion')),
        urlLogin: @json(url('login')),
        csrfToken: @json(csrf_token())
    };
</script>

{{-- 2. Modal Signup: Verificación previa de email antes de enviar el formulario --}}
<div class="modal fade" id="reda_modal_confirmar_email_signup" tabindex="-1" role="dialog" aria-labelledby="reda_modal_signup_label" aria-hidden="true" style="z-index: 1080;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-700 text-dark" id="reda_modal_signup_label">
                    <i class="fa fa-envelope text-success mr-2"></i>{{ __('Verifique su correo electrónico') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="outline: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pt-3 pb-3">
                {{-- Paso 1: Preguntar si el email escrito es correcto --}}
                <div id="reda_signup_paso_verificar">
                    <p class="text-secondary text-15 mb-3">
                        {{ __('Por favor verifique si la dirección de su correo es correcta:') }}
                    </p>
                    <div class="p-3 mb-3 bg-light rounded text-center border" style="border-color: #e2e8f0 !important;">
                        <span id="reda_signup_email_mostrado" class="font-weight-bold text-dark text-16" style="word-break: break-all;"></span>
                    </div>
                    <div class="d-flex justify-content-end align-items-center mt-4">
                        <button type="button" id="reda_btn_corregir_email_signup" class="btn btn-outline-secondary px-3 mr-2" style="border-radius: 20px; font-weight: 600;">
                            <i class="fa fa-pencil mr-1"></i>{{ __('Corregir email') }}
                        </button>
                        <button type="button" id="reda_btn_email_correcto_signup" class="btn vbtn-success px-4" style="border-radius: 20px; font-weight: 600;">
                            <i class="fa fa-check mr-1"></i>{{ __('Email correcto') }}
                        </button>
                    </div>
                </div>

                {{-- Paso 2: Formulario para corregir el email --}}
                <div id="reda_signup_paso_corregir" class="d-none">
                    <p class="text-secondary text-14 mb-2">
                        {{ __('Escriba la dirección de correo correcta:') }}
                    </p>
                    <div class="form-group mb-2">
                        <input type="email" id="reda_signup_input_nuevo_email" class="form-control text-15 p-3" placeholder="nombre@ejemplo.com" style="border-radius: 8px;">
                    </div>
                    <div id="reda_signup_alerta_error" class="alert alert-danger py-2 px-3 text-13 d-none mb-3" role="alert">
                        <i class="fa fa-exclamation-circle mr-1"></i><span id="reda_signup_alerta_error_texto">{{ __('Por favor ingrese una dirección de correo válida.') }}</span>
                    </div>
                    <div class="d-flex justify-content-end align-items-center mt-3">
                        <button type="button" id="reda_btn_cancelar_corregir_signup" class="btn btn-light px-3 mr-2" style="border-radius: 20px;">
                            {{ __('Volver') }}
                        </button>
                        <button type="button" id="reda_btn_enviar_email_corregido_signup" class="btn vbtn-success px-4" style="border-radius: 20px; font-weight: 600;">
                            <i class="fa fa-paper-plane mr-1"></i>{{ __('Enviar') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 3. Modal Login: Correo no verificado con opción para corregir --}}
<div class="modal fade" id="reda_modal_correo_no_verificado_login" tabindex="-1" role="dialog" aria-labelledby="reda_modal_no_verificado_label" aria-hidden="true" style="z-index: 1080;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-700 text-dark" id="reda_modal_no_verificado_label">
                    <i class="fa fa-exclamation-circle text-warning mr-2"></i>{{ __('Correo no verificado') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="outline: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pt-3 pb-3">
                {{-- Paso 1: Notificación de correo no verificado --}}
                <div id="reda_login_paso_aviso">
                    <p class="text-secondary text-15 mb-2 leading-relaxed">
                        {{ __('Se envió un correo de verificación a') }} <strong id="reda_login_email_no_verificado_texto" class="text-dark" style="word-break: break-all;"></strong> {{ __('pero no ha sido confirmado, por favor ingrese a su bandeja de entrada y lo confirma.') }}
                    </p>
                    <p class="text-muted text-14 mt-3 mb-4">
                        {{ __('En caso que la dirección de correo') }} <span id="reda_login_email_no_verificado_subtexto" class="font-weight-600 text-dark"></span> {{ __('sea incorrecta haga clic en "Corregir correo".') }}
                    </p>
                    <div class="d-flex justify-content-end align-items-center">
                        <button type="button" class="btn btn-light px-3 mr-2" style="border-radius: 20px;" data-dismiss="modal">
                            {{ __('Cerrar') }}
                        </button>
                        <button type="button" id="reda_btn_abrir_corregir_correo_login" class="btn vbtn-success px-4" style="border-radius: 20px; font-weight: 600;">
                            <i class="fa fa-pencil mr-1"></i>{{ __('Corregir correo') }}
                        </button>
                    </div>
                </div>

                {{-- Paso 2: Formulario para ingresar nuevo correo y reenviar confirmación --}}
                <div id="reda_login_paso_formulario" class="d-none">
                    <p class="text-secondary text-14 mb-2">
                        {{ __('Ingrese la nueva dirección de correo electrónico:') }}
                    </p>
                    <div class="form-group mb-2">
                        <input type="email" id="reda_login_input_nuevo_correo" class="form-control text-15 p-3" placeholder="nombre@ejemplo.com" style="border-radius: 8px;">
                    </div>
                    <div id="reda_login_alerta_error" class="alert alert-danger py-2 px-3 text-13 d-none mb-3" role="alert">
                        <i class="fa fa-exclamation-circle mr-1"></i><span id="reda_login_alerta_error_texto">{{ __('Por favor ingrese una dirección de correo válida.') }}</span>
                    </div>
                    <div id="reda_login_alerta_exito" class="alert alert-success py-2 px-3 text-13 d-none mb-3" role="alert">
                        <i class="fa fa-check-circle mr-1"></i><span id="reda_login_alerta_exito_texto"></span>
                    </div>
                    <div class="d-flex justify-content-end align-items-center mt-3">
                        <button type="button" id="reda_btn_cancelar_corregir_login" class="btn btn-light px-3 mr-2" style="border-radius: 20px;">
                            {{ __('Cancelar') }}
                        </button>
                        <button type="button" id="reda_btn_enviar_nuevo_correo_login" class="btn vbtn-success px-4" style="border-radius: 20px; font-weight: 600;">
                            <span class="btn-text"><i class="fa fa-paper-plane mr-1"></i>{{ __('Enviar') }}</span>
                            <i class="spinner fa fa-spinner fa-spin d-none ml-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 4. Modal Confirmación Exitosa: Notificación tras validar el enlace del correo --}}
<div class="modal fade" id="reda_modal_correo_confirmado_exito" tabindex="-1" role="dialog" aria-labelledby="reda_modal_exito_label" aria-hidden="true" style="z-index: 1080;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-body text-center pt-5 pb-4 px-4">
                <div class="mb-3">
                    <i class="fa fa-check-circle fa-4x text-success" style="color: #28a745;"></i>
                </div>
                <h4 class="font-weight-700 text-dark mb-2" id="reda_modal_exito_label">
                    {{ __('¡Correo confirmado!') }}
                </h4>
                <p class="text-secondary text-15 mb-4">
                    {{ __('Su correo ha sido confirmado, por favor inicie sesión con su usuario y clave.') }}
                </p>
                <div>
                    <button type="button" id="reda_btn_iniciar_sesion_exito" class="btn vbtn-success px-5 py-2" style="border-radius: 20px; font-weight: 600; font-size: 15px;">
                        <i class="fa fa-sign-in mr-2"></i>{{ __('Iniciar sesión') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
