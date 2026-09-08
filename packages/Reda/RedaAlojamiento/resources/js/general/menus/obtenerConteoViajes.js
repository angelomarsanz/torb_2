/**
 * Resumen: Función AJAX para obtener el conteo de reservaciones activas (viajes).
 * Sigue la estructura estandarizada del plugin REDA.
 */

/**
 * Realiza una petición al servidor para obtener la cantidad de viajes activos del usuario.
 * @returns {Promise<Object>} Promesa con la respuesta estandarizada del servidor.
 */
export const obtenerConteoViajes = () => {
    return new Promise((resolve) => {
        (function( $ ) {
            "use strict";

            $.ajax({
                url: APP_URL + '/reda/bookings/count-active',
                type: 'GET',
                success: function(data) {
                    resolve(data);
                },
                error: function (x) {
                    let respuestaServidor = {};
                    try {
                        respuestaServidor = JSON.parse(x.responseText);
                    } catch (e) {
                        respuestaServidor = {};
                    }

                    let respuesta = {
                        'success': false,
                        'message' : 'Error al obtener conteo de viajes',
                        'mensaje_usuario': respuestaServidor.mensaje_usuario ?? '',
                        'respuesta': 0,
                        'code': x.status !== 0 ? x.status : 504,
                    };
                    resolve(respuesta);
                }
            });
        })(jQuery);
    });
};
