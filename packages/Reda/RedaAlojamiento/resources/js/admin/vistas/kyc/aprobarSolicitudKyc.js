/**
 * Resumen: Acción administrativa "aprobar solicitud KYC". Envía la observación opcional al servidor, que verifica
 * el permiso manage_kyc, el estado de la solicitud y los documentos antes de aprobar.
 */
import { peticionAdminKyc } from './peticionAdminKyc.js';

/**
 * @param {string} url          Ruta de aprobación.
 * @param {string} observacion  Observación interna opcional.
 */
export const aprobarSolicitudKyc = (url, observacion = '') => {
    return peticionAdminKyc(url, { observacion }, window.RedaAlojamientoJson["Error aprobando la solicitud"] || 'Error aprobando la solicitud');
};
