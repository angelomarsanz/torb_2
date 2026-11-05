# Registro Previo de Cambios en Curso

## Estado actual
- **Fecha:** 5 de noviembre de 2026
- **Tarea:** Añadir a `.github/copilot-instructions.md` una directriz para revisar respaldos recientes de la base de desarrollo antes de cambios de aplicación que consulten, creen, actualicen o eliminen datos.
- **Alcance de la directriz:** localizar el volcado más reciente con el patrón `documentacion-torbian-2/BD/appvac_torbDev2-AAAAMMDD-HHMM.sql`, inspeccionar tablas y relaciones afectadas, proyectar el comportamiento con los registros actuales, revisar incongruencias relevantes y proponer SQL acotado y verificable sin ejecutarlo en este entorno.
- **Archivos previstos:** `.github/copilot-instructions.md`, `LOG_DESARROLLO_REDA.md` y este punto de control.
- **Restricciones:** documentación únicamente; no ejecutar SQL, Artisan, compilación ni scripts de subida.
