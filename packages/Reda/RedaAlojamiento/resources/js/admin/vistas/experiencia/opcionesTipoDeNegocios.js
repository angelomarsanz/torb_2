/**
 * Gestiona la creación, edición y eliminación de tipos de negocios en el panel administrativo,
 * incluyendo sus formularios, confirmaciones y notificaciones.
 */

/**
 * Envía la petición para crear o actualizar un tipo de negocio.
 * @param {string} url - URL del endpoint.
 * @param {object} formData - Datos serializados del formulario.
 * @returns {Promise}
 */
const saveOpcionTipoNegocio = (url, formData) => {
    return new Promise((resolve) => {
        (function( $ ) {
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(data) {
                    console.log('saveOpcionTipoNegocio, data:', data);
                    resolve(data);
                },
                error: function (x, xs, xt) {
                    let respuestaServidor = {};
                    try {
                        respuestaServidor = JSON.parse(x.responseText);
                    } catch (e) {
                        respuestaServidor = {};
                    }

                    const mensajeErrorBase = window.RedaAlojamiento?.general?.error_en_el_servidor_de_Torbian || 'Error en el servidor de Torbian';
                    const detalleError = respuestaServidor.message ? `<br />${respuestaServidor.message}` : '';

                    resolve({
                        'success': false,
                        'message' : 'Error guardando tipo de negocio',
                        'mensaje_usuario': respuestaServidor.mensaje_usuario ?? `${mensajeErrorBase}${detalleError}`,
                        'respuesta': respuestaServidor.respuesta || '',
                        'code': x.status !== 0 ? x.status : 504,
                    });
                }
            })
        })(jQuery);
    });
}

/**
 * Envía la petición para eliminar un tipo de negocio.
 * @param {string} clave - Clave única del tipo de negocio.
 * @returns {Promise}
 */
const destroyOpcionTipoNegocio = (clave) => {
    return new Promise((resolve) => {
        (function( $ ) {
            $.ajax({
                url: APP_URL + '/admin/reda/negocios/opciones-tipos-de-negocios/destroy/' + clave,
                type: 'DELETE',
                data: {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                },
                dataType: 'json',
                success: function(data) {
                    resolve(data);
                },
                error: function (x, xs, xt) {
                    let respuestaServidor = {};
                    try {
                        respuestaServidor = JSON.parse(x.responseText);
                    } catch (e) {
                        respuestaServidor = {};
                    }

                    const mensajeErrorBase = window.RedaAlojamiento?.general?.error_en_el_servidor_de_Torbian || 'Error en el servidor de Torbian';
                    const detalleError = respuestaServidor.message ? `<br />${respuestaServidor.message}` : '';

                    resolve({
                        'success': false,
                        'message' : 'Error eliminando tipo de negocio',
                        'mensaje_usuario': respuestaServidor.mensaje_usuario ?? `${mensajeErrorBase}${detalleError}`,
                        'respuesta': respuestaServidor.respuesta || '',
                        'code': x.status !== 0 ? x.status : 504,
                    });
                }
            })
        })(jQuery);
    });
}

