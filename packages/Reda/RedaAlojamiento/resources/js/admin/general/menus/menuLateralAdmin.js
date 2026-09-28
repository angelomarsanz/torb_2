/**
 * Resumen: Gestión de menús adicionales en la barra lateral del panel administrativo (Backend).
 * Este archivo inyecta de forma reactiva y no invasiva las opciones de "Negocios", "Mediaciones"
 * y "Soporte Técnico" en el menú lateral AdminLTE del proyecto original.
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

                // 2. Inyección de Opción "Mediaciones"
                // Disponible para usuarios con Rol 1 (Admin) y Rol 2 (Atención al usuario)
                if (!$('#menu-mediaciones').length) {
                    // Validar si window.RedaAdminUser indica permiso explícito de mediaciones
                    const tienePermisoMediaciones = window.RedaAdminUser ? window.RedaAdminUser.tieneAccesoMediaciones : true;

                    if (tienePermisoMediaciones) {
                        const linkMediaciones = `${baseUrl}/admin/reda/disputas`;
                        const labelMediaciones = window.RedaAlojamientoJson["Mediaciones"] || "Mediaciones";
                        const esRutaActiva = window.location.href.includes('admin/reda/disputas');

                        const mediacionMenuHtml = `
                            <li id="menu-mediaciones" class="nav-item">
                                <a href="${linkMediaciones}" class="nav-link btn-menu-mediacion d-flex align-items-center ${esRutaActiva ? 'active' : ''}">
                                    <span class="reda-icon-svg-18 me-2">
                                        ${mediacionSvg}
                                    </span>
                                    <span class="reda-mediaciones-texto-admin">${labelMediaciones}</span>
                                </a>
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

                        console.log('Opción "Mediaciones" inyectada para rol con acceso.');

                        // Animación de espera al hacer clic en Mediaciones
                        $(document).on('click', '.btn-menu-mediacion', function(e) {
                            if (this.href && !this.target && !e.ctrlKey && !e.metaKey) {
                                if (window.RedaNotificaciones && typeof window.RedaNotificaciones.esperar === 'function') {
                                    window.RedaNotificaciones.esperar();
                                }
                            }
                        });

                        // Actualizar contador de mediaciones activas
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