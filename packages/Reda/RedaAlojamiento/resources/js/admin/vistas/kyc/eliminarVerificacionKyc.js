/**
 * Resumen: Acción administrativa "eliminar verificación KYC". Borra los datos, documentos y solicitudes del usuario
 * y lo deja sin verificar para que pueda verificarse de nuevo. El motivo es obligatorio (el servidor lo exige) y no
 * se comunica al usuario; queda en la auditoría junto con el administrador y la IP.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Resources\Js\Admin\Vistas\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */
import { peticionAdminKyc } from './peticionAdminKyc.js';

/**
 * @param {string} url     Ruta de eliminación de la solicitud.
 * @param {string} motivo  Motivo de la eliminación (de 5 a 500 caracteres).
 * @returns {Promise<{success:boolean,message:string,mensaje_usuario:string,respuesta:any,code:number}>}
 */
export const eliminarVerificacionKyc = (url, motivo) => {
    return peticionAdminKyc(url, { motivo }, window.RedaAlojamientoJson["Error eliminando la verificación"] || 'Error eliminando la verificación');
};
