# Registro previo de cambios en curso

- **Fecha:** 9 de noviembre de 2026.
- **Tarea:** Actualizar estado y paso a «En revisión» al seleccionar esa opción y mostrar la mediación en su pestaña.
- **Respaldo:** `documentacion-torbian-2/BD/appvac_torbDev2-20261109-0523.sql`: dos mediaciones abiertas y asignadas; campos `estado` y `paso_actual` admiten texto y son nullable.
- **Servidor:** `packages/Reda/RedaAlojamiento/routes/web.php` y `src/Http/Controllers/Admin/Disputa/DisputaController.php` implementan la transición con permisos y bloqueo de fila, y la ubicación paginada del caso; `src/Http/Controllers/Disputa/DisputaController.php` permite filtrar el valor persistido independientemente del idioma.
- **Interfaz:** `resources/js/admin/vistas/disputa/indexDisputas.js` envía el cambio con espera, recupera errores y abre la pestaña con el caso seleccionado; las otras opciones informan que aún no están disponibles. `resources/lang/es.json` añade los mensajes correspondientes.
- **Documentación:** Actualizados `manual_tecnico_plugin_reda_alojamiento.md` y `LOG_DESARROLLO_REDA.md`.
- **Validación:** Sintaxis de tres archivos PHP, JavaScript y JSON y `git diff --check` correctos; no se ejecutaron consultas ni pruebas funcionales. Pendientes subida, compilación y pruebas en Vesta.
