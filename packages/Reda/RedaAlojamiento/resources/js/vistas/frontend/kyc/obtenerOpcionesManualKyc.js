/**
 * Resumen: Obtiene las opciones del formulario de verificación manual (GET /kyc/manual/opciones): tipos de
 * documento con los archivos que exige cada uno, país predeterminado, países admitidos, edad mínima y límites
 * de los archivos. Así el formulario siempre usa las mismas reglas que valida el servidor.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Resources\Js\Vistas\Frontend\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

import { peticionUsuarioKyc } from './peticionUsuarioKyc.js';
import { t } from './utilidadesKyc.js';

/**
 * Consulta las opciones del formulario manual.
 *
 * @returns {Promise<{success:boolean,message:string,mensaje_usuario:string,respuesta:any,code:number}>}
 */
export const obtenerOpcionesManualKyc = () => {
    return peticionUsuarioKyc({
        url: APP_URL + '/kyc/manual/opciones',
        tipo: 'GET',
        mensajeFallback: t('Error obteniendo las opciones del formulario'),
    });
};