(function( $ ) {
    "use strict";
    const containerId = '#opciones_tipos_de_negocios';
    if ($(containerId).length) {
        console.log('Script para "Opciones Tipo de Negocios" cargado correctamente.');
        $(function() {
            // Abrir el modal para agregar un tipo de negocio.
            $(document).on('click', '#btn-add-category', function(e) {
                e.preventDefault();
                $('#form-category')[0].reset();
                $('#old_clave').val(''); // Limpiar old_clave
                $('#form-category').attr('action', window.RedaRutas.store_categoria);
                $('#modal-title-category').text(
                    window.RedaAlojamientoJson["Agregar Nuevo Tipo de Negocio"] || "Agregar Nuevo Tipo de Negocio"
                );

                const $btn = $('#btn-save-category');
                $btn.prop('disabled', false);
                $btn.find('.btn-text').text(window.RedaAlojamiento.general.guardar);
                $btn.find('.fa-spinner').addClass('d-none');

                $('#modal-category').modal('show');
            });

            // Abrir el modal para editar un tipo de negocio.
            $(document).on('click', '.btn-edit-category', function(e) {
                e.preventDefault();
                const clave = $(this).data('clave');
                const nombre = $(this).data('nombre');

                $('#form-category')[0].reset();
                $('#clave').val(clave);
                $('#old_clave').val(clave);
                $('#nombre').val(nombre);
                $('#form-category').attr('action', window.RedaRutas.update_categoria);
                $('#modal-title-category').text(
                    window.RedaAlojamientoJson["Editar tipo de negocio"] || "Editar tipo de negocio"
                );

                const $btn = $('#btn-save-category');
                $btn.prop('disabled', false);
                $btn.find('.btn-text').text(window.RedaAlojamiento.general.actualizar || 'Actualizar');
                $btn.find('.fa-spinner').addClass('d-none');

                $('#modal-category').modal('show');
            });

            // Manejo del envío del formulario mediante Ajax (tanto para agregar como editar)
            $('#form-category').on('submit', async function(e) {
                e.preventDefault();

                const $form = $(this);
                const $btn = $('#btn-save-category');
                const clave = $('#clave').val().trim();
                const nombre = $('#nombre').val().trim();

                // Validación básica manual
                if (clave === '' || nombre === '') {
                    mostrarNotificacion(
                        window.RedaAlojamiento.general.informacion,
                        window.RedaAlojamiento.general.ambos_campos_son_obligatorios,
                        'info'
                    );
                    return;
                }

                // Bloqueamos el botón y mostramos spinner
                $btn.prop('disabled', true);
                const textoOriginal = $btn.find('.btn-text').text();
                $btn.find('.btn-text').text(window.RedaAlojamiento.general.guardando || 'Guardando...');
                $btn.find('.fa-spinner').removeClass('d-none');

                const response = await saveOpcionTipoNegocio($form.attr('action'), $form.serialize());

                console.log('saveOpcionTipoNegocio, response: ', response)

                const mensajeErrorServidor = response.mensaje_usuario === 'La categoría no existe'
                    ? (window.RedaAlojamientoJson["El tipo de negocio no existe"] || "El tipo de negocio no existe")
                    : response.mensaje_usuario;
                const mensajeRaw = response.success
                    ? (window.RedaAlojamientoJson["Tipo de negocio guardado correctamente"] || "Tipo de negocio guardado correctamente")
                    : (mensajeErrorServidor || response.message || 'Error inesperado en el servidor');

                if (response.success) {
                    $('#modal-category').modal('hide');
                    mostrarNotificacion(
                        window.RedaAlojamiento.general.exito,
                        mensajeRaw,
                        'exito',
                        true
                    );
                } else {
                    mostrarNotificacion(
                        window.RedaAlojamiento.general.error,
                        mensajeRaw,
                        'error'
                    );
                }
                $btn.prop('disabled', false);
                $btn.find('.btn-text').text(textoOriginal);
                $btn.find('.fa-spinner').addClass('d-none');
            });

            // Acción de eliminar un tipo de negocio.
            $(document).on('click', '.btn-delete-category', function(e) {
                e.preventDefault();
                const clave = $(this).data('clave');
                
                mostrarConfirmacion(
                    window.RedaAlojamientoJson["¿Estás seguro que deseas eliminar este tipo de negocio?"] || "¿Estás seguro que deseas eliminar este tipo de negocio?",
                    async () => {
                        const response = await destroyOpcionTipoNegocio(clave);
                        if (response.success) {
                            mostrarNotificacion(
                                window.RedaAlojamiento.general.exito,
                                window.RedaAlojamientoJson["Tipo de negocio eliminado correctamente"] || "Tipo de negocio eliminado correctamente",
                                'exito',
                                true
                            );
                        } else {
                            const mensajeErrorServidor = response.mensaje_usuario === 'La categoría no existe'
                                ? (window.RedaAlojamientoJson["El tipo de negocio no existe"] || "El tipo de negocio no existe")
                                : response.mensaje_usuario;
                            mostrarNotificacion(
                                window.RedaAlojamiento.general.error,
                                mensajeErrorServidor,
                                'error'
                            );
                        }
                    },
                    window.RedaAlojamiento.general.confirmar_accion,
                    window.RedaAlojamiento.general.eliminar
                );
            });

            // Limpiar espacios en la clave mientras el usuario escribe
            $('#clave').on('input', function() {
                $(this).val($(this).val().toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, ''));
            });

            console.log('Módulo de Opciones de Negocios inicializado.');
        });
    }
})(jQuery);
