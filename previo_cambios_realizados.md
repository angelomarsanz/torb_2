# Registro previo de cambios en curso

- **Fecha:** 9 de noviembre de 2026.
- **Tarea:** Evitar que los textos largos del selector de paso actual se superpongan con la flecha.
- **Estilos:** `packages/Reda/RedaAlojamiento/resources/sass/admin/main.scss` reserva espacio a la derecha para la flecha y aplica recorte con puntos suspensivos al selector compacto, sin modificar sus opciones ni valores.
- **Documentación:** Actualizados `manual_tecnico_plugin_reda_alojamiento.md` y `LOG_DESARROLLO_REDA.md`.
- **Validación:** `git diff --check` correcto; pendientes subida, compilación y prueba visual en Vesta.
