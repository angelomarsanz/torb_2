/**
 * Objeto global para gestionar notificaciones y animaciones de carga del plugin Reda.
 */

window.RedaNotificaciones = {

    /**
     * Muestra una animación de espera (spinner) bloqueando la interacción.
     */
    esperar: function() {
        (function( $ ) {
            "use strict";
            const $modal = $('#modal-notificacion');
            if (!$modal.length) return;

            const $titulo = $('#notificacion-titulo');
            const $mensaje = $('#notificacion-mensaje');
            const $icono = $('#notificacion-icono');
            const $footer = $modal.find('.modal-footer');

            // Configuración para estado de carga
            $icono.html('<i class="fa fa-spinner fa-spin fa-4x text-success"></i>');
            $titulo.text(window.RedaAlojamientoJson["Por favor espere"] || "Por favor espere");
            $mensaje.text(window.RedaAlojamientoJson["Estamos procesando su solicitud..."] || "Estamos procesando su solicitud...");
            
            // Ocultamos el botón de aceptar y la X de cerrar para que sea un bloqueo real
            $footer.addClass('d-none');
            $modal.find('.close').addClass('d-none');

            // Evitar que se cierre al hacer clic fuera o presionar ESC
            $modal.modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
        })(jQuery);
    },

    /**
     * Muestra un modal de notificación (Éxito, Error, Info).
     */
    notificar: function(titulo, mensaje, tipo = 'info', recargar = false) {
        (function( $ ) {
            "use strict";
            const $modal = $('#modal-notificacion');
            if (!$modal.length) return;

            const $titulo = $('#notificacion-titulo');
            const $mensaje = $('#notificacion-mensaje');
            const $icono = $('#notificacion-icono');
            const $footer = $modal.find('.modal-footer');

            // Restauramos controles ocultos por 'esperar'
            $footer.removeClass('d-none');
            $modal.find('.close').removeClass('d-none');

            let iconoHtml = '';
            switch (tipo) {
                case 'exito':
                    iconoHtml = '<i class="fa fa-check-circle fa-4x text-success"></i>';
                    $titulo.text(titulo || (window.RedaAlojamiento?.general?.exito || "¡Éxito!"));
                    break;
                case 'error':
                    iconoHtml = '<i class="fa fa-times-circle fa-4x text-danger"></i>';
                    $titulo.text(titulo || (window.RedaAlojamiento?.general?.error || "Error"));
                    break;
                default:
                    iconoHtml = '<i class="fa fa-info-circle fa-4x text-primary"></i>';
                    $titulo.text(titulo || (window.RedaAlojamiento?.general?.notificacion || "Notificación"));
            }

            $icono.html(iconoHtml);
            $mensaje.html(mensaje);

            $modal.off('hidden.bs.modal').on('hidden.bs.modal', function () {
                if (recargar) location.reload();
            });

            // Si ya estaba abierto (por ejemplo desde esperar), solo actualizamos contenido
            if (($modal.data('bs.modal') || {})._isShown) {
                // Ya se ve
            } else {
                $modal.modal('show');
            }
        })(jQuery);
    },

    /**
     * Oculta el modal de notificación si está abierto.
     */
    ocultar: function() {
        (function( $ ) {
            "use strict";
            const $modal = $('#modal-notificacion');
            if (!$modal.length) return;

            // Intentamos ocultar de forma normal
            $modal.modal('hide');

            // Refuerzo: Si después de un breve momento el modal sigue visible o el backdrop existe, forzamos limpieza.
            // PERO: Solo si no hay otros modales abiertos (para no romper el modal de detalle que viene después).
            setTimeout(() => {
                // Si el modal de notificación sigue teniendo la clase 'show' o hay un backdrop huérfano
                if ($modal.hasClass('show') || ($('.modal-backdrop').length > 0 && $('.modal.show').length === 0)) {
                    
                    // Si el modal de notificación específicamente es el que no cerró
                    if ($modal.hasClass('show')) {
                        $modal.removeClass('show').css('display', 'none').attr('aria-hidden', 'true');
                        if ($modal.data('bs.modal')) {
                            $modal.data('bs.modal')._isShown = false;
                            $modal.data('bs.modal')._isTransitioning = false;
                        }
                    }

                    // Solo limpiamos el backdrop y el scroll si NO hay ningún otro modal abierto ahora
                    if ($('.modal.show').length === 0) {
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open').css('padding-right', '');
                    }
                }
            }, 400); // Aumentamos un poco el tiempo para dar margen a la apertura del siguiente modal
        })(jQuery);
    }
};

/**
 * Detección prioritaria de alertas enviadas vía URL.
 * Se ejecuta después de definir el objeto RedaNotificaciones para evitar errores de referencia.
 */
