/**
 * Resumen: Envía la solicitud de verificación manual con sus datos personales y documentos (multipart).
 * Con esCorreccion = true reenvía los datos y archivos pedidos por un administrador
 * (POST /kyc/manual/correccion); en caso contrario crea la solicitud (POST /kyc/manual).
 * El servidor es quien valida todo; el formulario solo adelanta las comprobaciones básicas.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Resources\Js\Vistas\Frontend\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

import { peticionUsuarioKyc } from './peticionUsuarioKyc.js';
import { t } from './utilidadesKyc.js';

/**
 * Envía el formulario manual.
 *
 * @param {FormData} datosFormulario Campos y archivos del formulario.
 * @param {boolean}  esCorreccion    Indica si se atiende una corrección solicitada.
 * @returns {Promise<{success:boolean,message:string,mensaje_usuario:string,respuesta:any,code:number}>}
 */
export const enviarSolicitudManualKyc = (datosFormulario, esCorreccion = false) => {
    return peticionUsuarioKyc({
        url: APP_URL + (esCorreccion ? '/kyc/manual/correccion' : '/kyc/manual'),
        tipo: 'POST',
        datos: datosFormulario,
        mensajeFallback: esCorreccion
            ? t('Error enviando la corrección de la solicitud')
            : t('Error enviando la solicitud de verificación manual'),
    });
};
