# Registro previo de cambios en curso

- **Fecha:** 27 de noviembre de 2026.
- **Tarea:** Mostrar un resumen informativo con tarifa base por noche y capacidad en la columna de reserva de la propiedad, conservando el botón flotante y el modal.
- **Respaldo:** `documentacion-torbian-2/BD/appvac_torbDev2-20261127-1555.sql`, generación interna 14:55:34. Se revisaron `properties` y `property_price`: la capacidad puede ser nula, y existen precios de fin de semana y cargos adicionales; el resumen identificará la tarifa como base y no presentará un total.
- **Enfoque:** Reutilizar el precio ya renderizado y la capacidad del selector existente, sin consultas ni modificaciones de datos adicionales.
- **Código:** `resources/js/vistas/frontend/propiedad_detalle.js` inserta un resumen en la tarjeta existente; `resources/sass/main.scss` define sus estilos y `resources/lang/es.json` sus textos. No se toca el botón flotante ni la lógica de reservas.
- **Documentación y subida:** Actualizados `manual_tecnico_plugin_reda_alojamiento.md`, `LOG_DESARROLLO_REDA.md` y la lista de `subir_archivos_puntuales.sh` con el JavaScript, SCSS y JSON. Las tres rutas ya aparecen en `lista_archivos_aplicacion.md`.
- **Estado:** Sintaxis JavaScript y JSON verificadas; pendientes compilación y prueba visual y funcional en Vesta.
