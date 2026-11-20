/**
 * Archivo: packages/Reda/RedaAlojamiento/resources/js/vistas/pago/frontend/pagos.js
 * 
 * Descripción:
 * Gestiona los formularios de selección de pago (#checkout-form) y confirmación (#payment-form).
 * Incluye la validación estricta del lado del cliente cuando aplica la carga de comprobante de pago
 * (adjunto) y mensaje obligatorio al anfitrión (ej. pasarela de Transferencia Bancaria Directa).
 * 
 * Evita recargas innecesarias de la página si el usuario olvida escribir el mensaje al anfitrión o
 * adjuntar el comprobante, previniendo la pérdida del archivo ya seleccionado en el input de archivo.
 * Muestra mensajes de error en español bajo cada campo y gestiona la animación de espera al procesar.
 * Carga las opciones en ambos mensajes y actualiza país y método de pago sin alterar textos personalizados.
 * 
 * Funciones contenidas:
 * - cargarPlantillas($campo, $boton): Carga opciones para el campo indicado y conserva texto personalizado.
 * - obtenerContenedorError($campo, claseEspecifica): Localiza o genera el contenedor para el mensaje de error.
 * - restaurarBotonSubmit(): Restablece el estado activo del botón de envío y oculta el spinner en fallos de validación.
 * - validarFormularioPago(): Comprueba que el comprobante y el mensaje al anfitrión estén presentes antes de enviar.
 */

