# Registro previo de cambios en curso

- **Fecha:** 8 de noviembre de 2026.
- **Tarea:** Añadir un selector visual de pasos junto al estado de cada tarjeta de mediación, en escritorio y móvil, sin acciones al seleccionar.
- **Código:** `packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/indexDisputas.js` comparte los seis pasos entre línea de tiempo y selector visual, los dos primeros inhabilitados. El selector aparece en cada tarjeta, no en la información adicional, y no guarda cambios.
- **Estilos y textos:** `packages/Reda/RedaAlojamiento/resources/sass/admin/main.scss` adapta el espacio de la tarjeta; `packages/Reda/RedaAlojamiento/resources/lang/es.json` añade el nombre accesible y aviso del selector.
- **Documentación:** Actualizados `manual_tecnico_plugin_reda_alojamiento.md` y `LOG_DESARROLLO_REDA.md`.
- **Validación:** Sintaxis JavaScript, lectura del JSON y `git diff --check` correctos. Pendientes subida, compilación y prueba visual en Vesta.
