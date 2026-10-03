/**
 * Resumen: Gestión de menús adicionales en la barra lateral del panel administrativo (Backend).
 * Este archivo inyecta de forma reactiva y no invasiva las opciones de "Negocios", "Mediaciones"
 * (como submenú desplegable con "Listado" y "Configuración") y "Soporte Técnico" en el menú lateral AdminLTE del proyecto original.
 * Permite que los usuarios con rol 1 (Admin) y rol 2 (Atención al usuario) tengan acceso a "Mediaciones".
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
                        const linkMediaciones = `${baseUrl}/admin/reda/disputas`;
                        const linkConfiguracion = '#';
                        const labelMediaciones = window.RedaAlojamientoJson["Mediaciones"] || "Mediaciones";
                        const labelListado = window.RedaAlojamientoJson["Listado"] || "Listado";
                        const labelConfiguracion = window.RedaAlojamientoJson["Configuración"] || "Configuración";
                        const esRutaActiva = window.location.href.includes('admin/reda/disputas');

                        const mediacionMenuHtml = `
                            <li class="nav-item treeview ${esRutaActiva ? 'active menu-open' : ''}" id="menu-mediaciones">
                                <a href="#" class="nav-link mediaciones-toggle d-flex align-items-center justify-content-between ${esRutaActiva ? 'active' : ''}">
                                    <div class="d-flex align-items-center">
                                        <span class="reda-icon-svg-18 me-2">
                                            ${mediacionSvg}
                                        </span>
                                        <span class="reda-mediaciones-texto-admin">${labelMediaciones}</span>
                                    </div>
                                    <i class="fa ${esRutaActiva ? 'fa-angle-down' : 'fa-angle-left'} pull-right ms-auto"></i>
                                </a>
                                <ul class="nav nav-treeview treeview-menu ${esRutaActiva ? '' : 'reda-admin-menu-hidden'}" style="${esRutaActiva ? 'display: block;' : ''}">
                                    <li class="nav-item">
                                        <a href="${linkMediaciones}" class="nav-link btn-menu-mediacion-item ${esRutaActiva ? 'active' : ''}">
                                            <i class="far fa-circle nav-icon me-2" style="font-size: 0.65rem;"></i>
                                            <span>${labelListado}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="${linkConfiguracion}" class="nav-link btn-menu-mediacion-config">
                                            <i class="far fa-circle nav-icon me-2" style="font-size: 0.65rem;"></i>
                                            <span>${labelConfiguracion}</span>
                                        </a>
                                    </li>
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
                            }
                        });

                        // Animación de espera al hacer clic en Listado de Mediaciones
                        $(document).on('click', '.btn-menu-mediacion-item', function(e) {
                            if (this.href && !this.target && !e.ctrlKey && !e.metaKey) {
                                if (window.location.href.includes('admin/reda/disputas')) {
                                    e.preventDefault();
                                    return;
                                }
                                if (window.RedaNotificaciones && typeof window.RedaNotificaciones.esperar === 'function') {
                                    window.RedaNotificaciones.esperar();
                                }
                            }
                        });

                        // Evento para Configuración de Mediaciones (Enlace con # temporal)
                        $(document).on('click', '.btn-menu-mediacion-config', function(e) {
                            e.preventDefault();
                        });

                        // Actualizar contador de mediaciones activas en el submenú
                        const actualizarContadorAdmin = async () => {
                            const adminCountUrl = `${baseUrl}/admin/reda/disputas/count-activas`;
                            const respuesta = await obtenerConteoMediaciones(adminCountUrl);
                            if (respuesta.success) {
                                const count = respuesta.respuesta;
                                $('.reda-mediaciones-texto-admin').text(`${labelMediaciones} (${count})`);
                            }
                        };
                        actualizarContadorAdmin();
                    }
                }

                // 3. Inyección de Opción "Soporte Técnico" (Después de Messages)
                const $messagesMenuItem = $('.sidebar-menu a[href*="admin/messages"]').closest('li');

                if ($messagesMenuItem.length && !$('#menu-soporte').length) {
                    const linkSoporte = `${baseUrl}/admin/reda/general/soporte-tecnico`;
                    const labelSoporte = window.RedaAlojamientoJson["Soporte técnico"] || "Soporte técnico";

                    const soporteMenuHtml = `
                        <li id="menu-soporte" class="nav-item">
                            <a href="${linkSoporte}" class="nav-link btn-menu-soporte">
                                <i class="fa fa-life-ring"></i> <span>${labelSoporte}</span>
                            </a>
                        </li>
                    `;
                    $messagesMenuItem.after(soporteMenuHtml);
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