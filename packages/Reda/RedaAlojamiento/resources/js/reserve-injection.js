/**
 * Script reserve-injection.js
 * 
 * Este script se encarga de inyectar dinámicamente el botón de "Reservar" o "Ver reserva"
 * en las tarjetas de inmuebles (cards) detectadas en la página.
 */

(function( $ ) {
    'use strict';

    console.log('REDA Reserve Injection: Iniciando script en ' + window.location.href);

    let activeBookingProperties = {}; 
    let isFetchingBookings = false;
    let bookingsFetched = false;
    let isScanning = false;
    let scanTimeout = null;

    /**
     * Realiza una petición AJAX para obtener los IDs y nombres de las propiedades con reservas activas.
     */
    async function fetchActiveBookings() {
        if (!window.AuthCheck || isFetchingBookings || bookingsFetched) return;
        
        isFetchingBookings = true;
        return new Promise((resolve) => {
            $.ajax({
                url: `${window.APP_URL}/reda/bookings/check-active`,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.success && typeof data.respuesta === 'object') {
                        activeBookingProperties = data.respuesta;
                    }
                    bookingsFetched = true;
                    resolve(data);
                },
                error: function() {
                    bookingsFetched = true; 
                    resolve({ success: false });
                },
                complete: function() {
                    isFetchingBookings = false;
                    debounceScan();
                }
            });
        });
    }

    /**
     * Ejecuta el escaneo con un pequeño retraso para evitar ejecuciones masivas.
     */
    function debounceScan() {
        if (scanTimeout) clearTimeout(scanTimeout);
        scanTimeout = setTimeout(() => {
            scan();
        }, 500);
    }

    /**
     * Inyecta el botón de reserva en una tarjeta específica si no existe ya.
     */
    function addReserveButton(card) {
        if (card.querySelector('#booking_form')) return;

        const path = window.location.pathname;
        const isTripsPage = path.includes('/trips/active');
        
        let propertyId = getPropertyId(card);
        let propertySlug = getPropertySlug(card);

        let infoReserva = null;
        if (propertyId && activeBookingProperties[String(propertyId)]) {
            infoReserva = activeBookingProperties[String(propertyId)];
        } else if (propertySlug) {
            for (let id in activeBookingProperties) {
                if (activeBookingProperties[id].slug === propertySlug) {
                    infoReserva = activeBookingProperties[id];
                    propertyId = id;
                    break;
                }
            }
        }

        // --- DETERMINACIÓN DE LÓGICA "VER RESERVA" (REGLAS ACTUALIZADAS) ---
        let forzarVerReserva = false;

        if (isTripsPage) {
            // Regla de Oro: LA FECHA TIENE PRIORIDAD MÁXIMA
            const calendarText = $(card).find('i.fa-calendar').parent().text().trim();
            const fechas = calendarText.split('-');
            let fechaFinVigente = false;

            if (fechas.length > 1) {
                const fechaFinStr = fechas[1].trim(); 
                const fechaFin = new Date(fechaFinStr);
                const hoy = new Date();
                hoy.setHours(0,0,0,0);

                if (!isNaN(fechaFin) && fechaFin >= hoy) {
                    fechaFinVigente = true;
                }
            }

            if (fechaFinVigente) {
                // Si la fecha es futura o hoy, siempre es Ver Reserva
                forzarVerReserva = true;
            } else {
                // Si la fecha ya pasó, verificamos si el estatus aún obliga a Ver Reserva
                const statusBadge = card.querySelector('.badge');
                const textContent = statusBadge ? statusBadge.textContent.trim().toLowerCase() : '';
                
                const estadosVer = ['actual', 'pendiente', 'próximamente', 'pending', 'processing', 'procesando', 'upcoming', 'current'];
                const estadosReservar = ['completado', 'completada', 'vencido', 'vencida', 'rechazado', 'rechazada', 'completed', 'expired', 'declined', 'cancelled', 'cancelada'];

                if (estadosVer.some(s => textContent.includes(s))) {
                    forzarVerReserva = true;
                } else if (estadosReservar.some(s => textContent.includes(s))) {
                    forzarVerReserva = false;
                }
            }
        } else if (infoReserva) {
            // Fuera de Mis Viajes, confiamos en el mapa filtrado del servidor
            forzarVerReserva = true;
        }

        const existingRedaBtn = card.querySelector('.reda-reserve-btn');
        const verReservaText = (window.RedaAlojamientoJson && window.RedaAlojamientoJson["Ver reserva"]) || "Ver reserva";
        const reservarText = (window.RedaAlojamientoJson && window.RedaAlojamientoJson["Reservar"]) || "Reservar";

        if (existingRedaBtn) {
            const btn = existingRedaBtn.querySelector('.reda-btn-reservar');
            if (forzarVerReserva && propertyId) {
                if (!btn.textContent.includes(verReservaText)) {
                    btn.innerHTML = `<i class="far fa-calendar-check"></i> ${verReservaText}`;
                    btn.classList.add('btn-reda-ver-reserva-modal');
                    btn.setAttribute('data-property-id', propertyId);
                    btn.href = 'javascript:void(0)';
                }
            } else {
                if (btn.textContent.includes(verReservaText)) {
                    btn.innerHTML = `<i class="far fa-calendar-check"></i> ${reservarText}`;
                    btn.classList.remove('btn-reda-ver-reserva-modal');
                    btn.removeAttribute('data-property-id');
                    btn.href = propertySlug ? `${window.APP_URL}/properties/${propertySlug}#reservar` : `${window.APP_URL}/payments/book/${propertyId}`;
                }
            }
            return;
        }

        if (!propertySlug && !propertyId) return;

        const container = card.querySelector('.review-0') || card.querySelector('.card-body') || card;
        if (!container) return;

        const buttonWrapper = document.createElement('div');
        buttonWrapper.className = 'reda-reserve-btn';
        
        let targetUrl = propertySlug ? `${window.APP_URL}/properties/${propertySlug}#reservar` : `${window.APP_URL}/payments/book/${propertyId}`;
        let buttonText = reservarText;
        let extraClass = '';
        let dataAttrs = '';

        if (forzarVerReserva && propertyId) {
            targetUrl = 'javascript:void(0)';
            buttonText = verReservaText;
            extraClass = 'btn-reda-ver-reserva-modal';
            dataAttrs = `data-property-id="${propertyId}"`;
        } else if (!window.AuthCheck && propertySlug) {
            targetUrl = `${window.APP_URL}/reda/auth-reserve/${propertySlug}`;
        }

        buttonWrapper.innerHTML = `
            <a href="${targetUrl}" class="btn-reda-chat-soft-v2 reda-btn-reservar ${extraClass}" ${dataAttrs}>
                <i class="far fa-calendar-check"></i> ${buttonText}
            </a>
        `;

        container.appendChild(buttonWrapper);
    }

    function getPropertyId(card) {
        const bookmarkBtn = card.querySelector('.book_mark_change');
        if (bookmarkBtn) return bookmarkBtn.getAttribute('data-id');
        const link = card.querySelector('a[data-id]');
        if (link) return link.getAttribute('data-id');
        return null;
    }

    function getPropertySlug(card) {
        const propertyLink = card.querySelector('a[href*="properties/"]');
        if (propertyLink) {
            const href = propertyLink.getAttribute('href');
            const parts = href.split('properties/');
            if (parts.length > 1) {
                return parts[1].split('?')[0].split('#')[0];
            }
        }
        return null;
    }

    async function scan() {
        if (isScanning) return;
        isScanning = true;

        try {
            if (window.AuthCheck && !bookingsFetched && !isFetchingBookings) {
                await fetchActiveBookings();
            }
            const cardSelectors = '.card, .card-shadow, .card-1, .row.border.p-2.rounded-3, .col-md-6.col-lg-4.col-xl-3';
            const cards = document.querySelectorAll(cardSelectors);
            cards.forEach(addReserveButton);
        } finally {
            isScanning = false;
        }
    }

    function init() {
        scan();
        const observer = new MutationObserver((mutations) => {
            const shouldReact = mutations.some(m => {
                return m.type === 'childList' && 
                       !$(m.target).hasClass('reda-reserve-btn') && 
                       !$(m.target).hasClass('reda-btn-reservar');
            });
            if (shouldReact) debounceScan();
        });
        observer.observe(document.body, { childList: true, subtree: true });
        setInterval(debounceScan, 5000);
    }

    if (typeof jQuery !== 'undefined') {
        $(document).ready(init);
    } else {
        document.addEventListener('DOMContentLoaded', init);
    }
})(jQuery);
