# Registro previo de cambios en curso

- **Fecha:** 25 de noviembre de 2026.
- **Tarea:** Mostrar `{llegada}` y `{salida}` como día/mes/año en las plantillas de reserva inicial.
- **Respaldo revisado:** `documentacion-torbian-2/BD/appvac_torbDev2-20261125-1221.sql`, del día actual; la cabecera indica 11:21:43. Hay una plantilla de reserva con ambas variables y dos de pago; `bookings` guarda fechas como `date`, `messages` guarda el texto y la preferencia del respaldo es `dd/mm/yyyy`.
- **Código:** `PlantillasTextoService.php` formatea solo las variables de la reserva inicial, sin cambiar sesión ni datos; admite ISO y formato regional y comunica errores mediante el controlador existente.
- **Subida:** `subir_archivos_puntuales.sh` contiene solo la ruta del servicio de esta solicitud; se sustituyeron las rutas de la tarea anterior sin modificar la lógica.
- **Documentación:** Se actualizaron `manual_tecnico_plugin_reda_alojamiento.md` y `LOG_DESARROLLO_REDA.md`.
- **Estado:** Sintaxis PHP, formato y ruta de subida comprobados; prueba funcional pendiente en Vesta. No se ejecutó SQL ni compilación.
