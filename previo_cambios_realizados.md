# Registro Previo de Cambios en Curso

## Estado actual
- **Fecha:** 8 de noviembre de 2026.
- **Tarea:** Mantener visible la notificación de error al enviar una solicitud de mediación desde el Inbox, en particular cuando el usuario tiene acciones restringidas.
- **Hallazgo:** `guardarMediacionReda()` conserva el mensaje recibido en la respuesta HTTP 403 del middleware; el formulario llama a `ocultar()` y después a `notificar()` sobre el mismo modal, por lo que la limpieza diferida puede cerrar la notificación nueva.
- **Cambio:** `packages/Reda/RedaAlojamiento/resources/js/general/mensajes.js` reutiliza el modal de espera para el resultado sin programar su cierre, tanto en éxito como en error; si no está disponible la notificación, cierra la espera antes de la alerta alternativa.
- **Documentación:** Actualizados `manual_tecnico_plugin_reda_alojamiento.md` y `LOG_DESARROLLO_REDA.md`.
- **Validación:** Sintaxis JavaScript y `git diff --check` correctos. Pendiente comprobar la respuesta real, el cierre manual y la creación exitosa en Vesta. El archivo `resources/js/general/mensajes.js` aparece comentado en la lista de subida y debe habilitarse manualmente por el usuario.