(function() {
    "use strict";

    const checkAlert = () => {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('reda_alert')) {
            const alerta = urlParams.get('reda_alert');
            console.log('REDA Notificaciones: Alerta detectada en URL:', alerta);
            
            if (alerta === 'active_booking') {
                const propertyName = urlParams.get('property_name') || "";
                const propertyId = urlParams.get('property_id');
                const propertySlug = urlParams.get('property_slug');

                let intentos = 0;
                const checkInterval = setInterval(() => {
                    intentos++;

                    // Verificamos jQuery, el Modal y nuestro objeto
                    const jqueryListo = typeof jQuery !== 'undefined';
                    const $modal = jqueryListo ? jQuery('#modal-notificacion') : [];

                    if (jqueryListo && $modal.length && window.RedaNotificaciones && typeof window.RedaNotificaciones.notificar === 'function') {
                        clearInterval(checkInterval);
                        console.log('REDA Notificaciones: Disparando modal informativo tras ' + (intentos * 100) + 'ms');

                        const dict = window.RedaAlojamientoJson || {};
                        let mensajeBase = dict["Estimado usuario usted tiene una reservación activa para la propiedad"] || "Estimado usuario usted tiene una reservación activa para la propiedad";

                        // Construir el mensaje final con el nombre en negrita
                        const mensajeFinal = propertyName 
                            ? `${mensajeBase} **${propertyName}**`
                            : (dict["Estimado usuario ya usted tiene una reservación activa para esta propiedad"] || "Estimado usuario ya usted tiene una reservación activa para esta propiedad");

                        const titulo = dict["Notificación"] || "Notificación";

                        // Pequeño delay extra para asegurar que Bootstrap modal esté listo para mostrarse
                        setTimeout(() => {
                            window.RedaNotificaciones.notificar(titulo, mensajeFinal, 'info');

                            // Lógica de desplazamiento (Scroll)
                            let $target = [];
                            
                            // Intentamos por ID (si la vista fuese modificada)
                            if (propertyId) {
                                $target = jQuery(`#property-${propertyId}`);
                            }
                            
                            // Si no encontramos por ID, buscamos por Slug en los enlaces (Vista original intacta)
                            if ((!$target || !$target.length) && propertySlug) {
                                console.log('REDA Notificaciones: Buscando por slug:', propertySlug);
                                const $link = jQuery(`a[href*="properties/${propertySlug}"]`).first();
                                if ($link.length) {
                                    $target = $link.closest('.row.border');
                                }
                            }

                            if ($target && $target.length) {
                                console.log('REDA Notificaciones: Posicionando scroll en elemento detectado.');
                                jQuery('html, body').animate({
                                    scrollTop: $target.offset().top - 120 // 120px de margen superior para el header
                                }, 800);
                                // Resaltado temporal sutil
                                $target.addClass('reda-highlight-border');
                                setTimeout(() => $target.removeClass('reda-highlight-border'), 4000);
                            }
                        }, 200);
                    }

                    if (intentos > 60) { // Aumentamos a 6 segundos el tiempo de espera máximo
                        console.error('REDA Notificaciones: Tiempo de espera agotado para el modal o dependencias');
                        clearInterval(checkInterval);
                    }
                }, 100);
            }

        }
    };

    // Ejecutar al cargar el script y también cuando el DOM esté listo por seguridad
    checkAlert();
    if (typeof jQuery !== 'undefined') {
        jQuery(document).ready(checkAlert);
    }
})();

// Mantener compatibilidad con funciones globales previas si existen
window.mostrarNotificacion = (titulo, mensaje, tipo, recargar) => {
    window.RedaNotificaciones.notificar(titulo, mensaje, tipo, recargar);
};

// --- GESTIÓN DE BFCACHE Y FOCO ---
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        if (window.RedaNotificaciones && typeof window.RedaNotificaciones.ocultar === 'function') {
            window.RedaNotificaciones.ocultar();
        }
    }
});

window.addEventListener('focus', function() {
    if (window.RedaNotificaciones && typeof window.RedaNotificaciones.ocultar === 'function') {
        const $modal = jQuery('#modal-notificacion');
        if ($modal.length && $modal.find('.modal-footer').hasClass('d-none') && $modal.is(':visible')) {
            window.RedaNotificaciones.ocultar();
        }
    }
});

/**
 * Muestra un modal de confirmación.
 */
window.mostrarConfirmacion = (mensaje, callback, titulo = '', textoBoton = '') => {
    (function( $ ) {
        "use strict";
        const $modal = $('#modal-confirmacion');
        const $btnConfirmar = $('#btn-confirmar-si');

        $('#confirmacion-mensaje').html(mensaje);
        if (titulo) $('#confirmacion-titulo').text(titulo);
        if (textoBoton) $btnConfirmar.find('.btn-text').text(textoBoton);

        $btnConfirmar.off('click').on('click', async function() {
            const $btn = $(this);
            $btn.prop('disabled', true);
            $btn.find('.fa-spinner').removeClass('d-none');

            if (callback && typeof callback === 'function') {
                await callback();
            }

            $btn.prop('disabled', false);
            $btn.find('.fa-spinner').addClass('d-none');
            $modal.modal('hide');
        });

        $modal.modal('show');
    })(jQuery);
}

// --- GESTIÓN DE CLICKS GLOBAL PARA EL PLUGIN (ANIMACIÓN DE CARGA) ---
$(function() {
    $(document).on('click', 'a[href*="/reda/negocios"]', function(e) {
        if (this.href && !this.target && !e.ctrlKey && !e.metaKey && !$(this).hasClass('no-esperar')) {
             if (!this.href.includes(window.location.pathname + '#')) {
                window.RedaNotificaciones.esperar();
            }
        }
    });
});
