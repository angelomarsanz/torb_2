/**
 * Inicializa el editor y los controles de navegación de las plantillas administrativas.
 */
(function ($) {
    'use strict';
    const $vista = $('#plantillas-textos');
    if (!$vista.length) return;

    $(function () {
        /** Activa el editor disponible y avisa si no se puede inicializar. */
        const iniciarEditor = () => {
            if (!window.CKEDITOR) {
                $('<div class="alert alert-warning" role="alert"></div>')
                    .text(window.RedaAlojamientoJson['No se pudo cargar el editor de formato.'] || 'No se pudo cargar el editor de formato.')
                    .prependTo($vista.find('.reda-plantillas-editor .card-body'));
                return;
            }
            window.CKEDITOR.replace('contenido-plantilla', {
                height: 300,
                toolbar: [
                    ['Bold', 'Italic', 'Underline'],
                    ['BulletedList', 'NumberedList'],
                    ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
                    ['Undo', 'Redo']
                ]
            });
        };
        iniciarEditor();

        $vista.on('submit', '.reda-eliminar-plantilla', function () {
            return window.confirm(window.RedaAlojamientoJson['¿Eliminar esta opción de texto?'] || '¿Eliminar esta opción de texto?');
        });
        $vista.on('submit', 'form', function (evento) {
            if (!evento.isDefaultPrevented() && window.RedaNotificaciones) window.RedaNotificaciones.esperar();
        });
        $vista.on('click', '.reda-pagination-container a, .list-group-item, .btn-outline-primary', function (evento) {
            if (!evento.ctrlKey && !evento.metaKey && window.RedaNotificaciones) window.RedaNotificaciones.esperar();
        });
    });
})(jQuery);
