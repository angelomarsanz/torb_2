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
 * - Cambia dinámicamente el botón a "Ver reserva" si ya existe una reservación.
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
            this.checkActiveBookingStatus(); // Verificación proactiva para el botón flotante
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
         * Verifica proactivamente si el usuario ya tiene una reserva para esta propiedad
         * y actualiza el botón flotante si es necesario.
         */
        checkActiveBookingStatus: function() {
            if (!window.AuthCheck) return;

            const self = this;
            // REDA: Usamos el ID del input ya que el archivo original no tiene atributo 'name'
            const propertyId = String($('#property_id').val() || '');

            if (!propertyId) {
                console.warn('REDA Property Detail: No se pudo obtener el ID de la propiedad.');
                return;
            }

            $.ajax({
                url: `${window.APP_URL}/reda/bookings/check-active`,
                type: 'GET',
                success: function(data) {
                    if (data.success && typeof data.respuesta === 'object' && data.respuesta[propertyId]) {
                        console.log('REDA Property Detail: Reserva activa detectada proactivamente para ID:', propertyId);
                        const verReservaText = (window.RedaAlojamientoJson && window.RedaAlojamientoJson["Ver reserva"]) || "Ver reserva";
                        const $btn = $('#trigger-modal-reserva');

                        // Cambiamos el texto e ícono
                        $btn.find('span').text(verReservaText);
                        $btn.find('i').removeClass('far fa-calendar-alt').addClass('far fa-calendar-check');

                        // Cambiamos el comportamiento: de disparar modal de reserva a disparar modal de detalles
                        $btn.addClass('btn-reda-ver-reserva-modal');
                        $btn.attr('data-property-id', propertyId);
                        $btn.attr('href', 'javascript:void(0)');
                        $btn.removeAttr('id'); // Quitamos el ID que dispara el modal de reserva normal
                    }
                }
            });
        },

        /**
         * Realiza la verificación AJAX estandarizada y procede a abrir el modal o mostrar detalles.
         */
        ejecutarAperturaSegura: function() {
            const self = this;
            const $form = $(this.config.formId);
            const $modalBody = $(this.config.modalBodyId);
            const $modal = $(this.config.modalId);

            if (window.AuthCheck) {
                const propertyId = String($('#property_id').val() || '');

                if (window.RedaNotificaciones && typeof window.RedaNotificaciones.esperar === 'function') {
                    window.RedaNotificaciones.esperar();
                }

                $.ajax({
                    url: `${window.APP_URL}/reda/bookings/check-active`,
                    type: 'GET',
                    success: function(data) {
                        // REDA: Ocultamos el loader inmediatamente al recibir respuesta
                        if (window.RedaNotificaciones && typeof window.RedaNotificaciones.ocultar === 'function') {
                            window.RedaNotificaciones.ocultar();
                        }

                        if (data.success && typeof data.respuesta === 'object' && data.respuesta[propertyId]) {
                            // Si ya tiene reserva, disparamos el modal de detalles manualmente
                            const $tempBtn = $('<a class="btn-reda-ver-reserva-modal d-none"></a>')
                                .attr('data-property-id', propertyId)
                                .appendTo('body');
                            
                            setTimeout(() => {
                                $tempBtn.trigger('click');
                                $tempBtn.remove();
                            }, 100);

                        } else {
                            // Esperamos un momento para que el backdrop del loader se limpie antes de abrir el nuevo modal
                            setTimeout(() => {
                                self.mostrarModalFinal($form, $modalBody, $modal);
                            }, 600);
                        }
                    },
                    error: function() {
                        if (window.RedaNotificaciones && typeof window.RedaNotificaciones.ocultar === 'function') {
                            window.RedaNotificaciones.ocultar();
                        }
                        setTimeout(() => {
                            self.mostrarModalFinal($form, $modalBody, $modal);
                        }, 600);
                    }
                });
            } else {
                this.mostrarModalFinal($form, $modalBody, $modal);
            }
        },

        /**
         * Muestra el modal físicamente después de las validaciones, inhíbe el selector original
         * y configura la nueva lógica de calendario Flatpickr.
         */
        mostrarModalFinal: function($form, $modalBody, $modal) {
            const self = this;
            if (!$modalBody.find(this.config.formId).length) {
                $modalBody.append($form);
                $form.removeClass('d-none').show();
            }

            // 1. Inhibir/Desactivar el daterangepicker original del core para evitar interferencias
            const $daterangeBtn = $('#daterange-btn');
            if ($daterangeBtn.length) {
                $daterangeBtn.off('.daterangepicker');
                if ($daterangeBtn.data('daterangepicker')) {
                    console.log('REDA: Desactivando e inhibiendo daterangepicker original');
                    $daterangeBtn.data('daterangepicker').remove();
                }
            }

            // 2. Inyectar nuestros nuevos inputs de fecha si no existen en el formulario
            if (!$form.find('.reda-new-daterange-container').length) {
                console.log('REDA: Inyectando nuevos inputs de fecha compatibles con Flatpickr');
                const checkInLabel = (window.RedaAlojamientoJson && window.RedaAlojamientoJson["Llegada"]) || "Llegada";
                const checkOutLabel = (window.RedaAlojamientoJson && window.RedaAlojamientoJson["Salida"]) || "Salida";

                const newDaterangeHtml = `
                    <div class="row p-2 reda-new-daterange-container">
                        <div class="col-6 p-0">
                            <label>${checkInLabel}</label>
                            <div class="mr-2">
                                <input class="form-control reda-flatpickr-input" id="new_startDate" placeholder="dd-mm-yyyy" type="text" readonly required>
                            </div>
                        </div>
                        <div class="col-6 p-0">
                            <label>${checkOutLabel}</label>
                            <div class="ml-2">
                                <input class="form-control reda-flatpickr-input" id="new_endDate" placeholder="dd-mm-yyyy" type="text" readonly required>
                            </div>
                        </div>
                    </div>
                `;
                // Insertamos justo después del daterange-btn original (el cual estará oculto por CSS)
                $('#daterange-btn').after(newDaterangeHtml);
            }

            // 3. Inicializar Flatpickr en los nuevos inputs de la modal
            const initialStartDate = $('#startDate').val();
            const initialEndDate = $('#endDate').val();

            // Destruir instancias previas para evitar duplicidad de elementos/eventos al abrir varias veces
            if (window.redaFpCheckIn && typeof window.redaFpCheckIn.destroy === 'function') {
                window.redaFpCheckIn.destroy();
            }
            if (window.redaFpCheckOut && typeof window.redaFpCheckOut.destroy === 'function') {
                window.redaFpCheckOut.destroy();
            }

            // Inicializar llegada (Check-In)
            window.redaFpCheckIn = flatpickr("#new_startDate", {
                dateFormat: "d-m-Y",
                minDate: "today",
                locale: "es",
                defaultDate: initialStartDate || "today",
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates[0]) {
                        console.log('REDA Flatpickr: Fecha de llegada cambiada a:', dateStr);
                        // Sincronizar con el input original del core
                        $("#startDate").val(dateStr);

                        // Configurar la fecha mínima de salida (Checkout) para el día siguiente
                        const nextDay = new Date(selectedDates[0]);
                        nextDay.setDate(nextDay.getDate() + 1);
                        window.redaFpCheckOut.set("minDate", nextDay);

                        // Si el Checkout es menor o igual al Check-In, limpiarlo
                        if (window.redaFpCheckOut.selectedDates[0] && window.redaFpCheckOut.selectedDates[0] <= selectedDates[0]) {
                            window.redaFpCheckOut.clear();
                            $("#endDate").val("");
                        }

                        // Forzar el recálculo de precios del core
                        self.recargarPrecios();
                    }
                }
            });

            // Establecer fecha mínima inicial para el Checkout basada en el Check-In seleccionado
            const minCheckOutDate = window.redaFpCheckIn.selectedDates[0]
                ? new Date(window.redaFpCheckIn.selectedDates[0].getTime() + 86400000)
                : "today";

            // Inicializar salida (Check-Out)
            window.redaFpCheckOut = flatpickr("#new_endDate", {
                dateFormat: "d-m-Y",
                minDate: minCheckOutDate,
                locale: "es",
                defaultDate: initialEndDate || "",
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates[0]) {
                        console.log('REDA Flatpickr: Fecha de salida cambiada a:', dateStr);
                        // Sincronizar con el input original del core
                        $("#endDate").val(dateStr);

                        // Forzar el recálculo de precios del core
                        self.recargarPrecios();
                    }
                }
            });

            // Sincronizar el minDate del checkout inicialmente
            if (window.redaFpCheckIn.selectedDates[0]) {
                const nextDay = new Date(window.redaFpCheckIn.selectedDates[0]);
                nextDay.setDate(nextDay.getDate() + 1);
                window.redaFpCheckOut.set("minDate", nextDay);
            }

            // 4. Ocultar el selector de huéspedes original del core e inyectar el desglose de Adultos y Niños
            const $guestSelect = $form.find('#number_of_guests');
            const $originalGuestRow = $guestSelect.closest('.row');
            $originalGuestRow.addClass('d-none');

            if (!$form.find('.reda-new-guests-container').length) {
                console.log('REDA: Inyectando nuevos selectores de Adulto(s) y Niño(s)');
                const adultosLabel = (window.RedaAlojamientoJson && window.RedaAlojamientoJson["Adulto(s)"]) || "Adulto(s)";
                const ninosLabel = (window.RedaAlojamientoJson && window.RedaAlojamientoJson["Niño(s)"]) || "Niño(s)";

                // Capacidad máxima de la propiedad obtenida del último option del select original
                const maxCapacidad = parseInt($guestSelect.find('option').last().val()) || 1;
                const initialGuests = parseInt($guestSelect.val()) || 1;

                const newGuestsHtml = `
                    <div class="row p-2 reda-new-guests-container">
                        <div class="col-6 p-0">
                            <label class="font-weight-600 text-uppercase text-13">${adultosLabel}</label>
                            <div class="mr-2">
                                <select id="reda_adultos" name="adultos" class="form-control reda-flatpickr-input" style="height: 45px; border-radius: 8px;">
                                </select>
                            </div>
                        </div>
                        <div class="col-6 p-0">
                            <label class="font-weight-600 text-uppercase text-13">${ninosLabel}</label>
                            <div class="ml-2">
                                <select id="reda_ninos" name="ninos" class="form-control reda-flatpickr-input" style="height: 45px; border-radius: 8px;">
                                </select>
                            </div>
                        </div>
                    </div>
                `;

                $originalGuestRow.after(newGuestsHtml);

                const sincronizarHuespedes = function(adultosSel, ninosSel) {
                    adultosSel = parseInt(adultosSel) || 1;
                    ninosSel = parseInt(ninosSel) || 0;

                    // Ajustar si la suma excede la capacidad máxima
                    if (adultosSel + ninosSel > maxCapacidad) {
                        ninosSel = Math.max(0, maxCapacidad - adultosSel);
                    }

                    // Opciones de Adultos: mínimo 1, máximo maxCapacidad - ninosSel
                    const maxAdultos = Math.max(1, maxCapacidad - ninosSel);
                    let optAdultos = '';
                    for (let i = 1; i <= maxCapacidad; i++) {
                        if (i <= maxAdultos) {
                            optAdultos += `<option value="${i}" ${i === adultosSel ? 'selected' : ''}>${i}</option>`;
                        }
                    }
                    $('#reda_adultos').html(optAdultos);

                    // Opciones de Niños: mínimo 0, máximo maxCapacidad - adultosSel
                    const maxNinos = Math.max(0, maxCapacidad - adultosSel);
                    let optNinos = '';
                    for (let i = 0; i <= maxCapacidad - 1; i++) {
                        if (i <= maxNinos) {
                            optNinos += `<option value="${i}" ${i === ninosSel ? 'selected' : ''}>${i}</option>`;
                        }
                    }
                    $('#reda_ninos').html(optNinos);

                    const total = adultosSel + ninosSel;
                    $guestSelect.val(total);
                };

                sincronizarHuespedes(initialGuests, 0);

                $(document).off('change', '#reda_adultos').on('change', '#reda_adultos', function() {
                    const ad = parseInt($(this).val()) || 1;
                    const ni = parseInt($('#reda_ninos').val()) || 0;
                    sincronizarHuespedes(ad, ni);
                    self.recargarPrecios();
                });

                $(document).off('change', '#reda_ninos').on('change', '#reda_ninos', function() {
                    const ad = parseInt($('#reda_adultos').val()) || 1;
                    const ni = parseInt($(this).val()) || 0;
                    sincronizarHuespedes(ad, ni);
                    self.recargarPrecios();
                });
            }

            // Ejecutar un recálculo inicial para asegurar que la modal abra con los datos cargados correctamente
            self.recargarPrecios();

            $modal.modal('show');
        },

        /**
         * Invoca de manera segura la función global de recálculo de precios del core del sistema (Laravel/vRent).
         */
        recargarPrecios: function() {
            if (typeof window.price_calculation === 'function') {
                window.price_calculation('', '', '');
            } else if (typeof price_calculation === 'function') {
                price_calculation('', '', '');
            } else {
                console.warn('REDA: No se detectó la función global price_calculation en la ventana.');
            }
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
