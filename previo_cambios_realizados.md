# Registro previo de cambios en curso

- **Fecha:** 8 de noviembre de 2026.
- **Tarea:** Cambiar la etiqueta a «Paso actual» y retirar el aviso de selección del selector de mediaciones.
- **Código:** `packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/indexDisputas.js` cambia la etiqueta y elimina el aviso y su referencia `aria-describedby`, sin cambiar la selección ni el guardado.
- **Estilos y textos:** `packages/Reda/RedaAlojamiento/resources/sass/admin/main.scss` elimina la regla del aviso retirado; `packages/Reda/RedaAlojamiento/resources/lang/es.json` añade «Paso actual:» y elimina el texto anterior.
- **Documentación:** Actualizados `manual_tecnico_plugin_reda_alojamiento.md` y `LOG_DESARROLLO_REDA.md`.
- **Validación:** Sintaxis JavaScript, lectura del JSON y `git diff --check` correctos; pendientes subida, compilación y prueba visual en Vesta.
