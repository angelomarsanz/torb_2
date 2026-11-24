/**
 * Resumen: Petición AJAX común de las acciones administrativas del KYC (POST con token CSRF). Devuelve siempre
 * una promesa que se resuelve con la estructura estándar del plugin {success, message, mensaje_usuario, respuesta,
 * code}, tanto si el servidor responde bien como si falla (error de red, 4xx, 5xx), para que quien llama no
 * necesite manejar excepciones. La animación de "Espera" la muestra quien invoca (detalleKyc.js).
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Resources\Js\Admin\Vistas\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

/**
 * Envía una acción al servidor.
 *
 * @param {string} url              Ruta completa de la acción.
 * @param {Object} datos            Campos a enviar (el token CSRF se agrega solo).
 * @param {string} mensajeFallback  Mensaje corto para soporte si el servidor no responde.
 * @returns {Promise<{success:boolean,message:string,mensaje_usuario:string,respuesta:any,code:number}>}
 */
export const peticionAdminKyc = (url, datos, mensajeFallback) => {
    return new Promise((resolve) => {
        (function ($) {
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: Object.assign({ '_token': $('meta[name="csrf-token"]').attr('content') }, datos),
                success: function (data) {
                    resolve(data);
                },
                error: function (x) {
                    let respuestaServidor = {};
                    try {
                        respuestaServidor = JSON.parse(x.responseText);
                    } catch (e) {
                        respuestaServidor = {};
                    }

                    const mensajeErrorBase = window.RedaAlojamientoJson["Error en el servidor de Torbian"] || 'Error en el servidor de Torbian';
                    const detalleError = respuestaServidor.message ? `<br />${respuestaServidor.message}` : '';

                    resolve({
                        'success': false,
                        'message': mensajeFallback,
                        'mensaje_usuario': respuestaServidor.mensaje_usuario || `${mensajeErrorBase}.${detalleError}`,
                        'respuesta': respuestaServidor.respuesta || '',
                        'code': x.status !== 0 ? x.status : 504,
                    });
                }
            });
        })(jQuery);
    });
};
