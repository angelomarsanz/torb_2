/**
 * Resumen: Consulta al servidor el estado KYC del usuario autenticado (GET /kyc/estado). El servidor también
 * sincroniza con Didit cuando hay una verificación abierta. No incluye datos personales.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Resources\Js\Vistas\Frontend\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

import { peticionUsuarioKyc } from './peticionUsuarioKyc.js';
import { t } from './utilidadesKyc.js';

/**
 * Obtiene el resumen del estado de verificación.
 *
 * @returns {Promise<{success:boolean,message:string,mensaje_usuario:string,respuesta:any,code:number}>}
 */
export const consultarEstadoKyc = () => {
    return peticionUsuarioKyc({
        url: APP_URL + '/kyc/estado',
        tipo: 'GET',
        mensajeFallback: t('Error consultando el estado de la verificación'),
    });
};
