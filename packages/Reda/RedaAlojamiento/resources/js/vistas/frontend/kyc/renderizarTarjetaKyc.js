/**
 * Resumen: Genera el HTML de la tarjeta "Verificación de identidad" que se inyecta en la pantalla
 * "Verificaciones" del usuario (users/edit-verification). Según el estado KYC que informa el servidor muestra:
 * verificado, solicitud en revisión, corrección pendiente, verificación de Didit abierta o en revisión, rechazo
 * (con el motivo), fallo, expiración o aún sin verificar, y los botones de acción que correspondan.
 * Solo construye texto: los eventos los maneja verificacionIdentidad.js mediante los atributos data-kyc-accion.
 * Todo valor del servidor se escapa; el motivo y la corrección escritos por el administrador conservan sus saltos
 * de línea mediante CSS (white-space: pre-wrap), nunca como HTML.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Resources\Js\Vistas\Frontend\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

import { t, escapar } from './utilidadesKyc.js';

/**
 * Nombre legible de cada documento cargado.
 *
 * @param {string} tipo frente | reverso | selfie
 * @returns {string}
 */
const etiquetaDocumento = (tipo) => {
    switch (tipo) {
        case 'frente': return t('Frente del documento');
        case 'reverso': return t('Reverso del documento');
        case 'selfie': return t('Selfie con el documento');
        default: return tipo;
    }
};

/**
 * Botón de opción grande (se ve bien con el dedo en el celular).
 *
 * @param {string} accion    Valor de data-kyc-accion.
 * @param {string} icono     Clase de Font Awesome.
 * @param {string} titulo    Texto principal.
 * @param {string} detalle   Texto de apoyo.
 * @param {boolean} principal Resalta la opción recomendada.
 * @returns {string}
 */
const botonOpcion = (accion, icono, titulo, detalle, principal = false) => `
    <button type="button" class="reda-kyc-opcion ${principal ? 'reda-kyc-opcion--principal' : ''}" data-kyc-accion="${escapar(accion)}">
        <span class="reda-kyc-opcion__icono"><i class="fa ${escapar(icono)}" aria-hidden="true"></i></span>
        <span class="reda-kyc-opcion__texto">
            <strong>${escapar(titulo)}</strong>
            <small>${escapar(detalle)}</small>
        </span>
        <i class="fa fa-angle-right reda-kyc-opcion__flecha" aria-hidden="true"></i>
    </button>`;

/**
 * Opción "Verificar con Didit" (automática).
 *
 * @param {boolean} continuar Si ya hay una verificación abierta, se retoma.
 * @returns {string}
 */
const opcionDidit = (continuar = false) => botonOpcion(
    'didit',
    'fa-bolt',
    continuar ? t('Continuar la verificación con Didit') : t('Verificar ahora con Didit'),
    t('Es lo más rápido: fotografías tu documento y te tomas una selfie. El resultado suele llegar en minutos.'),
    true
);

/**
 * Opción "Enviar mis documentos" (manual).
 *
 * @returns {string}
 */
const opcionManual = () => botonOpcion(
    'manual',
    'fa-file-text-o',
    t('Enviar mis documentos para revisión manual'),
    t('Completas tus datos y subes las fotos de tus documentos. Una persona los revisará, por lo que puede tardar más.')
);

/**
 * Estructura común de la tarjeta (encabezado como el resto de la pantalla de verificaciones).
 *
 * @param {Object} datos
 * @param {string} datos.tono        success | info | warning | danger | secondary
 * @param {string} datos.icono       Clase de Font Awesome.
 * @param {string} datos.etiqueta    Texto de la insignia de estado.
 * @param {string} datos.titulo
 * @param {string} datos.descripcion
 * @param {string} datos.extra       HTML adicional ya escapado (motivo, documentos...).
 * @param {string} datos.acciones    HTML de los botones.
 * @returns {string}
 */
