/**
 * Resumen: Script para ocultar las consultas (Inquiries) y reservaciones sin pago
 * en las vistas de "Mis Viajes" y "Mis Reservas".
 * 
 * Funcionalidades:
 * - Obtiene del servidor los datos de reservaciones pagadas (IDs, Códigos, Nombres y Fechas).
 * - Filtra dinámicamente el DOM mediante emparejamiento por texto para ocultar registros sin pago.
 * - Maneja estados vacíos tras el filtrado.
 */

(function( $ ) {
    "use strict";

    console.log('REDA: Script ocultar-consultas.js CARGADO');

    let paidBookings = [];
    let bookingsFetched = false;
    let isFetching = false;

    /**
     * Obtiene del servidor la lista de reservaciones que tienen algún pago registrado.
     */
    const fetchPaidBookings = async () => {
        if (isFetching || bookingsFetched) return;
        isFetching = true;

        return new Promise((resolve) => {
            $.ajax({
                url: APP_URL + '/reda/bookings/paid-ids',
                type: 'GET',
                success: function(data) {
                    if (data.success) {
                        paidBookings = data.respuesta.items || [];
                        console.log('REDA: Datos de reservaciones pagadas obtenidos:', paidBookings);
                    }
                    bookingsFetched = true;
                    resolve(data);
                },
                error: function() {
                    bookingsFetched = true; // Evitar bucles en caso de error
                    resolve({ success: false });
                },
                complete: function() {
                    isFetching = false;
                    filtrarFilas(); // Forzar filtrado tras obtener datos
                }
            });
        });
    };

    /**
     * Filtra las filas de reservaciones en el DOM.
     * Utiliza emparejamiento por ID/Código o por Nombre+Fechas para identificar registros pagados.
     */
    const filtrarFilas = () => {
        const path = window.location.pathname;
        const isTripsPage = path.includes('/trips/active');

        const $filas = $('.row.border.p-2');
        
        $filas.each(function(index) {
            const $fila = $(this);
            const textoFila = $fila.text().toLowerCase();
            
            // 1. Filtrado de Consultas (Inquiry)
            const tieneInquiry = textoFila.includes('inquiry') || textoFila.includes('consulta');
            if (tieneInquiry) {
                $fila.hide();
                return;
            }

            // 2. Filtrado de Reservaciones sin Pago (Solo en Mis Viajes)
            if (isTripsPage && bookingsFetched) {
                let tienePago = false;

                // --- ESTRATEGIA A: Emparejamiento por Enlaces (ID o Código) ---
                const links = $fila.find('a').map(function() { return $(this).attr('href'); }).get();
                for (let link of links) {
                    if (!link) continue;
                    for (let item of paidBookings) {
                        if ((item.id && (link.includes('/' + item.id) || link.endsWith('/' + item.id))) ||
                            (item.code && link.includes('code=' + item.code))) {
                            tienePago = true;
                            break;
                        }
                    }
                    if (tienePago) break;
                }

                // --- ESTRATEGIA B: Emparejamiento por Texto (Nombre + Fechas) ---
                if (!tienePago) {
                    const nombrePropiedad = $fila.find('p.text-18').text().trim().toLowerCase();
                    const rangoFechas = $fila.find('i.fa-calendar').parent().text().trim().toLowerCase();

                    for (let item of paidBookings) {
                        const itemNombre = (item.property_name || '').toLowerCase();
                        const itemFechaInicio = (item.start_date || '').toLowerCase();
                        const itemFechaFin = (item.end_date || '').toLowerCase();

                        // Verificamos si el nombre coincide y si AMBAS fechas están presentes en el texto del rango
                        if (nombrePropiedad.includes(itemNombre) && 
                            rangoFechas.includes(itemFechaInicio) && 
                            rangoFechas.includes(itemFechaFin)) {
                            tienePago = true;
                            break;
                        }
                    }
                }

                // --- ESTRATEGIA C: Salvaguarda por Status "Aceptado" ---
                if (!tienePago) {
                    const statusBadge = $fila.find('.badge');
                    const status = statusBadge.text().trim().toLowerCase();
                    if (status.includes('aceptado') || status.includes('accepted')) {
                        tienePago = true; 
                    }
                }

                if (!tienePago) {
                    console.log(`REDA: Ocultando fila ${index} (No se encontró pago para "${$fila.find('p.text-18').text().trim()}").`);
                    $fila.hide();
                } else {
                    $fila.show();
                }
            }
        });

        verificarContenedorVacio();
    };

    const verificarContenedorVacio = () => {
        const $filasVisibles = $('.row.border.p-2:visible');
        if ($filasVisibles.length === 0 && $('.row.border.p-2').length > 0) {
            if (!$('#reda-empty-state-message').length) {
                const msg = (window.RedaAlojamientoJson && window.RedaAlojamientoJson["No se encontraron resultados"]) || "No se encontraron resultados";
                const $resultados = $('.list-bacground').parent();
                $resultados.append(`<div id="reda-empty-state-message" class="text-center p-5 mt-5"><p>${msg}</p></div>`);
            }
        } else {
            $('#reda-empty-state-message').remove();
        }
    };

    $(function() {
        if (window.location.pathname.includes('/trips/active')) {
            fetchPaidBookings();
        }

        filtrarFilas();
        
        const observer = new MutationObserver(() => filtrarFilas());
        const target = document.querySelector('.main-panel') || document.body;
        if (target) {
            observer.observe(target, { childList: true, subtree: true });
        }

        // Ejecuciones retardadas para asegurar captura tras renderizado del core
        setTimeout(filtrarFilas, 1000);
        setTimeout(filtrarFilas, 3000);
    });

})(jQuery);
