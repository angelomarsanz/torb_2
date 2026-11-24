/**
 * Resumen: Utilidades compartidas de la interfaz KYC del usuario (verificación de identidad):
 * traducción con respaldo en español, escape de HTML y conversión segura de mensajes del servidor.
 * Todo texto que proviene del servidor o del usuario debe pasar por escapar() antes de insertarse en el DOM.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Resources\Js\Vistas\Frontend\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

/**
 * Traduce una clave con el archivo es.json del plugin; si no existe, devuelve el respaldo (o la propia clave).
 *
 * @param {string} clave    Texto en español usado como clave.
 * @param {string} respaldo Texto alternativo si no hay traducción.
 * @returns {string}
 */
export const t = (clave, respaldo) => {
    const traducciones = window.RedaAlojamientoJson || {};
    return traducciones[clave] || respaldo || clave;
};

/**
 * Escapa un valor para insertarlo como texto o como valor de atributo en HTML.
 *
 * @param {*} valor
 * @returns {string}
 */
export const escapar = (valor) => {
    return String(valor === null || valor === undefined ? '' : valor)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
};

/**
 * Prepara un mensaje del servidor para mostrarlo en una notificación: escapa todo el HTML y conserva
 * únicamente los saltos de línea <br /> que agregan las respuestas de error de las peticiones.
 *
 * @param {string} mensaje
 * @returns {string}
 */
export const mensajeSeguro = (mensaje) => {
    return escapar(mensaje).replace(/&lt;br \/&gt;/g, '<br />');
};

/**
 * Convierte un tamaño en bytes a un texto legible (KB o MB).
 *
 * @param {number} bytes
 * @returns {string}
 */
export const formatearTamano = (bytes) => {
    if (bytes >= 1048576) {
        return (bytes / 1048576).toFixed(1).replace('.', ',') + ' MB';
    }
    return Math.max(1, Math.round(bytes / 1024)) + ' KB';
};
