# Registro previo de cambios en curso

- **Fecha:** 25 de noviembre de 2026.
- **Tarea:** Corregir la apertura del modal de reserva en celular cuando la propiedad no tiene fechas ocupadas.
- **Respaldo:** `documentacion-torbian-2/BD/appvac_torbDev2-20261125-1221.sql`, del día actual; propiedad 1 sin reservas futuras incluidas por el endpoint de fechas ocupadas.
- **Código:** `packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/propiedad_detalle.js` fuerza el calendario de Flatpickr en móvil para llegada y salida, incluso sin fechas ocupadas.
- **Documentación y subida:** Actualizados `manual_tecnico_plugin_reda_alojamiento.md` y la lista de `subir_archivos_puntuales.sh` para el JavaScript de esta solicitud.
- **Registro y validación:** Actualizado `LOG_DESARROLLO_REDA.md`; sintaxis JavaScript y formato de diferencias comprobados.
- **Estado:** Pendientes subida, compilación y prueba funcional en móvil y escritorio en Vesta.