const tarjeta = ({ tono, icono, etiqueta, titulo, descripcion, extra = '', acciones = '' }) => `
    <div class="card card-default reda-kyc-tarjeta">
        <div class="main-panel border main-panelbg">
            <p class="p-2 pl-4 font-weight-700">${escapar(t('Verificación de identidad'))}</p>
        </div>
        <div class="reda-kyc-cuerpo">
            <div class="reda-kyc-estado reda-kyc-estado--${escapar(tono)}">
                <span class="reda-kyc-estado__icono"><i class="fa ${escapar(icono)}" aria-hidden="true"></i></span>
                <div class="reda-kyc-estado__contenido">
                    <span class="reda-kyc-insignia reda-kyc-insignia--${escapar(tono)}">${escapar(etiqueta)}</span>
                    <h4 class="reda-kyc-estado__titulo">${escapar(titulo)}</h4>
                    <p class="reda-kyc-estado__descripcion">${escapar(descripcion)}</p>
                </div>
            </div>
            ${extra}
            ${acciones ? `<div class="reda-kyc-opciones">${acciones}</div>` : ''}
            <p class="reda-kyc-privacidad">
                <i class="fa fa-lock" aria-hidden="true"></i>
                ${escapar(t('Tu información se usa solo para verificar tu identidad. Los documentos se guardan cifrados, solo los revisa el equipo autorizado y se eliminan automáticamente pasado un tiempo.'))}
            </p>
        </div>
    </div>`;

/**
 * Bloque con un texto escrito por un administrador (motivo de rechazo o corrección solicitada).
 *
 * @param {string} titulo
 * @param {string} texto
 * @param {string} tono
 * @returns {string}
 */
const citaAdministrador = (titulo, texto, tono) => `
    <div class="reda-kyc-cita reda-kyc-cita--${escapar(tono)}">
        <strong>${escapar(titulo)}</strong>
        <div class="reda-kyc-cita__texto">${escapar(texto)}</div>
    </div>`;

/**
 * Lista de documentos enviados (con enlace a la descarga protegida del propio usuario).
 *
 * @param {Array<{id:number,tipo:string}>} documentos
 * @returns {string}
 */
const listaDocumentos = (documentos) => {
    if (!Array.isArray(documentos) || documentos.length === 0) {
        return '';
    }

    const elementos = documentos.map((documento) => `
        <li>
            <i class="fa fa-check text-success" aria-hidden="true"></i>
            <span>${escapar(etiquetaDocumento(documento.tipo))}</span>
            <a href="${escapar(APP_URL + '/kyc/documentos/' + parseInt(documento.id, 10))}" target="_blank" rel="noopener noreferrer">${escapar(t('Ver'))}</a>
        </li>`).join('');

    return `
        <div class="reda-kyc-documentos">
            <strong>${escapar(t('Documentos enviados'))}</strong>
            <ul>${elementos}</ul>
        </div>`;
};

/**
 * Botón secundario "Actualizar estado".
 *
 * @returns {string}
 */
const botonActualizar = () => botonOpcion(
    'actualizar',
    'fa-refresh',
    t('Actualizar estado'),
    t('Comprueba si ya tenemos el resultado de tu verificación.')
);

/**
 * Tarjeta de carga mientras llega el estado.
 *
 * @returns {string}
 */
export const renderizarCargandoKyc = () => `
    <div class="card card-default reda-kyc-tarjeta">
        <div class="main-panel border main-panelbg">
            <p class="p-2 pl-4 font-weight-700">${escapar(t('Verificación de identidad'))}</p>
        </div>
        <div class="reda-kyc-cuerpo text-center text-muted py-4">
            <i class="fa fa-spinner fa-spin fa-2x" aria-hidden="true"></i>
            <p class="mb-0 mt-2">${escapar(t('Consultando tu estado de verificación...'))}</p>
        </div>
    </div>`;

/**
 * Tarjeta cuando no se pudo consultar el estado.
 *
 * @param {string} mensaje Mensaje ya seguro para mostrar (se escapa aquí).
 * @returns {string}
 */
export const renderizarErrorKyc = (mensaje) => tarjeta({
    tono: 'secondary',
    icono: 'fa-exclamation-circle',
    etiqueta: t('Sin conexión'),
    titulo: t('No pudimos consultar tu verificación'),
    descripcion: mensaje || t('Inténtalo de nuevo en unos segundos.'),
    acciones: botonActualizar(),
});

/**
 * Genera la tarjeta según el resumen del estado KYC.
 *
 * @param {Object} resumen Respuesta de GET /kyc/estado (campo "respuesta").
 * @returns {string}
 */
