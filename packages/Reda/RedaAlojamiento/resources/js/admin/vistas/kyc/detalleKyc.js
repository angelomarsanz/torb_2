/**
 * Resumen: Interactividad del detalle administrativo de una solicitud KYC (#reda-kyc-detalle).
 * Gestiona las acciones del administrador: aprobar, rechazar (motivo obligatorio), solicitar corrección, agregar
 * una observación interna, suspender o reanudar la eliminación automática de documentos, mostrar el número de
 * documento completo y eliminar la verificación del usuario (motivo obligatorio y confirmación explícita; al
 * terminar lleva al listado de eliminadas, porque la solicitud ya no existe). Cada petición AJAX muestra la
 * animación de "Espera" hasta que responde el servidor.
 * El servidor es quien autoriza y valida; aquí solo se evitan envíos vacíos. Los textos del servidor se escapan antes
 * de mostrarlos.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Resources\Js\Admin\Vistas\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

import { aprobarSolicitudKyc } from './aprobarSolicitudKyc.js';
import { rechazarSolicitudKyc } from './rechazarSolicitudKyc.js';
import { solicitarCorreccionKyc } from './solicitarCorreccionKyc.js';
import { agregarObservacionKyc } from './agregarObservacionKyc.js';
import { alternarRetencionKyc } from './alternarRetencionKyc.js';
import { revelarNumeroDocumentoKyc } from './revelarNumeroDocumentoKyc.js';
import { eliminarVerificacionKyc } from './eliminarVerificacionKyc.js';

(function ($) {
    "use strict";

    const $vista = $('#reda-kyc-detalle');
    if (!$vista.length) return;

    $(function () {
        const t = (clave) => window.RedaAlojamientoJson[clave] || clave;
        const escapar = (texto) => $('<div>').text(texto == null ? '' : String(texto)).html();
        const esperar = () => window.RedaNotificaciones && window.RedaNotificaciones.esperar();

        /**
         * Muestra el resultado de una acción: éxito (y recarga la página) o error con el mensaje del servidor.
         *
         * @param {Object} r Respuesta estándar del servidor.
         */
        const mostrarResultado = (r) => {
            if (!window.RedaNotificaciones) return;
            if (r.success) {
                window.RedaNotificaciones.notificar(t('¡Éxito!'), escapar(r.mensaje_usuario || r.message), 'exito', true);
            } else {
                // Se conserva únicamente el salto de línea que agrega la petición ante un error de servidor.
                const mensaje = escapar(r.mensaje_usuario || r.message).replace(/&lt;br \/&gt;/g, '<br />');
                window.RedaNotificaciones.notificar(t('Error'), mensaje, 'error');
            }
        };

        /**
         * Cierra un modal de Bootstrap 5 y ejecuta la función cuando termina de ocultarse.
         *
         * @param {string}   selector
         * @param {Function} despues
         */
        const cerrarModalYEjecutar = (selector, despues) => {
            const el = document.querySelector(selector);
            const instancia = el && window.bootstrap && bootstrap.Modal ? bootstrap.Modal.getInstance(el) : null;
            if (instancia) {
                el.addEventListener('hidden.bs.modal', () => despues(), { once: true });
                instancia.hide();
            } else {
                despues();
            }
        };

        // Aprobar
        $vista.on('click', '#btn-aprobar-kyc', async function () {
            if (!window.confirm(t('¿Aprobar esta solicitud? El usuario quedará con la identidad verificada.'))) return;
            esperar();
            mostrarResultado(await aprobarSolicitudKyc($vista.data('url-aprobar')));
        });

        // Rechazar (motivo obligatorio)
        $vista.on('click', '#btn-confirmar-rechazo-kyc', function () {
            const motivo = ($('#kyc-motivo-rechazo').val() || '').trim();
            const $error = $('#error-motivo-rechazo');
            if (motivo.length < 5) {
                $error.text(t('El motivo del rechazo es obligatorio (entre 5 y 1000 caracteres).'));
                return;
            }
            $error.text('');
            cerrarModalYEjecutar('#modal-rechazar-kyc', async () => {
                esperar();
                mostrarResultado(await rechazarSolicitudKyc($vista.data('url-rechazar'), motivo));
            });
        });

        // Solicitar corrección
        $vista.on('click', '#btn-confirmar-correccion-kyc', function () {
            const texto = ($('#kyc-texto-correccion').val() || '').trim();
            const $error = $('#error-texto-correccion');
            if (texto.length < 5) {
                $error.text(t('Indica qué debe corregir el usuario (entre 5 y 1000 caracteres).'));
                return;
            }
            $error.text('');
            cerrarModalYEjecutar('#modal-correccion-kyc', async () => {
                esperar();
                mostrarResultado(await solicitarCorreccionKyc($vista.data('url-correccion'), texto));
            });
        });

        // Observación interna
        $vista.on('click', '#btn-observacion-kyc', async function () {
            const texto = ($('#kyc-nueva-observacion').val() || '').trim();
            if (texto.length < 2) {
                $('#kyc-nueva-observacion').addClass('is-invalid').trigger('focus');
                return;
            }
            $('#kyc-nueva-observacion').removeClass('is-invalid');
            esperar();
            mostrarResultado(await agregarObservacionKyc($vista.data('url-observacion'), texto));
        });

        // Suspender / reanudar la eliminación automática de documentos
        $vista.on('click', '#btn-retencion-kyc', async function () {
            const suspender = String($(this).data('suspender')) === '1';
            const aviso = suspender
                ? t('¿Suspender la eliminación automática de los documentos de esta solicitud?')
                : t('¿Reanudar la eliminación automática de los documentos de esta solicitud?');
            if (!window.confirm(aviso)) return;
            esperar();
            mostrarResultado(await alternarRetencionKyc($vista.data('url-retencion'), suspender));
        });

        // Eliminar la verificación (motivo obligatorio y confirmación). La solicitud deja de existir: se va al listado.
        $vista.on('click', '#btn-confirmar-eliminacion-kyc', function () {
            const motivo = ($('#kyc-motivo-eliminacion').val() || '').trim();
            const confirmado = $('#kyc-confirmar-eliminacion').is(':checked');
            const $errorMotivo = $('#error-motivo-eliminacion');
            const $errorConfirmar = $('#error-confirmar-eliminacion');
            $errorMotivo.text('');
            $errorConfirmar.text('');
            if (motivo.length < 5) {
                $errorMotivo.text(t('El motivo de la eliminación es obligatorio (entre 5 y 500 caracteres).'));
                return;
            }
            if (!confirmado) {
                $errorConfirmar.text(t('Marca la casilla para confirmar la eliminación.'));
                return;
            }
            cerrarModalYEjecutar('#modal-eliminar-kyc', async () => {
                esperar();
                const r = await eliminarVerificacionKyc($vista.data('url-eliminar'), motivo);
                if (r.success && window.RedaNotificaciones) {
                    window.RedaNotificaciones.notificar(t('¡Éxito!'), escapar(r.mensaje_usuario || r.message), 'exito', String($vista.data('url-eliminadas')));
                } else {
                    mostrarResultado(r);
                }
            });
        });

        // Mostrar el número de documento completo (queda auditado)
        $vista.on('click', '#btn-revelar-documento', async function () {
            const $boton = $(this);
            esperar();
            const r = await revelarNumeroDocumentoKyc($vista.data('url-revelar'));
            if (window.RedaNotificaciones) window.RedaNotificaciones.ocultar();
            if (r.success && r.respuesta && r.respuesta.numero_documento) {
                $('#kyc-numero-documento').text(r.respuesta.numero_documento);
                $boton.remove();
            } else {
                mostrarResultado(r);
            }
        });
    });
})(jQuery);
