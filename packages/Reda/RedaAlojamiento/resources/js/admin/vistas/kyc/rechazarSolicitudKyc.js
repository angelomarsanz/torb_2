/**
 * Resumen: Acción administrativa "rechazar solicitud KYC". El motivo es obligatorio (el servidor lo exige) y se
 * comunica al usuario; la observación interna es opcional.
 */
import { peticionAdminKyc } from './peticionAdminKyc.js';

/**
 * @param {string} url          Ruta de rechazo.
 * @param {string} motivo       Motivo comunicado al usuario.
 * @param {string} observacion  Observación interna opcional.
 */
export const rechazarSolicitudKyc = (url, motivo, observacion = '') => {
    return peticionAdminKyc(url, { motivo, observacion }, window.RedaAlojamientoJson["Error rechazando la solicitud"] || 'Error rechazando la solicitud');
};
