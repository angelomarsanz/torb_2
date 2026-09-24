/**
 * Script reserve-injection.js
 * 
 * Este script se encarga de inyectar dinámicamente el botón de "Reservar" o "Ver reserva"
 * en las tarjetas de inmuebles (cards) detectadas en la página.
 * 
 * Funcionalidades:
 * - Detecta el ID y slug de la propiedad desde el DOM.
 * - Verifica si el usuario tiene una reserva activa para cambiar el texto del botón.
 * - Implementa un MutationObserver para manejar contenido cargado dinámicamente.
 * - Soporta emparejamiento por slug en la vista de viajes (/trips/active).
 */

(function( $ ) {
    'use strict';

    console.log('REDA Reserve Injection: Iniciando script en ' + window.location.href);

    let activeBookingProperties = {}; // Estructura: { id: { name, slug } }
    let isFetchingBookings = false;
    let bookingsFetched = false;

    /**
     * Realiza una petición AJAX para obtener los IDs y nombres de las propiedades con reservas activas.
     * 
     * @returns {Promise} Resuelve cuando la petición termina.
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
                        console.log('REDA Reserve Injection: Mapa de reservas activas obtenido:', activeBookingProperties);
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
                    scan();
                }
            });
        });
    }

    /**
     * Inyecta el botón de reserva en una tarjeta específica si no existe ya.
     * 
     * @param {HTMLElement} card El elemento DOM de la tarjeta.
     */
    function addReserveButton(card) {
        if (card.querySelector('#booking_form')) return;

        const path = window.location.pathname;
        const isTripsPage = path.includes('/trips/active');
        
        // 1. OBTENER IDENTIFICADORES (ID o Slug)
        let propertyId = getPropertyId(card);
        let propertySlug = getPropertySlug(card);

        // 2. BUSCAR EN EL MAPA DE RESERVAS ACTIVAS/PASADAS
        let infoReserva = null;
        if (propertyId && activeBookingProperties[String(propertyId)]) {
            infoReserva = activeBookingProperties[String(propertyId)];
        } else if (propertySlug) {
            // Busqueda por slug (útil en página de viajes)
            for (let id in activeBookingProperties) {
                if (activeBookingProperties[id].slug === propertySlug) {
                    infoReserva = activeBookingProperties[id];
                    propertyId = id;
                    break;
                }
            }
        }

        // 3. DETERMINAR SI DEBE SER "VER RESERVA"
        let forzarVerReserva = false;
        if (isTripsPage) {
            const statusBadge = card.querySelector('.badge');
            const statusText = statusBadge ? statusBadge.textContent.trim().toLowerCase() : '';
            
            // Criterio A: Estatus específico
            const estadosVer = ['actual', 'pendiente', 'próximamente', 'accepted', 'pending', 'processing'];
            if (estadosVer.some(s => statusText.includes(s))) {
                forzarVerReserva = true;
            }

            // Criterio B: Fecha final <= hoy
            if (!forzarVerReserva) {
                const calendarText = $(card).find('i.fa-calendar').parent().text().trim();
                const fechas = calendarText.split('-');
                if (fechas.length > 1) {
                    const fechaFinStr = fechas[1].trim(); // Formato: "M d, Y"
                    const fechaFin = new Date(fechaFinStr);
                    const hoy = new Date();
                    hoy.setHours(0,0,0,0);
                    if (!isNaN(fechaFin) && fechaFin <= hoy) {
                        forzarVerReserva = true;
                    }
                }
            }
        } else if (infoReserva) {
            // Fuera de viajes, si está en el mapa -> Es Ver Reserva
            forzarVerReserva = true;
        }

        // 4. LÓGICA DE INYECCIÓN / ACTUALIZACIÓN
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
            }
            return;
        }

        // INYECCIÓN INICIAL (Si no existe el botón)
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
        if (window.AuthCheck && !bookingsFetched && !isFetchingBookings) {
            await fetchActiveBookings();
        }
        const cardSelectors = '.card, .card-shadow, .card-1, .row.border.p-2.rounded-3, .col-md-6.col-lg-4.col-xl-3';
        const cards = document.querySelectorAll(cardSelectors);
        cards.forEach(addReserveButton);
    }

    function init() {
        scan();
        const observer = new MutationObserver(() => scan());
        observer.observe(document.body, { childList: true, subtree: true });
        setInterval(scan, 3000);
    }

    if (typeof jQuery !== 'undefined') {
        $(document).ready(init);
    } else {
        document.addEventListener('DOMContentLoaded', init);
    }
})(jQuery);
