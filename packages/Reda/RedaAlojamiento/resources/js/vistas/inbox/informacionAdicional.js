/**
 * Abre desde el inbox o correo las solicitudes autorizadas y carga documentos privados.
 * El servidor comprueba de nuevo el destinatario y el plazo en cada operación.
 */
(function ($) {
    'use strict';

    /** Muestra un error del servidor sin interpretar HTML de terceros. */
    const mostrarError = (error) => {
        const trans = window.RedaAlojamientoJson || {};
        const mensaje = error.responseJSON?.mensaje_usuario || trans["No se pudo procesar la solicitud."] || "No se pudo procesar la solicitud.";
        window.RedaNotificaciones.notificar(trans["Información"] || "Información", $('<div>').text(mensaje).html(), 'error');
    };

    /** Consulta la vigencia y presenta el formulario únicamente al destinatario. */
    const abrirSolicitud = async (id) => {
        if (!/^[1-9]\d*$/.test(String(id))) return;
        const trans = window.RedaAlojamientoJson || {};
        window.RedaNotificaciones.esperar();
        try {
            const resultado = await $.ajax({ url: APP_URL + '/reda/informacion-mediacion/' + id, type: 'GET' });
            window.RedaNotificaciones.ocultar();
            setTimeout(() => {
                $('#reda-modal-cargar-informacion').remove();
                $('body').append(`
                    <div class="modal fade" id="reda-modal-cargar-informacion" tabindex="-1" role="dialog" aria-labelledby="reda-titulo-cargar-informacion" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content">
                        <form id="reda-form-cargar-informacion">
                          <div class="modal-header"><h5 class="modal-title" id="reda-titulo-cargar-informacion">${trans["Cargar archivos solicitados"] || "Cargar archivos solicitados"}</h5><button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button></div>
                          <div class="modal-body">
                            <p class="reda-texto-solicitud"></p>
                            <p class="small text-muted reda-plazo-solicitud"></p>
                            <div class="reda-complementos mb-3"></div>
                            <label for="reda-archivos-solicitados">${trans["Archivos solicitados"] || "Archivos solicitados"}</label>
                            <input type="file" id="reda-archivos-solicitados" class="form-control" name="archivos[]" accept=".jpg,.jpeg,.png,.webp,.pdf" multiple required>
                            <small class="text-muted">${trans["Hasta 5 archivos JPG, PNG, WebP o PDF de 10 MB cada uno."] || "Hasta 5 archivos JPG, PNG, WebP o PDF de 10 MB cada uno."}</small>
                          </div>
                          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">${trans["Cancelar"] || "Cancelar"}</button><button type="submit" class="btn btn-success">${trans["Enviar archivos"] || "Enviar archivos"}</button></div>
                        </form>
                      </div></div>
                    </div>`);
                const modal = $('#reda-modal-cargar-informacion');
                modal.find('.reda-texto-solicitud').text(resultado.respuesta.solicitud);
                modal.find('.reda-plazo-solicitud').text((trans["Plazo hasta"] || "Plazo hasta") + ': ' + resultado.respuesta.vence_en);
                resultado.respuesta.archivos.forEach(archivo => {
                    $('<a target="_blank" rel="noopener" class="d-block"></a>')
                        .attr('href', APP_URL + '/reda/informacion-mediacion/archivo/' + encodeURIComponent(archivo.id))
                        .text(archivo.nombre).appendTo(modal.find('.reda-complementos'));
                });
                modal.find('form').on('submit', async function (evento) {
                    evento.preventDefault();
                    const archivos = this.elements['archivos[]'].files;
                    if (archivos.length > 5 || Array.from(archivos).some(archivo => archivo.size > 10 * 1024 * 1024)) {
                        window.RedaNotificaciones.notificar(trans["Información"] || "Información", trans["Hasta 5 archivos JPG, PNG, WebP o PDF de 10 MB cada uno."] || "Hasta 5 archivos JPG, PNG, WebP o PDF de 10 MB cada uno.", 'error');
                        return;
                    }
                    const datos = new FormData(this);
                    datos.append('_token', $('meta[name="csrf-token"]').attr('content'));
                    const boton = $(this).find('[type="submit"]').prop('disabled', true);
                    window.RedaNotificaciones.esperar();
                    try {
                        const respuesta = await $.ajax({ url: APP_URL + '/reda/informacion-mediacion/' + id + '/archivos', type: 'POST', data: datos, processData: false, contentType: false });
                        modal.modal('hide');
                        let mensaje = respuesta.mensaje_usuario;
                        if (respuesta.respuesta.correos_pendientes) mensaje += ' ' + (trans["El aviso por correo está pendiente; la alerta fue registrada."] || "El aviso por correo está pendiente; la alerta fue registrada.");
                        window.RedaNotificaciones.notificar(trans["Información"] || "Información", $('<div>').text(mensaje).html(), 'exito');
                    } catch (error) {
                        boton.prop('disabled', false);
                        mostrarError(error);
                    }
                });
                modal.modal('show');
            }, 500);
        } catch (error) {
            mostrarError(error);
        }
    };

    $(document).on('click', '.reda-abrir-informacion', function (evento) {
        evento.preventDefault();
        abrirSolicitud($(this).data('solicitud'));
    });

    $(function () {
        const parametros = new URLSearchParams(window.location.search);
        const id = parametros.get('solicitud_informacion');
        if (id) {
            window.RedaAbreSolicitudInformacion = true;
            parametros.delete('solicitud_informacion');
            const consulta = parametros.toString();
            window.history.replaceState(window.history.state, '', window.location.pathname + (consulta ? '?' + consulta : '') + window.location.hash);
            abrirSolicitud(id);
        }
    });
})(jQuery);
