/**
 * Archivo: packages/Reda/RedaAlojamiento/resources/js/vistas/pago/frontend/pagos.js
 * 
 * Descripción:
 * Gestiona el comportamiento en el cliente del formulario de confirmación de pago (#payment-form).
 * Incluye la validación estricta del lado del cliente cuando aplica la carga de comprobante de pago
 * (adjunto) y mensaje obligatorio al anfitrión (ej. pasarela de Transferencia Bancaria Directa).
 * 
 * Evita recargas innecesarias de la página si el usuario olvida escribir el mensaje al anfitrión o
 * adjuntar el comprobante, previniendo la pérdida del archivo ya seleccionado en el input de archivo.
 * Muestra mensajes de error en español bajo cada campo y gestiona la animación de espera al procesar.
 * 
 * Funciones contenidas:
 * - obtenerContenedorError($campo, claseEspecifica): Localiza o genera el contenedor para el mensaje de error.
 * - restaurarBotonSubmit(): Restablece el estado activo del botón de envío y oculta el spinner en fallos de validación.
 * - validarFormularioPago(): Comprueba que el comprobante y el mensaje al anfitrión estén presentes antes de enviar.
 */

(function( $ ) {
    "use strict";

    const formId = '#payment-form';

    if ($(formId).length) {
        $(function() {
            const $form = $(formId);
            const formEl = $form[0];
            const $btnSubmit = $('#payment-form-submit');
            const btnSubmitEl = $btnSubmit.length ? $btnSubmit[0] : null;

            // Desactivar la validación HTML5 nativa del navegador para utilizar validación estilizada con mensajes en español
            $form.attr('novalidate', 'novalidate');

            // Selectores para comprobante (adjunto) y mensaje al anfitrión (nota)
            const $inputAttachment = $form.find('input[name="attachment[]"], input[type="file"][name*="attachment"]');
            const $textareaNote = $form.find('textarea[name="note"], [name="note"]');

            /**
             * Obtiene o crea el elemento contenedor para mostrar el mensaje de error.
             * Reutiliza el span.text-danger original de la plantilla Blade si está presente en la misma celda.
             *
             * @param {jQuery} $campo - Elemento del DOM del campo a validar.
             * @param {string} claseEspecifica - Clase CSS única para identificar el contenedor de error.
             * @returns {jQuery} Elemento contenedor del mensaje de error.
             */
            const obtenerContenedorError = ($campo, claseEspecifica) => {
                let $error = $campo.siblings('.' + claseEspecifica);
                if (!$error.length) {
                    $error = $campo.closest('td').find('.' + claseEspecifica);
                }
                if (!$error.length) {
                    const $spanOriginal = $campo.closest('td').find('span.text-danger');
                    if ($spanOriginal.length) {
                        $spanOriginal.addClass(claseEspecifica);
                        $error = $spanOriginal;
                    } else {
                        $error = $('<span class="text-danger ' + claseEspecifica + ' d-block mt-1"></span>');
                        $campo.after($error);
                    }
                }
                return $error;
            };

            /**
             * Restaura el estado visual y funcional del botón de confirmación y el spinner
             * en caso de que la validación del formulario falle.
             */
            const restaurarBotonSubmit = () => {
                if ($btnSubmit.length) {
                    $btnSubmit.prop('disabled', false);
                    $btnSubmit.removeAttr('disabled');
                    $btnSubmit.find('.spinner').addClass('d-none');
                }
                $('.spinner').addClass('d-none');
                $form.data('modal-mostrado', false);
                if (window.RedaNotificaciones && typeof window.RedaNotificaciones.ocultar === 'function') {
                    window.RedaNotificaciones.ocultar();
                }
            };

            /**
             * Valida en el cliente la presencia del comprobante de pago y el mensaje al anfitrión.
             * Si falta alguno de los datos obligatorios, previene el envío, resalta los campos con error
             * y muestra los mensajes explicativos sin recargar la página ni perder archivos seleccionados.
             *
             * @returns {boolean} True si el formulario es válido o si los campos no aplican; False en caso contrario.
             */
            const validarFormularioPago = () => {
                const aplicaComprobante = $inputAttachment.length > 0;
                const aplicaNota = $textareaNote.length > 0;

                // Si no aplican estos campos en la pasarela actual, permitir flujo estándar
                if (!aplicaComprobante && !aplicaNota) {
                    return true;
                }

                let esValido = true;
                let $primerInvalido = null;

                // 1. Validación de Comprobante / Adjunto de pago
                if (aplicaComprobante) {
                    const inputEl = $inputAttachment[0];
                    const tieneArchivo = Boolean(inputEl && inputEl.files && inputEl.files.length > 0);
                    const $errorAttachment = obtenerContenedorError($inputAttachment, 'reda-error-attachment');

                    if (!tieneArchivo) {
                        esValido = false;
                        const mensajeError = window.RedaAlojamientoJson["El comprobante de pago es obligatorio."] || 
                                             window.RedaAlojamientoJson["Por favor adjunte el comprobante de pago."] || 
                                             "El comprobante de pago es obligatorio.";
                        $errorAttachment.text(mensajeError).show();
                        $inputAttachment.addClass('is-invalid').css('border-color', '#dc3545');
                        if (!$primerInvalido) {
                            $primerInvalido = $inputAttachment;
                        }
                    } else {
                        $errorAttachment.text('').hide();
                        $inputAttachment.removeClass('is-invalid').css('border-color', '');
                    }
                }

                // 2. Validación de Mensaje al Anfitrión
                if (aplicaNota) {
                    const textoNota = ($textareaNote.val() || '').trim();
                    const tieneNota = textoNota.length > 0;
                    const $errorNote = obtenerContenedorError($textareaNote, 'reda-error-note');

                    if (!tieneNota) {
                        esValido = false;
                        const mensajeError = window.RedaAlojamientoJson["El mensaje al anfitrión es obligatorio."] || 
                                             window.RedaAlojamientoJson["Por favor escriba un mensaje para el anfitrión."] || 
                                             "El mensaje al anfitrión es obligatorio.";
                        $errorNote.text(mensajeError).show();
                        $textareaNote.addClass('is-invalid').css('border-color', '#dc3545');
                        if (!$primerInvalido) {
                            $primerInvalido = $textareaNote;
                        }
                    } else {
                        $errorNote.text('').hide();
                        $textareaNote.removeClass('is-invalid').css('border-color', '');
                    }
                }

                // Si hay campos inválidos, enfocar y hacer scroll suave al primero de ellos
                if (!esValido && $primerInvalido && $primerInvalido.length) {
                    $primerInvalido.focus();
                    $('html, body').animate({
                        scrollTop: Math.max(0, $primerInvalido.offset().top - 140)
                    }, 300);
                }

                return esValido;
            };

            // Limpieza reactiva en tiempo real al seleccionar un archivo en el input
            if ($inputAttachment.length) {
                $inputAttachment.on('change', function() {
                    if (this.files && this.files.length > 0) {
                        const $err = obtenerContenedorError($inputAttachment, 'reda-error-attachment');
                        $err.text('').hide();
                        $inputAttachment.removeClass('is-invalid').css('border-color', '');
                    }
                });
            }

            // Limpieza reactiva en tiempo real al escribir en el campo de texto del mensaje
            if ($textareaNote.length) {
                $textareaNote.on('input keyup change', function() {
                    if ($(this).val().trim().length > 0) {
                        const $err = obtenerContenedorError($textareaNote, 'reda-error-note');
                        $err.text('').hide();
                        $textareaNote.removeClass('is-invalid').css('border-color', '');
                    }
                });
            }

            // Intercepción prioritaria en fase de captura del botón de submit (antes de que actúen scripts de terceros como initial.js)
            if (btnSubmitEl) {
                btnSubmitEl.addEventListener('click', function(e) {
                    if (!validarFormularioPago()) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        restaurarBotonSubmit();
                        return false;
                    }
                }, true); // true = Capture Phase
            }

            // Intercepción prioritaria en fase de captura del submit del formulario
            if (formEl) {
                formEl.addEventListener('submit', function(e) {
                    if (!validarFormularioPago()) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        restaurarBotonSubmit();
                        return false;
                    }
                }, true); // true = Capture Phase
            }

            // Intercepción jQuery delegada para click en el botón de confirmación
            $form.on('click', '#payment-form-submit', function(e) {
                if (!validarFormularioPago()) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    restaurarBotonSubmit();
                    return false;
                }
            });

            // Manejador del evento submit en jQuery para mostrar la animación de espera solo si el formulario es válido
            $form.on('submit', function(e) {
                if (!validarFormularioPago()) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    restaurarBotonSubmit();
                    return false;
                }

                // Si algo canceló el envío previamente, no continuar
                if (e.isDefaultPrevented()) {
                    return;
                }

                // Evitamos que el modal se muestre dos veces o bloquee reintentos legítimos
                if ($form.data('modal-mostrado') === true) {
                    return;
                }

                $form.data('modal-mostrado', true);

                if (window.RedaNotificaciones && typeof window.RedaNotificaciones.esperar === 'function') {
                    // Usamos un pequeño delay para asegurar que el submit del navegador ya inició
                    setTimeout(() => {
                        window.RedaNotificaciones.esperar();
                        
                        const $modal = $('#modal-notificacion');
                        $modal.addClass('modal-procesando');

                        const mensajeProcesando = window.RedaAlojamientoJson["Su reserva está siendo procesada"] || "Su reserva está siendo procesada";
                        $('#notificacion-mensaje').text(mensajeProcesando);
                    }, 50);
                }
            });
        });
    }
})(jQuery);
