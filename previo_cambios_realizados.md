# Registro previo de cambios en curso

- **Fecha:** 20 de noviembre de 2026.
- **Tarea:** Separar las plantillas de la reserva inicial de las de confirmación de pago y añadir la variable de total de huéspedes.
- **Respaldo revisado:** `documentacion-torbian-2/BD/appvac_torbDev2-20261120-1857.sql`, generado el 20 de noviembre (cabecera 17:56:20). Hay tres plantillas existentes en `mensaje_anfitrion`, ninguna en el nuevo contexto; `reserva_huespedes` está vacía y el helper usa `booking_details` o `bookings.guest` como respaldo. No se modifican registros históricos.
- **Código:** `PlantillaTexto.php` define contextos; `PlantillaTextoController.php` administra cada contexto por separado; `textos.blade.php` permite elegirlo; `PlantillasTextoService.php` filtra por pantalla y resuelve `{total_personas}`; `PlantillaPagoController.php` documenta el filtro y `es.json` incorpora textos.
- **Documentación:** Actualizados `manual_tecnico_plugin_reda_alojamiento.md` y `LOG_DESARROLLO_REDA.md`. La segunda mención a fecha desde se interpretó como fecha de salida; se conserva `{salida}`.
- **Estado:** Sintaxis PHP y JSON comprobadas; pruebas funcionales pendientes en Vesta. No se ejecutó SQL ni compilación.