export const renderizarTarjetaKyc = (resumen) => {
    const r = resumen || {};
    const puedeDidit = !!r.puede_iniciar;
    const puedeManual = !!r.puede_solicitar_manual;
    const botones = (...opciones) => opciones.filter(Boolean).join('');

    if (r.estado === 'verified' && r.verificado) {
        return tarjeta({
            tono: 'success',
            icono: 'fa-check-circle',
            etiqueta: t('Identidad verificada'),
            titulo: t('Tu identidad está verificada'),
            descripcion: t('Ya puedes publicar alojamientos y negocios, y hacer reservas.'),
        });
    }

    switch (r.estado) {
        case 'manual_review':
            if (r.correccion_pendiente) {
                return tarjeta({
                    tono: 'warning',
                    icono: 'fa-pencil',
                    etiqueta: t('Corrección solicitada'),
                    titulo: t('Debes corregir tu solicitud'),
                    descripcion: t('Un administrador revisó tu solicitud y necesita que corrijas algo para poder continuar.'),
                    extra: citaAdministrador(t('Lo que debes corregir'), r.correccion_solicitada || '', 'warning') + listaDocumentos(r.documentos),
                    acciones: botonOpcion('corregir', 'fa-pencil', t('Corregir mi solicitud'), t('Actualiza los datos o reemplaza solo los documentos que se indican.'), true),
                });
            }
            return tarjeta({
                tono: 'info',
                icono: 'fa-clock-o',
                etiqueta: t('Solicitud pendiente de revisión'),
                titulo: t('Estamos revisando tus documentos'),
                descripcion: t('Recibimos tus datos y documentos. Una persona los revisará y verás el resultado aquí. No necesitas hacer nada más por ahora.'),
                extra: listaDocumentos(r.documentos),
            });

        case 'pending':
            return tarjeta({
                tono: 'info',
                icono: 'fa-clock-o',
                etiqueta: t('Verificación pendiente de revisión'),
                titulo: t('Tu verificación está en revisión'),
                descripcion: t('Didit recibió tu información y la está revisando. Puede tardar un poco; el resultado aparecerá aquí.'),
                acciones: botonActualizar(),
            });

        case 'in_progress':
            return tarjeta({
                tono: 'info',
                icono: 'fa-hourglass-half',
                etiqueta: t('Verificación en proceso'),
                titulo: t('Tu verificación aún no termina'),
                descripcion: t('Iniciaste la verificación con Didit. Puedes continuar donde la dejaste o actualizar el estado si ya la terminaste.'),
                acciones: botones(r.metodo === 'didit' ? opcionDidit(true) : '', botonActualizar(), puedeManual ? opcionManual() : ''),
            });

        case 'rejected':
            return tarjeta({
                tono: 'danger',
                icono: 'fa-times-circle',
                etiqueta: t('Solicitud rechazada'),
                titulo: t('Tu solicitud no fue aprobada'),
                descripcion: t('Puedes volver a intentarlo teniendo en cuenta el motivo indicado.'),
                extra: r.motivo_rechazo ? citaAdministrador(t('Motivo del rechazo'), r.motivo_rechazo, 'danger') : '',
                acciones: botones(puedeDidit ? opcionDidit() : '', puedeManual ? opcionManual() : ''),
            });

        case 'failed':
            return tarjeta({
                tono: 'danger',
                icono: 'fa-exclamation-triangle',
                etiqueta: t('Verificación automática fallida'),
                titulo: t('No pudimos completar la verificación automática'),
                descripcion: t('Puedes intentarlo de nuevo con Didit o enviar tus documentos para que una persona los revise.'),
                acciones: botones(puedeDidit ? opcionDidit() : '', puedeManual ? opcionManual() : ''),
            });

        case 'expired':
        case 'verified':
            return tarjeta({
                tono: 'warning',
                icono: 'fa-clock-o',
                etiqueta: t('Verificación expirada'),
                titulo: t('Tu verificación expiró'),
                descripcion: t('Necesitas verificar tu identidad de nuevo para publicar y reservar.'),
                acciones: botones(puedeDidit ? opcionDidit() : '', puedeManual ? opcionManual() : ''),
            });

        default:
            return tarjeta({
                tono: 'secondary',
                icono: 'fa-id-card-o',
                etiqueta: t('Sin verificar'),
                titulo: t('Verifica tu identidad'),
                descripcion: t('Para publicar alojamientos o negocios y hacer reservas necesitamos confirmar tu identidad. Elige cómo prefieres hacerlo.'),
                acciones: botones(puedeDidit ? opcionDidit() : '', puedeManual ? opcionManual() : ''),
            });
    }
};
