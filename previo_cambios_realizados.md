# Registro Previo de Cambios en Curso

## Estado final
- **Fecha:** 18 de noviembre de 2026
- **Tarea:** Restablecer el botón «Enviar» y ocultar su spinner al regresar con Atrás a la página de la propiedad desde la selección del método de pago.
- **Respaldo revisado:** `documentacion-torbian-2/BD/appvac_torbDev2-20261118-1140.sql`, generado hoy a las 11:40. Los bookings 77–79 tienen sus noches correspondientes bloqueadas en `property_dates`; no se detectó una incongruencia de disponibilidad relacionada con el síntoma.
- **Archivo de lógica actualizado:** `packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/propiedad_detalle.js`. El evento `pageshow` restablece el botón y oculta el spinner únicamente cuando la página se restaura desde BFCache; conserva los datos del formulario.
- **Documentación actualizada:** `manual_tecnico_plugin_reda_alojamiento.md` y `LOG_DESARROLLO_REDA.md`.
- **Validación local:** `node --check` y `git diff --check` pendientes.
- **Pendiente del usuario:** subir el JavaScript y la documentación al servidor Vesta, compilar con `./compilar.sh` y probar el flujo de regreso desde la página de pago. No se ejecutó SQL, Artisan, compilación ni subida desde este entorno.
