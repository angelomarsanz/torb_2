/**
 * Resumen: Función AJAX para obtener el conteo de mensajes no leídos.
 * Cumple con las directrices de promesas, animaciones de espera y manejo de errores de REDA.
 */

/**
 * Realiza una petición al servidor para obtener el número de mensajes no leídos del usuario.
 * @param {boolean} mostrarLoader - Indica si se debe mostrar la animación de espera.
 * @returns {Promise<Object>} Promesa con la respuesta estandarizada del servidor.
 */
export const obtenerConteoNoLeidos = (mostrarLoader = true) => {
    return new Promise((resolve) => {
        (function( $ ) {
            "use strict";

            if (mostrarLoader && window.RedaNotificaciones) {
                window.RedaNotificaciones.esperar();
            }

            $.ajax({
                url: APP_URL + '/reda/messaging/unread-count',
                type: 'GET',
                success: function(data) {
                    if (mostrarLoader && window.RedaNotificaciones) {
                        window.RedaNotificaciones.ocultar();
                    }
                    resolve(data);
                },
                error: function (x) {
                    if (mostrarLoader && window.RedaNotificaciones) {
                        window.RedaNotificaciones.ocultar();
                    }

                    let respuestaServidor = {};
                    try {
                        respuestaServidor = JSON.parse(x.responseText);
                    } catch (e) {
                        respuestaServidor = {};
                    }

                    const mensajeErrorBase = window.RedaAlojamientoJson["Error en el servidor de Torbian"] || 'Error en el servidor de Torbian';
                    
                    let respuesta = {
                        'success': false,
                        'message' : window.RedaAlojamientoJson["Error al obtener mensajes no leídos"] || 'Error al obtener mensajes no leídos',
                        'mensaje_usuario': respuestaServidor.mensaje_usuario ?? mensajeErrorBase,
                        'respuesta': { count: 0 },
                        'code': x.status !== 0 ? x.status : 504,
                    };
                    resolve(respuesta);
                }
            });
        })(jQuery);
    });
};
