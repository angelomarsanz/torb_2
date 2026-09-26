/**
 * Script desgloseHuespedes.js
 * 
 * Propósito: Gestiona la presentación visual consistente del desglose de huéspedes (Adultos y Niños)
 * en todas las vistas de la aplicación (Pago, Confirmación de Reserva, Detalle de Reserva y Admin)
 * mediante inyección y manipulación JavaScript de forma no invasiva sobre las vistas originales.
 * 
 * Vistas impactadas:
 * - /payments/book/{id}: Inyecta inputs ocultos en #checkout-form y sustituye texto de huéspedes.
 * - /booking/requested: Consulta vía AJAX y actualiza encabezados y tablas de resumen.
 * - /booking/{id}: Consulta vía AJAX y actualiza el detalle de la reserva para el anfitrión.
 * - /admin/bookings/detail/{id}: Actualiza el indicador de huéspedes en el panel de administración.
 */

(function( $ ) {
    "use strict";

    const DesgloseHuespedesManager = {
        /**
         * Inicializa la detección de la vista actual y aplica las transformaciones correspondientes.
         */
        init: function() {
            const path = window.location.pathname;

            // 1. Página de pago (/payments/book)
            if (path.includes('/payments/book') || $('#checkout-form').length) {
                this.procesarPaginaPago();
            }

            // 2. Página de confirmación / solicitud de reserva (/booking/requested)
            if (path.includes('/booking/requested')) {
                this.procesarPaginaRequested();
            }

            // 3. Página de detalle de reserva para usuario/anfitrión (/booking/{id})
            const matchBookingDetail = path.match(/\/booking\/(\d+)/);
            if (matchBookingDetail && !path.includes('/admin/')) {
                const bookingId = matchBookingDetail[1];
                this.procesarPaginaDetalleReserva(bookingId);
            }

            // 4. Detalle de reserva en el panel de administración (/admin/bookings/detail/{id})
            const matchAdminBookingDetail = path.match(/\/admin\/bookings\/detail\/(\d+)/);
            if (matchAdminBookingDetail) {
                const adminBookingId = matchAdminBookingDetail[1];
                this.procesarPaginaAdminDetalle(adminBookingId);
            }

            // 5. Listado de mis reservas (/my-bookings)
            if (path.includes('/my-bookings')) {
                this.procesarListadoMisReservas();
            }
        },

        /**
         * Procesa la vista de pago: sustituye la etiqueta de huéspedes e inyecta los campos ocultos
         * de adultos y niños en el formulario de confirmación.
         */
        procesarPaginaPago: function() {
            const sessionData = window.RedaSessionHuespedes || {};
            const adultos = parseInt(sessionData.adultos) || 1;
            const ninos = parseInt(sessionData.ninos) || 0;
            const json = window.RedaAlojamientoJson || {};

            const labelAdultos = json["Adulto(s)"] || "Adulto(s)";
            const labelNinos = json["Niño(s)"] || "Niño(s)";

            let textoDesglose = `${adultos} ${labelAdultos}`;
            if (ninos > 0) {
                textoDesglose += `, ${ninos} ${labelNinos}`;
            }

            // 1. Actualizar el texto en la tarjeta resumen de reserva
            $('.border.p-4.mt-3.text-center.rounded-3 strong.secondary-text-color').each(function() {
                const texto = $(this).text();
                if (texto.includes('Guest') || texto.includes('Huésped') || texto.includes('Huéspedes')) {
                    $(this).text(textoDesglose);
                }
            });

            // 2. Inyectar o actualizar inputs ocultos en #checkout-form para persistencia en backend
            const $form = $('#checkout-form');
            if ($form.length) {
                if (!$form.find('input[name="adultos"]').length) {
                    $form.append(`<input type="hidden" name="adultos" value="${adultos}">`);
                } else {
                    $form.find('input[name="adultos"]').val(adultos);
                }

                if (!$form.find('input[name="ninos"]').length) {
                    $form.append(`<input type="hidden" name="ninos" value="${ninos}">`);
                } else {
                    $form.find('input[name="ninos"]').val(ninos);
                }
            }
        },

        /**
         * Procesa la vista de reserva solicitada / confirmada (/booking/requested?code=XXX).
         */
        procesarPaginaRequested: function() {
            const self = this;
            const urlParams = new URLSearchParams(window.location.search);
            const code = urlParams.get('code');

            if (!code) return;

            $.ajax({
                url: `${window.APP_URL}/reda/bookings/huespedes-info`,
                type: 'GET',
                data: { code: code },
                dataType: 'json',
                success: function(res) {
                    if (res.success && res.respuesta) {
                        self.aplicarDesgloseEnVistaDetalle(res.respuesta);
                    }
                }
            });
        },

        /**
         * Procesa la vista de detalle de reserva del usuario / anfitrión (/booking/{id}).
         * 
         * @param {number|string} bookingId 
         */
        procesarPaginaDetalleReserva: function(bookingId) {
            const self = this;
            $.ajax({
                url: `${window.APP_URL}/reda/bookings/huespedes-info`,
                type: 'GET',
                data: { booking_id: bookingId },
                dataType: 'json',
                success: function(res) {
                    if (res.success && res.respuesta) {
                        self.aplicarDesgloseEnVistaDetalle(res.respuesta);
                    }
                }
            });
        },

        /**
         * Sustituye en el DOM de la vista de detalle los textos de huéspedes por el desglose.
         * 
         * @param {Object} data 
         */
        aplicarDesgloseEnVistaDetalle: function(data) {
            const json = window.RedaAlojamientoJson || {};
            const labelAdultos = json["Adulto(s)"] || "Adulto(s)";
            const labelNinos = json["Niño(s)"] || "Niño(s)";

            let textoDesglose = `${data.adultos} ${labelAdultos}`;
            if (data.ninos > 0) {
                textoDesglose += `, ${data.ninos} ${labelNinos}`;
            }

            // 1. Encabezado principal: "Propiedad for X Guest"
            $('strong.secondary-text-color').each(function() {
                const txt = $(this).text();
                if (txt.includes('Guest') || txt.includes('Huésped') || txt.includes('Huéspedes')) {
                    $(this).text(textoDesglose);
                }
            });

            // 2. Fila en la tabla de desglose ("Guests ... X")
            $('.d-flex.justify-content-between.text-16').each(function() {
                const $labelCol = $(this).find('div').first();
                const $valCol = $(this).find('div').last();
                const labelText = $labelCol.text().trim();

                if (labelText.includes('Guest') || labelText.includes('Huésped') || labelText.includes('Huéspedes')) {
                    $valCol.find('p').text(textoDesglose);
                }
            });
        },

        /**
         * Procesa la vista de detalle de reservación en el panel de administración.
         * 
         * @param {number|string} adminBookingId 
         */
        procesarPaginaAdminDetalle: function(adminBookingId) {
            $.ajax({
                url: `${window.APP_URL}/reda/bookings/huespedes-info`,
                type: 'GET',
                data: { booking_id: adminBookingId },
                dataType: 'json',
                success: function(res) {
                    if (res.success && res.respuesta) {
                        const data = res.respuesta;
                        const texto = `${data.adultos} Adulto(s)` + (data.ninos > 0 ? `, ${data.ninos} Niño(s)` : '');

                        // Buscar elemento con texto de noches y huéspedes en Admin
                        $('.f-14.font-weight-bold.text-dark').each(function() {
                            const html = $(this).html();
                            if (html.includes('•') && (html.includes('Guest') || html.includes('Guests') || html.includes('Huésped'))) {
                                const partes = html.split('•');
                                if (partes.length >= 2) {
                                    $(this).html(`${partes[0].trim()} • ${texto}`);
                                }
                            }
                        });
                    }
                }
            });
        },

        /**
         * Procesa la vista de listado de reservas del usuario (/my-bookings).
         */
        procesarListadoMisReservas: function() {
            // Escanear cada bloque de reservación que contenga ícono de cama y link a booking/{id}
            $('p.text-14.mt-3').each(function() {
                const $p = $(this);
                const $link = $p.closest('.row').find('a[href*="/booking/"]').first();
                if ($link.length) {
                    const href = $link.attr('href');
                    const match = href.match(/\/booking\/(\d+)/);
                    if (match) {
                        const bookingId = match[1];
                        $.ajax({
                            url: `${window.APP_URL}/reda/bookings/huespedes-info`,
                            type: 'GET',
                            data: { booking_id: bookingId },
                            dataType: 'json',
                            success: function(res) {
                                if (res.success && res.respuesta) {
                                    const data = res.respuesta;
                                    const texto = `${data.adultos} Adulto(s)` + (data.ninos > 0 ? `, ${data.ninos} Niño(s)` : '');
                                    $p.html(`<i class="fas fa-bed text-20 d-none d-sm-inline-block pr-2 text-success"></i><strong>${texto}</strong>`);
                                }
                            }
                        });
                    }
                }
            });
        }
    };

    $(function() {
        DesgloseHuespedesManager.init();
    });

})(jQuery);
