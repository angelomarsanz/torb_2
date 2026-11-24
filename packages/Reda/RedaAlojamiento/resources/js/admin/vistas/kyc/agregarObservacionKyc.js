/**
 * Resumen: Acción administrativa "agregar observación interna" a una solicitud KYC (el usuario nunca la ve).
 */
import { peticionAdminKyc } from './peticionAdminKyc.js';

/**
 * @param {string} url          Ruta de observaciones.
 * @param {string} observacion  Texto de la observación.
 */
export const agregarObservacionKyc = (url, observacion) => {
    return peticionAdminKyc(url, { observacion }, window.RedaAlojamientoJson["Error guardando la observación"] || 'Error guardando la observación');
};
