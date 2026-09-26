/**
 * Módulo de Verificación y Corrección de Correo Electrónico en Frontend
 *
 * Este script gestiona tres flujos esenciales:
 * 1. Intercepta el formulario de registro (Signup) para abrir un modal que permita
 *    al usuario revisar si su correo está bien escrito, confirmarlo o corregirlo
 *    con validación en tiempo real antes del envío.
 * 2. Detecta intentos de inicio de sesión con correo no verificado, abriendo un modal
 *    explicativo con la opción de corregir el email y reenviar la verificación vía AJAX.
 * 3. Detecta la confirmación exitosa del correo (tras hacer clic en el enlace del email)
 *    y abre un modal amigable con un botón directo para iniciar sesión.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage resources/js/vistas/frontend
 * @author     REDA Tech Team
 * @version    1.0.0
 */

(function($) {
    "use strict";

    /**
     * Expresión regular estándar para validación de direcciones de correo electrónico.
     */
    const REGEX_EMAIL = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    /**
     * Valida sintácticamente una dirección de correo electrónico.
     *
     * @param {string} email
     * @returns {boolean}
     */
    function esCorreoValido(email) {
        if (!email || typeof email !== 'string') {
            return false;
        }
        return REGEX_EMAIL.test(email.trim());
    }

    /**
     * Obtiene una traducción desde window.RedaAlojamientoJson con fallback en español.
     *
     * @param {string} clave
     * @param {string} fallback
     * @returns {string}
     */
    function trans(clave, fallback) {
        if (window.RedaAlojamientoJson && window.RedaAlojamientoJson[clave]) {
            return window.RedaAlojamientoJson[clave];
        }
        return fallback || clave;
    }

    /**
     * Inicializa los manejadores de eventos del flujo de verificación de correo.
     */
    function inicializarVerificacionCorreo() {
        const datosServidor = window.RedaVerificacionData || {};

        // ----------------------------------------------------------------------
        // 1. FLUJO DE REGISTRO (SIGNUP)
        // ----------------------------------------------------------------------
        const $formSignup = $('#signup_form');
        let correoConfirmadoEnModal = false;

        if ($formSignup.length) {
            // Escuchar el clic sobre el botón de envío del formulario de registro
            $(document).on('click', '#signup_form button[type="submit"], #signup_form #btn', function(e) {
                if (correoConfirmadoEnModal) {
                    // Si el usuario ya confirmó en el modal, permitimos el flujo normal
                    return true;
                }

                // 1. Validar edad si existe la función global ageValidate()
                if (typeof window.ageValidate === 'function') {
                    if (!window.ageValidate()) {
                        return false;
                    }
                }

                // 2. Validar campos con jQuery.validate si está activo
                if (typeof $formSignup.valid === 'function') {
                    if (!$formSignup.valid()) {
                        return false;
                    }
                }

                const emailActual = ($('#email').val() || '').trim();
                if (!emailActual || !esCorreoValido(emailActual)) {
                    // Dejar que jQuery Validate muestre el error de correo si está vacío o inválido
                    return false;
                }

                // Si todas las validaciones pasaron, pausamos el envío para abrir el modal de confirmación
                e.preventDefault();
                e.stopImmediatePropagation();

                // Mostrar el correo en el modal y resetear las vistas internas
                $('#reda_signup_email_mostrado').text(emailActual);
                $('#reda_signup_input_nuevo_email').val(emailActual);
                $('#reda_signup_alerta_error').addClass('d-none');
                $('#reda_signup_paso_verificar').removeClass('d-none');
                $('#reda_signup_paso_corregir').addClass('d-none');

                $('#reda_modal_confirmar_email_signup').modal('show');
                return false;
            });

            // Botón: "Email correcto" -> Procede al envío del formulario
            $(document).on('click', '#reda_btn_email_correcto_signup', function(e) {
                e.preventDefault();
                correoConfirmadoEnModal = true;
                $('#reda_modal_confirmar_email_signup').modal('hide');

                // Enviar el formulario
                setTimeout(function() {
                    $('#signup_form #btn').trigger('click');
                }, 300);
            });

            // Botón: "Corregir email" -> Muestra el input de edición dentro del modal
            $(document).on('click', '#reda_btn_corregir_email_signup', function(e) {
                e.preventDefault();
                $('#reda_signup_alerta_error').addClass('d-none');
                $('#reda_signup_paso_verificar').addClass('d-none');
                $('#reda_signup_paso_corregir').removeClass('d-none');
                $('#reda_signup_input_nuevo_email').focus();
            });

            // Botón: "Volver" desde el modo corregir email
            $(document).on('click', '#reda_btn_cancelar_corregir_signup', function(e) {
                e.preventDefault();
                $('#reda_signup_alerta_error').addClass('d-none');
                $('#reda_signup_paso_corregir').addClass('d-none');
                $('#reda_signup_paso_verificar').removeClass('d-none');
            });

            // Botón: "Enviar" tras corregir el email en Signup
            $(document).on('click', '#reda_btn_enviar_email_corregido_signup', function(e) {
                e.preventDefault();
                const nuevoEmail = ($('#reda_signup_input_nuevo_email').val() || '').trim();

                if (!esCorreoValido(nuevoEmail)) {
                    $('#reda_signup_alerta_error_texto').text(
                        trans('Por favor ingrese una dirección de correo válida.', 'Por favor ingrese una dirección de correo válida.')
                    );
                    $('#reda_signup_alerta_error').removeClass('d-none');
                    return;
                }

                // Ocultar alerta de error
                $('#reda_signup_alerta_error').addClass('d-none');

                // Actualizar el correo en el formulario principal
                $('#email').val(nuevoEmail);

                correoConfirmadoEnModal = true;
                $('#reda_modal_confirmar_email_signup').modal('hide');

                // Enviar el formulario principal
                setTimeout(function() {
                    $('#signup_form #btn').trigger('click');
                }, 300);
            });
        }

        // ----------------------------------------------------------------------
        // 2. FLUJO DE LOGIN: CORREO NO VERIFICADO
        // ----------------------------------------------------------------------
        if (datosServidor.correoNoVerificado) {
            const correoPendiente = datosServidor.correoNoVerificado;

            $('#reda_login_email_no_verificado_texto').text(correoPendiente);
            $('#reda_login_email_no_verificado_subtexto').text(correoPendiente);
            $('#reda_login_input_nuevo_correo').val(correoPendiente);

            // Colocar el correo en el input de login para facilitar la experiencia
            if ($('#login_form input[name="email"]').length) {
                $('#login_form input[name="email"]').val(correoPendiente);
            }

            // Mostrar el modal
            $('#reda_modal_correo_no_verificado_login').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
        }

        // Botón: "Corregir correo" en el modal de Login
        $(document).on('click', '#reda_btn_abrir_corregir_correo_login', function(e) {
            e.preventDefault();
            $('#reda_login_alerta_error').addClass('d-none');
            $('#reda_login_alerta_exito').addClass('d-none');
            $('#reda_login_paso_aviso').addClass('d-none');
            $('#reda_login_paso_formulario').removeClass('d-none');
            $('#reda_login_input_nuevo_correo').focus();
        });

        // Botón: "Cancelar" corrección en Login
        $(document).on('click', '#reda_btn_cancelar_corregir_login', function(e) {
            e.preventDefault();
            $('#reda_login_alerta_error').addClass('d-none');
            $('#reda_login_alerta_exito').addClass('d-none');
            $('#reda_login_paso_formulario').addClass('d-none');
            $('#reda_login_paso_aviso').removeClass('d-none');
        });

        // Botón: "Enviar" nuevo correo desde el modal de Login (petición AJAX)
        $(document).on('click', '#reda_btn_enviar_nuevo_correo_login', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const correoActual = ($('#reda_login_email_no_verificado_texto').text() || '').trim();
            const nuevoCorreo = ($('#reda_login_input_nuevo_correo').val() || '').trim();

            $('#reda_login_alerta_error').addClass('d-none');
            $('#reda_login_alerta_exito').addClass('d-none');

            if (!esCorreoValido(nuevoCorreo)) {
                $('#reda_login_alerta_error_texto').text(
                    trans('Por favor ingrese una dirección de correo válida.', 'Por favor ingrese una dirección de correo válida.')
                );
                $('#reda_login_alerta_error').removeClass('d-none');
                return;
            }

            // Estado de carga en botón
            $btn.attr('disabled', true);
            $btn.find('.btn-text').addClass('d-none');
            $btn.find('.spinner').removeClass('d-none');

            // Llamada AJAX hacia el endpoint de Reda
            $.ajax({
                url: datosServidor.rutaActualizarCorreo,
                type: 'POST',
                data: {
                    _token: datosServidor.csrfToken || $('meta[name="csrf-token"]').attr('content'),
                    correo_actual: correoActual,
                    nuevo_correo: nuevoCorreo
                },
                dataType: 'json',
                success: function(respuesta) {
                    $btn.attr('disabled', false);
                    $btn.find('.btn-text').removeClass('d-none');
                    $btn.find('.spinner').addClass('d-none');

                    if (respuesta && respuesta.success) {
                        const emailActualizado = (respuesta.respuesta && respuesta.respuesta.nuevo_correo) ? respuesta.respuesta.nuevo_correo : nuevoCorreo;

                        // Actualizar textos en el modal y en el formulario de login
                        $('#reda_login_email_no_verificado_texto').text(emailActualizado);
                        $('#reda_login_email_no_verificado_subtexto').text(emailActualizado);
                        if ($('#login_form input[name="email"]').length) {
                            $('#login_form input[name="email"]').val(emailActualizado);
                        }

                        $('#reda_login_alerta_exito_texto').text(
                            respuesta.mensaje_usuario || trans('Se ha enviado un nuevo enlace de verificación a su correo.', 'Se ha enviado un nuevo enlace de verificación a su correo.')
                        );
                        $('#reda_login_alerta_exito').removeClass('d-none');

                        // Opcionalmente regresar al paso 1 después de 2.5 segundos
                        setTimeout(function() {
                            $('#reda_login_paso_formulario').addClass('d-none');
                            $('#reda_login_paso_aviso').removeClass('d-none');
                        }, 2500);

                    } else {
                        const mensajeError = respuesta.mensaje_usuario || trans('Ocurrió un error al procesar la solicitud.', 'Ocurrió un error al procesar la solicitud.');
                        $('#reda_login_alerta_error_texto').text(mensajeError);
                        $('#reda_login_alerta_error').removeClass('d-none');
                    }
                },
                error: function(xhr) {
                    $btn.attr('disabled', false);
                    $btn.find('.btn-text').removeClass('d-none');
                    $btn.find('.spinner').addClass('d-none');

                    let mensajeServidor = trans('Error en el servidor al intentar actualizar el correo.', 'Error en el servidor al intentar actualizar el correo.');
                    try {
                        const errorJson = JSON.parse(xhr.responseText);
                        if (errorJson.mensaje_usuario) {
                            mensajeServidor = errorJson.mensaje_usuario;
                        } else if (errorJson.message) {
                            mensajeServidor = errorJson.message;
                        }
                    } catch (e) {
                        // Error de parseo, usar mensaje por defecto
                    }

                    $('#reda_login_alerta_error_texto').text(mensajeServidor);
                    $('#reda_login_alerta_error').removeClass('d-none');
                }
            });
        });

        // ----------------------------------------------------------------------
        // 3. FLUJO TRAS CONFIRMAR CORREO: MODAL DE ÉXITO
        // ----------------------------------------------------------------------
        if (datosServidor.correoConfirmadoExitoso) {
            // Rellenar el email en el formulario de login si está disponible
            if (datosServidor.emailConfirmado && $('#login_form input[name="email"]').length) {
                $('#login_form input[name="email"]').val(datosServidor.emailConfirmado);
            }

            $('#reda_modal_correo_confirmado_exito').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
        }

        // Botón: "Iniciar sesión" en el modal de éxito
        $(document).on('click', '#reda_btn_iniciar_sesion_exito', function(e) {
            e.preventDefault();
            $('#reda_modal_correo_confirmado_exito').modal('hide');

            // Si estamos en la página de login, enfocar el campo de password
            if ($('#login_form').length) {
                setTimeout(function() {
                    const $pass = $('#login_form input[name="password"]');
                    if ($pass.length) {
                        $pass.focus();
                    }
                }, 300);
            } else {
                // Si está en otra vista, redirigir a login
                window.location.href = datosServidor.urlLogin || (APP_URL + '/login');
            }
        });
    }

    // Inicializar al cargar el DOM
    $(document).ready(function() {
        inicializarVerificacionCorreo();
    });

})(jQuery);
