/**
 * Resumen: Acción administrativa "solicitar corrección KYC": indica al usuario qué datos o documentos debe corregir.
 */
import { peticionAdminKyc } from './peticionAdminKyc.js';

/**
 * @param {string} url         Ruta de la solicitud de corrección.
 * @param {string} correccion  Texto que verá el usuario.
 */
export const solicitarCorreccionKyc = (url, correccion) => {
    return peticionAdminKyc(url, { correccion }, window.RedaAlojamientoJson["Error solicitando la corrección"] || 'Error solicitando la corrección');
};
