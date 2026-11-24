/**
 * Resumen: Interfaz KYC del usuario. En la pantalla "Verificaciones" (users/edit-verification) inyecta, sin
 * modificar la vista original, la tarjeta "Verificación de identidad" con el estado real del usuario y las
 * acciones que le corresponden:
 *  - Verificar con Didit: pide la sesión al servidor y lleva al usuario a Didit (el resultado se decide en el
 *    servidor por el webhook o la consulta a la API, nunca por la URL de regreso).
 *  - Enviar documentos para revisión manual: abre el formulario con los datos personales y los documentos.
 *  - Corregir la solicitud cuando un administrador lo pide (formulario precargado).
 *  - Actualizar el estado: consulta de nuevo; mientras la verificación está abierta se actualiza sola cada 20 s
 *    (solo con la pestaña visible y con un tope de tiempo) para no cargar el servidor.
 * El bloqueo de publicar y reservar lo aplica el servidor; esta interfaz solo guía al usuario.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Resources\Js\Vistas\Frontend\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

import { consultarEstadoKyc } from './consultarEstadoKyc.js';
import { iniciarVerificacionDiditKyc } from './iniciarVerificacionDiditKyc.js';
import { obtenerOpcionesManualKyc } from './obtenerOpcionesManualKyc.js';
import { obtenerSolicitudCorreccionKyc } from './obtenerSolicitudCorreccionKyc.js';
import { abrirFormularioManualKyc } from './formularioManualKyc.js';
import { renderizarTarjetaKyc, renderizarCargandoKyc, renderizarErrorKyc } from './renderizarTarjetaKyc.js';
import { t, mensajeSeguro } from './utilidadesKyc.js';

(function ($) {
    "use strict";

    const ESTADOS_ABIERTOS = ['pending', 'in_progress'];
    const INTERVALO_SONDEO_MS = 20000;
    const MAXIMO_SONDEOS = 45;

    /**
     * Pausa para dejar que termine la animación de cierre del modal de espera antes de abrir otro modal.
     *
     * @param {number} milisegundos
     * @returns {Promise<void>}
     */
    const pausar = (milisegundos) => new Promise((resolver) => setTimeout(resolver, milisegundos));

    $(function () {
        if (!window.AuthCheck || !$('.edit-verifications-list').length || $('#reda-kyc-contenedor').length) {
            return;
        }

        // La tarjeta se coloca antes de las verificaciones actuales (correo, Facebook, Google).
        const $fila = $('.edit-verifications-list').first().closest('.row');
        if (!$fila.length) {
            return;
        }

        const $contenedor = $('<div class="col-md-12 p-0 mb-4" id="reda-kyc-contenedor" aria-live="polite"></div>');
        $fila.prepend($contenedor);

        let resumenActual = null;
        let ocupado = false;
        let temporizador = null;
        let sondeos = 0;

        /**
         * Dibuja la tarjeta con el resumen recibido.
         *
         * @param {Object} resumen
         */
        const dibujar = (resumen) => {
            resumenActual = resumen;
            $contenedor.html(renderizarTarjetaKyc(resumen));
            programarSondeo();
        };

        /**
         * Consulta el estado y actualiza la tarjeta. Si falla, muestra el error con opción de reintentar.
         *
         * @returns {Promise<boolean>} true si se obtuvo el estado.
         */
        const cargarEstado = async () => {
            const respuesta = await consultarEstadoKyc();

            if (respuesta.success && respuesta.respuesta && typeof respuesta.respuesta === 'object') {
                if (JSON.stringify(respuesta.respuesta) !== JSON.stringify(resumenActual)) {
                    dibujar(respuesta.respuesta);
                } else {
                    programarSondeo();
                }
                return true;
            }

            $contenedor.html(renderizarErrorKyc($('<div>').html(mensajeSeguro(respuesta.mensaje_usuario)).text()));
            return false;
        };

        /**
         * Mientras la verificación esté abierta, vuelve a consultar cada 20 segundos (con tope de tiempo).
         */
        const programarSondeo = () => {
            clearTimeout(temporizador);

            if (!resumenActual || !ESTADOS_ABIERTOS.includes(resumenActual.estado) || sondeos >= MAXIMO_SONDEOS) {
                return;
            }

            temporizador = setTimeout(async () => {
                if (document.hidden || ocupado) {
                    programarSondeo();
                    return;
                }
                sondeos++;
                await cargarEstado();
            }, INTERVALO_SONDEO_MS);
        };

        /**
         * Muestra un error con el modal de notificaciones.
         *
         * @param {Object} respuesta Respuesta estándar del servidor.
         */
        const mostrarError = (respuesta) => {
            window.RedaNotificaciones.notificar(t('Error'), mensajeSeguro(respuesta.mensaje_usuario), 'error');
        };

        /**
         * Lleva al usuario a Didit.
         */
        const iniciarDidit = async () => {
            window.RedaNotificaciones.esperar();
            const respuesta = await iniciarVerificacionDiditKyc();

            const url = respuesta.success && respuesta.respuesta ? String(respuesta.respuesta.url || '') : '';
            if (url.startsWith('https://')) {
                window.location.href = url;
                return;
            }

            if (respuesta.success) {
                respuesta.mensaje_usuario = t('No pudimos abrir la verificación de Didit. Inténtalo de nuevo.');
            }
            mostrarError(respuesta);
            await cargarEstado();
        };

        /**
         * Abre el formulario manual (nuevo o de corrección) tras cargar sus opciones.
         *
         * @param {boolean} esCorreccion
         */
        const abrirManual = async (esCorreccion) => {
            window.RedaNotificaciones.esperar();

            const [opciones, correccion] = await Promise.all([
                obtenerOpcionesManualKyc(),
                esCorreccion ? obtenerSolicitudCorreccionKyc() : Promise.resolve(null),
            ]);

            if (!opciones.success) {
                mostrarError(opciones);
                return;
            }

            if (esCorreccion && !correccion.success) {
                mostrarError(correccion);
                await cargarEstado();
                return;
            }

            window.RedaNotificaciones.ocultar();
            await pausar(550);
            abrirFormularioManualKyc({
                opciones: opciones.respuesta,
                correccion: esCorreccion ? correccion.respuesta : null,
            });
        };

        $contenedor.on('click', '[data-kyc-accion]', async function (evento) {
            evento.preventDefault();

            if (ocupado) {
                return;
            }
            ocupado = true;

            try {
                switch ($(this).data('kyc-accion')) {
                    case 'didit':
                        await iniciarDidit();
                        break;
                    case 'manual':
                        await abrirManual(false);
                        break;
                    case 'corregir':
                        await abrirManual(true);
                        break;
                    case 'actualizar':
                        window.RedaNotificaciones.esperar();
                        sondeos = 0;
                        await cargarEstado();
                        window.RedaNotificaciones.ocultar();
                        break;
                }
            } finally {
                ocupado = false;
            }
        });

        // Al volver a la pestaña se actualiza enseguida si la verificación sigue abierta.
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden && resumenActual && ESTADOS_ABIERTOS.includes(resumenActual.estado) && !ocupado) {
                cargarEstado();
            }
        });

        $contenedor.html(renderizarCargandoKyc());
        cargarEstado();
    });
})(jQuery);
