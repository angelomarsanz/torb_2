# Registro previo de cambios en curso

## Alineación de Alertas

- **Fecha:** 27 de noviembre de 2026.
- **Tarea actual:** Corregir la alineación de «Alertas», que permanece a la derecha tras la subida y compilación informadas por el usuario.
- **Diagnóstico:** El enlace utiliza `justify-content-between`; cuando la insignia está oculta distribuye el icono y el texto en extremos opuestos. `text-start` solo alinea el texto dentro del elemento, no cambia su posición en el enlace.
- **Archivos modificados:** `packages/Reda/RedaAlojamiento/resources/js/admin/general/menus/menuLateralAdmin.js`, `manual_tecnico_plugin_reda_alojamiento.md`, `LOG_DESARROLLO_REDA.md` y este punto de control. La lista puntual ya contiene únicamente el JavaScript de esta solicitud; no requiere cambios.
- **Código:** Se retiró `justify-content-between`; la insignia conserva `ms-auto` para permanecer a la derecha cuando es visible.
- **Validación:** Sintaxis JavaScript y formato de diferencias comprobados; revisadas las reglas Bootstrap de distribución y visibilidad.
- **Estado:** Pendientes subida, compilación y prueba visual en Vesta con y sin alertas no leídas; se conservan los puntos de control de las otras tareas pendientes.
## Desglose de huéspedes

- **Fecha:** 25 de noviembre de 2026.
- **Tarea:** Corregir el desglose de adultos y niños en el modal «Ver reserva».
- **Respaldo:** `documentacion-torbian-2/BD/appvac_torbDev2-20261125-2107.sql`, del día actual; cabecera 20:06:31. No contiene registros de desglose en `reserva_huespedes` ni en `booking_details`.
- **Diagnóstico:** El modal ya usa el desglose del servidor; `ReservaObserver.php` usa asignación masiva con `BookingDetails`, modelo que protege todos sus atributos, y el error impide guardar el desglose en ambas tablas.
- **Código:** `ReservaObserver.php` guarda los detalles con atributos explícitos. Las reservas existentes sin desglose siguen mostrando el total como adultos, según confirmó el usuario.
- **Documentación y subida:** Actualizados `manual_tecnico_plugin_reda_alojamiento.md` y la lista de `subir_archivos_puntuales.sh` para el observador; se sustituyó la ruta de pagos pendiente de la tarea anterior.
- **Registro y validación:** Actualizado `LOG_DESARROLLO_REDA.md`; sintaxis PHP 8.2 y formato de diferencias comprobados.
- **Estado:** Pendientes subida y prueba funcional en Vesta; los datos históricos no se modifican.
## Notificaciones KYC

- **Fecha:** 26 de noviembre de 2026.
- **Tarea:** Fase 8 del KYC (notificaciones): avisos al usuario cuando su verificación cambia de estado o se le pide una corrección, y avisos a los administradores con permiso `manage_kyc` cuando llega una solicitud que requiere revisión.
- **Respaldo:** `documentacion-torbian-2/BD/appvac_torbDev2-20261126-1731.sql`, del día actual. Hallazgos: `alertas_admin.tipo` es `varchar(100)` (se usa el tipo `kyc` sin migración); `admin.status` es enum `Active/Inactive`; el usuario 1 ya tiene una solicitud manual aprobada.
- **Diseño:** correo al usuario (sin texto libre del administrador, solo enlace a la pantalla de verificaciones); alerta en `alertas_admin` y correo a los administradores con `manage_kyc`; correos enviados tras la respuesta (`terminating`) y sin romper nunca el flujo KYC; fallos/expiraciones solo si vienen de Didit (el error técnico de creación de sesión no se notifica).
- **Plan de archivos (bajo `packages/Reda/RedaAlojamiento/`):** `config/kyc.php`, `src/Events/CorreccionKycAtendida.php` (nuevo), `src/Services/Kyc/KycService.php`, `src/Services/Kyc/NotificacionesKycService.php` (nuevo), `resources/views/emails/kyc_notificacion_usuario.blade.php` y `kyc_notificacion_admin.blade.php` (nuevos), `src/RedaAlojamientoServiceProvider.php`, `resources/js/admin/vistas/alerta/indexAlertas.js`, `resources/lang/es.json`.
- **Archivos (bajo `packages/Reda/RedaAlojamiento/`):** nuevos `src/Services/Kyc/NotificacionesKycService.php`, `src/Events/CorreccionKycAtendida.php`, `resources/views/emails/kyc_notificacion_usuario.blade.php` y `kyc_notificacion_admin.blade.php`; modificados `config/kyc.php`, `src/Services/Kyc/KycService.php`, `src/RedaAlojamientoServiceProvider.php`, `resources/js/admin/vistas/alerta/indexAlertas.js` y `resources/lang/es.json`. En la raíz: `lista_archivos_aplicacion.md` (+73 rutas del módulo KYC), `manual_tecnico_plugin_reda_alojamiento.md`, `LOG_DESARROLLO_REDA.md`, `REDA_PAUTAS_DESARROLLO.md` (sección 9) y la lista de `subir_archivos_puntuales.sh` (10 rutas, incluye `BloqueoKycObserver.php` de la integración).
- **Registro y validación:** `php -l`, plantillas Blade, SCSS y `git diff --check` correctos; 80 pruebas de notificaciones, 13 de la pantalla de alertas y las 10 suites anteriores en verde. No se compiló ni se modificaron datos.
- **Estado:** Pendientes subida con `./subir.sh`, `./compilar.sh`, `artisan config:clear` y prueba funcional en Vesta (ver `LOG_DESARROLLO_REDA.md`).

## Fusión de las dos ramas

- **Fecha:** 27 de noviembre de 2026.
- **Tarea:** Integrar los cambios locales de Alertas y los cambios remotos de notificaciones KYC.
- **Resolución:** Se conservaron los tres registros anteriores y se retiraron los marcadores de conflicto, incluidos los que ya estaban presentes en la versión remota. Las entradas de ambos trabajos se conservan en el log y el manual.
- **Subida:** La lista puntual existente conserva el JavaScript del menú; no contiene los archivos de notificaciones KYC. Revisar los pendientes de ambos trabajos antes de desplegar.
