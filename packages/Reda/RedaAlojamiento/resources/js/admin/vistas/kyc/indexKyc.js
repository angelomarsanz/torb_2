/**
 * Resumen: Interactividad del listado administrativo de solicitudes KYC (#reda-kyc-index). Muestra la animación de
 * "Espera" mientras el servidor responde al cambiar de pestaña, filtrar, buscar, paginar o abrir un detalle (el
 * listado es de recarga completa, paginado de 10 en 10). No intercepta los clics con Ctrl o Cmd (abrir en otra pestaña).
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Resources\Js\Admin\Vistas\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

(function ($) {
    "use strict";

    const $vista = $('#reda-kyc-index');
    if (!$vista.length) return;

    $(function () {
        const esperar = () => window.RedaNotificaciones && window.RedaNotificaciones.esperar();

        $vista.on('submit', '.reda-kyc-filtros', function () {
            esperar();
        });

        $vista.on('click', '.reda-kyc-pestanas a, .reda-pagination-container a, .reda-kyc-tabla a, a.reda-kyc-tarjeta', function (evento) {
            if (evento.ctrlKey || evento.metaKey || evento.shiftKey || evento.which === 2) return;
            esperar();
        });
    });
})(jQuery);