(function( $ ) {
    "use strict";

    /**
     * Carga opciones y actualiza sus variables de pago sin alterar textos personalizados.
     * Se utiliza tanto en la reserva inicial como en el formulario de comprobante.
     * @param {jQuery} $campo Campo de mensaje.
     * @param {jQuery} $boton Botón de envío del formulario correspondiente.
     */
    const cargarPlantillas = ($campo, $boton) => {
        if (!$campo.length) return;
        const traducciones = window.RedaAlojamientoJson || {};
        const $aviso = $('<div class="text-danger mt-2" role="alert"></div>');
        const $etiqueta = $('<label class="d-block mt-2" for="reda-textos-predefinidos"></label>')
            .text(traducciones['Otros textos predefinidos'] || 'Otros textos predefinidos');
        const $selector = $('<select class="form-control mb-2" id="reda-textos-predefinidos"></select>');
        const $personalizado = $('<option value="personalizado"></option>')
            .text(traducciones['Personalizado'] || 'Personalizado');
        const $formulario = $campo.closest('form');
        const esReservaInicial = $formulario.is('#checkout-form');
        const $pais = $formulario.find('select[name="payment_country"]');
        const $metodoPago = $formulario.find('select[name="payment_method"]');
        const noSeleccionado = traducciones['No seleccionado'] || 'No seleccionado';
        $campo.before($etiqueta, $selector);
        $campo.after($aviso);
        $selector.prop('disabled', true);
        const botonEstabaDeshabilitado = $boton.prop('disabled');
        $boton.prop('disabled', true);
        let textoPersonalizado = $campo.val() || '';
        if (window.RedaNotificaciones) window.RedaNotificaciones.esperar();
        $.ajax({
            url: APP_URL + '/reda/pago/plantillas',
            type: 'GET',
            dataType: 'json',
            data: { pantalla: esReservaInicial ? 'reserva' : 'confirmacion' },
            success: function (datos) {
                if (!datos.success || !Array.isArray(datos.respuesta)) {
                    $aviso.text(datos.mensaje_usuario || traducciones['No se pudieron cargar los textos predefinidos.'] || 'No se pudieron cargar los textos predefinidos.');
                    $selector.empty().append($personalizado).val('personalizado');
                    return;
                }
                datos.respuesta.forEach((opcion) => {
                    const pais = esReservaInicial ? ($pais.find(':selected').text().trim() || noSeleccionado) : opcion.pais;
                    const metodo = esReservaInicial ? ($metodoPago.find(':selected').text().trim() || noSeleccionado) : opcion.metodo_pago;
                    const texto = opcion.texto_con_variables_pago
                        .split('{pais}').join(pais)
                        .split('{metodo_pago}').join(metodo);
                    $selector.append($('<option></option>').val(String(opcion.id)).text(opcion.nombre)
                        .data('plantilla', opcion.texto_con_variables_pago).data('texto', texto));
                });
                $selector.append($personalizado);
                textoPersonalizado = $campo.val() || '';
                const $coincidente = $selector.find('option').filter(function () {
                    return $(this).data('texto') === textoPersonalizado && textoPersonalizado !== '';
                }).first();
                if ($coincidente.length) {
                    $selector.val($coincidente.val());
                } else if (textoPersonalizado) {
                    $selector.val('personalizado');
                } else if (datos.respuesta.length) {
                    $campo.val($selector.find(':selected').data('texto'));
                } else {
                    $selector.val('personalizado');
                }
            },
            error: function (error) {
                const respuesta = error.responseJSON || {};
                $aviso.text(respuesta.mensaje_usuario || traducciones['No se pudieron cargar los textos predefinidos.'] || 'No se pudieron cargar los textos predefinidos.');
                $selector.empty().append($personalizado).val('personalizado');
            },
            complete: function () {
                $selector.prop('disabled', false);
                $boton.prop('disabled', botonEstabaDeshabilitado);
                if (window.RedaNotificaciones) window.RedaNotificaciones.ocultar();
            }
        });
        $selector.on('change', function () {
            const valor = $(this).val();
            $campo.val(valor === 'personalizado' ? textoPersonalizado : $(this).find(':selected').data('texto')).trigger('input');
        });
        $campo.on('input', function () {
            const textoActual = $(this).val();
            const $opcion = $selector.find(':selected');
            if ($opcion.val() === 'personalizado' || textoActual !== $opcion.data('texto')) {
                textoPersonalizado = textoActual;
                $selector.val('personalizado');
            }
        });
        $pais.add($metodoPago).on('change', function () {
            const valorPais = $pais.find(':selected').text().trim() || noSeleccionado;
            const valorMetodo = $metodoPago.find(':selected').text().trim() || noSeleccionado;
            const $seleccionada = $selector.find(':selected');
            const actualizarCampo = $seleccionada.val() !== 'personalizado' && $campo.val() === $seleccionada.data('texto');
            $selector.find('option').each(function () {
                const $opcion = $(this);
                const plantilla = $opcion.data('plantilla');
                if (typeof plantilla === 'string') {
                    $opcion.data('texto', plantilla.split('{pais}').join(valorPais).split('{metodo_pago}').join(valorMetodo));
                }
            });
            if (actualizarCampo) $campo.val($seleccionada.data('texto'));
        });
    };

    $(function () {
        const $reserva = $('#checkout-form');
        if ($reserva.length) {
            cargarPlantillas($reserva.find('textarea[name="message_to_host"]'), $reserva.find('#payment-form-submit'));
        }
    });

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

            if ($inputAttachment.length) cargarPlantillas($textareaNote, $btnSubmit);

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
             * Valida la presencia del comprobante y la longitud del mensaje al anfitrión.
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
                    const notaDemasiadoLarga = Array.from(textoNota).length > 10000;
                    const $errorNote = obtenerContenedorError($textareaNote, 'reda-error-note');

                    if (!tieneNota || notaDemasiadoLarga) {
                        esValido = false;
                        const mensajeError = notaDemasiadoLarga
                            ? (window.RedaAlojamientoJson["El mensaje no puede superar 10000 caracteres."] || "El mensaje no puede superar 10000 caracteres.")
                            : (window.RedaAlojamientoJson["El mensaje al anfitrión es obligatorio."] ||
                               window.RedaAlojamientoJson["Por favor escriba un mensaje para el anfitrión."] ||
                               "El mensaje al anfitrión es obligatorio.");
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
                    if ($(this).val().trim().length > 0 && Array.from($(this).val().trim()).length <= 10000) {
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
