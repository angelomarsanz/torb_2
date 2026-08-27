// packages/Reda/RedaAlojamiento/resources/js/general/menus/menuPrincipal.js
/**
 * Resumen: Script para inyectar y gestionar el menú principal REDA en el navbar.
 * Agrega accesos rápidos a Alojamientos, Comercios y Mensajes (Inbox) con
 * contador dinámico de notificaciones no leídas.
 */
import { alojamientosSvg, comerciosSvg, notificacionesSvg } from '../iconos';
import { obtenerConteoNoLeidos } from '../ajax';

/**
 * Función principal que construye e inyecta el menú en el navbar.
 */
export const menuPrincipal = () => {
    (function( $ ) {
        "use strict";

        const navbarContainer = $('.navbar .container-fluid');
        if (!navbarContainer.length) return;

        // Evitar duplicados
        if ($('#reda-menu-principal').length) return;

        const urlAlojamientos = APP_URL + '/';
        const urlComercios = APP_URL + '/reda/negocios/listado-negocios';
        const urlInbox = APP_URL + '/inbox';

        const textoAlojamientos = window.RedaAlojamientoJson["Alojamientos"] || "Alojamientos";
        const textoComercios = window.RedaAlojamientoJson["Comercios"] || "Comercios";
        const textoMensajes = window.RedaAlojamientoJson["Mensajes"] || "Mensajes";

        // Determinar cuál está activo basándonos en la URL actual
        const pathActual = window.location.pathname;
        
        // El ícono "Comercios" se activa si la URL contiene "admin/reda/negocios/" o "/reda/negocios"
        const esComercios = pathActual.includes('admin/reda/negocios/') 
            || pathActual.includes('/reda/negocios');
            
        const esInbox = pathActual.includes('/inbox');

        // El ícono "Alojamientos" se activa en cualquier otro caso que no sea comercios ni inbox
        const esAlojamientos = !esComercios && !esInbox;

        const menuHtml = `
            <div id="reda-menu-principal" class="d-flex align-items-center reda-menu-principal" data-role="added-by-reda">
                <a href="${urlAlojamientos}" class="reda-menu-item ${esAlojamientos ? 'active' : ''}">
                    <div class="reda-menu-icon">${alojamientosSvg}</div>
                    <span class="reda-menu-text">${textoAlojamientos}</span>
                </a>
                <a href="${urlComercios}" class="reda-menu-item ${esComercios ? 'active' : ''}">
                    <div class="reda-menu-icon">${comerciosSvg}</div>
                    <span class="reda-menu-text">${textoComercios}</span>
                </a>
                <a href="${urlInbox}" class="reda-menu-item ${esInbox ? 'active' : ''}">
                    <div class="reda-menu-icon">
                        ${notificacionesSvg}
                        <span id="reda-inbox-badge" class="reda-menu-badge d-none">0</span>
                    </div>
                    <span class="reda-menu-text">${textoMensajes}</span>
                </a>
            </div>
        `;

        /**
         * Actualiza el badge del menú con el conteo de mensajes no leídos.
         * @param {boolean} mostrarLoader - Indica si se muestra el bloqueo de pantalla.
         */
        const refrescarBadgeMensajes = async (mostrarLoader = false) => {
            const data = await obtenerConteoNoLeidos(mostrarLoader);
            if (data.success && data.respuesta.count > 0) {
                $('#reda-inbox-badge').text(data.respuesta.count).removeClass('d-none');
            } else {
                $('#reda-inbox-badge').addClass('d-none');
            }
        };

        // Insertar después del logo (navbar-brand) para que esté entre el logo y el toggler/collapse
        const logo = navbarContainer.find('.navbar-brand');
        if (logo.length) {
            logo.after(menuHtml);
            
            // Si el menú se inyectó, cargar el contador si el usuario parece estar autenticado
            if ($('.nav-item.dropdown').length || $('#logout_link').length) {
                // En el primer refresco usamos loader si no estamos en inbox para cumplir directrices,
                // pero lo desactivamos para el intervalo recurrente por UX.
                refrescarBadgeMensajes(false); 
                
                // Intervalo de actualización cada 2 minutos
                setInterval(() => refrescarBadgeMensajes(false), 120000);
            }
        }

    })(jQuery);
};

// Iniciar al cargar el DOM
$(function () {
    menuPrincipal();
});
