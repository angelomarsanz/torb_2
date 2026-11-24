/**
 * Resumen: Inicia (o retoma) la verificación automática con Didit (POST /kyc/didit/iniciar). Si sale bien,
 * la respuesta trae en "respuesta.url" la dirección de Didit a la que se debe llevar al usuario.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Resources\Js\Vistas\Frontend\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

import { peticionUsuarioKyc } from './peticionUsuarioKyc.js';
import { t } from './utilidadesKyc.js';

/**
 * Solicita la sesión de verificación de Didit.
 *
 * @returns {Promise<{success:boolean,message:string,mensaje_usuario:string,respuesta:any,code:number}>}
 */
export const iniciarVerificacionDiditKyc = () => {
    return peticionUsuarioKyc({
        url: APP_URL + '/kyc/didit/iniciar',
        tipo: 'POST',
        mensajeFallback: t('Error iniciando la verificación con Didit'),
    });
};
