/**
 * Resumen: Gestión de menús adicionales en la barra lateral del panel administrativo (Backend).
 * Este archivo inyecta de forma reactiva y no invasiva las opciones de "Negocios", "Mediaciones"
 * (como submenú desplegable con "Listado" y condicionalmente "Configuración" exclusivo para Rol 1)
 * y "Soporte Técnico" en el menú lateral AdminLTE del proyecto original.
 * Permite que los usuarios con rol 1 (Admin) y rol 2 (Atención al usuario) tengan acceso a "Mediaciones",
 * garantizando que "Configuración" solo esté visible y disponible para administradores con Rol 1.
 * Actualiza dinámicamente el contador de mediaciones tanto en el encabezado padre como en "Listado".
 */
import { mediacionSvg } from '../../../general/iconos';
import { obtenerConteoMediaciones } from '../../../general/menus/obtenerConteoMediaciones.js';

/**
 * Función principal que orquesta la inyección y comportamiento de los menús del plugin en el panel admin.
 */
export const menuLateralAdmin = () =>
{
    (function( $ ) {
        "use strict";
        const containerId = '.sidebar-menu';

        if ($(containerId).length) {
            console.log('Script para "Menú Lateral Admin" cargado correctamente.');

            $(function() {
                const baseUrl = window.location.origin;

                // 1. Inyección de Menú "Negocios" (Después de Properties)
                const $propertiesMenuItem = $('.sidebar-menu a[href*="admin/properties"]').closest('li');

                if ($propertiesMenuItem.length && !$('#menu-negocios').length) {
                    const linkOpcionesNegocios = `${baseUrl}/admin/reda/negocios/opciones-tipos-de-negocios`;
                    const linkConfiguracionPlanes = `${baseUrl}/admin/reda/negocios/configuracion-planes`;
                    const labelNegocios = window.RedaAlojamientoJson["Negocios"] || "Negocios";
                    const labelConfigurarPlanes = window.RedaAlojamientoJson["Configurar planes"] || "Configurar planes";
                    const labelTiposNegocios = window.RedaAlojamientoJson["Tipos de negocios"] || "Tipos de negocios";

                    const nuevoMenuHtml = `
                        <li class="treeview" id="menu-negocios">
                            <a href="#" class="negocios-toggle">
                                <i class="fa fa-briefcase"></i> <span>${labelNegocios}</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu reda-admin-menu-hidden">
                                <li>
                                    <a href="${linkConfiguracionPlanes}" class="btn-menu-negocios"><span>${labelConfigurarPlanes}</span></a>
                                </li>
                                <li>
                                    <a href="${linkOpcionesNegocios}" class="btn-menu-negocios"><span>${labelTiposNegocios}</span></a>
                                </li>
                            </ul>
                        </li>
                    `;
                    $propertiesMenuItem.after(nuevoMenuHtml);
                    console.log('Opción "Negocios" inyectada.');

                    // Animación de espera al hacer clic en submenú de Negocios
                    $(document).on('click', '.btn-menu-negocios', function(e) {
                        if (this.href && !this.target && !e.ctrlKey && !e.metaKey) {
                            if (window.RedaNotificaciones && typeof window.RedaNotificaciones.esperar === 'function') {
                                window.RedaNotificaciones.esperar();
                            }
                        }
                    });

                    $('#menu-negocios > .negocios-toggle').on('click', function(e) {
                        e.preventDefault();
                        e.stopImmediatePropagation(); 

                        const $liPadre = $(this).closest('#menu-negocios');
                        const $subMenu = $liPadre.find('.treeview-menu');
                        const $flecha = $(this).find('.pull-right');

                        if ($subMenu.is(':visible')) {
                            $subMenu.slideUp('fast');
                            $liPadre.removeClass('active menu-open');
                            $flecha.removeClass('fa-angle-down').addClass('fa-angle-left');
                        } else {
                            $subMenu.slideDown('fast');
                            $liPadre.addClass('active menu-open');
                            $flecha.removeClass('fa-angle-left').addClass('fa-angle-down');
                        }
                    });
                }

                // 2. Inyección de Submenú "Mediaciones"
                // Disponible para usuarios con Rol 1 (Admin) y Rol 2 (Atención al usuario)
                if (!$('#menu-mediaciones').length) {
                    // Validar si window.RedaAdminUser indica permiso explícito de mediaciones
                    const tienePermisoMediaciones = window.RedaAdminUser ? window.RedaAdminUser.tieneAccesoMediaciones : true;

                    if (tienePermisoMediaciones) {
                        const esAdminRol1 = Boolean(
                            window.RedaAdminUser && (
                                window.RedaAdminUser.roleId === 1 || 
                                window.RedaAdminUser.esAdminTotal === true || 
                                (window.RedaAdminUser.roleName && window.RedaAdminUser.roleName.toLowerCase() === 'admin')
                            )
                        );

                        const linkMediaciones = `${baseUrl}/admin/reda/disputas`;
                        const linkConfiguracion = `${baseUrl}/admin/reda/disputas/configuracion`;
                        const labelMediaciones = window.RedaAlojamientoJson["Mediaciones"] || "Mediaciones";
                        const labelListado = window.RedaAlojamientoJson["Listado"] || "Listado";
                        const labelConfiguracion = window.RedaAlojamientoJson["Configuración"] || "Configuración";

                        const esRutaConfigActiva = window.location.href.includes('admin/reda/disputas/configuracion');
                        const esRutaListadoActiva = window.location.href.includes('admin/reda/disputas') && !esRutaConfigActiva;
                        const esRutaSubmenuActiva = window.location.href.includes('admin/reda/disputas');

                        let ultimoConteoMediaciones = null;

                        // La opción "Configuración" solo es visible y disponible para usuarios admin con Rol 1
                        const configuracionHtml = esAdminRol1 ? `
                            <li class="nav-item">
                                <a href="${linkConfiguracion}" class="nav-link btn-menu-mediacion-config ${esRutaConfigActiva ? 'active' : ''}">
                                    <i class="far fa-circle nav-icon me-2" style="font-size: 0.65rem;"></i>
                                    <span>${labelConfiguracion}</span>
                                </a>
                            </li>
                        ` : '';

                        const mediacionMenuHtml = `
                            <li class="nav-item treeview ${esRutaSubmenuActiva ? 'active menu-open' : ''}" id="menu-mediaciones">
                                <a href="#" class="nav-link mediaciones-toggle d-flex align-items-center justify-content-between ${esRutaSubmenuActiva ? 'active' : ''}">
                                    <div class="d-flex align-items-center">
                                        <span class="reda-icon-svg-18 me-2">
                                            ${mediacionSvg}
                                        </span>
                                        <span class="reda-mediaciones-texto-admin">${labelMediaciones}</span>
                                    </div>
                                    <i class="fa ${esRutaSubmenuActiva ? 'fa-angle-down' : 'fa-angle-left'} pull-right ms-auto"></i>
                                </a>
                                <ul class="nav nav-treeview treeview-menu ${esRutaSubmenuActiva ? '' : 'reda-admin-menu-hidden'}" style="${esRutaSubmenuActiva ? 'display: block;' : ''}">
                                    <li class="nav-item">
                                        <a href="${linkMediaciones}" class="nav-link btn-menu-mediacion-item ${esRutaListadoActiva ? 'active' : ''}">
                                            <i class="far fa-circle nav-icon me-2" style="font-size: 0.65rem;"></i>
                                            <span class="reda-mediaciones-listado-texto-admin">${labelListado}</span>
                                        </a>
                                    </li>
                                    ${configuracionHtml}
                                </ul>
                            </li>
                        `;

                        // Puntos de anclaje jerárquicos: Bookings -> Menú Negocios -> Properties -> Customers -> Dashboard -> Final
                        const $bookingsMenuItem = $('.sidebar-menu a[href*="admin/bookings"]').closest('li');
                        const $negociosMenuItem = $('#menu-negocios');
                        const $propertiesMenuItem = $('.sidebar-menu a[href*="admin/properties"]').closest('li');
                        const $customersMenuItem = $('.sidebar-menu a[href*="admin/customers"]').closest('li');
                        const $dashboardMenuItem = $('.sidebar-menu a[href*="admin/dashboard"]').closest('li');

                        if ($bookingsMenuItem.length) {
                            $bookingsMenuItem.after(mediacionMenuHtml);
                        } else if ($negociosMenuItem.length) {
                            $negociosMenuItem.after(mediacionMenuHtml);
                        } else if ($propertiesMenuItem.length) {
                            $propertiesMenuItem.after(mediacionMenuHtml);
                        } else if ($customersMenuItem.length) {
                            $customersMenuItem.after(mediacionMenuHtml);
                        } else if ($dashboardMenuItem.length) {
                            $dashboardMenuItem.after(mediacionMenuHtml);
                        } else {
                            $('.sidebar-menu').append(mediacionMenuHtml);
                        }

                        console.log('Submenú "Mediaciones" inyectado para rol con acceso.');

                        // Función para actualizar los contadores en el encabezado de Mediaciones y en la opción Listado
                        const aplicarTextoContador = (conteo) => {
                            ultimoConteoMediaciones = conteo;
                            $('.reda-mediaciones-texto-admin').text(`${labelMediaciones} (${conteo})`);
                            $('.reda-mediaciones-listado-texto-admin').text(`${labelListado} (${conteo})`);
                        };

                        // Toggle para desplegar y replegar el submenú de Mediaciones
                        $('#menu-mediaciones > .mediaciones-toggle').on('click', function(e) {
                            e.preventDefault();
                            e.stopImmediatePropagation(); 

                            const $liPadre = $(this).closest('#menu-mediaciones');
                            const $subMenu = $liPadre.find('.treeview-menu, .nav-treeview');
                            const $flecha = $(this).find('.pull-right');

                            if ($subMenu.is(':visible')) {
                                $subMenu.slideUp('fast');
                                $liPadre.removeClass('active menu-open');
                                $flecha.removeClass('fa-angle-down').addClass('fa-angle-left');
                            } else {
                                $subMenu.slideDown('fast');
                                $liPadre.addClass('active menu-open');
                                $flecha.removeClass('fa-angle-left').addClass('fa-angle-down');

                                // Al hacer clic o tocar "Mediaciones", asegurarse de que en "Listado" aparezca el contador
                                if (ultimoConteoMediaciones !== null) {
                                    $('.reda-mediaciones-listado-texto-admin').text(`${labelListado} (${ultimoConteoMediaciones})`);
                                } else {
                                    actualizarContadorAdmin();
                                }
                            }
                        });

                        // Animación de espera al hacer clic en Listado de Mediaciones
                        $(document).on('click', '.btn-menu-mediacion-item', function(e) {
                            if (this.href && !this.target && !e.ctrlKey && !e.metaKey) {
                                if (window.location.pathname === '/admin/reda/disputas' || (window.location.href.includes('admin/reda/disputas') && !window.location.href.includes('configuracion'))) {
                                    e.preventDefault();
                                    return;
                                }
                                if (window.RedaNotificaciones && typeof window.RedaNotificaciones.esperar === 'function') {
                                    window.RedaNotificaciones.esperar();
                                }
                            }
                        });

                        // Animación de espera al hacer clic en Configuración de Mediaciones
                        $(document).on('click', '.btn-menu-mediacion-config', function(e) {
                            if (this.href && !this.target && !e.ctrlKey && !e.metaKey && this.href !== '#') {
                                if (window.location.href.includes('admin/reda/disputas/configuracion')) {
                                    e.preventDefault();
                                    return;
                                }
                                if (window.RedaNotificaciones && typeof window.RedaNotificaciones.esperar === 'function') {
                                    window.RedaNotificaciones.esperar();
                                }
                            }
                        });

                        // Actualizar contador de mediaciones activas en el submenú vía AJAX
                        const actualizarContadorAdmin = async () => {
                            const adminCountUrl = `${baseUrl}/admin/reda/disputas/count-activas`;
                            const respuesta = await obtenerConteoMediaciones(adminCountUrl);
                            if (respuesta.success) {
                                aplicarTextoContador(respuesta.respuesta);
                            }
                        };
                        actualizarContadorAdmin();
                    }
                }

                // 3. Campanita de Notificaciones / Alertas en el Header del Panel Administrativo
                const labelAlertas = window.RedaAlojamientoJson["Alertas"] || "Alertas";
                const labelMensajes = window.RedaAlojamientoJson["Mensajes"] || "Mensajes";
                const linkAlertas = `${baseUrl}/admin/reda/alertas`;
                const linkMensajes = `${baseUrl}/admin/messages`;

                const $headerRightNav = $('.app-header .navbar-nav.ms-auto, .navbar-nav.ms-auto');
                if ($headerRightNav.length && !$('#reda-admin-header-bell').length) {
                    const headerBellHtml = `
                        <li class="nav-item me-2 d-flex align-items-center" id="reda-admin-header-bell">
                            <a href="${linkAlertas}" class="nav-link position-relative btn-menu-alertas-header px-2 py-1" title="${labelAlertas}">
                                <i class="fa fa-bell text-secondary f-18"></i>
                                <span id="reda-admin-bell-badge" class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle d-none f-10">0</span>
                            </a>
                        </li>
                    `;
                    $headerRightNav.prepend(headerBellHtml);
                    console.log('Campanita de Alertas inyectada en el header del admin.');

                    // Animación de espera al hacer clic en la campanita
                    $(document).on('click', '.btn-menu-alertas-header', function(e) {
                        if (this.href && !this.target && !e.ctrlKey && !e.metaKey) {
                            if (window.location.pathname === '/admin/reda/alertas') {
                                e.preventDefault();
                                return;
                            }
                            if (window.RedaNotificaciones && typeof window.RedaNotificaciones.esperar === 'function') {
                                window.RedaNotificaciones.esperar();
                            }
                        }
                    });
                }

                // 4. Transformación de la opción "Messages" en Submenú "Mensajes y Alertas"
                const $messagesMenuItem = $('.sidebar-menu a[href*="admin/messages"]').closest('li');

                if ($messagesMenuItem.length && !$('#menu-mensajes').length) {
                    const labelMensajesYAlertas = window.RedaAlojamientoJson["Mensajes y Alertas"] || "Mensajes y Alertas";
                    const esRutaAlertas = window.location.href.includes('admin/reda/alertas');
                    const esRutaMessages = (window.location.href.includes('admin/messages') || window.location.href.includes('admin/messaging')) && !esRutaAlertas;
                    const esSubmenuMensajesAbierto = esRutaAlertas || esRutaMessages;

                    const mensajesMenuHtml = `
                        <li class="nav-item treeview ${esSubmenuMensajesAbierto ? 'active menu-open' : ''}" id="menu-mensajes">
                            <a href="#" class="nav-link mensajes-toggle d-flex align-items-center justify-content-between ${esSubmenuMensajesAbierto ? 'active' : ''}">
                                <div class="d-flex align-items-center">
                                    <i class="nav-icon fa fa-comments me-2"></i>
                                    <span class="reda-mensajes-padre-texto">${labelMensajesYAlertas}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span id="reda-admin-alertas-badge" class="badge bg-danger rounded-pill me-2 d-none">0</span>
                                    <i class="fa ${esSubmenuMensajesAbierto ? 'fa-angle-down' : 'fa-angle-left'} pull-right ms-auto"></i>
                                </div>
                            </a>
                            <ul class="nav nav-treeview treeview-menu ${esSubmenuMensajesAbierto ? '' : 'reda-admin-menu-hidden'}" style="${esSubmenuMensajesAbierto ? 'display: block;' : ''}">
                                <li class="nav-item">
                                    <a href="${linkMensajes}" class="nav-link btn-menu-mensajes-item ${esRutaMessages ? 'active' : ''}">
                                        <i class="far fa-circle nav-icon me-2" style="font-size: 0.65rem;"></i>
                                        <span>${labelMensajes}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="${linkAlertas}" class="nav-link btn-menu-alertas-item d-flex align-items-center justify-content-between ${esRutaAlertas ? 'active' : ''}">
                                        <div class="d-flex align-items-center">
                                            <i class="fa fa-bell nav-icon me-2" style="font-size: 0.75rem;"></i>
                                            <span class="reda-alertas-texto-admin">${labelAlertas}</span>
                                        </div>
                                        <span id="reda-admin-sub-alertas-badge" class="badge bg-danger rounded-pill ms-auto d-none">0</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    `;

                    $messagesMenuItem.replaceWith(mensajesMenuHtml);
                    console.log('Submenú "Mensajes y Alertas" inyectado en lugar de Messages.');

                    // Toggle interactivo para desplegar y replegar submenú de Mensajes
                    $('#menu-mensajes > .mensajes-toggle').on('click', function(e) {
                        e.preventDefault();
                        e.stopImmediatePropagation();

                        const $liPadre = $(this).closest('#menu-mensajes');
                        const $subMenu = $liPadre.find('.treeview-menu, .nav-treeview');
                        const $flecha = $(this).find('.pull-right');

                        if ($subMenu.is(':visible')) {
                            $subMenu.slideUp('fast');
                            $liPadre.removeClass('active menu-open');
                            $flecha.removeClass('fa-angle-down').addClass('fa-angle-left');
                        } else {
                            $subMenu.slideDown('fast');
                            $liPadre.addClass('active menu-open');
                            $flecha.removeClass('fa-angle-left').addClass('fa-angle-down');
                        }
                    });

                    // Animación de espera al hacer clic en Mensajes
                    $(document).on('click', '.btn-menu-mensajes-item', function(e) {
                        if (this.href && !this.target && !e.ctrlKey && !e.metaKey) {
                            if (window.location.pathname === '/admin/messages') {
                                e.preventDefault();
                                return;
                            }
                            if (window.RedaNotificaciones && typeof window.RedaNotificaciones.esperar === 'function') {
                                window.RedaNotificaciones.esperar();
                            }
                        }
                    });

                    // Animación de espera al hacer clic en Alertas
                    $(document).on('click', '.btn-menu-alertas-item', function(e) {
                        if (this.href && !this.target && !e.ctrlKey && !e.metaKey) {
                            if (window.location.pathname === '/admin/reda/alertas') {
                                e.preventDefault();
                                return;
                            }
                            if (window.RedaNotificaciones && typeof window.RedaNotificaciones.esperar === 'function') {
                                window.RedaNotificaciones.esperar();
                            }
                        }
                    });
                }

                // 5. Inyección de Opción "Soporte Técnico" (Después de Mensajes)
                const $anchorSoporte = $('#menu-mensajes').length ? $('#menu-mensajes') : $('.sidebar-menu a[href*="admin/messages"]').closest('li');

                if ($anchorSoporte.length && !$('#menu-soporte').length) {
                    const linkSoporte = `${baseUrl}/admin/reda/general/soporte-tecnico`;
                    const labelSoporte = window.RedaAlojamientoJson["Soporte técnico"] || "Soporte técnico";

                    const soporteMenuHtml = `
                        <li id="menu-soporte" class="nav-item">
                            <a href="${linkSoporte}" class="nav-link btn-menu-soporte">
                                <i class="fa fa-life-ring"></i> <span>${labelSoporte}</span>
                            </a>
                        </li>
                    `;
                    $anchorSoporte.after(soporteMenuHtml);
                    console.log('Opción "Soporte Técnico" inyectada.');

                    // Animación de espera al hacer clic en Soporte Técnico
                    $(document).on('click', '.btn-menu-soporte', function(e) {
                        if (this.href && !this.target && !e.ctrlKey && !e.metaKey) {
                            if (window.RedaNotificaciones && typeof window.RedaNotificaciones.esperar === 'function') {
                                window.RedaNotificaciones.esperar();
                            }
                        }
                    });
                }

                // 6. Función y eventos para sincronización reactiva de contadores de Alertas
                const aplicarConteoAlertas = (count) => {
                    const $bellBadge = $('#reda-admin-bell-badge');
                    const $parentBadge = $('#reda-admin-alertas-badge');
                    const $subBadge = $('#reda-admin-sub-alertas-badge');

                    if (count > 0) {
                        $bellBadge.text(count).removeClass('d-none');
                        $parentBadge.text(count).removeClass('d-none');
                        $subBadge.text(count).removeClass('d-none');
                        $('.reda-alertas-texto-admin').text(`${labelAlertas} (${count})`);
                    } else {
                        $bellBadge.addClass('d-none');
                        $parentBadge.addClass('d-none');
                        $subBadge.addClass('d-none');
                        $('.reda-alertas-texto-admin').text(labelAlertas);
                    }
                };

                const actualizarContadorAlertasAdmin = async () => {
                    try {
                        const urlAlertasCount = `${baseUrl}/admin/reda/alertas/count-no-leidas`;
                        const res = await $.ajax({
                            url: urlAlertasCount,
                            type: 'GET',
                            dataType: 'json'
                        });

                        if (res.success && res.respuesta) {
                            const count = res.respuesta.count || 0;
                            aplicarConteoAlertas(count);
                        }
                    } catch (e) {
                        // Silencioso
                    }
                };

                // Escuchar evento personalizado emitido desde indexAlertas.js
                $(document).on('reda:actualizar-contador-alertas', function(e, data) {
                    if (data && typeof data.conteo !== 'undefined') {
                        aplicarConteoAlertas(data.conteo);
                    }
                });

                // Carga inicial y sondeo periódico de alertas cada 60 segundos
                actualizarContadorAlertasAdmin();
                setInterval(actualizarContadorAlertasAdmin, 60000);

                // 4. Corrección de compatibilidad FontAwesome (FA4 a FA5/6) para iconos del sistema original
                $('.sidebar-menu i.fa-paypal').removeClass('fa').addClass('fab me-2');
                $('.sidebar-menu i.fa-newspaper-o').removeClass('fa fa-newspaper-o').addClass('far fa-newspaper me-2');
                $('.sidebar-menu i.fa-bar-chart-o').removeClass('fa fa-bar-chart-o').addClass('far fa-chart-bar me-2');
                $('.sidebar-menu i.fa-trash-o').removeClass('fa fa-trash-o').addClass('far fa-trash-alt me-2');
            });
        }
    })(jQuery);
}
menuLateralAdmin();