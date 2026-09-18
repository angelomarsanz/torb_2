/**
 * Resumen: Script para ocultar las consultas (Inquiries) en las vistas de "Mis Viajes" y "Mis Reservas".
 */

(function( $ ) {
    "use strict";

    console.log('REDA: Script ocultar-consultas.js CARGADO');

    const filtrarConsultas = () => {
        const path = window.location.pathname;
        console.log('REDA: Procesando path:', path);

        const $filas = $('.row.border.p-2');
        console.log(`REDA: Se encontraron ${$filas.length} filas en total.`);

        $filas.each(function(index) {
            const $fila = $(this);
            const textoFila = $fila.text().toLowerCase();
            const tieneInquiry = textoFila.includes('inquiry') || textoFila.includes('consulta');
            
            console.log(`REDA: Fila ${index} - Texto: ${textoFila.substring(0, 100)}... - ¿Tiene Inquiry/Consulta?: ${tieneInquiry}`);

            if (tieneInquiry) {
                console.log(`REDA: ¡Consulta detectada en Fila ${index}! Ocultando...`);
                $fila.hide();
                // Si la fila ocultada tiene un badge, logueamos sus detalles para saber por qué falló el filtro anterior
                const $badge = $fila.find('.badge');
                if ($badge.length) {
                    console.log(`REDA: Detalles del badge ocultado: { texto: "${$badge.text().trim()}", clases: "${$badge.attr('class')}" }`);
                }
            }
        });

        verificarContenedorVacio();
    };

    const verificarContenedorVacio = () => {
        const $filasVisibles = $('.row.border.p-2:visible');
        if ($filasVisibles.length === 0 && $('.row.border.p-2').length > 0) {
            if (!$('#reda-empty-state-message').length) {
                console.log('REDA: Mostrando mensaje de listado vacío.');
                const msg = (window.RedaAlojamientoJson && window.RedaAlojamientoJson["No se encontraron resultados"]) || "No se encontraron resultados";
                const $resultados = $('.list-bacground').parent();
                $resultados.append(`<div id="reda-empty-state-message" class="text-center p-5 mt-5"><p>${msg}</p></div>`);
            }
        } else {
            $('#reda-empty-state-message').remove();
        }
    };

    $(function() {
        filtrarConsultas();
        setTimeout(filtrarConsultas, 1000);
        setTimeout(filtrarConsultas, 3000);

        const observer = new MutationObserver(() => filtrarConsultas());
        const target = document.querySelector('.main-panel') || document.body;
        observer.observe(target, { childList: true, subtree: true });
    });

})(jQuery);
