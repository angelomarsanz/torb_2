/**
 * Resumen: Agrega al menú lateral del panel administrativo la opción "Verificación KYC" (con el contador de
 * solicitudes pendientes de revisión) solo a los administradores cuyo rol tiene el permiso manage_kyc. El permiso se
 * calcula en el servidor (window.RedaAdminUser.tieneAccesoKyc); ocultar el enlace aquí es solo presentación, porque
 * las rutas y las acciones exigen el permiso en el servidor. Se coloca después de "Mediaciones" y, si no existe, junto
 * a "Reservas" o "Clientes".
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Resources\Js\Admin\General\Menus
 * @author     REDA Tech Team
 * @version    1.0.0
 */

(function ($) {
    "use strict";

    if (!$('.sidebar-menu').length) return;

    $(function () {
        const usuario = window.RedaAdminUser || {};
        if (!usuario.tieneAccesoKyc || $('#menu-kyc').length) return;

        const baseUrl = typeof APP_URL !== 'undefined' ? APP_URL : window.location.origin;
        const etiqueta = window.RedaAlojamientoJson['Verificación KYC'] || 'Verificación KYC';
        const activa = window.location.pathname.includes('/admin/reda/kyc');
        const pendientes = parseInt(usuario.kycPendientes, 10) || 0;
        const contador = pendientes > 0 ? ` <span class="badge bg-warning text-dark ms-1">${pendientes}</span>` : '';

        const $item = $(`
            <li class="nav-item ${activa ? 'active' : ''}" id="menu-kyc">
                <a href="${baseUrl}/admin/reda/kyc" class="nav-link ${activa ? 'active' : ''}">
                    <i class="far fa-id-card nav-icon"></i>
                    <p>${$('<span>').text(etiqueta).html()}${contador}</p>
                </a>
            </li>
        `);

        $item.find('a').on('click', function (evento) {
            if (!evento.ctrlKey && !evento.metaKey && window.RedaNotificaciones) window.RedaNotificaciones.esperar();
        });

        const $mediaciones = $('#menu-mediaciones');
        const $reservas = $('.sidebar-menu a[href*="admin/bookings"]').closest('li');
        const $clientes = $('.sidebar-menu a[href*="admin/customers"]').closest('li');

        if ($mediaciones.length) {
            $mediaciones.after($item);
        } else if ($reservas.length) {
            $reservas.after($item);
        } else if ($clientes.length) {
            $clientes.after($item);
        } else {
            $('.sidebar-menu').append($item);
        }
    });
})(jQuery);
