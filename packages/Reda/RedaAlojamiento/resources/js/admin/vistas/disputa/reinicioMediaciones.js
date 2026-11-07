/**
 * Controla la confirmación y el estado de espera del reinicio de mediaciones en el panel admin.
 */
(( $ ) => {
    'use strict';

    /** Envía la operación y normaliza también las respuestas de error HTTP o de conexión. */
    const solicitarReinicio = (url, datos) => new Promise((resolver) => {
        $.ajax({
            url,
            type: 'POST',
            data: datos,
            dataType: 'json',
            success: (respuesta) => resolver(respuesta),
            error: (error) => {
                const respuesta = error.responseJSON || {};
                resolver({
                    success: false,
                    message: respuesta.message || 'Error al reiniciar mediaciones',
                    mensaje_usuario: respuesta.mensaje_usuario || '',
                    respuesta: respuesta.respuesta || '',
                    code: error.status || 504
                });
            }
        });
    });

    /** Envía la confirmación y muestra el resultado sin cerrar la pantalla durante la operación. */
    $(function () {
        const $formulario = $('#form-reinicio-mediaciones');
        if (!$formulario.length) return;

        const traducciones = window.RedaAlojamientoJson || {};
        const $estado = $('#estado-reinicio');
        const $boton = $('#confirmar-reinicio');
        const frase = 'Estoy seguro de reiniciar las mediaciones';
        let procesando = false;

        $('#modal-reinicio-mediaciones').on('hide.bs.modal', (evento) => {
            if (procesando) evento.preventDefault();
        });

        $formulario.on('submit', async function (evento) {
            evento.preventDefault();
            if ($('#confirmacion-reinicio').val() !== frase) {
                $estado.text(traducciones['Escriba la frase de confirmación exactamente.'] || 'Escriba la frase de confirmación exactamente.').addClass('text-danger');
                return;
            }
            const datos = $formulario.serialize();
            procesando = true;
            $boton.prop('disabled', true);
            $formulario.find('input, [data-bs-dismiss]').prop('disabled', true);
            $estado.removeClass('text-danger').text(traducciones['Se están reiniciando las mediaciones. Por favor espere...'] || 'Se están reiniciando las mediaciones. Por favor espere...');
            $estado.prepend($('<i class="fa fa-spinner fa-spin me-2" aria-hidden="true"></i>'));
            const respuesta = await solicitarReinicio($formulario.attr('action'), datos);
            procesando = false;
            if (respuesta.success) {
                $estado.text(respuesta.mensaje_usuario);
                $formulario.find('[data-bs-dismiss]').prop('disabled', false);
                const resultado = respuesta.respuesta;
                const resumen = [
                    `${traducciones['Mediaciones'] || 'Mediaciones'}: ${resultado.cantidadDisputas}`,
                    `${traducciones['Mensajes identificados'] || 'Mensajes identificados'}: ${resultado.cantidadMensajes}`,
                    `${traducciones['Alertas'] || 'Alertas'}: ${resultado.alertas}`,
                    `${traducciones['Avisos'] || 'Avisos'}: ${resultado.avisos}`,
                    `${traducciones['Cuentas reactivadas'] || 'Cuentas reactivadas'}: ${resultado.reactivados}`,
                    `${traducciones['Adjuntos conservados por respaldo histórico'] || 'Adjuntos conservados por respaldo histórico'}: ${resultado.adjuntosConservados}`
                ];
                $('#resultado-reinicio').text(`${respuesta.mensaje_usuario} ${resumen.join('; ')}.`);
                $formulario.find('input').prop('disabled', true);
            } else {
                $estado.addClass('text-danger').text(respuesta.mensaje_usuario || traducciones['No se pudo completar el reinicio. Revise el registro y actualice la página antes de volver a intentarlo; los datos podrían haberse reiniciado parcialmente.'] || 'No se pudo completar el reinicio. Revise el registro y actualice la página antes de volver a intentarlo; los datos podrían haberse reiniciado parcialmente.');
                $formulario.find('[data-bs-dismiss]').prop('disabled', false);
            }
        });
    });
})(jQuery);
