/**
 * Script reserve-injection.js
 * 
 * Este script se encarga de inyectar dinámicamente el botón de "Reservar" o "Ver reserva"
 * en las tarjetas de inmuebles (cards) detectadas en la página.
 */

(function( $ ) {
    'use strict';

    console.log('REDA Reserve Injection: Iniciando script en ' + window.location.href);

    let activeBookingProperties = {
        properties: {},
        bookings: [],
        codes: []
    }; 
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
                        console.log('REDA Reserve Injection: Lista blanca sincronizada');
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
        
        // 1. OBTENER IDENTIFICADORES
        let propertyId = getPropertyId(card);
        let propertySlug = getPropertySlug(card);
        let bookingId = getBookingId(card);
        let bookingCode = getBookingCode(card);

        // 2. RECUPERAR ID DE PROPIEDAD DESDE EL MAPA SI NO ESTÁ EN EL DOM (POR SLUG)
        if (!propertyId && propertySlug && bookingsFetched) {
            for (let id in activeBookingProperties.properties) {
                if (activeBookingProperties.properties[id].slug === propertySlug) {
                    propertyId = id;
                    break;
                }
            }
        }

        // 3. DETERMINAR VIGENCIA DE FECHA (PARA PRIORIDAD)
        let esFechaVigente = true; // Por defecto asumimos vigente para Home/Propiedad
        const calendarText = $(card).find('i.fa-calendar').parent().text().trim();
        const fechas = calendarText.split('-');
        if (fechas.length > 1) {
            const fechaFinStr = fechas[1].trim(); 
            const fechaFin = new Date(fechaFinStr);
            const hoy = new Date();
            hoy.setHours(0,0,0,0);
            if (!isNaN(fechaFin) && fechaFin < hoy) {
                esFechaVigente = false;
            }
        }

        // 4. DETERMINAR SI DEBE SER "VER RESERVA" (Sincronizado con Servidor)
        let forzarVerReserva = false;

        if (bookingsFetched) {
            const propEnWhitelist = !!(propertyId && activeBookingProperties.properties[propertyId]);
            
            if (isTripsPage) {
                // En Mis Viajes: Prioridad a la FECHA y luego al ID específico
                const idReservaEnWhitelist = (bookingId && activeBookingProperties.bookings.includes(Number(bookingId))) ||
                                            (bookingCode && activeBookingProperties.codes.includes(bookingCode));
                
                // Si la fecha ya pasó -> Siempre "Reservar"
                if (!esFechaVigente) {
                    forzarVerReserva = false;
                } else {
                    // Si la fecha es vigente: "Ver reserva" si el ID o la Propiedad están en la lista blanca
                    forzarVerReserva = idReservaEnWhitelist || propEnWhitelist;
                }
            } else {
                // En Home o Propiedad: Confiamos en el mapa del servidor (que ya está filtrado por fecha y pago)
                forzarVerReserva = propEnWhitelist;
            }
        }

        // 5. LÓGICA DE INYECCIÓN / ACTUALIZACIÓN
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

    function getBookingId(card) {
        const paymentLink = card.querySelector('a[href*="booking_payment/"]');
        if (paymentLink) {
            const href = paymentLink.getAttribute('href');
            const parts = href.split('booking_payment/');
            return parts.length > 1 ? parts[1].split('?')[0] : null;
        }
        return null;
    }

    function getBookingCode(card) {
        const receiptLink = card.querySelector('a[href*="code="]');
        if (receiptLink) {
            const href = receiptLink.getAttribute('href');
            const parts = href.split('code=');
            return parts.length > 1 ? parts[1].split('&')[0] : null;
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
