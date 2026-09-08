/**
 * Script propiedad_detalle.js
 * 
 * Gestiona el sistema de reservación mediante modales y botones flotantes
 * en la vista de detalle de la propiedad (property.single).
 * 
 * Funcionalidades:
 * - Oculta el sidebar de reserva original.
 * - Inyecta un botón flotante responsivo.
 * - Verifica reservas activas antes de permitir una nueva reservación.
 * - Maneja disparadores por hash (#reservar) para integraciones externas.
 */

(function( $ ) {
    "use strict";

    const PropertyDetailReservation = {
        config: {
            formId: '#booking_form',
            sidebarContainerId: '#booking-price',
            modalId: '#modalReservar',
            modalBodyId: '#modalReservarBody',
            saveBtnId: '#save_btn',
            floatingBtnContainerClass: 'reda-btn-flotante-reserva',
            hashTrigger: '#reservar'
        },

        /**
         * Inicializa el módulo de reserva si el formulario existe en el DOM.
         */
        init: function() {
            if (!$(this.config.formId).length) return;

            console.log('REDA Property Detail: Inicializando sistema de reserva en modal');

            this.setupUI();
            this.handleHash();
            this.bindEvents();
        },

        /**
         * Configura la interfaz de usuario inicial, ocultando el sidebar original 
         * e inyectando el botón flotante.
         */
        setupUI: function() {
            const self = this;
            const $form = $(this.config.formId);
            const $sidebar = $(this.config.sidebarContainerId);

            // 1. Ocultar sidebar original (agregando clase de SASS)
            $sidebar.closest('.card').parent().addClass('hide-booking-sidebar');
            $sidebar.addClass('d-none');

            // 2. Determinar texto del botón según tipo de reserva
            const isInstant = $('#booking_type').val() === 'instant';
            const btnText = isInstant 
                ? (window.RedaAlojamientoJson["Reservar"] || "Reservar")
                : (window.RedaAlojamientoJson["Solicitud para reservar"] || "Solicitud para reservar");

            // 3. Inyectar botón flotante
            const floatingBtnHtml = `
                <div class="${this.config.floatingBtnContainerClass}">
                    <a href="javascript:void(0)" class="btn-flotante-inner" id="trigger-modal-reserva">
                        <i class="far fa-calendar-alt mr-2"></i>
                        <span>${btnText}</span>
                    </a>
                </div>
            `;
            $('body').append(floatingBtnHtml);

            // Animación sutil de entrada mediante clase SASS
            setTimeout(() => {
                $(`.${this.config.floatingBtnContainerClass}`).addClass('is-visible');
            }, 100);

            // 4. Personalizar el botón de envío (que estará dentro del modal)
            const $saveBtn = $(this.config.saveBtnId);
            const enviarText = window.RedaAlojamientoJson["Enviar"] || "Enviar";

            $saveBtn.find('span:not(.display-off)').text(enviarText);
            $saveBtn.addClass('btn-enviar-latido');
        },

        /**
         * Realiza la verificación AJAX estandarizada y procede a abrir el modal o redirigir.
         */
        ejecutarAperturaSegura: function() {
            const self = this;
            const $form = $(this.config.formId);
            const $modalBody = $(this.config.modalBodyId);
            const $modal = $(this.config.modalId);

            if (window.AuthCheck) {
                const propertyId = String($('input[name="property_id"]').val());
                const propertyName = $('.property-name').text().trim() || $('h1').first().text().trim() || "";
                const slug = window.location.pathname.split('/').pop();
                
                if (window.RedaNotificaciones && typeof window.RedaNotificaciones.esperar === 'function') {
                    window.RedaNotificaciones.esperar();
                }

                $.ajax({
                    url: `${window.APP_URL}/reda/bookings/check-active`,
                    type: 'GET',
                    success: function(data) {
                        if (data.success && typeof data.respuesta === 'object' && data.respuesta[propertyId]) {
                            // Si ya tiene reserva, redirigimos a viajes activos con alerta, nombre de propiedad, ID y slug para scroll
                            window.location.href = `${window.APP_URL}/trips/active?reda_alert=active_booking&property_id=${propertyId}&property_name=${encodeURIComponent(propertyName)}&property_slug=${slug}`;
                        } else {
                            self.mostrarModalFinal($form, $modalBody, $modal);
                        }
                    },
                    error: function() {
                        self.mostrarModalFinal($form, $modalBody, $modal);
                    },
                    complete: function() {
                        if (window.RedaNotificaciones && typeof window.RedaNotificaciones.ocultar === 'function') {
                            window.RedaNotificaciones.ocultar();
                        }
                    }
                });
            } else {
                this.mostrarModalFinal($form, $modalBody, $modal);
            }
        },

        /**
         * Muestra el modal físicamente después de las validaciones.
         */
        mostrarModalFinal: function($form, $modalBody, $modal) {
            if (!$modalBody.find(this.config.formId).length) {
                $modalBody.append($form);
                $form.removeClass('d-none').show();
            }
            $modal.modal('show');
        },

        /**
         * Maneja el disparador por hash (#reservar) en la URL.
         */
        handleHash: function() {
            if (window.location.hash === this.config.hashTrigger) {
                if (!window.AuthCheck) {
                    const slug = window.location.pathname.split('/').pop();
                    window.location.href = `${window.APP_URL}/reda/auth-reserve/${slug}`;
                    return;
                }

                setTimeout(() => {
                    this.ejecutarAperturaSegura();
                }, 500);
            }
        },

        /**
         * Asocia los eventos de clic y cambios de estado.
         */
        bindEvents: function() {
            const self = this;

            $(document).on('click', '#trigger-modal-reserva', function(e) {
                e.preventDefault();

                if (!window.AuthCheck) {
                    const slug = window.location.pathname.split('/').pop();
                    window.location.href = `${window.APP_URL}/reda/auth-reserve/${slug}`;
                    return;
                }

                self.ejecutarAperturaSegura();
            });

            $(window).on('hashchange', function() {
                self.handleHash();
            });
        }
    };

    $(function() {
        PropertyDetailReservation.init();
    });

})(jQuery);
