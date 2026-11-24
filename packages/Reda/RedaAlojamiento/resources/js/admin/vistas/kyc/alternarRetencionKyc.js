/**
 * Resumen: Acción administrativa que suspende o reanuda la eliminación automática de los documentos de una
 * solicitud KYC (retención legal). No cambia el estado de verificación.
 */
import { peticionAdminKyc } from './peticionAdminKyc.js';

/**
 * @param {string}  url        Ruta de retención.
 * @param {boolean} suspender  true suspende la eliminación; false la reanuda.
 */
export const alternarRetencionKyc = (url, suspender) => {
    return peticionAdminKyc(url, { suspender: suspender ? 1 : 0 }, window.RedaAlojamientoJson["Error cambiando la retención"] || 'Error cambiando la retención');
};
