/**
 * Resumen: Obtiene los datos de la solicitud manual del propio usuario cuando un administrador pidió una
 * corrección (GET /kyc/manual/solicitud). Sirve para precargar el formulario. Si no hay corrección pendiente,
 * el servidor responde 404.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Resources\Js\Vistas\Frontend\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

import { peticionUsuarioKyc } from './peticionUsuarioKyc.js';
import { t } from './utilidadesKyc.js';

/**
 * Consulta la solicitud con corrección pendiente.
 *
 * @returns {Promise<{success:boolean,message:string,mensaje_usuario:string,respuesta:any,code:number}>}
 */
export const obtenerSolicitudCorreccionKyc = () => {
    return peticionUsuarioKyc({
        url: APP_URL + '/kyc/manual/solicitud',
        tipo: 'GET',
        mensajeFallback: t('Error obteniendo la solicitud con corrección pendiente'),
    });
};
