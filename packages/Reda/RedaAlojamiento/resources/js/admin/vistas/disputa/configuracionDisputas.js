/**
 * Resumen: Controlador JavaScript para la vista de configuración de mediaciones en el panel administrativo.
 * Gestiona la validación del lado del cliente y el guardado asíncrono vía AJAX de las cantidades permitidas
 * para el primer aviso y para el segundo aviso y suspensión de cuenta, interactuando con la tabla settings
 * mediante la estructura estándar de respuestas y notificaciones de REDA.
 */

(function( $ ) {
    "use strict";

    const formId = '#form-configuracion-mediaciones';

    if ($(formId).length) {
        console.log('Script "configuracionDisputas" cargado correctamente.');

        $(function() {
            const trans = window.RedaAlojamientoJson || {};

            const $form = $(formId);
            const $btnGuardar = $('#btn-guardar-config-mediaciones');
            const $btnText = $btnGuardar.find('.btn-text');
            const $spinner = $btnGuardar.find('.spinner-guardar');
            const $inputPrimerAviso = $('#input-primer-aviso');
            const $inputSegundoAviso = $('#input-segundo-aviso');

            /**
             * Activa el estado de carga visual en el botón de guardar.
             */
            const activarCargando = () => {
                $btnGuardar.prop('disabled', true);
                $spinner.removeClass('d-none');
                if (window.RedaNotificaciones && typeof window.RedaNotificaciones.esperar === 'function') {
                    window.RedaNotificaciones.esperar();
                }
            };

            /**
             * Desactiva el estado de carga visual en el botón de guardar.
             */
            const desactivarCargando = () => {
                $btnGuardar.prop('disabled', false);
                $spinner.addClass('d-none');
                if (window.RedaNotificaciones && typeof window.RedaNotificaciones.ocultar === 'function') {
                    window.RedaNotificaciones.ocultar();
                }
            };

            /**
             * Envía los datos de configuración mediante petición AJAX al servidor.
             * 
             * @param {number} primerAviso 
             * @param {number} segundoAviso 
             * @param {string} urlAction 
             * @param {string} token 
             * @returns {Promise<object>}
             */
            const guardarConfiguracionAjax = (primerAviso, segundoAviso, urlAction, token) => {
                return new Promise((resolve) => {
                    $.ajax({
                        url: urlAction,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            _token: token,
                            primer_aviso: primerAviso,
                            segundo_aviso: segundoAviso
                        },
                        success: function(data) {
                            resolve(data);
                        },
                        error: function(x) {
                            let respuestaServidor = {};
                            try {
                                respuestaServidor = JSON.parse(x.responseText);
                            } catch (e) {
                                respuestaServidor = {};
                            }

                            const mensajeErrorBase = trans["Error en el servidor"] || "Error en el servidor de Torbian";
                            const detalleError = respuestaServidor.message ? `<br>${respuestaServidor.message}` : '';

                            const respuesta = {
                                success: false,
                                message: trans["Error al guardar la configuración"] || "Error al guardar la configuración",
                                mensaje_usuario: respuestaServidor.mensaje_usuario || `${mensajeErrorBase}.${detalleError}`,
                                respuesta: respuestaServidor.respuesta || '',
                                code: x.status !== 0 ? x.status : 500
                            };
                            resolve(respuesta);
                        }
                    });
                });
            };

            // Escuchar el evento submit del formulario
            $form.on('submit', async function(e) {
                e.preventDefault();

                const valPrimerAviso = $inputPrimerAviso.val().trim();
                const valSegundoAviso = $inputSegundoAviso.val().trim();

                // Validación 1: Campos vacíos o no numéricos
                if (valPrimerAviso === '' || isNaN(valPrimerAviso) || parseInt(valPrimerAviso, 10) < 0) {
                    const msg = trans["Ingrese una cantidad válida para el primer aviso (número mayor o igual a 0)."] 
                        || "Ingrese una cantidad válida para el primer aviso (número mayor o igual a 0).";
                    if (window.RedaNotificaciones && typeof window.RedaNotificaciones.notificar === 'function') {
                        window.RedaNotificaciones.notificar(trans["Validación"] || "Validación", msg, 'error');
                    } else {
                        alert(msg);
                    }
                    $inputPrimerAviso.focus();
                    return;
                }

                if (valSegundoAviso === '' || isNaN(valSegundoAviso) || parseInt(valSegundoAviso, 10) < 0) {
                    const msg = trans["Ingrese una cantidad válida para el segundo aviso y suspensión (número mayor o igual a 0)."] 
                        || "Ingrese una cantidad válida para el segundo aviso y suspensión (número mayor o igual a 0).";
                    if (window.RedaNotificaciones && typeof window.RedaNotificaciones.notificar === 'function') {
                        window.RedaNotificaciones.notificar(trans["Validación"] || "Validación", msg, 'error');
                    } else {
                        alert(msg);
                    }
                    $inputSegundoAviso.focus();
                    return;
                }

                const intPrimerAviso = parseInt(valPrimerAviso, 10);
                const intSegundoAviso = parseInt(valSegundoAviso, 10);

                // Advertencia lógica opcional: el segundo aviso no debería ser inferior al primer aviso
                if (intSegundoAviso < intPrimerAviso) {
                    const msg = trans["La cantidad para segundo aviso debe ser mayor o igual a la cantidad del primer aviso."] 
                        || "La cantidad para segundo aviso y suspensión debe ser mayor o igual a la cantidad del primer aviso.";
                    if (window.RedaNotificaciones && typeof window.RedaNotificaciones.notificar === 'function') {
                        window.RedaNotificaciones.notificar(trans["Advertencia"] || "Advertencia", msg, 'error');
                    } else {
                        alert(msg);
                    }
                    $inputSegundoAviso.focus();
                    return;
                }

                const urlAction = $form.attr('action');
                const token = $('input[name="_token"]').val() || $('meta[name="csrf-token"]').attr('content');

                activarCargando();

                try {
                    const respuesta = await guardarConfiguracionAjax(intPrimerAviso, intSegundoAviso, urlAction, token);
                    desactivarCargando();

                    if (respuesta.success) {
                        const tituloExito = trans["¡Éxito!"] || "¡Éxito!";
                        const mensajeExito = respuesta.mensaje_usuario || trans["Configuración de mediaciones guardada con éxito."] || "Configuración de mediaciones guardada con éxito.";
                        if (window.RedaNotificaciones && typeof window.RedaNotificaciones.notificar === 'function') {
                            window.RedaNotificaciones.notificar(tituloExito, mensajeExito, 'exito');
                        } else {
                            alert(mensajeExito);
                        }
                    } else {
                        const tituloError = trans["Error"] || "Error";
                        const mensajeError = respuesta.mensaje_usuario || trans["Ocurrió un error al guardar la configuración."] || "Ocurrió un error al guardar la configuración.";
                        if (window.RedaNotificaciones && typeof window.RedaNotificaciones.notificar === 'function') {
                            window.RedaNotificaciones.notificar(tituloError, mensajeError, 'error');
                        } else {
                            alert(mensajeError);
                        }
                    }
                } catch (err) {
                    desactivarCargando();
                    console.error("Error al procesar la solicitud de guardado de configuración:", err);
                    const msgInesperado = trans["Ocurrió un error inesperado al procesar la solicitud."] || "Ocurrió un error inesperado al procesar la solicitud.";
                    if (window.RedaNotificaciones && typeof window.RedaNotificaciones.notificar === 'function') {
                        window.RedaNotificaciones.notificar(trans["Error"] || "Error", msgInesperado, 'error');
                    } else {
                        alert(msgInesperado);
                    }
                }
            });
        });
    }
})(jQuery);
