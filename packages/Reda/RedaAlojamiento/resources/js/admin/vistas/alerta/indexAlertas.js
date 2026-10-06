/**
 * Resumen: Controlador JavaScript para la vista de Alertas del Sistema en el panel administrativo.
 * Gestiona la carga asíncrona de alertas, paginación de 10 en 10, filtrado por estado (todas, no leídas, leídas),
 * marcado de alertas como leídas de forma individual o masiva, y la sincronización reactiva de los contadores
 * de insignias (badges) en la campanita del header y en el menú lateral.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Resources\Js\Admin\Vistas\Alerta
 * @author     REDA Tech Team
 * @version    1.0.0
 */

(function ($) {
    "use strict";

    const containerId = '#index_alertas_admin';

    if ($(containerId).length) {
        console.log('Script para "Index Alertas Admin" inicializado.');

        $(function () {
            const baseUrl = window.location.origin;
            let estadoActual = 'todos';
            let paginaActual = 1;

            /**
             * Carga el listado de alertas vía AJAX aplicando el filtro y la página solicitada.
             *
             * @param {number} pagina
             * @param {string} estado
             * @param {boolean} conLoader
             */
            const cargarAlertas = async (pagina = 1, estado = 'todos', conLoader = true) => {
                paginaActual = pagina;
                estadoActual = estado;

                if (conLoader && window.RedaNotificaciones && typeof window.RedaNotificaciones.esperar === 'function') {
                    window.RedaNotificaciones.esperar();
                }

                const url = `${baseUrl}/admin/reda/alertas/get-listado?page=${pagina}&estado=${estado}`;

                try {
                    const res = await $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: 'json'
                    });

                    if (res.success && res.respuesta) {
                        renderizarAlertas(res.respuesta.alertas);
                        renderizarPaginacion(res.respuesta.html_paginacion);
                        actualizarBadgesFiltro(res.respuesta.conteo_no_leidas);

                        // Disparar evento para sincronizar campanita y menú lateral
                        $(document).trigger('reda:actualizar-contador-alertas', {
                            conteo: res.respuesta.conteo_no_leidas
                        });
                    } else {
                        mostrarError(res.mensaje_usuario || window.RedaAlojamientoJson["Error al cargar las alertas"] || "Error al cargar las alertas");
                    }
                } catch (error) {
                    console.error("Error al obtener alertas:", error);
                    mostrarError(window.RedaAlojamientoJson["Error al cargar las alertas del servidor"] || "Error al cargar las alertas del servidor");
                } finally {
                    if (window.RedaNotificaciones && typeof window.RedaNotificaciones.ocultarEspera === 'function') {
                        window.RedaNotificaciones.ocultarEspera();
                    }
                }
            };

            /**
             * Renderiza las tarjetas de alertas en el contenedor principal.
             *
             * @param {Array} alertas
             */
            const renderizarAlertas = (alertas) => {
                const $container = $('#alertas-list-container');
                $container.empty();

                if (!alertas || alertas.length === 0) {
                    const textoVacio = window.RedaAlojamientoJson["No hay alertas para mostrar en este momento."] || "No hay alertas para mostrar en este momento.";
                    $container.html(`
                        <div class="card border rounded-3 shadow-sm bg-white p-5 text-center">
                            <i class="far fa-bell-slash fa-3x text-muted mb-3"></i>
                            <h5 class="fw-semibold text-muted">${textoVacio}</h5>
                        </div>
                    `);
                    return;
                }

                alertas.forEach((alerta) => {
                    const esLeida = Boolean(alerta.leido);
                    const bgClass = esLeida ? 'bg-white' : 'bg-light border-start border-4 border-warning shadow-sm';
                    
                    // Icono temático
                    let iconoHtml = '<i class="fa fa-bell text-primary f-20"></i>';
                    let badgeTipoHtml = `<span class="badge bg-secondary">${window.RedaAlojamientoJson["General"] || "General"}</span>`;

                    if (alerta.tipo === 'suspension') {
                        iconoHtml = '<i class="fa fa-ban text-danger f-22"></i>';
                        badgeTipoHtml = `<span class="badge bg-danger">${window.RedaAlojamientoJson["Suspensión de cuenta"] || "Suspensión de cuenta"}</span>`;
                    } else if (alerta.tipo === 'primer_aviso') {
                        iconoHtml = '<i class="fa fa-exclamation-triangle text-warning f-22"></i>';
                        badgeTipoHtml = `<span class="badge bg-warning text-dark">${window.RedaAlojamientoJson["Primer aviso preventivo"] || "Primer aviso preventivo"}</span>`;
                    }

                    // Botón para marcar como leída
                    const labelMarcarLeida = window.RedaAlojamientoJson["Marcar como leída"] || "Marcar como leída";
                    const labelLeida = window.RedaAlojamientoJson["Leída"] || "Leída";
                    const btnMarcarHtml = !esLeida ? `
                        <button type="button" class="btn btn-outline-success btn-xs btn-marcar-alerta-leida py-1 px-2 f-12" data-id="${alerta.id}">
                            <i class="fa fa-check me-1"></i> <span>${labelMarcarLeida}</span>
                            <i class="fa fa-spinner fa-spin ms-1 d-none spinner-leida"></i>
                        </button>
                    ` : `
                        <span class="badge bg-success-subtle text-success border border-success f-11 py-1 px-2">
                            <i class="fa fa-check-double me-1"></i> ${labelLeida}
                        </span>
                    `;

                    // Enlace a la mediación si existe disputa_id
                    const labelVerMediacion = window.RedaAlojamientoJson["Ver mediación"] || "Ver mediación";
                    const linkMediacionHtml = alerta.disputa_id ? `
                        <a href="${baseUrl}/admin/reda/disputas" class="btn btn-outline-primary btn-xs py-1 px-2 f-12 btn-ir-mediaciones ms-2">
                            <i class="fa fa-external-link-alt me-1"></i> <span>${labelVerMediacion} #${alerta.disputa_id}</span>
                        </a>
                    ` : '';

                    // Datos adicionales del usuario involucrado
                    let infoUsuarioHtml = '';
                    if (alerta.usuario_nombre) {
                        infoUsuarioHtml = `
                            <div class="mt-2 text-muted f-12">
                                <i class="fa fa-user me-1"></i> <strong>${alerta.usuario_nombre}</strong> (${alerta.usuario_email || ''})
                            </div>
                        `;
                    }

                    const cursorStyle = !esLeida ? 'style="cursor: pointer;"' : '';

                    const tarjetaHtml = `
                        <div class="card rounded-3 mb-3 ${bgClass} alerta-card-item" id="alerta-card-${alerta.id}" ${cursorStyle}>
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start">
                                    <div class="me-3 mt-1 pt-1">
                                        ${iconoHtml}
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-1">
                                            <div class="d-flex align-items-center gap-2">
                                                <h6 class="fw-bold mb-0 text-dark f-15">${alerta.titulo || ''}</h6>
                                                ${badgeTipoHtml}
                                            </div>
                                            <span class="text-muted f-12 ms-auto" title="${alerta.fecha}">
                                                <i class="far fa-clock me-1"></i>${alerta.fecha_humana || alerta.fecha}
                                            </span>
                                        </div>

                                        <p class="text-secondary f-13 mb-2 mt-1 lh-sm">
                                            ${alerta.mensaje || ''}
                                        </p>

                                        ${infoUsuarioHtml}

                                        <div class="d-flex justify-content-end align-items-center mt-3 pt-2 border-top">
                                            ${linkMediacionHtml}
                                            <div class="contenedor-accion-leida ms-2">
                                                ${btnMarcarHtml}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    $container.append(tarjetaHtml);
                });
            };

            /**
             * Inyecta el bloque HTML de paginación de 10 en 10.
             *
             * @param {string} htmlPaginacion
             */
            const renderizarPaginacion = (htmlPaginacion) => {
                $('#alertas-pagination-container').html(htmlPaginacion || '');
            };

            /**
             * Actualiza el badge del filtro de no leídas.
             *
             * @param {number} conteo
             */
            const actualizarBadgesFiltro = (conteo) => {
                const $badge = $('#badge-conteo-filtro-no-leidas');
                if (conteo > 0) {
                    $badge.text(conteo).removeClass('d-none');
                } else {
                    $badge.addClass('d-none');
                }
            };

            /**
             * Muestra mensaje de error en el contenedor de listado.
             *
             * @param {string} mensaje
             */
            const mostrarError = (mensaje) => {
                $('#alertas-list-container').html(`
                    <div class="alert alert-danger shadow-sm rounded-3 p-3 text-center">
                        <i class="fa fa-exclamation-circle me-2"></i> ${mensaje}
                    </div>
                `);
            };

            // -------------------------------------------------------------
            // MANEJADORES DE EVENTOS
            // -------------------------------------------------------------

            // 1. Filtrar por estado (Todas / No leídas / Leídas)
            $(document).on('click', '.btn-filtro-alerta', function (e) {
                e.preventDefault();
                $('.btn-filtro-alerta').removeClass('active');
                $(this).addClass('active');

                const nuevoEstado = $(this).data('estado');
                cargarAlertas(1, nuevoEstado, true);
            });

            // 2. Navegación por las páginas de la paginación (10 en 10)
            $(document).on('click', '#alertas-pagination-container a.page-link', function (e) {
                e.preventDefault();
                const url = $(this).attr('href');
                if (!url || url === '#' || $(this).closest('li').hasClass('disabled') || $(this).closest('li').hasClass('active')) {
                    return;
                }

                // Extraer el número de página de la URL
                const match = url.match(/page=(\d+)/);
                const pageNum = match ? parseInt(match[1]) : 1;

                cargarAlertas(pageNum, estadoActual, true);
            });

            // 3. Marcar alerta individual como leída vía AJAX
            $(document).on('click', '.btn-marcar-alerta-leida', async function (e) {
                e.preventDefault();
                const $btn = $(this);
                const alertaId = $btn.data('id');
                const $spinner = $btn.find('.spinner-leida');

                $btn.prop('disabled', true);
                $spinner.removeClass('d-none');

                try {
                    const res = await $.ajax({
                        url: `${baseUrl}/admin/reda/alertas/${alertaId}/marcar-leida`,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    if (res.success && res.respuesta) {
                        // Transformar tarjeta en el DOM
                        const $card = $(`#alerta-card-${alertaId}`);
                        $card.removeClass('bg-light border-start border-4 border-warning shadow-sm')
                             .addClass('bg-white');

                        const labelLeida = window.RedaAlojamientoJson["Leída"] || "Leída";
                        $card.find('.contenedor-accion-leida').html(`
                            <span class="badge bg-success-subtle text-success border border-success f-11 py-1 px-2">
                                <i class="fa fa-check-double me-1"></i> ${labelLeida}
                            </span>
                        `);

                        const nuevoConteo = res.respuesta.conteo_no_leidas;
                        actualizarBadgesFiltro(nuevoConteo);

                        // Notificar globalmente a los contadores de la campanita y menú
                        $(document).trigger('reda:actualizar-contador-alertas', {
                            conteo: nuevoConteo
                        });

                        // Si estamos filtrando solo por "no_leidas", recargar la lista
                        if (estadoActual === 'no_leidas') {
                            cargarAlertas(paginaActual, estadoActual, false);
                        }
                    }
                } catch (error) {
                    console.error("Error al marcar alerta como leída:", error);
                } finally {
                    $btn.prop('disabled', false);
                    $spinner.addClass('d-none');
                }
            });

            // 3b. Al hacer clic en cualquier parte de una tarjeta no leída, marcarla automáticamente como leída
            $(document).on('click', '.alerta-card-item.bg-light', function (e) {
                if ($(e.target).closest('a, button, .btn').length) {
                    return;
                }
                const $btnMarcar = $(this).find('.btn-marcar-alerta-leida');
                if ($btnMarcar.length) {
                    $btnMarcar.trigger('click');
                }
            });

            // 4. Marcar todas las alertas como leídas vía AJAX
            $(document).on('click', '#btn-marcar-todas-leidas', async function (e) {
                e.preventDefault();
                const $btn = $(this);
                const $spinner = $btn.find('.spinner-marcar-todas');

                $btn.prop('disabled', true);
                $spinner.removeClass('d-none');

                try {
                    const res = await $.ajax({
                        url: `${baseUrl}/admin/reda/alertas/marcar-todas-leidas`,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    if (res.success) {
                        actualizarBadgesFiltro(0);

                        // Notificar globalmente
                        $(document).trigger('reda:actualizar-contador-alertas', {
                            conteo: 0
                        });

                        // Recargar el listado actual
                        cargarAlertas(1, estadoActual, false);

                        if (window.RedaNotificaciones && typeof window.RedaNotificaciones.notificar === 'function') {
                            window.RedaNotificaciones.notificar(
                                window.RedaAlojamientoJson["Éxito"] || "Éxito",
                                res.mensaje_usuario || window.RedaAlojamientoJson["Todas las alertas fueron marcadas como leídas."] || "Todas las alertas fueron marcadas como leídas.",
                                'success'
                            );
                        }
                    }
                } catch (error) {
                    console.error("Error al marcar todas como leídas:", error);
                } finally {
                    $btn.prop('disabled', false);
                    $spinner.addClass('d-none');
                }
            });

            // 5. Animación de espera al hacer clic en "Ver mediación" y marcado en segundo plano si está pendiente
            $(document).on('click', '.btn-ir-mediaciones', function (e) {
                const $card = $(this).closest('.alerta-card-item');
                const $btnMarcar = $card.find('.btn-marcar-alerta-leida');
                if ($btnMarcar.length) {
                    const alertaId = $btnMarcar.data('id');
                    $.ajax({
                        url: `${baseUrl}/admin/reda/alertas/${alertaId}/marcar-leida`,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                }

                if (this.href && !this.target && !e.ctrlKey && !e.metaKey) {
                    if (window.RedaNotificaciones && typeof window.RedaNotificaciones.esperar === 'function') {
                        window.RedaNotificaciones.esperar();
                    }
                }
            });

            // Carga inicial
            cargarAlertas(1, 'todos', false);
        });
    }
})(jQuery);
