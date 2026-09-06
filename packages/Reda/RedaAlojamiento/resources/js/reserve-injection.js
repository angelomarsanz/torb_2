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
 */

(function( $ ) {
    'use strict';

    console.log('REDA Reserve Injection: Iniciando script en ' + window.location.href);

    let activeBookingPropertyIds = [];
    let isFetchingBookings = false;
    let bookingsFetched = false;

    /**
     * Realiza una petición AJAX para obtener los IDs de las propiedades con reservas activas.
     * Muestra una animación de espera durante la carga.
     * 
     * @returns {Promise} Resuelve cuando la petición termina.
     */
    async function fetchActiveBookings() {
        if (!window.AuthCheck || isFetchingBookings || bookingsFetched) return;
        
        isFetchingBookings = true;
        
        // Mostrar animación de espera según lineamientos
        if (window.RedaNotificaciones && typeof window.RedaNotificaciones.esperar === 'function') {
            window.RedaNotificaciones.esperar();
        }

        return new Promise((resolve) => {
            $.ajax({
                url: `${window.APP_URL}/reda/bookings/check-active`,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.success && Array.isArray(data.respuesta)) {
                        activeBookingPropertyIds = data.respuesta.map(id => String(id));
                    }
                    bookingsFetched = true;
                    resolve(data);
                },
                error: function(x, xs, xt) {
                    console.error('REDA Reserve Injection: Error al obtener reservas activas', x);
                    bookingsFetched = true; // Evitar reintentos infinitos si falla
                    resolve({ success: false });
                },
                complete: function() {
                    isFetchingBookings = false;
                    // Ocultar animación de espera
                    if (window.RedaNotificaciones && typeof window.RedaNotificaciones.ocultar === 'function') {
                        window.RedaNotificaciones.ocultar();
                    }
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
        if (card.querySelector('.reda-reserve-btn')) {
            // Si ya existe el botón, verificamos si necesita actualizarse (de Reservar a Ver reserva)
            const existingBtn = card.querySelector('.reda-btn-reservar');
            const propertyId = getPropertyId(card);
            
            if (existingBtn && propertyId && activeBookingPropertyIds.includes(String(propertyId))) {
                const verReservaText = (window.RedaAlojamientoJson && window.RedaAlojamientoJson["Ver reserva"]) || "Ver reserva";
                if (!existingBtn.textContent.includes(verReservaText)) {
                    existingBtn.innerHTML = `<i class="far fa-calendar-check"></i> ${verReservaText}`;
                    existingBtn.href = `${window.APP_URL}/trips/active`;
                }
            }
            return;
        }

        // Evitar inyectar en el formulario de reserva de la página individual
        if (card.querySelector('#booking_form')) return;

        // Intentar encontrar el ID y el slug de la propiedad
        let propertyId = getPropertyId(card);
        let propertySlug = null;
        
        // Buscar el slug en los enlaces de la tarjeta
        const propertyLink = card.querySelector('a[href*="properties/"]');
        if (propertyLink) {
            const href = propertyLink.getAttribute('href');
            // Extraer slug del href
            const parts = href.split('properties/');
            if (parts.length > 1) {
                propertySlug = parts[1].split('?')[0].split('#')[0];
            }
        }

        if (!propertySlug && !propertyId) return;

        // Encontrar el contenedor donde se adjuntará el botón
        const container = card.querySelector('.review-0') || 
                          card.querySelector('.card-body') || 
                          card;

        if (!container) return;

        const buttonWrapper = document.createElement('div');
        buttonWrapper.className = 'reda-reserve-btn';
        
        let targetUrl = propertySlug ? `${window.APP_URL}/properties/${propertySlug}#reservar` : `${window.APP_URL}/payments/book/${propertyId}`;
        let buttonText = (window.RedaAlojamientoJson && window.RedaAlojamientoJson["Reservar"]) || "Reservar";

        // Lógica de REDA: Si el usuario está logueado y tiene reserva activa, cambiar a "Ver reserva"
        if (window.AuthCheck && propertyId && activeBookingPropertyIds.includes(String(propertyId))) {
            targetUrl = `${window.APP_URL}/trips/active`;
            buttonText = (window.RedaAlojamientoJson && window.RedaAlojamientoJson["Ver reserva"]) || "Ver reserva";
        } else if (!window.AuthCheck && propertySlug) {
            // Si no está autenticado, redirigimos a través de nuestra ruta de control para asegurar el retorno
            targetUrl = `${window.APP_URL}/reda/auth-reserve/${propertySlug}`;
        }

        buttonWrapper.innerHTML = `
            <a href="${targetUrl}" class="btn-reda-chat-soft-v2 reda-btn-reservar">
                <i class="far fa-calendar-check"></i> ${buttonText}
            </a>
        `;

        container.appendChild(buttonWrapper);
    }

    /**
     * Obtiene el ID de la propiedad desde los atributos de datos de la tarjeta.
     * 
     * @param {HTMLElement} card El elemento DOM de la tarjeta.
     * @returns {string|null} El ID de la propiedad o null si no se encuentra.
     */
    function getPropertyId(card) {
        // Verificar data-id en botones de favoritos (estándar vRent)
        const bookmarkBtn = card.querySelector('.book_mark_change');
        if (bookmarkBtn) {
            return bookmarkBtn.getAttribute('data-id');
        }
        return null;
    }

    /**
     * Escanea el documento en busca de tarjetas y aplica la inyección del botón.
     */
    async function scan() {
        if (window.AuthCheck && !bookingsFetched && !isFetchingBookings) {
            await fetchActiveBookings();
        }
        const cardSelectors = '.card, .card-shadow, .card-1, .row.border.p-2.rounded-3, .col-md-6.col-lg-4.col-xl-3';
        const cards = document.querySelectorAll(cardSelectors);
        cards.forEach(addReserveButton);
    }

    /**
     * Inicializa los observadores y procesos de escaneo.
     */
    function init() {
        scan();

        const observer = new MutationObserver(() => scan());
        observer.observe(document.body, { childList: true, subtree: true });

        setInterval(scan, 2000);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})(jQuery);
