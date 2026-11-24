/**
 * Resumen: Petición AJAX común de la interfaz KYC del usuario. Devuelve siempre una promesa que se resuelve con
 * la estructura estándar del plugin {success, message, mensaje_usuario, respuesta, code}, tanto si el servidor
 * responde bien como si falla (red, sesión vencida, límite de intentos, archivos demasiado grandes o error 5xx),
 * de modo que quien llama no necesita manejar excepciones. Admite envío normal y multipart (archivos).
 * La animación de "Espera" la muestra quien invoca la función.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Resources\Js\Vistas\Frontend\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

import { t } from './utilidadesKyc.js';

/**
 * Mensaje para el usuario según el código HTTP cuando el servidor no envía uno propio.
 *
 * @param {number} codigo
 * @returns {string|null}
 */
const mensajePorCodigo = (codigo) => {
    switch (codigo) {
        case 401:
        case 419:
            return t('Tu sesión expiró. Recarga la página e inténtalo de nuevo.');
        case 413:
            return t('Los archivos superan el tamaño permitido por el servidor. Reduce su tamaño e inténtalo de nuevo.');
        case 429:
            return t('Demasiados intentos. Espera un momento e inténtalo de nuevo.');
        default:
            return null;
    }
};

/**
 * Envía una petición al servidor.
 *
 * @param {Object}   opciones
 * @param {string}   opciones.url              Ruta completa.
 * @param {string}   [opciones.tipo='GET']     Método HTTP.
 * @param {Object|FormData} [opciones.datos]   Campos (con FormData se envía como multipart).
 * @param {string}   opciones.mensajeFallback  Mensaje corto para soporte si el servidor no responde.
 * @returns {Promise<{success:boolean,message:string,mensaje_usuario:string,respuesta:any,code:number}>}
 */
export const peticionUsuarioKyc = ({ url, tipo = 'GET', datos = null, mensajeFallback }) => {
    return new Promise((resolve) => {
        (function ($) {
            const token = $('meta[name="csrf-token"]').attr('content');
            const opciones = {
                url: url,
                type: tipo,
                dataType: 'json',
                cache: false,
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

                    const codigo = x.status !== 0 ? x.status : 504;
                    const mensajeErrorBase = t('Error en el servidor de Torbian');
                    const detalleError = respuestaServidor.message ? `<br />${respuestaServidor.message}` : '';

                    resolve({
                        'success': false,
                        'message': mensajeFallback,
                        'mensaje_usuario': respuestaServidor.mensaje_usuario || mensajePorCodigo(codigo) || `${mensajeErrorBase}.${detalleError}`,
                        'respuesta': respuestaServidor.respuesta || '',
                        'code': codigo,
                    });
                }
            };

            if (datos instanceof FormData) {
                datos.append('_token', token);
                opciones.data = datos;
                opciones.processData = false;
                opciones.contentType = false;
            } else if (tipo !== 'GET') {
                opciones.data = Object.assign({ '_token': token }, datos || {});
            }

            $.ajax(opciones);
        })(jQuery);
    });
};
