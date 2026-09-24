/**
 * Script verDetalleReservaModal.js
 * 
 * Este script maneja la apertura de un modal dinámico para mostrar los detalles
 * de una reserva activa cuando el usuario hace clic en "Ver reserva".
 * 
 * Funcionalidades:
 * - Escucha clics en elementos con la clase .btn-reda-ver-reserva-modal.
 * - Obtiene datos del servidor mediante AJAX.
 * - Renderiza un modal con Bootstrap 4.5.
 */

(function( $ ) {
    'use strict';

    const modalId = 'modalDetalleReservaReda';

    /**
     * Obtiene los detalles de la reserva desde el servidor.
     * @param {number} propertyId 
     * @returns {Promise}
     */
    const obtenerDetallesReserva = (propertyId) => {
        return new Promise((resolve) => {
            $.ajax({
                url: `${window.APP_URL}/reda/bookings/details/${propertyId}`,
                type: 'GET',
                dataType: 'json',
                success: (data) => resolve(data),
                error: (x) => {
                    let respuesta = { success: false, mensaje_usuario: 'Error en el servidor' };
                    try { respuesta = JSON.parse(x.responseText); } catch (e) {}
                    resolve(respuesta);
                }
            });
        });
    };

    /**
     * Construye y muestra el modal con los datos de la reserva.
     * @param {Object} data 
     */
    const mostrarModalDetalle = (data) => {
        // Eliminar modal previo si existe
        $(`#${modalId}`).remove();

        const json = window.RedaAlojamientoJson || {};

        const html = `
            <div class="modal fade" id="${modalId}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title font-weight-bold">${json["Detalles de la reserva"] || "Detalles de la reserva"}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row">
                                <div class="col-md-5 mb-3 mb-md-0">
                                    <div class="position-relative">
                                        <img src="${data.propiedad_foto}" class="img-fluid rounded-3 shadow-sm w-100" style="height: 250px; object-fit: cover;" alt="${data.propiedad_nombre}">
                                        <div class="position-absolute" style="top: 10px; left: 10px;">
                                            <span class="badge badge-${data.estado_label} p-2 shadow-sm text-uppercase">${data.estado}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <h4 class="font-weight-bold mb-1" style="color: #484848;">${data.propiedad_nombre}</h4>
                                    <p class="text-muted mb-3">
                                        <i class="fas fa-map-marker-alt text-danger"></i> ${data.ubicacion.ciudad}, ${data.ubicacion.pais}
                                    </p>
                                    
                                    <div class="bg-light rounded-3 p-3 mb-3 border">
                                        <div class="row text-center">
                                            <div class="col-6 border-right">
                                                <small class="text-uppercase font-weight-bold text-muted d-block" style="font-size: 10px;">${json["Llegada"] || "Llegada"}</small>
                                                <span class="d-block font-weight-bold" style="font-size: 14px;">${data.fecha_inicio}</span>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-uppercase font-weight-bold text-muted d-block" style="font-size: 10px;">${json["Salida"] || "Salida"}</small>
                                                <span class="d-block font-weight-bold" style="font-size: 14px;">${data.fecha_fin}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="list-group list-group-flush mb-3">
                                        <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 py-2 border-top-0">
                                            <span class="text-muted"><i class="fas fa-users mr-2"></i> ${json["Huéspedes"] || "Huéspedes"}</span>
                                            <span class="font-weight-bold">${data.huespedes}</span>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 py-2">
                                            <span class="text-muted"><i class="fas fa-moon mr-2"></i> ${json["Noches"] || "Noches"}</span>
                                            <span class="font-weight-bold">${data.noches}</span>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 py-2">
                                            <span class="text-muted"><i class="fas fa-barcode mr-2"></i> ${json["Código"] || "Código"}</span>
                                            <span class="font-weight-bold text-uppercase">${data.codigo}</span>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                        <h5 class="font-weight-bold mb-0">${json["Costo Total"] || "Costo Total"}</h5>
                                        <h4 class="text-danger font-weight-bold mb-0">${data.simbolo_moneda} ${data.total}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-dismiss="modal">${json["Cerrar"] || "Cerrar"}</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('body').append(html);
        $(`#${modalId}`).modal('show');
    };

    /**
     * Inicializa los eventos del script.
     */
    const init = () => {
        $(document).on('click', '.btn-reda-ver-reserva-modal', async function(e) {
            e.preventDefault();
            const propertyId = $(this).data('property-id');
            const json = window.RedaAlojamientoJson || {};

            if (!propertyId) return;

            // Usar objeto global window.RedaNotificaciones definido en notificaciones.js
            if (window.RedaNotificaciones && typeof window.RedaNotificaciones.esperar === 'function') {
                window.RedaNotificaciones.esperar();
            }

            const respuesta = await obtenerDetallesReserva(propertyId);

            if (window.RedaNotificaciones && typeof window.RedaNotificaciones.ocultar === 'function') {
                window.RedaNotificaciones.ocultar();
            }

            if (respuesta.success) {
                mostrarModalDetalle(respuesta.respuesta);
            } else {
                // Mostrar error amigable usando el sistema de notificaciones
                if (window.RedaNotificaciones && typeof window.RedaNotificaciones.notificar === 'function') {
                    window.RedaNotificaciones.notificar(json["Error"] || "Error", respuesta.mensaje_usuario || 'Error al obtener detalles', 'error');
                } else {
                    alert(respuesta.mensaje_usuario || 'Error al obtener detalles');
                }
            }
        });
    };

    $(function() {
        init();
    });

})(jQuery);
