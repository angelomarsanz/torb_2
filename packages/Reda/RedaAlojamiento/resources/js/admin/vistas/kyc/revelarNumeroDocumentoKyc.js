/**
 * Resumen: Acción administrativa "mostrar el número de documento completo" de una solicitud KYC. El servidor
 * registra cada revelación en la auditoría con el administrador y la IP.
 */
import { peticionAdminKyc } from './peticionAdminKyc.js';

/**
 * @param {string} url  Ruta de revelación.
 */
export const revelarNumeroDocumentoKyc = (url) => {
    return peticionAdminKyc(url, {}, window.RedaAlojamientoJson["Error mostrando el número de documento"] || 'Error mostrando el número de documento');
};
