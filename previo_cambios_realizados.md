# Registro Previo de Cambios en Curso

## Estado actual
- **Fecha:** 6 de noviembre de 2026
- **Tarea:** Mantener visible la notificación de guardado de configuración de mediaciones hasta que el usuario la cierre.
- **Diagnóstico:** `desactivarCargando()` llama a `ocultar()` y después se abre la notificación en el mismo modal; los temporizadores de limpieza de `ocultar()` cierran el resultado.
- **Estado:** Flujo y documentación actualizados; revisión estática completada. Pendiente compilación y prueba funcional en Vesta.
- **Archivos actualizados:** `packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/configuracionDisputas.js`, `manual_tecnico_plugin_reda_alojamiento.md`, `LOG_DESARROLLO_REDA.md` y este archivo.
- **Cambio:** El modal de espera se reutiliza directamente para el resultado de éxito/error sin invocar su limpieza diferida; el botón vuelve a habilitarse y el fallback conserva la alerta.
- **Validación:** `node --check` y `git diff --check` sin errores.
- **Restricciones:** Preservar cambios existentes, no compilar ni hacer pruebas funcionales localmente y no modificar ni ejecutar scripts de subida.
