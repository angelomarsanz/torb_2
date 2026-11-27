# Registro previo de cambios en curso

- **Fecha:** 25 de noviembre de 2026.
- **Tarea:** Corregir el desglose de adultos y niños en el modal «Ver reserva».
- **Respaldo:** `documentacion-torbian-2/BD/appvac_torbDev2-20261125-2107.sql`, del día actual; cabecera 20:06:31. No contiene registros de desglose en `reserva_huespedes` ni en `booking_details`.
- **Diagnóstico:** El modal ya usa el desglose del servidor; `ReservaObserver.php` usa asignación masiva con `BookingDetails`, modelo que protege todos sus atributos, y el error impide guardar el desglose en ambas tablas.
- **Código:** `ReservaObserver.php` guarda los detalles con atributos explícitos. Las reservas existentes sin desglose siguen mostrando el total como adultos, según confirmó el usuario.
- **Documentación y subida:** Actualizados `manual_tecnico_plugin_reda_alojamiento.md` y la lista de `subir_archivos_puntuales.sh` para el observador; se sustituyó la ruta de pagos pendiente de la tarea anterior.
- **Registro y validación:** Actualizado `LOG_DESARROLLO_REDA.md`; sintaxis PHP 8.2 y formato de diferencias comprobados.
- **Estado:** Pendientes subida y prueba funcional en Vesta; los datos históricos no se modifican.
