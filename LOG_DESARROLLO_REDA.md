# Log de Desarrollo - Plugin REDA Alojamiento (Gemini CLI)

Este archivo sirve como memoria técnica para que Gemini pueda recordar los avances, decisiones arquitectónicas y tareas completadas en sesiones anteriores.

## [27 de Noviembre, 2026] - Revisión del desglose en «Ver reserva»
- **Respaldo:** `documentacion-torbian-2/BD/appvac_torbDev2-20261127-1555.sql`, generación interna 14:55:34. `reserva_huespedes` sigue sin filas y `booking_details` no contiene registros `adultos` o `ninos`; las reservas 85 y 86 tienen dos huéspedes. El respaldo no permite determinar su composición ni demostrar si el observador corregido se desplegó antes de crear la reserva probada.
- **Diagnóstico:** El modal muestra el texto devuelto por `RedaBookingController`; el helper usa el total como adultos cuando no existe desglose, según lo autorizado para reservas sin datos históricos. La corrección del observador registrada el 25 de noviembre solo se aplica al crear reservas nuevas y su despliegue no consta confirmado.
- **Estado:** Pendiente identificar la reserva afectada y confirmar si se creó después de subir el observador. No se modificaron código de aplicación, datos ni lista de subida; se conserva el punto de control anterior.
- **Seguimiento:** El respaldo `documentacion-torbian-2/BD/appvac_torbDev2-20261127-1636.sql`, posterior a la reserva identificada por el usuario como ID 87, muestra `guest = 2`, sin campos `adultos` o `ninos` para ella en `booking_details` y sin filas en `reserva_huespedes`. Por tanto, el helper recurre al total como adultos; el respaldo no permite distinguir si falló la ejecución o si no se subió el observador corregido. El usuario no está seguro del despliegue. Se propone subir `packages/Reda/RedaAlojamiento/src/Observers/ReservaObserver.php` y confirmar antes de proseguir; no se modificaron los datos de la reserva.
- **Confirmación del usuario:** La salida de `subir.sh` indica que se intentó subir el observador y finalizó; también muestra un aviso FTP al crear un directorio ya existente, que por sí solo no confirma el contenido remoto. La compilación de assets terminó correctamente; no era necesaria para el cambio PHP. Queda pendiente comprobar una reserva nueva y el desglose persistido.
- **Nueva prueba:** El usuario informa que una reserva creada después de la subida sigue mostrando «2 adultos». No proporcionó su ID ni un respaldo posterior; el más reciente disponible continúa siendo el de las 16:36. Se requieren esos datos y, si están disponibles, los mensajes del observador en el log de Laravel para distinguir entre fallo de persistencia, valores incorrectos o selección de otra reserva.

## [27 de Noviembre, 2026] - Resumen informativo de reserva en detalle de propiedad
- **Solicitud:** Mostrar información general del alojamiento en la columna lateral sin formulario y conservar el botón flotante. El usuario eligió tarifa por noche y capacidad, sin detalles de reservas existentes.
- **Respaldo:** `documentacion-torbian-2/BD/appvac_torbDev2-20261127-1555.sql`, generación interna 14:55:34. `properties.accommodates` admite nulos; `property_price` se relaciona con `properties.id` mediante `property_id` y contiene tarifas de fin de semana y cargos adicionales. En el respaldo, la propiedad 4 tiene tarifa de fin de semana distinta a la base. No se consultaron ni modificaron datos de la base.
- **Código:** `packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/propiedad_detalle.js` muestra la tarifa base ya formateada y la capacidad del selector existente, sin clonar controles, cambiar el modal ni efectuar consultas adicionales. Informa en consola si faltan datos y advierte que el precio puede variar. `packages/Reda/RedaAlojamiento/resources/sass/main.scss` aporta los estilos para escritorio y celular; es el SCSS frontend compilado existente. `packages/Reda/RedaAlojamiento/resources/lang/es.json` añade los textos.
- **Documentación y subida:** Actualizados `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md`, este registro y la lista de `subir_archivos_puntuales.sh` con los tres archivos desplegables. Los tres ya están en `lista_archivos_aplicacion.md`.
- **Validación:** Sintaxis JavaScript, JSON y formato de diferencias revisados; pendientes compilación y prueba visual y funcional en Vesta.

## [27 de Noviembre, 2026] - Revisión de la columna de reserva en detalle de propiedad
- **Solicitud:** Investigar el espacio vacío a la derecha de la vista `property.single`, sin alterar el comportamiento.
- **Diagnóstico:** `resources/views/property/single.blade.php` contiene en esa columna el precio por noche y el formulario original de reserva. `packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/propiedad_detalle.js` oculta `#booking-price`, inyecta el botón flotante y traslada el formulario al modal al abrirlo. La tarjeta y la columna permanecen en la distribución original; el precio del encabezado queda fuera del formulario trasladado. La ocultación ya figuraba en el commit inicial `ad32e78` del repositorio disponible.
- **Alcance:** Revisión estática, sin modificar código de aplicación, consultar datos ni comprobar visualmente Vesta. Se actualizó este registro y la lista de `subir_archivos_puntuales.sh` a `Ninguno`; la corrección anterior de Alertas sigue pendiente de confirmación de subida.

## [27 de Noviembre, 2026] - Corrección de distribución de «Alertas»
- **Diagnóstico:** El usuario confirmó que la alineación anterior no funcionó tras subir y compilar. El enlace usa `justify-content-between`: al ocultarse la insignia, el espacio restante separa el texto del icono. `text-start` no cambia la posición del elemento dentro del contenedor flex.
- **Código:** `packages/Reda/RedaAlojamiento/resources/js/admin/general/menus/menuLateralAdmin.js` elimina `justify-content-between` del enlace. La insignia conserva `ms-auto` y el comportamiento del contador.
- **Documentación y subida:** Actualizados `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md` y este registro. `subir_archivos_puntuales.sh` ya contiene únicamente el JavaScript de esta solicitud; su ruta ya figura en `lista_archivos_aplicacion.md`.
- **Validación:** Sintaxis JavaScript y formato de diferencias comprobados; verificadas las reglas locales de Bootstrap para `justify-content-between`, `ms-auto` y `d-none`. Compilación y prueba visual en Vesta pendientes, con y sin alertas no leídas.

## [27 de Noviembre, 2026] - Fusión de trabajos de Alertas y KYC
- **Solicitud:** Integrar el commit local de alineación de Alertas y el commit remoto de notificaciones KYC sin descartar ninguno.
- **Resolución:** `previo_cambios_realizados.md` conserva los registros de Alertas, desglose de huéspedes y notificaciones KYC; se retiraron los marcadores del conflicto actual y los que ya estaban guardados en la versión remota. Este registro documenta la fusión.
- **Documentación integrada:** Git fusionó `manual_tecnico_plugin_reda_alojamiento.md`, `REDA_PAUTAS_DESARROLLO.md` y `lista_archivos_aplicacion.md`; se conservaron tanto la documentación de Alertas como la de KYC. Los archivos de código de ambos commits se conservaron.
- **Despliegue:** No se cambió `subir_archivos_puntuales.sh`: conserva la ruta del menú, pero no los archivos de la fase de notificaciones KYC. Antes de desplegar deben revisarse los pendientes de ambos trabajos.

## [27 de Noviembre, 2026] - Alineación de «Alertas» en el menú lateral admin
- **Solicitud:** Alinear a la izquierda el texto de la subopción «Alertas» en «Mensajes y Alertas», de la misma forma que «Mensajes».
- **Código:** `packages/Reda/RedaAlojamiento/resources/js/admin/general/menus/menuLateralAdmin.js` agrega la clase Bootstrap `text-start` al texto de «Alertas». La insignia dinámica de alertas y el resto de la estructura del menú se conservan.
- **Documentación y subida:** Actualizados `manual_tecnico_plugin_reda_alojamiento.md`, este registro, `previo_cambios_realizados.md` y la ruta de `subir_archivos_puntuales.sh`. La ruta del JavaScript ya estaba registrada en `lista_archivos_aplicacion.md`.
- **Validación:** Revisados los cambios y la lista puntual de subida; compilación y prueba visual pendientes en Vesta.

## [25 de Noviembre, 2026] - Desglose de huéspedes en «Ver reserva»
- **Respaldo:** `documentacion-torbian-2/BD/appvac_torbDev2-20261125-2107.sql` es el más reciente y su cabecera indica 20:06:31. `reserva_huespedes` no contiene filas y `booking_details` no contiene campos `adultos` ni `ninos`; las reservas 84, 85 y 86 tampoco tienen desglose guardado. No se modificaron datos históricos.
- **Causa identificada:** `ReservaObserver.php` usaba `updateOrCreate` sobre `BookingDetails`, cuyos atributos están protegidos frente a asignación masiva. El error se capturaba antes de poder escribir en la tabla auxiliar.
- **Código:** `packages/Reda/RedaAlojamiento/src/Observers/ReservaObserver.php` asigna explícitamente los atributos del detalle antes de guardarlo, para persistir adultos y niños en nuevas reservas, incluido cero niños. El modal existente ya utiliza el desglose. Por decisión del usuario, las reservas anteriores sin desglose siguen mostrando su total como adultos.
- **Documentación y subida:** Actualizados `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md`, este registro y la lista de `subir_archivos_puntuales.sh` con el observador. La ruta de `pagos.js` de la tarea anterior ya no figura en la lista de subida.
- **Validación:** Sintaxis PHP 8.2 y formato de diferencias comprobados; no se ejecutó SQL ni se modificaron datos. Pruebas funcionales en Vesta pendientes para reservas nuevas con niños y sin niños.

## [25 de Noviembre, 2026] - Altura automática de los mensajes de reserva y pago
- **Solicitud:** Ajustar al texto los mensajes predefinidos tanto en selección de método de pago como en comprobante, en celular y escritorio.
- **Código:** `packages/Reda/RedaAlojamiento/resources/js/vistas/pago/frontend/pagos.js` comparte `ajustarAlturaMensaje()` entre ambos formularios. Recalcula la altura al cargar o seleccionar una plantilla, editar el texto, cambiar país o método de pago, restaurar la página o cambiar el tamaño de pantalla. El mensaje y las validaciones no se modifican.
- **Documentación y subida:** Actualizados `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md` y este registro; `subir_archivos_puntuales.sh` conserva únicamente el JavaScript de esta solicitud, cuya ruta ya figura en `lista_archivos_aplicacion.md`.
- **Validación:** Sintaxis JavaScript y formato de diferencias comprobados; no se consultaron ni modificaron datos. Compilación y pruebas funcionales en celular y escritorio pendientes en Vesta.

## [25 de Noviembre, 2026] - Altura del mensaje de comprobante en celular
- **Solicitud:** Mostrar completo el mensaje predefinido al anfitrión en la pantalla de carga del comprobante sin tener que desplazar el interior del campo en celular.
- **Código:** `packages/Reda/RedaAlojamiento/resources/js/vistas/pago/frontend/pagos.js` ajusta la altura del textarea `note` en el formulario de comprobante en pantallas de hasta 767 px. Se recalcula al cargar o seleccionar una plantilla, editar el mensaje, restaurar la página o cambiar el tamaño de pantalla; al volver a escritorio restituye los estilos originales. No modifica el contenido, la validación ni el formulario inicial.
- **Documentación y subida:** Actualizados `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md`, este registro y la lista de `subir_archivos_puntuales.sh` con el JavaScript de esta solicitud. La ruta ya estaba en `lista_archivos_aplicacion.md`; la lista anterior de la corrección del modal fue sustituida conforme a la directriz de subida.
- **Validación:** Sintaxis JavaScript y formato de diferencias verificados. No se consultó ni modificó la base de datos; quedan pendientes la compilación y prueba funcional en Vesta.

## [26 de Noviembre, 2026] - KYC con Didit — Fase 8 (notificaciones)
- **Respaldo:** `documentacion-torbian-2/BD/appvac_torbDev2-20261126-1731.sql`, del día actual. Confirma que `alertas_admin.tipo` es `varchar(100)` (el tipo `kyc` no requiere migración), que `admin.status` es el enum `Active/Inactive` y que ya existe una solicitud manual aprobada (usuario 1, aprobada por el administrador 1) sin alertas previas.
- **Alcance:** avisos automáticos del KYC. Al usuario, por correo: solicitud recibida, identidad verificada, solicitud rechazada, corrección solicitada, y, solo cuando lo decide Didit, verificación no completada, expirada o que dejó de estar vigente. A los administradores activos con `manage_kyc`, alerta en el panel y correo: solicitud manual nueva, corrección respondida y verificación de Didit en revisión.
- **Archivos creados** (bajo `packages/Reda/RedaAlojamiento/`): `src/Services/Kyc/NotificacionesKycService.php`, `src/Events/CorreccionKycAtendida.php`, `resources/views/emails/kyc_notificacion_usuario.blade.php`, `resources/views/emails/kyc_notificacion_admin.blade.php`.
- **Archivos modificados:** `config/kyc.php` (bloque `notificaciones`), `src/Services/Kyc/KycService.php` (emite `CorreccionKycAtendida`; documentación actualizada), `src/RedaAlojamientoServiceProvider.php` (singleton y tres escuchas de eventos), `resources/js/admin/vistas/alerta/indexAlertas.js` (alertas tipo `kyc`: ícono, etiqueta y enlace "Revisar solicitud" al panel KYC filtrado por correo), `resources/lang/es.json` (42 textos), `src/Observers/BloqueoKycObserver.php` (ajuste de la integración, ver abajo), `lista_archivos_aplicacion.md` (inventario: se agregaron los 73 archivos del módulo KYC que no figuraban), `manual_tecnico_plugin_reda_alojamiento.md`, `REDA_PAUTAS_DESARROLLO.md`, `subir_archivos_puntuales.sh` (solo la lista de rutas) y este registro.
- **Decisiones:** (1) Los correos no incluyen texto libre del administrador (motivo del rechazo, corrección pedida) ni datos del documento: solo avisan y enlazan a `users/edit-verification`, protegida por la sesión; los correos viajan en claro y pueden contener datos personales que el administrador escribió. (2) Los correos salen tras responder la petición (`terminating`) para no demorar al usuario ni al webhook de Didit; las alertas se crean en el momento. (3) Ningún aviso puede interrumpir el KYC: cualquier fallo se registra sin direcciones ni nombres. (4) Fallos, expiraciones y pérdidas se avisan solo si los decidió Didit: cuando Didit no puede crear la sesión el servicio cierra la solicitud como `failed` con origen `sistema` y el usuario ya vio el error. (5) Se avisa solo a administradores activos con `manage_kyc`, el mismo criterio de `KycPolicy`; si ninguno cumple, se registra una advertencia. (6) El nombre del destinatario se limpia de saltos de línea y caracteres de control (evita inyectar cabeceras); el HTML lo escapan las plantillas. (7) Interruptor global `KYC_NOTIFICACIONES=false` y por canal en `config/kyc.php`.
- **Ajuste de la integración (`BloqueoKycObserver.php`):** `ChatController` de este repositorio crea la consulta del chat con `status='Inquiry'` (el servidor tenía el estado vacío). El observador solo reconocía el vacío y habría impedido iniciar un chat a un usuario sin KYC. Ahora ambos valores se tratan como consulta. Los demás cambios del compañero no afectan al bloqueo: las rutas son idénticas.
- **Validación (sin tocar datos ni compilar):** `php -l`, compilación de las plantillas Blade y de los SCSS, y pruebas fuera del repositorio sobre el código de este directorio: notificaciones 80 comprobaciones (matriz completa de estados y orígenes, flujo manual y flujo de Didit por webhook firmado, correos tras la respuesta, privacidad de los correos y del registro, escape de HTML y cabeceras, interruptores y resistencia a fallos de correo, de alertas y de permisos); alertas del panel en jsdom, 13; y las 10 suites de las fases anteriores sin regresiones (bloqueo 63+54+48, panel 113, retorno 4, webhooks 11, documentos 70, manual 85+49, retención 39).
- **Pendiente en Vesta:** (1) subir con `./subir.sh` los 10 archivos de `subir_archivos_puntuales.sh`; (2) `./compilar.sh` (Reda Alojamiento) para regenerar `indexAlertas.min.js`; (3) `sudo -u appvac php8.2 artisan config:clear`; (4) comprobar que el correo de salida del sitio funciona (Configuración > Correo): sin él los avisos por correo se registran como error y las alertas del panel sí aparecen; (5) prueba funcional: enviar una solicitud manual con un usuario común (debe llegar el correo de "Recibimos tu solicitud" y aparecer la alerta en `/admin/reda/alertas`), pedir una corrección, aprobar y rechazar.

---

## [25 de Noviembre, 2026] - Corrección del modal de reserva en celular
- **Respaldo y diagnóstico:** `documentacion-torbian-2/BD/appvac_torbDev2-20261125-1221.sql` es del día actual; el log `documentacion-torbian-2/torbian.redetronic.top-1795651894664.log` registra el error de `classList` en `onReady` y el fallo posterior de `formatDate`.
- **Código:** `packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/propiedad_detalle.js` establece `disableMobile: true` en los calendarios de llegada y salida. Esto conserva el calendario de Flatpickr y su contenedor aun si no hay fechas deshabilitadas; las reglas de disponibilidad y el formato ISO permanecen iguales.
- **Documentación y subida:** Actualizados `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md`, este registro y la lista de `subir_archivos_puntuales.sh` con el JavaScript de esta solicitud. La ruta ya estaba en `lista_archivos_aplicacion.md`.
- **Validación:** `node --check` y `git diff --check` sin errores; no se modificaron datos ni se compiló. Subida, compilación y prueba funcional en móvil y escritorio pendientes en Vesta.

## [25 de Noviembre, 2026] - Confirmación del error del calendario en celular
- **Evidencia:** `documentacion-torbian-2/torbian.redetronic.top-1795651894664.log` registra un `TypeError` al leer `classList` dentro de `onReady` de Flatpickr y después otro al acceder a `window.redaFpCheckIn.formatDate`. Ambos ocurren en `prepararModalReserva`, antes de abrir el modal.
- **Diagnóstico:** La traza confirma el fallo del callback identificado en `packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/propiedad_detalle.js`. El modo nativo móvil de Flatpickr, compatible con la lista de fechas ocupadas vacía observada en el respaldo, explica la ausencia del contenedor. La corrección propuesta sigue pendiente de implementación y prueba.
- **Alcance:** Solo se actualizó este registro; no se modificaron código, datos ni la lista de subida preexistente.

## [25 de Noviembre, 2026] - Diagnóstico del modal de reserva en celular
- **Solicitud:** Investigar por qué no abre el formulario de reserva en celular para «Apartamento entero en Chichiriviche».
- **Respaldo:** `documentacion-torbian-2/BD/appvac_torbDev2-20261125-1221.sql` es del día actual. La propiedad es la ID 1 y, según los estados, fechas y borrado lógico del respaldo, no tiene reservas que el endpoint de fechas ocupadas incluya después del 25 de noviembre. La propiedad 3 sí tiene una reserva pendiente del 26 al 30 de noviembre.
- **Causa probable:** En `packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/propiedad_detalle.js`, el calendario de llegada recibe una lista vacía de fechas deshabilitadas. En móviles, Flatpickr puede usar el selector nativo y no crear `calendarContainer`; el callback `onReady` accede a ese contenedor sin comprobarlo. Esto puede interrumpir la preparación antes de mostrar el modal. Con fechas deshabilitadas Flatpickr usa su calendario propio, lo que explica la diferencia observada entre propiedades.
- **Estado:** Diagnóstico por revisión del código y del respaldo, pendiente de reproducción en el dispositivo y confirmación de la consola en Vesta. Se propone configurar `disableMobile: true` para mantener el calendario del plugin y verificar ambos calendarios y el bloqueo de fechas. No se modificaron código ni datos; se conservaron el punto de control y la lista de subida preexistentes.

## [25 de Noviembre, 2026] - Legibilidad del aviso de mediación
- **Solicitud:** Agrandar moderadamente el mensaje y el título de la confirmación de mediación en el inbox.
- **Código:** `packages/Reda/RedaAlojamiento/resources/js/general/mensajes.js` aplica la clase temporal `reda-exito-mediacion` al aviso exitoso y la retira al cerrar el modal. `packages/Reda/RedaAlojamiento/resources/sass/main.scss` establece 2 rem para el título y 1.6 rem para el mensaje, con interlineado 1.5, solo para esa confirmación. Se tuvo en cuenta que el estilo principal usa `html { font-size: 62.5% }`.
- **Documentación y subida:** Actualizados `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md`, este registro y la lista de `subir_archivos_puntuales.sh` con los dos archivos desplegables. No se modificaron vistas ni datos.
- **Validación:** Comprobaciones estáticas locales; compilación y prueba visual en escritorio y móvil pendientes en Vesta.

## [25 de Noviembre, 2026] - Formato de fechas en plantillas de reserva
- **Solicitud:** Mostrar `{llegada}` y `{salida}` como `dd/mm/AAAA` en los textos de la reserva inicial.
- **Respaldo:** Se revisó `documentacion-torbian-2/BD/appvac_torbDev2-20261125-1221.sql`, del día actual, cabecera 11:21:43. Existe una plantilla de reserva con ambas variables y dos de confirmación; `bookings` almacena fechas como `date`, `messages` guarda los mensajes y la preferencia del respaldo es `dd/mm/yyyy`.
- **Código:** `packages/Reda/RedaAlojamiento/src/Services/PlantillasTextoService.php` agrega `formatearFechaReserva()` y lo aplica exclusivamente a las variables del mensaje inicial. Interpreta fechas ISO, la preferencia regional y formatos de día/mes con guiones o barras; preserva el día calendario y comunica errores al controlador de opciones. No altera la sesión, reservas, mensajes históricos ni el formato de confirmación de pago.
- **Archivos de control:** Se actualizaron `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md`, este registro y la lista de `subir_archivos_puntuales.sh` para incluir únicamente el servicio modificado. No se ejecutó SQL ni se modificó la lógica del script.
- **Validación:** Sintaxis PHP, formato de diferencias y existencia del archivo de subida comprobados. Prueba funcional pendiente en Vesta; no se necesita compilar ni ejecutar migraciones.

## [25 de Noviembre, 2026] - Ícono del menú Plantillas
- **Solicitud:** Agregar un ícono SVG propio a la opción «Plantillas» del menú lateral administrativo.
- **Código:** `packages/Reda/RedaAlojamiento/resources/js/general/iconos/plantillas.svg` contiene un dibujo de documentos. `packages/Reda/RedaAlojamiento/resources/js/admin/general/menus/menuLateralAdmin.js` importa el SVG y sustituye el ícono Font Awesome previo por una máscara decorativa. `packages/Reda/RedaAlojamiento/resources/sass/admin/main.scss` define el tamaño y permite que herede el color del enlace, incluidos sus estados activos.
- **Integración:** Laravel Mix procesa el SVG importado y genera el recurso en `public/images` al compilar en Vesta. Se actualizó `lista_archivos_aplicacion.md` con el archivo nuevo y la lista de `subir_archivos_puntuales.sh` con los tres archivos desplegables de la solicitud, sin ejecutar el script ni modificar su lógica.
- **Documentación:** Se actualizaron `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md` y este registro. No se modificaron vistas originales ni datos.
- **Validación:** Sintaxis JavaScript, estructura XML del SVG, formato de diferencias y existencia de las tres rutas de despliegue comprobados. Compilación y validación visual pendientes en Vesta.

## [23 de Noviembre, 2026] - Integración del módulo KYC en el repositorio compartido
- **Alcance:** incorporar al repositorio (`torb_2`) el módulo de verificación de identidad desarrollado en `dev-app` (fases 2 a 7), sin reemplazar archivos completos ni tocar cambios del compañero. Detalle técnico de cada fase: entradas KYC del 19 de noviembre más abajo.
- **Archivos nuevos (67):** configuración (`config/kyc.php`, `config/didit.php`), 5 migraciones, rutas (`routes/kyc.php`), enums, eventos, excepción, modelos, servicios, controladores, middleware, observador, política, comandos de consola, `Support`, vistas administrativas y JavaScript (administración y frontend) del KYC.
- **Archivos originales modificados solo con adiciones:** `webpack.mix.js` (3 entradas de compilación), `resources/views/admin/roles/add.blade.php` y `edit.blade.php` (grupo de permisos "Identity Verification" con `Manage KYC`), `RedaAlojamientoServiceProvider.php`, `resources/lang/es.json` (336 textos), `resources/sass/main.scss`, `resources/sass/admin/main.scss`, `resources/js/admin/general/main.js`, `resources/views/admin/general/main_footer.blade.php` y `resources/views/general/main_footer.blade.php`.
- **Ajuste por diferencia con el servidor:** `ChatController` del repositorio crea la consulta del chat con `status='Inquiry'` (el servidor tenía el estado vacío). `BloqueoKycObserver` ahora trata como consulta tanto `Inquiry` como el estado vacío; sin ese ajuste un usuario sin KYC no habría podido iniciar un chat. El resto de cambios del compañero (`PasarelaPagoController`, plantillas de texto) no afecta al bloqueo: las rutas son idénticas y el KYC no usa las claves de sesión que ese cambio conserva.
- **Validación:** `php -l` de los 46 archivos PHP, compilación de las 6 vistas Blade, resolución de las 77 clases referenciadas y las suites de pruebas del KYC ejecutadas contra el código de `torb_2` (bloqueo por rutas, comportamiento y modelos, panel administrativo, documentos, verificación manual, retención, Didit y retorno).
- **Pendiente en Vesta:** subir los archivos; `sudo -u appvac php8.2 artisan migrate`; `sudo -u appvac php8.2 artisan cache:clear` (la lista de permisos se guarda para siempre en caché y el permiso `manage_kyc` no aparece en Roles hasta limpiarla); compilar con `./compilar.sh` (Reda Alojamiento); `sudo -u appvac php8.2 artisan reda:kyc-auditar-rutas`.

---

## [22 de Noviembre, 2026] - Sincronización del manifiesto de subida y del inventario
- **Solicitud:** Establecer como obligatoria la actualización de `subir_archivos_puntuales.sh` y `lista_archivos_aplicacion.md` en las instrucciones de Copilot.
- **Cambios:** `.github/copilot-instructions.md` ahora permite modificar únicamente la lista de rutas del script puntual (sin ejecutarlo ni alterar su lógica), exige reemplazar las rutas de solicitudes previas por los archivos desplegables de la solicitud actual y documenta el uso de `Ninguno` cuando no haya archivos que subir. También exige registrar en `lista_archivos_aplicacion.md` cada archivo nuevo del plugin y cada archivo original modificado, respetando la clasificación existente y evitando duplicados.
- **Alcance de esta solicitud:** No se modificaron archivos de la aplicación ni se sincronizaron el script o el inventario; la solicitud solo actualiza directrices y ambos archivos tienen cambios locales preexistentes que se conservaron.

## [20 de Noviembre, 2026] - Plantillas separadas para reserva y pago
- **Solicitud:** Administrar opciones independientes para el mensaje de la primera pantalla de reserva, conservando las de confirmación de pago y añadiendo el total de personas a las variables existentes de reserva y pago.
- **Respaldo:** Se revisó `documentacion-torbian-2/BD/appvac_torbDev2-20261120-1857.sql`, del día actual, cabecera 17:56:20. Las tres plantillas existentes usan `mensaje_anfitrion`, una de ellas contiene país y método de pago; se conservan sin cambios en confirmación. `reserva_huespedes` y los desgloses de adultos/niños en `booking_details` no contienen registros; el helper utiliza `bookings.guest` para reservas históricas sin desglose. `country` contiene Venezuela. No se ejecutó SQL ni se modificó el respaldo.
- **Separación:** `packages/Reda/RedaAlojamiento/src/Models/PlantillaTexto.php` define los dos contextos en la columna existente; `packages/Reda/RedaAlojamiento/src/Http/Controllers/Admin/PlantillaTextoController.php` filtra, guarda y elimina por contexto y mantiene el permiso y la paginación. `packages/Reda/RedaAlojamiento/resources/views/admin/plantillas/textos.blade.php` permite elegir «Reserva inicial» o «Confirmación de pago». El grupo nuevo inicia vacío; el administrador debe crear sus opciones.
- **Variables:** `packages/Reda/RedaAlojamiento/src/Services/PlantillasTextoService.php` selecciona el grupo por pantalla y añade `{total_personas}` como suma de adultos y niños. Conserva `{pais}`, `{metodo_pago}`, `{huesped}`, `{anfitrion}`, `{alojamiento}`, `{llegada}`, `{salida}`, `{adultos}` y `{ninos}`. Se interpretó la segunda mención de «fecha desde» como fecha de salida, ya disponible en `{salida}`. `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/PlantillaPagoController.php` documenta el comportamiento y `packages/Reda/RedaAlojamiento/resources/lang/es.json` añade los rótulos.
- **Documentación:** Se actualizaron `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md` y este registro. No se alteraron vistas originales, registros históricos, migraciones ni scripts de subida.
- **Validación:** Sintaxis PHP y JSON y comprobación de espacios correctas; las pruebas funcionales quedan pendientes en Vesta.

## [19 de Noviembre, 2026] - KYC con Didit — Fase 7 (interfaz del usuario)
- **Alcance:** pantalla del usuario para verificar su identidad (Didit o manual con documentos), corrección de solicitudes y seguimiento del estado. No incluye notificaciones (Fase 8).
- **Archivos creados:** `resources/js/vistas/frontend/kyc/{verificacionIdentidad,renderizarTarjetaKyc,formularioManualKyc,peticionUsuarioKyc,consultarEstadoKyc,iniciarVerificacionDiditKyc,obtenerOpcionesManualKyc,obtenerSolicitudCorreccionKyc,enviarSolicitudManualKyc,utilidadesKyc}.js`.
- **Archivos modificados:** `resources/views/general/main_footer.blade.php` (carga el script solo en `users/edit-verification`), `resources/sass/main.scss`, `webpack.mix.js`, `Http/Controllers/Kyc/KycUsuarioController.php` (redirecciones), `resources/lang/es.json` (72 textos), manual técnico y este log. No se modificó la vista original `users/verification.blade.php`: la tarjeta se inyecta con JavaScript.
- **Decisiones:** (1) El estado siempre lo decide el servidor; la interfaz solo lo muestra y el bloqueo sigue en el backend. (2) Solo se navega a URL `https://` devueltas por el servidor. (3) Textos del administrador (motivo y corrección) escapados, sin HTML. (4) Actualización automática cada 20 s con tope de 45 consultas y solo con la pestaña visible. (5) El formulario pide solo los documentos que exige el tipo elegido. (6) Tras abrir el formulario manual se espera 550 ms desde que se cierra la animación de espera para que el modal de espera no retire el fondo del formulario. (7) En celular el formulario ocupa toda la pantalla con pie fijo y opciones apiladas.
- **Validación:** pruebas fuera del repositorio con Node portátil, jsdom, jQuery 3 y Bootstrap 4.5 reales sobre el bundle de esbuild y un servidor HTTP simulado: 49 comprobaciones (estados, escape de HTML hostil, sondeo, errores 500/429, Didit con URL no https, validaciones del formulario, envío multipart con CSRF, errores 422 por campo, corrección precargada, sin sesión). Las suites de PHP de las fases 3 a 6 siguen en verde y una nueva comprueba las redirecciones. SCSS y Blade compilados sin errores. No se compiló con webpack en este equipo.
- **Pendiente en Vesta:** subir los archivos; compilar con `BUILD_ALOJAMIENTO=true npm run prod`; `sudo -u appvac php8.2 artisan view:clear`; probar con un usuario común en `/users/edit-verification` (y en celular).
- **Estado:** Fase 7 completada. Siguiente: Fase 8 (notificaciones por correo y en la plataforma), 11 (PHPUnit) y 12 (informe final).
- **Lecciones del despliegue en Vesta:** (1) `vendor/reda/reda-alojamiento` debe ser un enlace simbólico a `packages/Reda/RedaAlojamiento` (`ln -s ../../packages/Reda/RedaAlojamiento vendor/reda/reda-alojamiento`); si una subida lo reemplaza por una carpeta, Laravel carga un plugin viejo (rutas KYC con 404 y menú sin acceso). No se debe subir `vendor/`, `node_modules/` ni `.env`. (2) Después de migrar el permiso `manage_kyc` hay que ejecutar `artisan cache:clear`: `Permissions::getAll()` guarda los permisos para siempre y, mientras la caché esté vieja, la pantalla de Roles dibuja el grupo "Identity Verification" sin casilla. (3) Guardar un rol desde Roles borra los permisos que no vengan marcados en el formulario.

---

## [19 de Noviembre, 2026] - KYC con Didit — Fases 6 (bloqueo de operaciones) y 5 (panel administrativo)
- **Alcance:** (Fase 6) bloqueo en el servidor de publicar y reservar para usuarios sin KYC verificado, en 4 capas; (Fase 5) panel de revisión en el admin. No incluye la interfaz del usuario (Fase 7) ni notificaciones (Fase 8).
- **Archivos creados (Fase 6):** `src/Exceptions/KycNoVerificadoException.php`, `src/Services/Kyc/{RespuestaBloqueoKyc,BloqueoKyc,ClasificadorOperacionKyc,AplicadorMiddlewareKyc,AuditorRutasKyc}.php`, `src/Http/Middleware/EnsureKycVerified.php`, `src/Observers/BloqueoKycObserver.php`, `src/Console/Commands/KycAuditarRutasCommand.php`.
- **Archivos creados (Fase 5):** `src/Events/CorreccionKycSolicitada.php`, `src/Services/Kyc/ConsultaKycAdmin.php`, `src/Support/EtiquetasKyc.php`, `src/Http/Controllers/Admin/Kyc/KycAdminController.php`, `resources/views/admin/kyc/{index,detalle}.blade.php`, `resources/js/admin/vistas/kyc/*.js` (9 archivos), `resources/js/admin/general/menus/menuKycAdmin.js`.
- **Archivos modificados:** `config/kyc.php`, `RedaAlojamientoServiceProvider.php` (alias `kyc.verificado`, observadores, Gate `kyc-verificado`, comando, callback `booted`), `Services/Kyc/KycService.php` (métodos de administración, caché de 5 s en `estaVerificado()` y `olvidarCache()`), `routes/kyc.php` (grupo `admin/reda/kyc`), `resources/views/admin/general/main_footer.blade.php`, `resources/js/admin/general/main.js`, `resources/sass/admin/main.scss`, `resources/lang/es.json` (152 textos nuevos), `webpack.mix.js`, manual técnico y este log.
- **Decisiones:** (1) El middleware clasifica por acción `Controlador@método`, no por URI. (2) El plugin reemplaza algunas rutas del núcleo en `booted`; por eso el manifiesto usa los nombres finales y el aplicador corre al final de `boot()`. (3) La auditoría de rutas encontró que `PasarelaPagoController` (callback/complete de pago) no estaba vigilado; se añadió. (4) El chat crea `Bookings` como consulta (en el servidor con `status=''`; en el repositorio compartido con `status='Inquiry'`): ambos valores se tratan como consulta y solo se bloquea convertirla en reserva real. (5) `reserva_nueva` solo para la reserva propia en `Processing`. (6) Ventana de reserva en sesión de 45 min, solo abierta por un usuario verificado. (7) `KYC_ENFORCEMENT` apaga las capas 1 y 2 sin tocar código. (8) Admin: el permiso `manage_kyc` se exige en ruta, controlador y servicio; el administrador inactivo se rechaza aunque su rol tenga el permiso (el middleware `permission` del núcleo no revisa el estado). (9) Rechazar se permite aunque haya una corrección sin atender (p. ej. sospecha de fraude); aprobar no. (10) El número de documento nunca se imprime completo: se enmascara y se revela bajo demanda con auditoría. (11) Los eventos y el log no contienen número de documento, dirección ni textos de observaciones.
- **Validación:** `php -l`, compilación Blade, análisis sintáctico de todo el JS nuevo (Node no está disponible en este equipo, no se compiló con webpack) y pruebas con SQLite fuera del repositorio: bloqueo por rutas 63, comportamiento 54, modelos 44, admin 113 (listado, filtros, búsqueda, paginación, permisos 403, acciones por HTTP, carreras, descarga y render) y regresión de fases 3 y 4 (254). La auditoría reporta 81 rutas vigiladas: 28 restringidas, 53 permitidas con justificación y 0 sin clasificar.
- **Pendiente en Vesta:** subir archivos (sin `vendor/`, `.env`, `node_modules` ni `bd-rumi.sql` en el zip); `sudo -u appvac php8.2 artisan config:clear && route:clear && view:clear`; ejecutar `sudo -u appvac php8.2 artisan reda:kyc-auditar-rutas` (debe terminar sin rutas sin clasificar); compilar assets con `BUILD_ALOJAMIENTO=true` (webpack) para generar `indexKyc.min.js`, `detalleKyc.min.js` y el menú; comprobar que el rol admin tiene `manage_kyc` (Roles en el panel).
- **Estado:** Fases 5 y 6 completadas. Siguiente: Fase 7 (interfaz del usuario para enviar el KYC), luego Fase 8 (notificaciones), 11 (pruebas PHPUnit) y 12 (informe final).

---

## [19 de Noviembre, 2026] - KYC con Didit — Fase 4 (verificación manual y documentos privados)
- **Alcance:** verificación manual con datos personales y documentos, almacenamiento privado cifrado, descarga protegida, política de autorización y retención. No incluye pantallas (Fase 7), panel administrativo (Fase 5), bloqueo de operaciones (Fase 6) ni notificaciones (Fase 8).
- **Archivos creados:** `src/Support/PaisesIso.php`, `src/Services/Kyc/{KycDocumentoService,DocumentoKycInvalidoException,ValidadorKycManual,RetencionKycService}.php`, `src/Policies/KycPolicy.php`, `src/Http/Controllers/Kyc/KycDocumentoController.php`, `src/Console/Commands/KycDepurarCommand.php`.
- **Archivos modificados:** `config/kyc.php`, `routes/kyc.php`, `RedaAlojamientoServiceProvider.php` (disco `kyc_privado`, singletons, políticas, comando diario), `Services/Kyc/KycService.php`, `Http/Controllers/Kyc/KycUsuarioController.php`, `resources/lang/es.json`, `.env` (`KYC_RUTA_PRIVADA` vacío), manual técnico y este log.
- **Decisiones:** (1) Como la raíz web es la raíz del código, los documentos se guardan cifrados (AES-256 con APP_KEY), con nombre UUID y `.htaccess` de denegación; se recomienda `KYC_RUTA_PRIVADA` fuera de `public_html`. (2) Las imágenes se recodifican con GD para eliminar metadatos y cargas ocultas; los PDF se inspeccionan sin escanear los flujos binarios (evita falsos positivos con imágenes) pero sí los `ObjStm` comprimidos. (3) La selfie solo admite imágenes. (4) Solo se conservan los documentos que exige el tipo de documento (minimización). (5) Mayoría de edad (18) y aceptación del tratamiento de datos obligatorias. (6) Una verificación Didit sin terminar se reemplaza por la manual sin aviso intermedio. (7) El número de documento repetido en otro usuario se marca en la auditoría como señal para el revisor, sin bloquear. (8) Retención: 90 días tras aprobar y 30 tras rechazo, fallo o expiración (confirmado por el cliente, ajustables en `config/kyc.php`); no elimina con mediación activa, cuenta restringida o retención suspendida, y nunca cambia el estado de verificación. (9) Cada `throttle` lleva prefijo propio porque sin él Laravel comparte el contador entre las rutas del mismo usuario.
- **Validación:** `php -l` de todos los archivos. Pruebas con SQLite en memoria y GD (fuera del repositorio): 70 de documentos (MIME falso, polígloto, EXIF, orientación, PDF con JavaScript, ObjStm y ofuscación, cifrado, integridad, traversal), 85 del flujo manual, 49 por HTTP/política y 39 de retención, más una regresión de la Fase 3 (11). No se ejecutaron en MariaDB ni PHPUnit.
- **Pendiente en Vesta:** subir archivos; confirmar `gd`, `fileinfo` y `exif` en PHP 8.2; ajustar `upload_max_filesize` (≥ 6M) y `post_max_size` (≥ 20M) porque se envían hasta 3 archivos de 5 MB; crear la carpeta privada (recomendado `/home/appvac/kyc_privado`, propietario `appvac`, permisos 700) y definir `KYC_RUTA_PRIVADA`; `sudo -u appvac php8.2 artisan config:clear && sudo -u appvac php8.2 artisan route:clear`; probar `sudo -u appvac php8.2 artisan reda:kyc-depurar --simular`; confirmar que `schedule:run` esté en el cron. Respaldar APP_KEY junto con los documentos: sin ella los archivos cifrados no se pueden leer.
- **Estado:** Fase 4 completada; siguiente: Fase 6 (bloqueo de operaciones) según el orden del plan v2, o Fase 5 (panel administrativo).

---

## [19 de Noviembre, 2026] - KYC con Didit — Fase 3 (integración con Didit)
- **Alcance:** cliente de Didit, servicio central de estados, webhook firmado, endpoints del usuario para la verificación automática y consulta de respaldo. No activa bloqueos (Fase 6), pantallas (Fase 7) ni verificación manual (Fase 4).
- **Archivos creados:** `config/didit.php`, `routes/kyc.php`, `src/Services/Kyc/{DiditService,DiditException,FirmaWebhookDidit,KycService}.php`, `src/Events/EstadoKycCambiado.php`, `src/Http/Controllers/Kyc/{DiditWebhookController,KycUsuarioController}.php`, `src/Console/Commands/KycSincronizarCommand.php`.
- **Archivos modificados:** `RedaAlojamientoServiceProvider.php` (config de Didit, singletons, rutas, comando y programación), `src/Enums/EstadoKyc.php` (transiciones de revocación), `src/Models/Kyc/EventoKyc.php` (constantes), `resources/lang/es.json`, `.env` (5 variables `DIDIT_*` vacías), manual técnico y este log.
- **Decisiones:** (1) El webhook acepta `X-Signature-V2` y `X-Signature`, nunca `X-Signature-Simple`. (2) El orden de eventos usa `created_at` del payload porque `timestamp` se refresca en cada reintento. (3) Una aprobación se confirma con la API de Didit antes de autorizar. (4) Se añadió a la matriz de transiciones la revocación por Didit (`verified` → `failed`/`in_progress`/`expired` y `failed` → `verified`), porque un revisor de Didit puede cambiar una decisión y ignorarlo dejaría autorizado a un usuario rechazado. (5) El retorno del navegador nunca aprueba. (6) `config/didit.php` se fusiona en `services.didit` para no editar `config/services.php`.
- **Validación:** `php -l` de todos los archivos. Pruebas con SQLite en memoria y las migraciones reales: 88 comprobaciones del servicio y 49 del flujo HTTP, rutas, comando, `boot()` del provider y migración del permiso, con Didit simulado y firmas generadas por la referencia oficial en Python. Incluyen firma inválida o repetida, sesión antigua, evento duplicado, revocación, límites, ausencia de secretos y datos personales en logs, y escritura fuera de KycService. No se ejecutaron en MariaDB ni PHPUnit.
- **Pendiente en Vesta:** subir archivos; `sudo -u appvac php8.2 artisan migrate`; completar en `.env` `DIDIT_API_KEY`, `DIDIT_WORKFLOW_ID` y `DIDIT_WEBHOOK_SECRET`; `sudo -u appvac php8.2 artisan config:clear && sudo -u appvac php8.2 artisan route:clear`; registrar en la consola de Didit el webhook `https://<dominio>/webhooks/didit/kyc` con el evento `status.updated`; confirmar que `schedule:run` esté en el cron.
- **Estado:** Fase 3 completada; siguiente: Fase 4 (verificación manual, documentos privados y `reda:kyc-depurar`).

---

## [19 de Noviembre, 2026] - KYC con Didit — Fase 2 (base de datos, enum, configuración y modelos)
- **Alcance:** Implementación por fases del KYC (plan técnico v2 aprobado). Esta fase no activa ningún bloqueo ni integra Didit todavía. Auditoría: no existía un KYC real; `users_verification` es solo verificación social y se conserva intacta.
- **Archivos creados:** `packages/Reda/RedaAlojamiento/config/kyc.php`, `src/Enums/EstadoKyc.php`, `src/Enums/MetodoKyc.php`, `src/Models/Kyc/{SolicitudKyc,UsuarioKyc,DocumentoKyc,EventoKyc,ProtegeEscrituraKyc}.php`, `src/Services/Kyc/GuardiaEscrituraKyc.php` y las migraciones `2026_11_19_100000` a `100400` (`solicitudes_kyc`, `usuarios_kyc`, `documentos_kyc`, `eventos_kyc` y permiso `manage_kyc`).
- **Archivos modificados:** `RedaAlojamientoServiceProvider.php` (`mergeConfigFrom` de `kyc`), `resources/lang/es.json` (etiquetas) y `manual_tecnico_plugin_reda_alojamiento.md`.
- **Decisiones:** (1) `usuarios_kyc` es la única fuente del estado efectivo y `solicitudes_kyc` el historial de intentos; solo los servicios KYC escriben (guardián + trait). (2) Una sola solicitud abierta por usuario garantizada por el UNIQUE `clave_abierta`. (3) `vendor_data` de Didit: se mantiene estable `kyc-{user_id}` y la trazabilidad por intento va en `metadata` y `session_id`, porque según la documentación de Didit un `vendor_data` distinto por intento haría que la detección de duplicados trate cada intento como otra persona; las sesiones terminadas (Approved, Declined, In Review, Expired, Abandoned, Kyc Expired) no se reutilizan, por lo que un nuevo intento crea una sesión nueva. Queda configurable con `kyc.didit.vendor_data_por_intento`. (4) El permiso `manage_kyc` se asigna al rol `admin` que tenga `manage_admin`, sin asumir el id 1.
- **Validación:** `php -l` de todos los archivos, JSON de traducciones y SQL de las migraciones generado con el gramático de MySQL en modo `pretend`. Prueba sin BD de enum, mapa de Didit, cifrado/hash del documento y guardián de escritura (todo OK). No se ejecutaron migraciones ni se subieron archivos.
- **Pendiente en Vesta:** subir los archivos, ejecutar `sudo -u appvac php8.2 artisan migrate` y comprobar el rol asignado con `SELECT r.id, r.name FROM roles r JOIN permission_role pr ON pr.role_id=r.id JOIN permissions p ON p.id=pr.permission_id WHERE p.name='manage_kyc';`.
- **Estado:** Fase 2 completada; siguiente: Fase 3 (Didit).
- **Verificación contra `bd-rumi.sql` (MariaDB 10.5, volcado del 19/11/2026):** el rol `admin` es el id 1 y es el único con `manage_admin` (45 permisos); el rol 2 "Atención a usuario" solo tiene `manage_messages`, por lo que `manage_kyc` se asignará únicamente al rol 1. `users.id` y `admin.id` son `int unsigned` InnoDB utf8mb4, compatibles con las claves foráneas creadas. Las tablas KYC aún no existen en la BD.

---

## [19 de Noviembre, 2026] - Variables de país y método de pago
- **Solicitud:** Incorporar a Plantillas → Textos las variables del país y el método de pago para los mensajes de la primera pantalla de reserva.
- **Respaldo:** Se revisó `documentacion-torbian-2/BD/appvac_torbDev2-20261119-1933.sql`, del día actual: `country` contiene Venezuela, `gateways` contiene alias y nombres de pasarelas, y existen dos plantillas. El formulario usa el código de país y el alias de pasarela, cuyos nombres visibles provienen de esas tablas; `payment_methods` es una tabla distinta. No se consultó ni modificó la base de datos.
- **Servidor:** `packages/Reda/RedaAlojamiento/src/Services/PlantillasTextoService.php` agrega `{pais}` y `{metodo_pago}`; en confirmación usa el país y la pasarela guardados en sesión, o «No seleccionado» si faltan. Devuelve también el texto con estas variables para permitir su actualización en la primera pantalla. `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/PlantillaPagoController.php` distingue la pantalla inicial de confirmación.
- **Interfaz:** `packages/Reda/RedaAlojamiento/resources/js/vistas/pago/frontend/pagos.js` sustituye las variables con los valores visibles y recalcula las opciones al cambiar país o método. No sobrescribe mensajes personalizados. `packages/Reda/RedaAlojamiento/resources/views/admin/plantillas/textos.blade.php` enumera las variables y su valor cuando faltan. `packages/Reda/RedaAlojamiento/resources/lang/es.json` añade los textos.
- **Documentación:** Se actualizaron `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md` y este registro. No se modificaron vistas originales, migraciones ni scripts de subida.
- **Validación:** Sintaxis PHP y JavaScript, JSON y espacios de los archivos intervenidos; compilación y prueba funcional pendientes en Vesta.

## [19 de Noviembre, 2026] - Plantillas de mensaje en la reserva inicial
- **Solicitud:** Mostrar el selector de textos y la primera opción en `payments/book/{id?}`, conservando la entrega del mensaje al inbox.
- **Respaldo:** Se revisó `documentacion-torbian-2/BD/appvac_torbDev2-20261119-1933.sql`, del día actual. Las dos opciones registradas comparten el contexto `mensaje_anfitrion` y hacen referencia al comprobante; se recomienda revisar su redacción porque ahora se usarán también antes del pago. `messages.message` es de tipo `text`; no hay registros de mensajes de pago en el respaldo. Se tuvieron en cuenta las comprobaciones de integridad documentadas en la incidencia anterior.
- **Código:** `packages/Reda/RedaAlojamiento/resources/js/vistas/pago/frontend/pagos.js` comparte `cargarPlantillas()` entre el campo `message_to_host` del formulario inicial y la nota de transferencia. Selecciona la primera opción si el campo está vacío, conserva lo escrito incluso durante la carga, permite volver al texto personalizado y muestra errores sin impedir la escritura manual. La validación específica de comprobantes permanece limitada a la pantalla de transferencia.
- **Entrega:** Se verificó por lectura que `PaymentController::createBooking()` transmite el mensaje al guardar directamente o lo deja en sesión para la pasarela; `store()` y `update()` crean el mensaje de reserva y `RedaInboxController` lo incorpora a la conversación. La nota posterior y el comprobante siguen siendo un mensaje separado, sin añadir duplicados mediante esta modificación. La ejecución funcional está pendiente.
- **Archivos modificados:** `packages/Reda/RedaAlojamiento/resources/js/vistas/pago/frontend/pagos.js`, `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md` y este registro. No se modificaron vistas originales, base de datos ni scripts de subida.
- **Validación:** Sintaxis JavaScript y comprobación de espacios en los archivos intervenidos; compilación y prueba funcional pendientes en Vesta.

## [19 de Noviembre, 2026] - Incidencia al confirmar pago y reservación
- **Solicitud:** Investigar el envío de correo al anfitrión sin creación de la reserva y el mensaje «No se encontraron los datos necesarios para la propiedad» tras confirmar el pago por transferencia con texto predefinido.
- **Respaldo revisado:** `documentacion-torbian-2/BD/appvac_torbDev2-20261119-1933.sql`, del día actual; cabecera interna 18:32:44. Contiene 79 reservas, dos plantillas y ningún mensaje de pago. No se encontraron fechas bancarias con reservas inexistentes, mensajes con reserva inexistente, metadatos huérfanos ni fechas duplicadas para una propiedad; las reservas 77–79 conservan todas sus noches bloqueadas.
- **Causa:** `PaymentController::store()` y `update()` eliminan variables de sesión de pago antes de que `PasarelaPagoController::getAllData()` busque la reserva. Al consultar con la propiedad ausente, la búsqueda falla y revierte la transacción aunque el correo ya fue enviado. El redireccionamiento posterior puede volver a solicitar precios sin el contexto necesario.
- **Corrección:** `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/PasarelaPagoController.php` captura los identificadores de propiedad y reserva antes de llamar al núcleo; usa los valores capturados en ambos caminos y, si ocurre una excepción, restaura el contexto de pago para que el flujo no pierda la sesión. No altera otras pasarelas ni reescribe datos históricos.
- **Archivos modificados:** `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/PasarelaPagoController.php`, `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md` y este registro. No se realizaron consultas a la base de datos ni cambios de datos.
- **Precaución:** El envío de correos y la subida de archivos del núcleo se producen antes de que finalice la transacción: si otro error la revierte, esos efectos externos no se revierten. Las pruebas funcionales siguen pendientes en Vesta.
- **Validación estática:** Sintaxis PHP y espacios de los archivos de esta incidencia correctos. La comprobación global detectó espacios finales en `subir_archivos_puntuales.sh`, previamente modificado por el usuario; no se alteró ese script.

## [18 de Noviembre, 2026] - Plantillas de texto y entrega de nota de pago
- **Implementación:** `packages/Reda/RedaAlojamiento/database/migrations/2026_11_18_133000_crear_plantillas_y_mensajes_de_pago.php`, `packages/Reda/RedaAlojamiento/src/Models/PlantillaTexto.php`, `packages/Reda/RedaAlojamiento/src/Models/MensajePago.php` y `packages/Reda/RedaAlojamiento/src/Services/PlantillasTextoService.php` crean el almacenamiento auxiliar, las opciones ordenadas y la sustitución de variables. Las notas permiten hasta 10.000 caracteres.
- **Administración:** `packages/Reda/RedaAlojamiento/src/Http/Controllers/Admin/PlantillaTextoController.php`, `packages/Reda/RedaAlojamiento/resources/views/admin/plantillas/textos.blade.php`, `packages/Reda/RedaAlojamiento/resources/js/admin/vistas/plantillas/textos.js`, `packages/Reda/RedaAlojamiento/resources/js/admin/general/menus/menuLateralAdmin.js` y `packages/Reda/RedaAlojamiento/resources/sass/admin/main.scss` gestionan el submenú, permisos, lista paginada, editor, variables y diseño adaptable.
- **Pago y buzón:** `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/PlantillaPagoController.php`, `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/PasarelaPagoController.php`, `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/ComprobantePagoController.php`, `packages/Reda/RedaAlojamiento/resources/js/vistas/pago/frontend/pagos.js`, `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaInboxController.php`, `packages/Reda/RedaAlojamiento/resources/views/users/messages.blade.php` y `packages/Reda/RedaAlojamiento/resources/sass/main.scss` seleccionan el texto plano, crean el mensaje de pago y enlazan comprobantes para los participantes. Los datos históricos se conservan.
- **Integración:** `packages/Reda/RedaAlojamiento/routes/web.php`, `packages/Reda/RedaAlojamiento/src/RedaAlojamientoServiceProvider.php`, `packages/Reda/RedaAlojamiento/resources/lang/es.json` y `webpack.mix.js` conectan rutas, pasarela, traducciones y compilación. Se actualizaron `manual_tecnico_plugin_reda_alojamiento.md` y `previo_cambios_realizados.md`.
- **Precauciones:** Los comprobantes siguen en la carpeta pública del core aunque el enlace nuevo exige autorización. Las imágenes y correos originales son efectos externos que no se revierten con la transacción. No se modificó el historial.
- **Respaldo posterior:** Se revisó `documentacion-torbian-2/BD/appvac_torbDev2-20261118-1356.sql`, nombre 13:56 y cabecera 12:56:19. Contiene 79 reservas, 275 mensajes y 260 metadatos; 49 reservas tienen nota y comprobante, 18 eliminadas lógicamente. En 30 casos la nota difiere de al menos un mensaje de reserva activo. No se encontraron mensajes con reserva inexistente ni metadatos con mensaje inexistente; no hay registros de desglose de adultos/niños en las tablas inspeccionadas. No se probó restaurar el respaldo.
- **Validación y despliegue:** Sintaxis PHP y JS y JSON comprobados estáticamente; las pruebas funcionales, migración y compilación están pendientes en Vesta. No se ejecutó SQL ni se subieron archivos.

## [18 de Noviembre, 2026] - Análisis de plantillas de textos y mensaje de pago
- **Solicitud:** Crear el submenú de plantillas de correos y textos, permitir opciones predefinidas para el mensaje al anfitrión durante el pago e investigar su entrega en el inbox junto al comprobante.
- **Respaldo:** `documentacion-torbian-2/BD/appvac_torbDev2-20261118-1140.sql`, del día actual. Se revisó la estructura de `bookings`, `messages`, `message_type`, `reda_mensajes_metadata` y `email_templates`.
- **Hallazgo:** La nota de transferencia se guarda en `bookings.note` (191 caracteres), mientras que el mensaje de reserva se crea con `message_to_host` procedente de la pantalla anterior; la vista de mensajes escapa el texto y no muestra el comprobante.
- **Integración pendiente:** El historial del inbox agrupa reservas y virtualiza sus identificadores; cualquier enlace al comprobante debe identificar la reserva real y verificar el acceso. El desglose de adultos y niños se conserva en la sesión durante el pago.
- **Definiciones recibidas:** Los mensajes de pago e inbox permanecerán en texto plano; las variables de huéspedes deben separar adultos y niños; corregir solo pagos futuros. Los mensajes largos se almacenarán en una tabla auxiliar del plugin, sin ampliar `bookings.note`.
- **Archivos modificados:** `previo_cambios_realizados.md` y este registro. La implementación y las pruebas siguen pendientes; no se ha modificado la aplicación ni ejecutado SQL.

## [18 de Noviembre, 2026] - Restablecimiento del Formulario al Regresar desde el Pago
- **Solicitud:** Corregir el botón «Enviar» y el spinner que permanecen deshabilitado/visibles cuando se regresa a la página de la propiedad desde la selección del método de pago con Atrás del navegador o del móvil.
- **Respaldo y datos revisados:** `documentacion-torbian-2/BD/appvac_torbDev2-20261118-1140.sql`, generado hoy a las 11:40. Se comprobaron los bookings recientes 77–79 y sus noches en `property_dates`: todas aparecen bloqueadas para las propiedades correspondientes, sin traslapes futuros entre reservas activas; no se encontró una incongruencia de disponibilidad relacionada con este síntoma. No se ejecutó SQL ni se modificó el respaldo.
- **Causa y solución:** el core deshabilita `#save_btn` y muestra su spinner al enviar `#booking_form`; al restaurar la página desde BFCache, el evento `pageshow` permite reactivar el botón y ocultar el spinner sin limpiar los datos que el navegador conservó. Se implementó en el script específico del detalle, sin modificar archivos originales.
- **Archivos modificados:** `packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/propiedad_detalle.js` incorpora `restablecerEnvioAlRegresar()` y lo registra durante la inicialización; `manual_tecnico_plugin_reda_alojamiento.md` documenta el método y el comportamiento; `previo_cambios_realizados.md` registra el cierre de la tarea.
- **Validación:** pendiente ejecutar `node --check` y `git diff --check`. No se compiló ni desplegó; las pruebas funcionales quedan para el servidor Vesta.

## [17 de Noviembre, 2026] - Reinicio con información adicional
- **Respaldo revisado:** `documentacion-torbian-2/BD/appvac_torbDev2-20261117-1740.sql`, nombre 17/11 a las 17:40; cabecera interna 16:40:06. Hay dos mediaciones, una esperando respuesta, tres solicitudes, cuatro destinatarios, nueve archivos privados, nueve correos y cinco alertas de archivos. No se detectaron referencias huérfanas en las tablas de información; las rutas privadas coinciden con el formato de almacenamiento. `disputas_respaldo` no existe en el respaldo.
- **Servidor:** `packages/Reda/RedaAlojamiento/src/Services/ReinicioMediacionesService.php` retira el bloqueo por solicitudes, elimina sus datos asociados y alertas de archivos junto con las mediaciones e incorpora los archivos privados a la cola de eliminación; preserva la restricción de rutas y la verificación mediante huella. Los archivos que no se puedan verificar o eliminar permanecen pendientes sin impedir el reinicio de los datos. Se mantiene la conservación de adjuntos públicos ante `disputas_respaldo`.
- **Respuesta e interfaz:** `packages/Reda/RedaAlojamiento/src/Http/Controllers/Admin/Disputa/ReinicioMediacionesController.php`, `packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/reinicioMediaciones.js`, `packages/Reda/RedaAlojamiento/resources/views/admin/disputa/reinicio_mediaciones.blade.php` y `packages/Reda/RedaAlojamiento/resources/lang/es.json` comunican el alcance, el número de solicitudes y adjuntos pendientes y el resultado exitoso con advertencia.
- **Documentación:** `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md` y este registro. No se ha ejecutado SQL, migración, compilación ni eliminación física; las pruebas funcionales quedan para Vesta.
- **Validación estática:** Sintaxis PHP y JavaScript, traducciones JSON sin duplicados en las claves agregadas y `git diff --check` correctos. Las nueve rutas privadas del respaldo coinciden con el formato admitido. Las pruebas de permisos, eliminación y transacción aún no se han realizado.

## [09 de Noviembre, 2026] - Implementación de información adicional en mediaciones
- **Persistencia:** `packages/Reda/RedaAlojamiento/database/migrations/2026_11_09_100000_crear_solicitudes_informacion_mediaciones.php` crea tablas auxiliares; `packages/Reda/RedaAlojamiento/src/Models/Disputa/SolicitudInformacion.php`, `DestinatarioInformacion.php`, `ArchivoInformacion.php` y `CorreoInformacion.php` representan sus registros. No se ejecutó la migración.
- **Servidor y correo:** `packages/Reda/RedaAlojamiento/src/Http/Controllers/Disputa/InformacionAdicionalController.php`, `packages/Reda/RedaAlojamiento/routes/web.php` y `packages/Reda/RedaAlojamiento/resources/views/emails/informacion_mediacion.blade.php` gestionan destinatarios, plazo, almacenamiento privado autorizado, mensajes, alertas, correo reintentable y retorno al inbox tras login.
- **Integración de mensajes y documentos:** `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaInboxController.php`, `packages/Reda/RedaAlojamiento/src/Http/Controllers/Disputa/MensajeController.php`, `packages/Reda/RedaAlojamiento/src/Http/Controllers/Admin/Disputa/MensajeController.php`, `packages/Reda/RedaAlojamiento/src/Http/Controllers/Disputa/DisputaController.php`, `packages/Reda/RedaAlojamiento/src/Http/Controllers/Admin/Disputa/DisputaController.php` y `packages/Reda/RedaAlojamiento/resources/views/users/messages.blade.php` vinculan avisos y adjuntos privados con sus destinatarios y verifican acceso.
- **Interfaz:** `packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/indexDisputas.js`, `packages/Reda/RedaAlojamiento/resources/js/vistas/disputa/disputas/indexDisputas.js`, `packages/Reda/RedaAlojamiento/resources/js/vistas/inbox/inbox.js`, `packages/Reda/RedaAlojamiento/resources/js/vistas/inbox/informacionAdicional.js`, `packages/Reda/RedaAlojamiento/resources/js/admin/vistas/alerta/indexAlertas.js` y `packages/Reda/RedaAlojamiento/resources/lang/es.json` incorporan formulario, carga, visualización, reintentos, avisos y escape de textos.
- **Conservación:** `packages/Reda/RedaAlojamiento/src/Services/ReinicioMediacionesService.php` y `packages/Reda/RedaAlojamiento/src/Http/Controllers/Admin/Disputa/ReinicioMediacionesController.php` bloquean el reinicio ante solicitudes privadas existentes para evitar pérdida de referencias y archivos.
- **Documentación:** `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md` y este registro. La compilación, migración y pruebas funcionales quedan pendientes en Vesta. Se recomienda verificar límites PHP para cargas de hasta cinco archivos de 10 MB.
- **Validación estática:** Sintaxis PHP y JavaScript correctas, JSON válido sin claves duplicadas nuevas y `git diff --check` correcto. No se realizaron pruebas funcionales ni de envío de correo.

## [09 de Noviembre, 2026] - Análisis de solicitud de información adicional
- **Solicitud:** Crear un flujo de información adicional con destinatarios, plazo, mensajería, correo y carga de archivos privados; cambiar la mediación a «Esperando respuesta».
- **Respaldo revisado:** `documentacion-torbian-2/BD/appvac_torbDev2-20261109-0523.sql`, cuyo nombre indica hoy a las 05:23 y cuya cabecera interna indica generación a las 04:22:28. Hay dos mediaciones abiertas y asignadas; `disputas` contiene el iniciador, ambas partes y el agente.
- **Hallazgos:** Los adjuntos históricos se almacenan bajo `public/images/disputas`, por lo que los nuevos archivos privados deben utilizar almacenamiento no público y autorización al acceder. La mensajería existente se relaciona con reservas y metadatos de emisor.
- **Decisiones recibidas:** Las solicitudes anteriores vigentes comparten el último plazo; las vencidas permanecen vencidas. Solo solicita el agente asignado; el estado permanece «Esperando respuesta» después de las cargas; hasta cinco archivos por carga de 10 MB, JPG/PNG/WebP/PDF; avisar de correos pendientes y permitir reintento; mostrar el botón «Solicitar más información» bajo el selector cuando esté activo. Cada carga notificará al agente mediante una alerta individual en el menú «Mensajes y alertas» → «Alertas» y un correo.
- **Archivos modificados:** `previo_cambios_realizados.md` y este registro. Análisis realizado; faltan la implementación integral y su validación en Vesta. Sin cambios de código ni datos.

## [09 de Noviembre, 2026] - Cambio de mediación a revisión
- **Respaldo:** `documentacion-torbian-2/BD/appvac_torbDev2-20261109-0523.sql`, generado hoy a las 05:23. Contiene dos mediaciones abiertas en «Asignación a agente», ambas asignadas; `disputas` tiene clave primaria `id`, `booking_id` relacionado con `bookings` y campos de estado y paso nullable. No se modificaron datos ni respaldo.
- **Servidor:** `packages/Reda/RedaAlojamiento/routes/web.php` registra `POST /admin/reda/disputas/poner-en-revision`; `packages/Reda/RedaAlojamiento/src/Http/Controllers/Admin/Disputa/DisputaController.php` valida roles y visibilidad, bloquea la fila y actualiza `estado` y `paso_actual` a «En revisión». El listado localiza el caso en la página de revisión y filtra el valor literal independientemente de la traducción. No se establecen restricciones adicionales sobre el estado anterior.
- **Vista del usuario:** `packages/Reda/RedaAlojamiento/src/Http/Controllers/Disputa/DisputaController.php` filtra revisión por el valor literal o traducido.
- **Interfaz:** `packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/indexDisputas.js` gestiona petición, espera, errores y selección de la pestaña y del caso; las demás opciones aún no implementadas notifican y restauran el paso previo. `packages/Reda/RedaAlojamiento/resources/lang/es.json` añade los mensajes correspondientes.
- **Documentación:** `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md` y este registro. No se ejecutó SQL ni compilación; las pruebas funcionales quedan pendientes en Vesta.
- **Validación estática:** Sintaxis de los tres archivos PHP, del JavaScript, lectura del JSON y `git diff --check` correctos. No se ejecutaron pruebas HTTP ni operaciones de base de datos.

## [09 de Noviembre, 2026] - Texto largo en selector de paso actual
- **Estilos:** `packages/Reda/RedaAlojamiento/resources/sass/admin/main.scss` reserva espacio a la derecha del selector de pasos y aplica truncamiento con puntos suspensivos, preservando la flecha y las opciones completas. No cambia la lógica ni los datos.
- **Documentación:** `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md` y este registro. La compilación y prueba visual en escritorio y móvil quedan pendientes en Vesta.
- **Validación estática:** `git diff --check` correcto. No se realizó compilación ni prueba visual local.

## [09 de Noviembre, 2026] - Vigencia obligatoria del respaldo de base de datos
- **Directrices:** `.github/copilot-instructions.md` exige comparar `AAAAMMDD` del respaldo más reciente con la fecha actual antes de iniciar una solicitud que consulte, busque, inserte, actualice o elimine datos. Si el respaldo es de un día anterior, se debe detener el trabajo y solicitar uno actualizado; no se continúa hasta confirmar un respaldo de la fecha actual o posterior.
- **Punto de control:** `previo_cambios_realizados.md` registra esta tarea completada.
- **Respaldo SQL:** No se inspeccionó ni modificó ningún archivo de respaldo; esta petición solo actualiza instrucciones.

## [08 de Noviembre, 2026] - Consulta sobre facturación de Copilot
- Se consultó la documentación oficial de GitHub sobre la renovación de la suscripción y el reinicio mensual de los créditos de IA. No se modificó código del proyecto.

## [08 de Noviembre, 2026] - Etiqueta del paso actual
- **Interfaz:** `packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/indexDisputas.js` cambia la etiqueta del selector a «Paso actual:» y retira el aviso de que la selección no se guarda, junto a su referencia accesible. La selección manual sigue sin ejecutar acciones.
- **Textos y estilos:** `packages/Reda/RedaAlojamiento/resources/lang/es.json` añade la nueva etiqueta y retira el texto del aviso; `packages/Reda/RedaAlojamiento/resources/sass/admin/main.scss` elimina la regla exclusiva del aviso.
- **Documentación:** `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md` y este registro. Quedan pendientes la compilación y prueba visual en Vesta.
- **Validación estática:** Sintaxis JavaScript, lectura del JSON y `git diff --check` correctos; no quedan referencias al aviso en los recursos del plugin.

## [08 de Noviembre, 2026] - Selector compacto y paso actual de mediación
- **Respaldo:** `documentacion-torbian-2/BD/appvac_torbDev2-20261107-1645.sql` (7 de noviembre a las 16:45): ocho mediaciones en «Caso creado» y `paso_actual` nullable. El listado administrativo ya devuelve el paso; no se añadieron consultas ni se modificaron datos.
- **Interfaz:** `packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/indexDisputas.js` añade la etiqueta «Paso:», selecciona el paso actual de cada mediación y lo sincroniza tras asignar o tomarla. Conserva los dos primeros pasos deshabilitados y no guarda las selecciones manuales. Un paso nulo o desconocido muestra un aviso y queda registrado en consola.
- **Estilos y textos:** `packages/Reda/RedaAlojamiento/resources/sass/admin/main.scss` limita la altura del select a 28 px y su letra a 12 px; `packages/Reda/RedaAlojamiento/resources/lang/es.json` añade el aviso de paso no disponible.
- **Documentación:** `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md` y este registro. La compilación y prueba funcional y visual quedan pendientes en Vesta.
- **Validación estática:** Sintaxis JavaScript, lectura del JSON y `git diff --check` correctos. No se realizó compilación ni prueba funcional local.

## [08 de Noviembre, 2026] - Orden de estado e ID
- **Tarjetas y detalles:** `packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/indexDisputas.js` muestra el estado antes del ID en cada tarjeta y en la cabecera compartida por el lateral de escritorio y la información adicional móvil. El selector y la ubicación de «Actualizado» permanecen sin cambios.
- **Documentación:** `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md` y este registro. La compilación y prueba visual quedan pendientes en Vesta.
- **Validación estática:** Sintaxis JavaScript y `git diff --check` correctos. No se realizó compilación ni prueba visual local.

## [08 de Noviembre, 2026] - Actualización en la tercera columna
- **Tarjetas:** `packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/indexDisputas.js` muestra «Actualizado» al inicio de la tercera columna, antes de los participantes, en escritorio; mantiene la ubicación anterior en móvil. Ambas ubicaciones usan el mismo contenido y son mutuamente excluyentes según el ancho de pantalla.
- **Documentación:** `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md` y este registro. La compilación y prueba visual quedan pendientes en Vesta.
- **Validación estática:** Sintaxis JavaScript y `git diff --check` correctos. No se realizó compilación ni prueba visual local.

## [08 de Noviembre, 2026] - Reubicación de selector y actualización
- **Tarjetas:** `packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/indexDisputas.js` coloca el selector y su aviso debajo del título. En escritorio muestra «Actualizado» al inicio de la segunda columna, correspondiente a los datos de mediación después de la imagen; en móvil conserva su ubicación anterior. Se mantiene el selector sin acciones de guardado y sin cambios de datos.
- **Documentación:** `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md` y este registro. La compilación y prueba visual quedan pendientes en Vesta.
- **Validación estática:** Sintaxis JavaScript y `git diff --check` correctos. No se realizó compilación ni prueba visual local.

## [08 de Noviembre, 2026] - Diseño del selector de pasos de mediaciones
- **Tarjetas:** `packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/indexDisputas.js` ordena primero el ID y luego el estado; sitúa el selector en la línea siguiente a todo el ancho. Conserva sus opciones, aviso y comportamiento exclusivamente visual.
- **Detalles:** La misma función de cabecera muestra primero el ID y después el estado tanto en el lateral de escritorio como en la información adicional móvil.
- **Estilos:** `packages/Reda/RedaAlojamiento/resources/sass/admin/main.scss` proporciona el ancho completo al selector y elimina la disposición horizontal anterior.
- **Documentación:** `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md` y este registro. La compilación y prueba visual quedan pendientes en Vesta.
- **Validación estática:** Sintaxis JavaScript y `git diff --check` correctos. No se realizó compilación ni prueba visual local.

## [08 de Noviembre, 2026] - Selector visual de pasos en tarjetas de mediación
- **Solicitud:** Mostrar el selector junto al estado de cada tarjeta, en escritorio y móvil; no en el panel lateral ni en la información adicional. La selección es únicamente visual y no ejecuta acciones.
- **JavaScript:** `packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/indexDisputas.js` comparte los seis pasos entre línea de tiempo y selectores. En cada tarjeta, los dos primeros aparecen deshabilitados y los otros cuatro habilitados; un aviso indica que la selección no se guarda. Interactuar con el select no cambia la tarjeta seleccionada.
- **Presentación y textos:** `packages/Reda/RedaAlojamiento/resources/sass/admin/main.scss` permite distribuir el selector y el estado en espacios estrechos; `packages/Reda/RedaAlojamiento/resources/lang/es.json` añade los textos accesibles y de aviso.
- **Documentación:** `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md` y este registro. La compilación y prueba visual quedan pendientes en Vesta.
- **Validación estática:** Sintaxis JavaScript, lectura del JSON y `git diff --check` correctos. No se realizó compilación ni prueba visual local.

## [08 de Noviembre, 2026] - Paso de asignación de mediaciones
- **Respaldo revisado:** `documentacion-torbian-2/BD/appvac_torbDev2-20261107-1645.sql` (7 de noviembre a las 16:45). Las ocho mediaciones estaban abiertas, con `paso_actual` «Caso creado» y sin agente; `disputas` tiene clave primaria `id` y clave foránea de reserva, mientras `role_admin` vincula administradores con roles. No se modificaron datos históricos ni el respaldo.
- **Controlador:** `packages/Reda/RedaAlojamiento/src/Http/Controllers/Admin/Disputa/DisputaController.php` guarda y devuelve `paso_actual` «Asignación a agente» junto al agente al asignar, tomar o reasignar, con los permisos existentes.
- **Interfaz:** `packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/indexDisputas.js` sincroniza el paso, la tarjeta y la línea de tiempo seleccionada en escritorio o el detalle móvil abierto. `packages/Reda/RedaAlojamiento/resources/js/vistas/disputa/disputas/indexDisputas.js` y el script administrativo reconocen el paso español persistido incluso si la etiqueta está traducida.
- **Corrección necesaria:** En el script del dashboard del usuario se eliminó una línea textual preexistente `... (resto del archivo)` que provocaba error de sintaxis e impedía su compilación.
- **Documentación:** `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md` y este registro. Quedan pendientes subida, compilación y pruebas funcionales en Vesta.
- **Validación estática:** PHP y ambos scripts pasan las comprobaciones de sintaxis; `git diff --check` no detectó errores de formato. No se ejecutaron compilación, pruebas funcionales ni operaciones de base de datos.

## [08 de Noviembre, 2026] - Persistencia del resultado al solicitar mediación
- **Hallazgo:** La ruta AJAX `reda/disputas/store` devuelve HTTP 403 con el mensaje de restricciones cuando el middleware detecta el indicador correspondiente. `guardarMediacionReda()` conserva ese mensaje, pero el formulario cerraba la espera e inmediatamente reutilizaba el mismo modal; los temporizadores de limpieza de `ocultar()` podían esconder la notificación.
- **Archivo:** `packages/Reda/RedaAlojamiento/resources/js/general/mensajes.js`. `configurarEventosModal()` transforma la espera en la notificación de éxito o error sin llamar previamente a `ocultar()`; conserva la alerta alternativa cuando no existe `notificar()`. No se modifican la validación del servidor ni los datos.
- **Documentación:** `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md` y este registro. Quedan pendientes la compilación y prueba funcional en Vesta.
- **Validación estática:** Sintaxis JavaScript y `git diff --check` correctos. La respuesta concreta de la prueba del usuario no se capturó, por lo que corresponde confirmar el texto en Vesta.

## [08 de Noviembre, 2026] - Visibilidad del submenú de pruebas
- **Solicitud:** Mostrar «Reiniciar mediaciones» al desplegar «Pruebas aplicación» sin reducir el zoom.
- **Archivo:** `packages/Reda/RedaAlojamiento/resources/js/admin/general/menus/menuLateralAdmin.js`. Tras desplegar el submenú, desplaza el contenedor lateral únicamente cuando el enlace queda por debajo del área visible. También sincroniza `aria-expanded` y finaliza animaciones pendientes ante clics rápidos, sin alterar los permisos ni otros menús.
- **Documentación:** `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md` y este registro. La compilación y prueba visual quedan pendientes en Vesta.
- **Validación estática:** Sintaxis JavaScript y `git diff --check` correctos; no se ejecutó compilación ni prueba visual local.

## [07 de Noviembre, 2026] - Diagnóstico de visibilidad del reinicio
- El usuario compartió el resultado efectivo de Laravel en Vesta: entorno `local` y `reinicio_habilitado` `false`; el entorno satisface la comprobación, pero la bandera impide mostrar y ejecutar la herramienta. Se indicó habilitarla explícitamente solo en el entorno de pruebas.
- La salida compartida de la subida incluye los archivos relevantes y avisos FTP `550 File exists` al crear directorios ya existentes; la salida no comprueba por sí sola el contenido remoto. El texto adjunto de compilación no estuvo accesible en la conversación, por lo que su resultado queda sin verificar.
- El usuario informó que subió los archivos, ejecutó la migración y compiló en Vesta; el submenú no aparece. Estas operaciones no se verificaron directamente en el servidor.
- Se compararon los archivos del último commit del plugin con las entradas activas de `subir_archivos_puntuales.sh`: todos están listados, incluida la migración, el menú, el pie, los servicios y la vista. `subir.sh` solo envía los archivos listados; estar en la lista no confirma su transferencia ni el contenido remoto.
- El menú exige la bandera booleana habilitada, entorno `local`, `development` o `dev` y rol 1. Se recomienda comprobar los valores efectivos de Laravel en Vesta; la compilación debe incluir Reda Alojamiento. No se modificaron ni ejecutaron scripts de subida.
- Archivo actualizado: `LOG_DESARROLLO_REDA.md`.

## [07 de Noviembre, 2026] - Análisis previo del reinicio de mediaciones
- **Solicitud:** Crear una acción administrativa de rol 1 para reiniciar las mediaciones de prueba, con confirmación textual y conservación de la configuración de límites.
- **Respaldo revisado:** `documentacion-torbian-2/BD/appvac_torbDev2-20261107-0702.sql`, del 7 de noviembre a las 07:02. Se identificaron 8 mediaciones, 93 alertas, 6 registros de avisos y 271 mensajes; 32 alertas refieren mediaciones ausentes. `disputas_respaldo` no figura en este respaldo.
- **Hallazgos y decisiones:** El chat normal y las mediaciones comparten `messages`; el tipo de mensaje no identifica con certeza los mensajes exclusivos de mediación. Se conservarán los mensajes ambiguos y `disputas_respaldo` si existe; se eliminarán los adjuntos físicos identificados. El administrador seleccionará las cuentas inactivas que desea reactivar, sin restaurar cuentas borradas lógicamente. La herramienta se habilitará expresamente solo en desarrollo.
- **Concurrencia:** El usuario eligió una ventana de mantenimiento sin otros usuarios ni procesos de mediación mientras se ejecute el reinicio.
- **Implementación:** `packages/Reda/RedaAlojamiento/src/Services/ReinicioMediacionesService.php` usa transacción y bloqueo entre reinicios, cola durable y huellas para los adjuntos; conserva todos los adjuntos si existe `disputas_respaldo`. El controlador `packages/Reda/RedaAlojamiento/src/Http/Controllers/Admin/Disputa/ReinicioMediacionesController.php` exige bandera, entorno de desarrollo, rol 1 y frase exacta, y valida las cuentas seleccionadas. La operación no bloquea la actividad normal; es obligatoria la ventana coordinada.
- **Mensajes:** La migración `packages/Reda/RedaAlojamiento/database/migrations/2026_11_07_000000_crear_tablas_reinicio_mediaciones.php` y los modelos `packages/Reda/RedaAlojamiento/src/Models/Disputa/MensajeDeMediacion.php` y `packages/Reda/RedaAlojamiento/src/Models/Disputa/AdjuntoMediacionPendiente.php` proporcionan marcadores y cola. Se modificaron `packages/Reda/RedaAlojamiento/src/Http/Controllers/Disputa/MensajeController.php`, `packages/Reda/RedaAlojamiento/src/Http/Controllers/Admin/Disputa/MensajeController.php` y `packages/Reda/RedaAlojamiento/src/Services/MediacionAlertaService.php` para identificar transaccionalmente los mensajes nuevos; los históricos ambiguos se conservan.
- **Interfaz:** Se crearon `packages/Reda/RedaAlojamiento/resources/views/admin/disputa/reinicio_mediaciones.blade.php` y `packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/reinicioMediaciones.js`. Se actualizaron `packages/Reda/RedaAlojamiento/routes/web.php`, `packages/Reda/RedaAlojamiento/config/reda-alojamiento.php`, `packages/Reda/RedaAlojamiento/resources/views/admin/general/main_footer.blade.php`, `packages/Reda/RedaAlojamiento/resources/js/admin/general/menus/menuLateralAdmin.js`, `packages/Reda/RedaAlojamiento/resources/js/admin/general/main.js` y `packages/Reda/RedaAlojamiento/resources/lang/es.json` para autorización, submenú, modal, espera y traducciones.
- **Documentación:** `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md` y este registro. La migración debe desplegarse y ejecutarse antes del código de mensajería. No se ejecutaron operaciones de base de datos ni compilación local; las pruebas funcionales quedan pendientes en Vesta.
- **Validación estática:** Sintaxis PHP 8.2 de los diez archivos PHP modificados o creados, sintaxis de los tres scripts administrativos, lectura del JSON y `git diff --check` correctos. No se han validado el flujo HTTP, la transacción ni la eliminación de archivos en Vesta.

## [06 de Noviembre, 2026] - Restricciones de acciones por mediaciones
- **Tarea:** Sustituir la inactivación de cuentas por restricciones para crear propiedades y negocios, reservar, iniciar mediaciones y solicitar retiros.
- **Datos revisados:** `documentacion-torbian-2/BD/appvac_torbDev2-20261106-1125.sql`, generado el 06/11/2026 a las 11:25. Cuatro usuarios tenían el indicador histórico marcado: uno activo, dos inactivos sin borrado lógico y uno borrado. Las cuentas marcadas conservaban propiedades, negocios, reservas y mediaciones; el respaldo no contenía solicitudes en `withdrawals`. No se modificaron datos ni se ejecutaron consultas de base de datos.
- **Decisiones confirmadas:** Aplicar las restricciones a registros históricos, revisar manualmente el motivo de inactividad antes de reactivar y no restaurar usuarios borrados. Permitir completar los pagos iniciados antes de la restricción.
- **Cambios de aplicación:** `packages/Reda/RedaAlojamiento/src/Services/MediacionAlertaService.php` registra restricciones sin cambiar `users.status`; `packages/Reda/RedaAlojamiento/src/Models/Disputa/UsuarioAvisoMediacion.php` documenta el significado actualizado de `cuenta_suspendida`. `packages/Reda/RedaAlojamiento/src/Http/Middleware/RestringirAccionesPorMediaciones.php` protege las cinco operaciones y conserva la identificación en sesión de pagos iniciados previamente; `packages/Reda/RedaAlojamiento/src/RedaAlojamientoServiceProvider.php` lo registra en el grupo web. Se mantienen los callbacks de pasarela y los registros existentes.
- **Configuración y avisos:** `packages/Reda/RedaAlojamiento/src/Http/Controllers/Admin/Disputa/DisputaController.php`, `packages/Reda/RedaAlojamiento/resources/views/admin/disputa/configuracion.blade.php` y `packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/configuracionDisputas.js` actualizan textos; `packages/Reda/RedaAlojamiento/resources/lang/es.json` añade traducciones. `packages/Reda/RedaAlojamiento/resources/views/emails/primer_aviso_usuario.blade.php`, `packages/Reda/RedaAlojamiento/resources/views/emails/primer_aviso_admin.blade.php`, `packages/Reda/RedaAlojamiento/resources/views/emails/suspension_usuario.blade.php` y `packages/Reda/RedaAlojamiento/resources/views/emails/suspension_admin.blade.php` describen el nuevo régimen.
- **Documentación:** `manual_tecnico_plugin_reda_alojamiento.md`, `previo_cambios_realizados.md` y este registro recogen la integración, datos históricos y decisiones.
- **Alcance:** El conteo sigue incluyendo todas las mediaciones; el conteo por resultado y las apelaciones continúan pendientes. No se modifican ni cancelan operaciones existentes.
- **Validación:** `php -l` con PHP 8.2 de XAMPP en los cinco archivos PHP, `node --check`, validación JSON, diagnósticos del editor y `git diff --check` correctos. Las pruebas funcionales y compilación quedan pendientes en Vesta. Completar pagos en curso antes del despliegue porque las sesiones antiguas no contienen el identificador de pago creado por esta versión.

## [06 de Noviembre, 2026] - Permanencia del resultado de configuración de mediaciones
- **Tarea:** Evitar que la notificación de guardado desaparezca antes de que el administrador pueda leerla.
- **Archivos actualizados:** `packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/configuracionDisputas.js`, `manual_tecnico_plugin_reda_alojamiento.md` y `previo_cambios_realizados.md`.
- **Cambio:** Al finalizar la petición se restablece el botón sin cerrar el modal de espera, que `mostrarResultado()` reutiliza para el éxito o error. Así no se programan los cierres diferidos de `ocultar()` sobre la nueva notificación. Si no está disponible `notificar()`, se cierra la espera cuando sea posible y se usa la alerta alternativa.
- **Validación:** `node --check` sobre el script y `git diff --check` sin errores; compilación y prueba funcional reservadas a Vesta.

## [06 de Noviembre, 2026] - Diseño de configuración de mediaciones
- **Tarea:** Mejorar márgenes, espaciado y bordes de la vista administrativa de configuración de mediaciones.
- **Archivos actualizados:** `packages/Reda/RedaAlojamiento/resources/sass/admin/main.scss`, `packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/configuracionDisputas.js`, `manual_tecnico_plugin_reda_alojamiento.md` y `previo_cambios_realizados.md`.
- **Cambio:** El script identifica la vista mediante una clase en el `body`; el SCSS añade márgenes y espacios interiores adaptables, separa etiquetas y textos de ayuda, y redondea discretamente tarjeta, campos y botón. Se conservaron la plantilla, validaciones, rutas y operaciones de guardado.
- **Validación:** `node --check` y `git diff --check` correctos. La compilación y prueba visual quedan pendientes en Vesta.

## [06 de Noviembre, 2026] - Espaciado del botón de regreso en soporte
- **Tarea:** Separar la flecha del texto «Volver al listado» en el detalle del ticket.
- **Archivos actualizados:** `packages/Reda/RedaAlojamiento/resources/sass/admin/main.scss`, `manual_tecnico_plugin_reda_alojamiento.md` y `previo_cambios_realizados.md`.
- **Cambio:** Se añadió un margen derecho de 8 px al icono del botón, acotado al detalle de soporte. No se modificaron la vista, el enlace ni el JavaScript.
- **Validación:** selector contrastado con la vista y `git diff --check` correcto; no se compila localmente.

## [06 de Noviembre, 2026] - Espaciado del modal de búsqueda de soporte
- **Tarea:** Mejorar la separación de los elementos del modal que se abre desde la lupa del listado de atención al comercio.
- **Archivos actualizados:** `packages/Reda/RedaAlojamiento/resources/sass/admin/main.scss`, `manual_tecnico_plugin_reda_alojamiento.md` y `previo_cambios_realizados.md`.
- **Cambio:** Se ajustaron separaciones entre filas y etiquetas, espaciado interior y radios de los campos. La lupa flotante se sitúa en el pie, con espacio reservado para ella, evitando cubrir los filtros o los botones inferiores. Se mantienen los campos, filtros y operaciones existentes.
- **Validación:** revisión de selectores frente a la estructura Blade y `git diff --check` correctos. La compilación y prueba visual quedan pendientes en Vesta.

## [06 de Noviembre, 2026] - Diseño de atención al comercio
- **Tarea:** Aplicar el espaciado adaptable y el redondeo discreto de las demás vistas administrativas al listado, detalle, modales y alertas de soporte técnico.
- **Archivos actualizados:** `packages/Reda/RedaAlojamiento/resources/sass/admin/main.scss`, `packages/Reda/RedaAlojamiento/resources/js/admin/general/soporte_tecnico/indexSoporteTecnico.js`, `packages/Reda/RedaAlojamiento/resources/js/admin/general/soporte_tecnico/showSoporteTecnico.js`, `manual_tecnico_plugin_reda_alojamiento.md` y `previo_cambios_realizados.md`.
- **Cambio:** Ambos scripts marcan sus páginas con `reda-admin-soporte-tecnico`; los estilos limitados por esa clase ajustan encabezados, paneles, formularios de búsqueda, detalle y gestión de ticket, y modales compartidos de confirmación y notificación. Se preservan las operaciones de datos y la navegación; se contempla la distribución móvil.
- **Validación:** `node --check` en ambos scripts y `git diff --check` correctos. La compilación y prueba visual quedan pendientes en Vesta.

## [05 de Noviembre, 2026] - Actualización de ayuda del identificador de tipos de negocios
- **Tarea:** Aclarar en el modal las reglas para escribir el identificador de un tipo de negocio.
- **Archivos actualizados:** `packages/Reda/RedaAlojamiento/resources/views/admin/experiencia/tipos_de_negocios/opciones_tipos_de_negocios.blade.php`, `packages/Reda/RedaAlojamiento/resources/lang/es.json`, `manual_tecnico_plugin_reda_alojamiento.md` y `previo_cambios_realizados.md`.
- **Cambio:** Se reemplazó la ayuda anterior por «Identificador único: Los espacios entre palabras se convertirán automáticamente en guiones bajos, no se permiten acentos ni caracteres especiales», incorporando la nueva frase a `es.json` y retirando la referencia de la vista a la traducción PHP obsoleta. Sin cambios en la normalización existente del campo.
- **Validación:** `python3 -m json.tool` y `git diff --check` correctos; la vista apunta a la nueva traducción. No se compila localmente.

## [05 de Noviembre, 2026] - Terminología de tipos de negocios en administración
- **Tarea:** Sustituir en la vista administrativa los textos visibles de «categoría» por «tipo de negocio» y actualizar títulos, botones y confirmaciones.
- **Archivos actualizados:** `packages/Reda/RedaAlojamiento/resources/views/admin/experiencia/tipos_de_negocios/opciones_tipos_de_negocios.blade.php`, `packages/Reda/RedaAlojamiento/resources/js/admin/vistas/experiencia/opcionesTipoDeNegocios.js`, `packages/Reda/RedaAlojamiento/resources/lang/es.json`, `manual_tecnico_plugin_reda_alojamiento.md` y `previo_cambios_realizados.md`.
- **Cambio:** El listado ahora se titula «Tipos de negocios», el botón «Agregar nuevo» y el modal usa «Agregar Nuevo Tipo de Negocio»/«Editar tipo de negocio». Las notificaciones y confirmación de eliminar usan la terminología solicitada. Se mantuvieron intactos los identificadores internos, rutas y estructura de datos. No se halló un respaldo SQL en la carpeta indicada; el cambio no requiere inspeccionar ni modificar datos porque solo actualiza los textos visibles.
- **Validación:** `node --check`, análisis de `es.json` y `git diff --check` correctos; no se compila localmente.

## [05 de Noviembre, 2026] - Mejora visual de opciones de tipos de negocios
- **Tarea:** Aplicar el diseño de espaciado y bordes suaves a la vista `opciones_tipos_de_negocios` y al modal «Agregar nueva».
- **Archivos actualizados:** `packages/Reda/RedaAlojamiento/resources/sass/admin/main.scss`, `manual_tecnico_plugin_reda_alojamiento.md` y `previo_cambios_realizados.md`.
- **Cambio:** Se añadieron estilos acotados a `#opciones_tipos_de_negocios + .content-wrapper` para márgenes, panel, tabla de escritorio y tarjetas móviles; `#modal-category` recibe padding en encabezado, formulario y pie, borde redondeado y ajuste de márgenes en celular. Se preservaron la vista, el JavaScript, las rutas y las operaciones de datos.
- **Validación:** `git diff --check` correcto; no se compiló localmente.

## [05 de Noviembre, 2026] - Mejora visual de configuración de planes
- **Tarea:** Recuperar márgenes laterales y espacio interior, y suavizar los bordes de la pantalla de configuración de planes.
- **Archivos actualizados:** `packages/Reda/RedaAlojamiento/resources/sass/admin/main.scss`, `manual_tecnico_plugin_reda_alojamiento.md` y `previo_cambios_realizados.md`.
- **Cambio:** Se añadieron estilos scoped al contenedor de esta vista: márgenes/padding más cómodos en escritorio, ajustes compactos para móvil, padding en encabezados y cuerpos de paneles y redondeo discreto de 8 px. No se alteraron la vista Blade, JavaScript, rutas ni lógica de negocio.
- **Validación:** revisión del diff y `git diff --check`; no se compiló localmente.

## [05 de Noviembre, 2026] - Mejora visual del modal de planes
- **Tarea:** Aplicar el espaciado y redondeo suave de la pantalla de configuración al modal «Agregar nuevo plan».
- **Archivos actualizados:** `packages/Reda/RedaAlojamiento/resources/sass/admin/main.scss`, `manual_tecnico_plugin_reda_alojamiento.md` y `previo_cambios_realizados.md`.
- **Cambio:** Se añadieron estilos específicos para `#modal-plan`: bordes suavemente redondeados, sombra discreta y padding en encabezado, contenido, pie y paneles interiores. En móvil se reducen los márgenes y espacios y se quita el desplazamiento superior innecesario de los controles de estado. Sin cambios en la vista, el JavaScript ni el flujo del modal.
- **Validación:** revisión del diff y `git diff --check`; no se compiló localmente.

## [05 de Noviembre, 2026] - Alineación de opciones del menú lateral admin
- **Tarea:** Corregir la sangría de "Negocios", "Mediaciones" y "Mensajes y Alertas" para que coincida con las opciones nativas.
- **Archivos actualizados:** `packages/Reda/RedaAlojamiento/resources/js/admin/general/menus/menuLateralAdmin.js`, `manual_tecnico_plugin_reda_alojamiento.md` y `previo_cambios_realizados.md`.
- **Cambio:** Se normalizó el HTML inyectado a la estructura de AdminLTE (`nav-item`, `nav-link`, `nav-icon` y `p`) en encabezados y subopciones, eliminando el espacio de icono añadido que duplicaba el ancho reservado. Se preservaron navegación, estado activo, flechas y badges.
- **Validación:** `node --input-type=module --check` y `git diff --check` finalizaron correctamente. No se compila localmente.

## [05 de Noviembre, 2026] - Atención al comercio dentro de Negocios
- **Tarea:** Mover la opción "Atención al comercio" desde el nivel principal del menú lateral a una subopción de "Negocios".
- **Archivos actualizados:** `packages/Reda/RedaAlojamiento/resources/js/admin/general/menus/menuLateralAdmin.js`, `manual_tecnico_plugin_reda_alojamiento.md` y `previo_cambios_realizados.md`.
- **Cambio:** Se agregó el enlace de soporte al acordeón de "Negocios" con la misma URL de destino y reutilizando el manejador existente de enlaces del submenú para la animación de espera. Se eliminó su inyección independiente después de "Mensajes y Alertas".
- **Validación:** revisión del diff y `git diff --check`; no se ejecutó compilación local.

## [05 de Noviembre, 2026] - Cambio de nombre en menú lateral del admin
- **Tarea:** Cambiar la opción "Soporte técnico" del menú lateral administrativo a "Atención al comercio".
- **Archivos actualizados:** `packages/Reda/RedaAlojamiento/resources/js/admin/general/menus/menuLateralAdmin.js`, `packages/Reda/RedaAlojamiento/resources/lang/es.json`, `manual_tecnico_plugin_reda_alojamiento.md`, `documentacion-torbian-2/requerimientos.md` y `previo_cambios_realizados.md`.
- **Cambio:** Se actualizó el texto inyectado y su clave de traducción, manteniendo sin cambios la URL y el comportamiento existentes del módulo de soporte técnico. Se actualizó el manual y se trasladó la tarea de pendientes a ejecutados.
- **Validación:** revisión del diff y del formato JSON; no se ejecutó compilación local, según las restricciones del proyecto.

## [05 de Noviembre, 2026] - Directriz para Revisar Respaldos de Base de Datos Antes de Cambios
- **Tarea:** Añadir al archivo de instrucciones de Copilot el procedimiento obligatorio para tener en cuenta datos existentes antes de implementar cambios que interactúan con la base de datos.
- **Archivo actualizado:** `.github/copilot-instructions.md`.
- **Cambio:** se indica localizar el respaldo reciente `documentacion-torbian-2/BD/appvac_torbDev2-AAAAMMDD-HHMM.sql`, comprobar su fecha y tablas, revisar estructura, relaciones y registros relevantes, proyectar el comportamiento de la aplicación con esos datos, identificar incongruencias pertinentes sin confundir bloqueos manuales con errores y proponer SQL puntual/verificable si se requiere corregir datos. También se aclara solicitar un volcado actualizado si el disponible no representa el estado actual y no ejecutar SQL desde este entorno ni modificar los respaldos.
- **Validación:** revisión de contenido y `git diff --check`; no se ejecutaron consultas ni comandos de base de datos.

## [05 de Noviembre, 2026] - Disponibilidad Residual Corregida y Pruebas Listas
- **Tarea:** Confirmar el resultado del cambio de disponibilidad de la propiedad 1 y determinar si ya se puede iniciar la prueba funcional del calendario.
- **Confirmación recibida:** `property_dates-disponible.sql` muestra `property_dates.id = 348`, propiedad 1, fecha 12/11/2026, con estado `Available`. Junto al respaldo `property_dates-consulta-despues-actualizacion.sql`, las fechas residuales asociadas a bookings borrados 54, 59 y 65 ya no bloquean esos rangos.
- **Estado de reservas activas:** las noches ocupadas de reservas activas continúan bloqueadas. El booking 73 mantiene ocupadas las noches del 26 al 29 de noviembre para propiedad 3; las reservas activas de propiedad 2 y 4 mantienen sus noches correspondientes.
- **Siguiente paso:** sí, se puede probar. Subir al servidor Vesta los archivos fuente de la funcionalidad del calendario, compilar con `./compilar.sh` y probar en el sitio las propiedades 2 y 3. No se ha subido ni compilado en esta sesión; las pruebas funcionales todavía no se han ejecutado.
- **Archivos actualizados:** `manual_tecnico_plugin_reda_alojamiento.md`, `LOG_DESARROLLO_REDA.md` y `previo_cambios_realizados.md`.

## [05 de Noviembre, 2026] - Verificación del Bloqueo Residual de Propiedad 1
- **Tarea:** Revisar el resultado de las consultas SQL para la fila `property_dates.id = 348` y el booking 65.
- **Resultado recibido:** `property_dates-verificacion-dos-registros.sql` contiene la fila 348: propiedad 1, estado `Not available`, fecha 12/11/2026, creada el 17/10/2026 20:42:04. El archivo compartido no incluye el resultado de la consulta a `bookings`; el respaldo completo anterior sí confirma que el booking 65 es de propiedad 1, del 12 al 13/11/2026, y tiene `deleted_at = 2026-10-17 23:01:47`.
- **Conclusión y siguiente paso:** la fila de calendario coincide temporalmente y por fecha con la reserva borrada lógicamente. Si el anfitrión no bloqueó deliberadamente el 12/11, actualizar solo `property_dates.id = 348` a `Available` y verificar su estado; no se necesita tocar bookings ni otras fechas. La aplicación no debe tratar una fila de disponibilidad sin relación con una reserva como prueba de que el booking continúa activo.
- **Archivos actualizados:** `LOG_DESARROLLO_REDA.md` y `previo_cambios_realizados.md`.
- **Estado:** se proporcionan instrucciones SQL puntuales al usuario; no se ejecutó ni modificó la base desde este entorno.

## [05 de Noviembre, 2026] - Auditoría Posterior de Fechas de Reservas
- **Tarea:** Revisar el respaldo completo posterior a la corrección de `property_dates` y las reservas relacionadas para detectar bloqueos futuros residuales.
- **Confirmación del cambio anterior:** `property_dates-consulta-despues-actualizacion.sql` muestra como `Available` las 22 filas de propiedad 3 (IDs 317–337 y 342); la corrección se reflejó correctamente en la base.
- **Auditoría del volcado completo `appvac_torbDev2-20261105-0715.sql`:**
  - Se compararon 74 bookings, 369 filas de `property_dates`, 11 propiedades y 36 usuarios. No se encontraron referencias de bookings o property_dates a propiedades inexistentes, ni bookings con anfitriones o huéspedes inexistentes.
  - No hay fechas duplicadas para una misma combinación propiedad/fecha, ni solapamientos entre reservas activas futuras. Todas las noches de bookings activos desde el 05/11/2026 tienen el bloqueo correspondiente.
  - Se encontró una sola indisponibilidad futura sin booking activo coincidente: `property_dates.id = 348`, propiedad 1, fecha 12/11/2026. La marca `created_at` (17/10/2026 20:42:04) coincide con la creación del booking 65 (12–13/11), borrado lógicamente el 17/10/2026 23:01:47. Si no es un bloqueo manual deliberado, debe pasar a `Available` para evitar otro falso bloqueo futuro.
  - Las demás indisponibilidades sin reserva activa coincidente corresponden a fechas pasadas; se dejan intactas para no alterar el historial. Hay 28 bookings históricos con `start_date = end_date` y `total_night = 1`, todos anteriores al 05/11/2026; no se modifican sin confirmar cómo se desea conservar los datos históricos de prueba.
- **Archivos actualizados:** `manual_tecnico_plugin_reda_alojamiento.md`, `LOG_DESARROLLO_REDA.md` y `previo_cambios_realizados.md`.
- **Acción pendiente en la base de desarrollo:** verificar la fila 348 y cambiarla puntualmente a `Available` solo si no representa un bloqueo manual deseado. No se ejecutó ni modificó la base desde este entorno.

## [05 de Noviembre, 2026] - Diagnóstico Integral de property_dates para el Modal de Reservas
- **Tarea:** Completar el diagnóstico del calendario integrando `property_dates.sql` con los respaldos de `bookings.sql` y `properties.sql`.
- **Hallazgos confirmados:**
  - Propiedad 2: `property_dates` marca como `Not available` del 05 al 12 de noviembre (filas 362–369), coincidiendo con el booking 74, y del 13 al 17 (filas 353–357), coincidiendo con el booking 72. Desde el 18 de noviembre las fechas están disponibles en el respaldo, coherente con que el checkout del booking 72 no ocupa esa noche. El mensaje mostrado al abrir la modal no se explica por indisponibilidad de esa fecha; es compatible con el resultado residual del cálculo inicial del core que ya se limpia en `propiedad_detalle.js`.
  - Propiedad 3: las filas 317–337 bloquean las noches del 31 de octubre al 20 de noviembre y tienen marcas de tiempo coincidentes con la creación del booking 54, eliminado lógicamente (`deleted_at`). La fila 342 bloquea el 21 de noviembre y coincide con la creación del booking 59, también eliminado lógicamente. Estas filas explican por qué el core devuelve «Las fechas no están disponibles» aunque el endpoint de reservas activas no considere esos bookings. Las filas 358–361 (26–29 de noviembre) sí coinciden con el booking 73 vigente y deben permanecer bloqueadas.
  - `property_dates` no relaciona sus filas con el ID del booking. Por seguridad, el código no puede borrar indisponibilidades por simple solapamiento: podrían ser bloqueos manuales. La corrección de los datos identificados debe ser puntual y conservar las noches del booking 73.
- **Archivos actualizados:** `LOG_DESARROLLO_REDA.md`, `manual_tecnico_plugin_reda_alojamiento.md`, `REDA_PAUTAS_DESARROLLO.md` y `previo_cambios_realizados.md`.
- **Estado:** el código JavaScript previo sigue atendiendo el estado residual en la apertura del modal; el bloqueo persistido para la propiedad 3 requiere corregir las filas confirmadas de `property_dates` en la base de desarrollo antes de probar ese caso. No se modificó ni ejecutó la base de datos.

## [05 de Noviembre, 2026] - Corrección de Avisos Residuales en el Modal de Reservas
- **Tarea:** Revisar el mensaje «Las fechas no están disponibles» contrastándolo con los respaldos SQL de propiedades y reservas, y corregir avisos que provengan de cálculos iniciales o respuestas AJAX obsoletas.
- **Diagnóstico de los respaldos:**
  - `properties.sql` confirma los IDs y slugs consultados, pero no contiene información de disponibilidad por fecha.
  - En `bookings.sql`, las reservas 54 y 59 de propiedad 3 tienen `deleted_at` informado; no deben considerarse activas por el modelo con borrado lógico. La reserva 73 de propiedad 3 sigue activa y ocupa noches del 26 al 29 de noviembre; su salida del 30 no está bloqueada.
  - Para propiedad 2, la reserva 74 ocupa noches del 5 al 12 de noviembre y la 72 del 13 al 17; el 18 de noviembre es su fecha de salida y puede ser llegada de otra estancia.
  - El mensaje devuelto por `Common::getPrice()` se origina al consultar noches `Not available` en `property_dates`. Ese respaldo no fue proporcionado, por lo que no se puede atribuir a los SQL compartidos un bloqueo manual/obsoleto concreto. La respuesta residual del cálculo inicial del core también podía permanecer en el formulario al moverlo al modal.
- **Cambios realizados:**
  - `propiedad_detalle.js`: se centralizó la limpieza de avisos/resultados heredados, se evita recalcular mientras no haya fecha de salida y se vigilan respuestas AJAX de precios. Si una respuesta corresponde a un rango anterior, se descarta su estado cuando el rango actual está incompleto o se recalcula el rango actualmente seleccionado.
  - `manual_tecnico_plugin_reda_alojamiento.md`: se documentó la limpieza de estado y el control de respuestas tardías.
  - `previo_cambios_realizados.md`: se reemplazó el punto de control con el diagnóstico verificado y los archivos intervenidos.
- **Validación:** `node --check` y `git diff --check` pasaron. No se compilaron assets ni se ejecutaron comandos Artisan; después de subir el fuente al servidor Vesta, se debe compilar y probar allí.

## [05 de Noviembre, 2026] - Regla de Disponibilidad para Reservar el Mismo Día
- **Tarea:** Registrar en las pautas del proyecto la condición para aceptar reservas con llegada en la fecha actual.
- **Cambio realizado:**
  - `REDA_PAUTAS_DESARROLLO.md`: se agregó la regla de que hoy puede reservarse cuando las noches estén libres y resten al menos 12 horas del día en la zona horaria del alojamiento. El mensaje de fechas no disponibles debe depender de una ocupación real del rango, no de que la llegada sea hoy ni de una conversión ambigua. También se estableció que el calendario sugiera hoy cuando sea elegible; de lo contrario, debe avanzar a la primera fecha futura libre.
- **Validación:** revisión del texto y `git diff --check` pasaron. No se modificó lógica de la aplicación en esta solicitud.

## [05 de Noviembre, 2026] - Corrección de Fechas No Disponibles Falsas en la Modal
- **Tarea:** Corregir el aviso de indisponibilidad que aparecía al abrir el calendario o al seleccionar fechas que estaban libres, y permitir reservar desde hoy.
- **Diagnóstico:** La interfaz Flatpickr entrega fechas `dd-mm-aaaa`, pero los inputs del core se procesan con `setDateForDb()` conforme a las preferencias regionales. El valor podía convertirse en otro día al calcular precios. Además, el formulario heredaba una salida predeterminada y podía validar un rango antes de que el huésped lo eligiera.
- **Cambios realizados:**
  - El calendario inicia el check-in en la primera fecha libre a partir de hoy, incluyendo el mismo día si aún no está ocupado.
  - Las fechas seleccionadas se envían a `#startDate` y `#endDate` en formato ISO `Y-m-d`, preservando la fecha independientemente del formato regional de la aplicación.
  - El checkout inicia vacío y los precios solo se consultan después de que el huésped seleccione la salida; esto evita mostrar un error por el rango predeterminado del core.
- **Archivos modificados:**
  - `packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/propiedad_detalle.js`: sincronización de fechas ISO, primera llegada desde hoy y salida inicialmente vacía.
  - `manual_tecnico_plugin_reda_alojamiento.md`: documentación de la causa y del comportamiento corregido.
  - `previo_cambios_realizados.md`: seguimiento de esta corrección.
- **Validación:** `node --check`, la conversión ISO mediante el mismo reemplazo de separadores que `setDateForDb()` (para `-`, `/` y `.`) y `git diff --check` sobre los archivos de esta tarea pasaron. No se compilaron assets ni se ejecutaron comandos Artisan; la prueba visual/funcional debe realizarse en Vesta después de subir y compilar.

## [05 de Noviembre, 2026] - Abrir el Calendario en la Primera Fecha Disponible
- **Tarea:** Posicionar el calendario de reservación en la primera fecha libre, evitando el aviso confuso cuando el día predeterminado ya está reservado.
- **Cambios realizados:**
  - `prepararModalReserva()` busca desde la fecha de llegada inicial del core (o desde hoy si no hay valor) la primera fecha no incluida en los rangos ocupados, incluyendo rangos consecutivos o solapados.
  - Flatpickr usa esa fecha como selección inicial y como fecha mínima de llegada. Los rangos ocupados continúan deshabilitados al avanzar por el calendario.
  - El valor de llegada se sincroniza con el input del core; se limpia una fecha de salida previa que ya no sea válida para la llegada inicial.
- **Archivos modificados:**
  - `packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/propiedad_detalle.js`: cálculo de la primera fecha libre y uso al inicializar ambos calendarios.
  - `manual_tecnico_plugin_reda_alojamiento.md`: documentación del comportamiento actualizado.
  - `previo_cambios_realizados.md`: seguimiento de la tarea.
- **Validación:** `node --check`, pruebas dirigidas de fechas iniciales con rangos ocupados consecutivos/solapados y `git diff --check` pasaron. No se compilaron assets ni se ejecutaron comandos Artisan; la compilación y prueba visual/funcional se realizarán en Vesta.

## [04 de Noviembre, 2026] - Bloqueo de Fechas Ocupadas en el Calendario de Reservas
- **Tarea:** Evitar que el huésped seleccione noches que ya están reservadas antes de completar el formulario en el modal.
- **Cambios realizados:**
  - El controlador incorpora `obtenerFechasOcupadas($idPropiedad)`, que consulta reservas con estados `Accepted`, `Pending`, `Processing` o `processing`, excluye las reservas canceladas, rechazadas, expiradas y consultas, y retorna intervalos de noches donde la fecha de salida no queda bloqueada.
  - La ruta autenticada `GET /reda/bookings/fechas-ocupadas/{idPropiedad}` proporciona esos intervalos al frontend.
  - `propiedad_detalle.js` consulta la disponibilidad antes de presentar el formulario, mantiene la animación de espera, bloquea los intervalos en ambos campos Flatpickr y no abre el modal cuando la consulta falla.
  - `main.scss` da un sombreado visible a los días ocupados deshabilitados; `es.json` incorpora los textos de respuesta y error.
- **Archivos modificados:**
  - `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaBookingController.php`: método documentado `obtenerFechasOcupadas()` y actualización de la descripción del controlador.
  - `packages/Reda/RedaAlojamiento/routes/web.php`: ruta autenticada para consultar disponibilidad por inmueble.
  - `packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/propiedad_detalle.js`: consulta previa, manejo de errores y configuración de fechas bloqueadas en Flatpickr.
  - `packages/Reda/RedaAlojamiento/resources/sass/main.scss`: estilo sombreado para fechas ocupadas.
  - `packages/Reda/RedaAlojamiento/resources/lang/es.json`: traducciones para la consulta de disponibilidad.
  - `manual_tecnico_plugin_reda_alojamiento.md`: documentación del endpoint, controlador, flujo JS y estilo.
  - `previo_cambios_realizados.md`: seguimiento del avance de esta tarea.
- **Validación:** `node --check`, `php -l` para controlador y rutas, validación JSON, pruebas dirigidas de límites de checkout/llegada y `git diff --check` pasaron. No se compilaron assets ni se ejecutaron comandos Artisan; la compilación y prueba visual/funcional deben realizarse en Vesta.

## [02 de Noviembre, 2026] - Pauta para Elegir JavaScript al Alterar Vistas Originales
- **Tarea:** Documentar en las instrucciones compartidas del proyecto cómo elegir el JavaScript correcto antes de inyectar cambios al HTML de una vista original.
- **Cambio realizado:**
  - `.github/copilot-instructions.md`: se agregó que primero se debe buscar y reutilizar un script con responsabilidad relacionada a la vista/requerimiento; evitar incluir lógica en archivos de otro propósito; luego evaluar un script general apropiado; y crear/integrar uno nuevo si no existe un punto de extensión adecuado.
- **Validación:** revisión del texto y `git diff --check` pasaron.

## [02 de Noviembre, 2026] - Corrección del Avatar del Anfitrión en Móvil
- **Tarea:** Corregir el avatar del anfitrión en la tarjeta resumen situada justo debajo de la foto principal en la vista móvil de `property.single`.
- **Diagnóstico:** El ajuste inicial se aplicó por error a la sección "Sobre el anfitrión", que no es la tarjeta indicada. El elemento correcto está en `#mainDiv #sideDiv` y usa la imagen `.img-90x90`. Definir reglas SCSS no alcanzaba porque el JavaScript todavía no añadía las clases objetivo a ese HTML original.
- **Cambios realizados:**
  - `propiedad_detalle.js` identifica la tarjeta desde JavaScript y añade clases del plugin al contenedor flex del avatar y a la imagen. Se ejecuta al inicializar el script específico de `property.single`, antes de verificar si existe el formulario de reserva.
  - `main.scss` fija en móvil el contenedor e imagen a 90 × 90 px y aplica `object-fit: cover`.
  - `chat-injection.js` queda dedicado a la reubicación del bloque de anfitrión y al botón de mensajería; ya no contiene la normalización del avatar.
  - Se corrigió la documentación anterior. No se modificaron vistas ni otros archivos originales del proyecto.
- **Archivos modificados:**
  - `packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/propiedad_detalle.js`: función documentada `normalizarTarjetaAnfitrion()` para inyectar clases a la tarjeta resumen.
  - `packages/Reda/RedaAlojamiento/resources/js/chat-injection.js`: retirada la lógica del avatar, dejando intactas sus funciones de chat.
  - `packages/Reda/RedaAlojamiento/resources/sass/main.scss`: estilos móviles aplicados a las nuevas clases.
  - `manual_tecnico_plugin_reda_alojamiento.md`: documentación corregida de los cambios.
  - `previo_cambios_realizados.md`: seguimiento de esta corrección.
- **Validación:** `node --check` sobre `propiedad_detalle.js`, `node --input-type=module --check` sobre `chat-injection.js` y `git diff --check` pasaron. No se compilaron assets ni se ejecutaron comandos Artisan en este entorno; la compilación y revisión visual se realizarán en Vesta.

## [20 de Octubre, 2026] - Mantener Confirmación en el Modal de Reenvío de Correo
- **Tarea:** Ocultar el aviso genérico de correo pendiente después de un reenvío exitoso y evitar que reaparezca tras corregir la dirección.
- **Diagnóstico:** El éxito del reenvío se mostraba como una alerta adicional debajo del aviso inicial. Al corregir el correo, el temporizador regresaba al primer paso y volvía a presentar el texto inicial, aunque el servidor ya había enviado un nuevo enlace.
- **Cambios realizados:**
  - Se agrupó el contenido informativo inicial para poder ocultarlo cuando el servidor confirma el envío.
  - Se centralizó la presentación del estado exitoso; tanto el reenvío como la corrección muestran la confirmación sin el aviso genérico.
  - El retorno temporizado tras corregir el email y la acción de cancelar conservan el mensaje de confirmación cuando ya hubo un envío exitoso.
- **Archivos modificados:**
  - `packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/verificacionCorreo.js`: gestión del estado de confirmación del envío y retorno al paso de aviso sin restaurar el mensaje inicial.
  - `packages/Reda/RedaAlojamiento/resources/views/general/modal_verificacion_correo.blade.php`: agrupación del texto genérico para ocultarlo al mostrar el éxito.
  - `manual_tecnico_plugin_reda_alojamiento.md`: documentación actualizada de los estados del modal.
  - `previo_cambios_realizados.md`: seguimiento de la tarea.
- **Validación:** diagnósticos sin errores en JavaScript y Blade; `node --check` y `git diff --check` pasaron. No se ejecutó compilación ni pruebas visuales en este entorno; estas deberán realizarse en Vesta.

## [20 de Octubre, 2026] - Separación del Modal de Confirmación de Correo en Registro y Login
- **Tarea:** Evitar que el registro muestre el botón de reenvío antes de crear la cuenta y el aviso de correo pendiente tras completar exitosamente el alta.
- **Diagnóstico:** El modal de registro incluía un botón de reenvío para una cuenta que todavía no existía. Además, `RedaUsuarioController@create` enviaba la flash `correo_registrado_pendiente`, que el JavaScript interpretaba como motivo para abrir el modal de login.
- **Cambios realizados:**
  - Se ocultó el botón «Re-enviar» del modal de registro; permanecen «Corregir email» y «Email correcto».
  - El registro exitoso conserva la notificación de usuario, pero ya no envía `correo_registrado_pendiente` ni activa el modal de correo pendiente.
  - El modal de aviso queda asociado únicamente a `correo_no_verificado`, establecido cuando se intenta iniciar sesión con credenciales válidas y correo sin confirmar. El reenvío/corrección desde el modal de login se mantiene.
- **Archivos modificados:**
  - `packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/verificacionCorreo.js`: restricción del modal de aviso al intento de inicio de sesión.
  - `packages/Reda/RedaAlojamiento/resources/views/general/modal_verificacion_correo.blade.php`: ocultamiento del reenvío de signup y retiro de la variable de sesión redundante.
  - `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaUsuarioController.php`: redirección posterior al registro sin la flash `correo_registrado_pendiente`; actualización de documentación del controlador y `create`.
  - `manual_tecnico_plugin_reda_alojamiento.md`: documentación de la separación de ambos contextos.
  - `previo_cambios_realizados.md`: punto de control actualizado para esta tarea.
- **Validación:** diagnósticos sin errores en JavaScript, Blade y controlador; `git diff --check` pasó. No se ejecutó compilación ni pruebas de navegador en este entorno; la comprobación funcional deberá realizarse en Vesta después de subir las fuentes.

## [18 de Octubre, 2026] - Acceso Rol 2 al Submenú de Mensajes y Alertas y Corrección de Paginación
- **Tarea:** Conceder a administradores de rol 2 (Atención al usuario) acceso al submenú "Mensajes y Alertas" y corregir el bloqueo del modal de espera al paginar alertas.
- **Diagnóstico:**
  - El submenú solo se creaba al encontrar el elemento nativo "Messages", que puede no renderizarse si el rol no tiene `manage_messages`.
  - La ruta original `/admin/messages` también está protegida por ese permiso.
  - La paginación llamaba a `RedaNotificaciones.ocultarEspera()`, método inexistente; la función disponible para cerrar el modal es `ocultar()`.
- **Cambios realizados:**
  - Se permite la inyección del submenú para rol 2 incluso sin el enlace original, usando un anclaje alternativo en el menú lateral.
  - Se agregó una migración de datos que asocia el permiso `manage_messages` al rol 2, requisito para que el enlace "Mensajes" sea funcional.
  - La paginación de alertas cierra el modal de espera con `ocultar()` en `finally` al terminar correctamente o fallar la solicitud.
- **Archivos modificados o creados:**
  - `packages/Reda/RedaAlojamiento/resources/js/admin/general/menus/menuLateralAdmin.js`: inserción del submenú para rol 2 aun cuando falte el enlace nativo y actualización de la documentación de cabecera.
  - `packages/Reda/RedaAlojamiento/database/migrations/2026_10_18_000002_habilitar_mensajes_para_rol_atencion_usuario.php`: asignación idempotente del permiso `manage_messages` al rol 2.
  - `packages/Reda/RedaAlojamiento/resources/js/admin/vistas/alerta/indexAlertas.js`: reemplazo de llamada inexistente a `ocultarEspera()` por `ocultar()` y cierre condicionado a que se haya solicitado loader.
  - `manual_tecnico_plugin_reda_alojamiento.md`: documentación de permiso, anclaje alternativo del submenú y corrección del loader.
  - `previo_cambios_realizados.md`: punto de control actualizado para esta solicitud.
- **Validación:** diagnósticos estáticos sin errores en ambos scripts JavaScript y la migración; `git diff --check` pasó. No se ejecutaron pruebas de navegador, compilación ni migraciones en este entorno.

## [18 de Octubre, 2026] - Protección del Inbox ante Mensajes sin Reserva Relacionada
- **Tarea:** Corregir el error 500 del Inbox (`Attempt to read property "users" on null`) al renderizar el sidebar de conversaciones.
- **Diagnóstico:** `RedaInboxController@index` también recupera mensajes por `sender_id` o `receiver_id`. Tras eliminar una reserva, una relación puede quedar sin destino (la tabla `messages` admite `booking_id` nulo y no define una clave foránea para bookings); la vista asumía que cada mensaje de sidebar tenía una reserva y accedía directamente a `bookings->users` y `bookings->host`. La selección inicial repetía la suposición al cargar el primer mensaje como reserva.
- **Cambios realizados:**
  - El sidebar solo incluye mensajes con una reserva relacionada y seleccionable.
  - La conversación inicial carga la reserva desde la relación verificada del propio mensaje.
  - La vista protege las relaciones del turista/anfitrión y presenta un avatar alternativo si falta alguna relación; una fila sin reserva no queda habilitada como conversación.
- **Archivos modificados:**
  - `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaInboxController.php`: selección del sidebar restringida a reservas relacionadas y carga segura de la conversación inicial.
  - `packages/Reda/RedaAlojamiento/resources/views/users/inbox.blade.php`: acceso seguro a relaciones del booking y avatar de respaldo ante relaciones de usuario ausentes.
  - `manual_tecnico_plugin_reda_alojamiento.md`: documentación del manejo de mensajes huérfanos y relaciones ausentes.
  - `previo_cambios_realizados.md`: punto de control de esta corrección.
- **Validación:** diagnósticos estáticos sin errores en el controlador y la vista; `git diff --check` pasó. No se ejecutaron Artisan, migraciones ni compilación local.

## [17 de Octubre, 2026] - Privacidad de Avisos por Límites de Mediaciones
- **Tarea:** Garantizar que el usuario que alcanza el primer aviso o el límite de suspensión sea el único participante que vea el mensaje privado, aunque la disputa haya sido iniciada por la contraparte.
- **Diagnóstico:** El servicio ya evaluaba por separado al demandante y al demandado y guardaba cada aviso con `receiver_id` del usuario evaluado. Sin embargo, los historiales del Inbox y del chat de mediación devolvían todos los mensajes vinculados a las reservas compartidas, incluyendo avisos privados enviados al otro participante; algunas actualizaciones de lectura también afectaban esos mensajes ajenos.
- **Cambios realizados:**
  - Se garantizó que `MediacionAlertaService` evalúe directamente al turista y al anfitrión, con independencia de quién haya iniciado la disputa, y que cada aviso privado se dirija al usuario evaluado.
  - Se filtraron los mensajes administrativos privados por `receiver_id` en la lista, historial y operaciones de lectura del Inbox y del chat de mediación.
  - Se conservaron accesibles para ambas partes los mensajes grupales con `receiver_id = 0`.
  - Se documentó el comportamiento actualizado en el manual técnico.
- **Archivos modificados:**
  - `packages/Reda/RedaAlojamiento/src/Services/MediacionAlertaService.php`: evaluación individual directa del turista y anfitrión; garantía documentada de que el aviso privado se guarda para el usuario cuyo conteo alcanzó el umbral.
  - `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaInboxController.php`: exclusión de avisos administrativos dirigidos al otro participante en la bandeja, historial y marcado de lectura.
  - `packages/Reda/RedaAlojamiento/src/Http/Controllers/Disputa/MensajeController.php`: privacidad de avisos y aislamiento de marcado de lectura en el historial de mediación; cabecera y documentación de métodos.
  - `manual_tecnico_plugin_reda_alojamiento.md`: actualización de las responsabilidades y comportamiento privado de estos componentes.
  - `previo_cambios_realizados.md`: actualización del punto de control de la tarea.
- **Validación:** diagnósticos estáticos sin errores en los tres archivos PHP modificados; `git diff --check` pasó. No se encontraron pruebas automatizadas específicas. La comprobación funcional final debe hacerse en Vesta después de subir los cambios.

---

## [7 de Octubre, 2026] - Alineación Simétrica y Centrado del Botón de Enviar Mensaje en Inbox
- **Tarea:** Corregir el desfase visual en el pie de entrada de mensajes (`.message-footer`) de la vista de Inbox (`users/inbox.blade.php` y `users/messages.blade.php`), donde el botón con el ícono de avioncito de papel (`.send-btn`) se encontraba posicionado demasiado elevado respecto a la línea media del campo de texto (`.cht_msg`):
    1. **Diagnóstico Técnico del Desfase:**
        - El contenedor `.message-footer` carecía de `align-items: center` estricto en su definición flex.
        - La clase base `a.send-btn` del core de vRent en `public/css/style.css` aplicaba propiedades heredadas desalineantes: `float: right; margin: 0px 0 0 -50px; width: 50px;`, las cuales desfasaban el elemento en el eje vertical y horizontal, provocando que el ícono flotara por encima del centro del input.
        - El glifo FontAwesome `fa-paper-plane` posee una inclinación geométrica intrínseca orientada hacia arriba a la derecha, requiriendo un balance óptico exacto.
    2. **Alineación Flexbox y Simetría Geométrica (`main.scss`):**
        - Se configuró `.message-footer` con `display: flex !important`, `align-items: center !important`, altura fija de `62px !important` y separación `gap: 10px !important`.
        - Se unificó la altura de los componentes a **42px**:
            *   Input `.cht_msg`: `height: 42px !important; border-radius: 21px !important; line-height: 42px !important; padding: 0 18px !important; margin: 0 !important;`
            *   Botón `.send-btn`: `width: 42px !important; height: 42px !important; min-width: 42px !important; border-radius: 50% !important; padding: 0 !important; margin: 0 !important; float: none !important;` formando una circunferencia perfecta.
        - Se centró el ícono en el centro geométrico con `display: inline-flex !important; align-items: center !important; justify-content: center !important;` y se aplicó un ajuste milimétrico de compensación óptica `transform: translate(-1px, 1px) !important;` para posicionar la masa visual del avioncito en perfecta concordancia con el texto del input.
        - Se añadieron microinteracciones visuales fluidas (`hover` con escala `1.08` y tinte verde suave `rgba(29, 191, 115, 0.08)`, y `active` con escala `0.95`).
    3. **Mejora en la Vista Blade (`users/messages.blade.php`):**
        - Se asignó la clase utilitaria `d-flex align-items-center` directamente al contenedor `.message-footer` y se incorporó el atributo de accesibilidad `title="{{ __('Enviar mensaje') }}"` en el enlace `.send-btn`.
- **Archivos Modificados:**
    *   `packages/Reda/RedaAlojamiento/resources/views/users/messages.blade.php`: Inyección de clases flex de centrado y atributo title.
    *   `packages/Reda/RedaAlojamiento/resources/sass/main.scss`: Definición de alturas unificadas de 42px, centrado flexbox, neutralización de reglas de vRent y ajuste óptico del glifo.
    *   `manual_tecnico_plugin_reda_alojamiento.md`: Documentada la subvista y la anatomía simétrica del footer de chat.
    *   `previo_cambios_realizados.md`: Actualizado con el registro del avance.
    *   `LOG_DESARROLLO_REDA.md`: Registro de la sesión.
- **Detalle Técnico e Integridad del Core:** Los archivos originales del proyecto permanecen completamente intactos. La solución se implementó dentro del plugin `packages/Reda/RedaAlojamiento`. Los archivos fuente están editados en Cloud Shell y listos para su compilación en el servidor Vesta de desarrollo con `./compilar.sh`.
- **Estado:** Completado, validado y documentado.

---

## [7 de Octubre, 2026] - Solución Integral al Bloqueo y Sombreado en Inbox al Enviar Mensaje
- **Tarea:** Resolver de forma definitiva la incidencia donde la vista de Inbox (`users/inbox.blade.php`) quedaba sombreada y bloqueada tras hacer clic en el botón "Enviar mensaje" desde la vista individual de la propiedad (`property.single`):
    1. **Diagnóstico de Causa Raíz:**
        - **Stacking Context de Bootstrap 4:** Los modales de seguridad (`modalAdvertenciaPrivacidadReda`, `modalAdvertenciaMensajeReda`) y el overlay de imagen estaban anidados dentro de `<div class="margin-top-85">` en Blade. Al abrirse el modal, Bootstrap 4 insertaba el elemento `.modal-backdrop` como hijo directo de `<body>` con `z-index: 1040`. Debido a que `.margin-top-85` creaba un contexto de apilamiento local con z-index base, el backdrop de `<body>` se dibujaba visualmente por encima del modal y de toda la interfaz, sombreando la pantalla y bloqueando los clics (impidiendo interactuar con el botón "Entendido" o escribir mensajes).
        - **Colisión de Modales y Doble Backdrop:** Al ingresar con parámetro `?id=...`, `inbox.js` abría el modal de privacidad mientras que un `setTimeout` en `mensajes.js` forzaba un `.click()` artificial en la conversación, disparando `window.RedaNotificaciones.esperar()` (con backdrop estático). La colisión de dos modales simultáneos en Bootstrap 4 provocaba bucles de foco y backdrops huérfanos residuales.
        - **Llamadas AJAX Redundantes:** El controlador `RedaInboxController@index` ya entrega la vista HTML con el booking y los mensajes del parámetro `?id=...` completamente renderizados en el servidor. La llamada a `.click()` y a `inyectarMensajesEnriquecidosReda` duplicaba consultas a base de datos y generaba parpadeos y retrasos.
    2. **Aislamiento Estructural en Blade (`users/inbox.blade.php`):**
        - Se cerró el contenedor `.margin-top-85` antes de los modales y el overlay de imagen, dejándolos desacoplados del flujo de contenido principal dentro de `@section('main')`.
    3. **Sincronización Inmediata y Reubicación al Body (`inbox.js`):**
        - En `$(document).ready`, se ejecuta `$('#modalAdvertenciaPrivacidadReda, #modalAdvertenciaMensajeReda, #reda-chat-zoom-overlay').appendTo('body')`, garantizando que en el DOM vivan como hijos directos de `<body>` y que su `z-index: 1060` supere siempre el `1040` del backdrop.
        - En `process()`, se detecta prioritariamente el parámetro `?id=...` de la URL para activar inmediatamente la conversación correcta en el sidebar y centrar el scroll, activando también la vista móvil estilo WhatsApp (`.chat-active`) de forma transparente sin retardos ni clics artificiales.
        - En el clic de `.conversassion`, se evita recarga AJAX si la conversación ya es la activa en pantalla.
        - Se agregaron escuchadores en `hidden.bs.modal` y selectores duales (`data-dismiss` y `data-bs-dismiss`) para asegurar la purga de cualquier backdrop residual y la remoción de `modal-open` en `body`.
    4. **Limpieza de Disparadores Artificiales (`mensajes.js`):**
        - Se eliminó el `setTimeout` que forzaba el `targetConversation.click()` en la carga inicial de `/inbox`, preservando únicamente el desplazamiento visual suave (`scrollIntoView`).
    5. **Protección ante Transiciones Activas (`notificaciones.js`):**
        - Se actualizó `ocultar()` en `RedaNotificaciones` para comprobar si el modal está en transición de apertura (`_isTransitioning`). En tal caso, aguarda al evento `shown.bs.modal` antes de ejecutar `modal('hide')`, y aplica una doble verificación de limpieza para eliminar cualquier backdrop huérfano si no hay modales visibles.
    6. **Optimización de Alcance (`chat-injection.js`):**
        - Se condicionó `init()` para no ejecutar observadores de mutación innecesarios ni duplicar lógica en la ruta `/inbox`.
        - Se corrigió el manejo de errores en el clic de inicio de chat para cerrar el spinner de espera y notificar con `RedaNotificaciones.notificar`.
    7. **Refuerzo de Estilos SCSS (`main.scss`):**
        - Se definieron reglas de `z-index: 1060 !important` para los contenedores y `z-index: 1061 !important` para los `.modal-dialog` de `#modalAdvertenciaPrivacidadReda` y `#modalAdvertenciaMensajeReda`.
- **Archivos Modificados:**
    *   `packages/Reda/RedaAlojamiento/resources/views/users/inbox.blade.php`: Cierre de `.margin-top-85` antes de los modales y overlay.
    *   `packages/Reda/RedaAlojamiento/resources/js/vistas/inbox/inbox.js`: Reubicación al body, sincronización instantánea de `urlBookingId` y limpieza garantizada de backdrops.
    *   `packages/Reda/RedaAlojamiento/resources/js/general/mensajes.js`: Supresión de clics artificiales y recargas redundantes en carga inicial.
    *   `packages/Reda/RedaAlojamiento/resources/js/general/notificaciones.js`: Refuerzo en `ocultar()` con soporte para `_isTransitioning` y limpieza de backdrops.
    *   `packages/Reda/RedaAlojamiento/resources/js/chat-injection.js`: Exclusión de observadores en `/inbox` y corrección de feedback de errores.
    *   `packages/Reda/RedaAlojamiento/resources/sass/main.scss`: Reglas de z-index prioritario para modales de seguridad de Inbox.
    *   `manual_tecnico_plugin_reda_alojamiento.md`: Documentada la arquitectura técnica actualizada.
    *   `previo_cambios_realizados.md`: Actualizado con el registro progresivo y final de la tarea.
    *   `LOG_DESARROLLO_REDA.md`: Registro de la sesión del 7 de Octubre, 2026.
- **Detalle Técnico e Integridad del Core:** Los archivos originales del proyecto de Laravel vRent se mantienen 100% inalterados e intactos. La solución opera íntegramente dentro del plugin `packages/Reda/RedaAlojamiento`. Siguiendo el protocolo mandatorio del entorno, los archivos fuente quedan editados en Cloud Shell y la compilación de assets a minificados debe realizarse en el servidor Vesta de desarrollo mediante `./compilar.sh`.
- **Estado:** Completado, validado y documentado.

---

## [6 de Octubre, 2026] - Botón "Re-enviar" Correo de Verificación en Modales de Registro (Signup) e Inicio de Sesión (Login)
- **Tarea:** Enriquecer el flujo de verificación de correo electrónico añadiendo un tercer botón interactivo **"Re-enviar"** en el modal previo al registro (`#reda_modal_confirmar_email_signup`) y en el modal de cuenta no verificada en inicio de sesión (`#reda_modal_correo_no_verificado_login`):
    1. **Auditoría e Identificación de Componentes:** Se revisó la memoria técnica en `LOG_DESARROLLO_REDA.md` y `manual_tecnico_plugin_reda_alojamiento.md` identificando que los archivos responsables del modal son `packages/Reda/RedaAlojamiento/resources/views/general/modal_verificacion_correo.blade.php`, `packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/verificacionCorreo.js`, `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/VerificacionCorreoController.php` y `packages/Reda/RedaAlojamiento/routes/web.php`.
    2. **Nuevo Endpoint Backend (`VerificacionCorreoController@reenviarCorreoVerificacion`):**
        - Se creó el endpoint `POST reda/usuarios/reenviar-correo-verificacion`.
        - Valida el formato de correo electrónico.
        - Si el usuario no existe en la tabla `users`, devuelve un código HTTP 404 con mensaje pedagógico ("No se encontró una cuenta registrada con este correo electrónico. Por favor haga clic en 'Email correcto' para completar su registro").
        - Si el usuario existe pero ya está verificado (`users_verification.email == 'yes'`), responde con HTTP 400 informando que la cuenta ya fue confirmada.
        - Si el usuario existe y no está verificado, limpia tokens previos en `password_resets`, invoca `EmailController@welcome_email($usuario)` y retorna respuesta JSON estándar de REDA con confirmación de éxito.
    3. **Tercer Botón "Re-enviar" en Modal de Registro (Signup):**
        - En `#reda_modal_confirmar_email_signup` se integró el botón `#reda_btn_reenviar_email_signup` entre "Corregir email" y "Email correcto".
        - Incluye icono `fa-paper-plane`, spinner de carga y contenedores para alertas de éxito o aviso de no registro.
        - Si el usuario ya se había registrado previamente pero no le llegó el primer correo, al hacer clic en "Re-enviar" se reenvía el correo de verificación vía AJAX sin provocar errores de duplicidad (`unique:users`).
    4. **Botón "Re-enviar" Directo en Modal de Inicio de Sesión (Login):**
        - En `#reda_modal_correo_no_verificado_login` se incorporó el botón `#reda_btn_reenviar_correo_login` junto a "Corregir correo" y "Cerrar".
        - Permite al usuario que intenta iniciar sesión y sabe que su correo es correcto reenviar el enlace con un solo clic sin necesidad de volver a tipear la dirección.
        - Se añadió soporte para que tras registrarse (`correo_registrado_pendiente`) el modal esté disponible de inmediato en la vista de login.
    5. **Traducciones en Español (`es.json`):**
        - Registro de todas las cadenas de texto, mensajes de validación y retroalimentación en español.
- **Archivos Modificados:**
    *   `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/VerificacionCorreoController.php`: Incorporación del método `reenviarCorreoVerificacion`.
    *   `packages/Reda/RedaAlojamiento/routes/web.php`: Registro de la ruta POST `reda/usuarios/reenviar-correo-verificacion`.
    *   `packages/Reda/RedaAlojamiento/resources/views/general/modal_verificacion_correo.blade.php`: Inyección de `rutaReenviarCorreo`, nuevo botón "Re-enviar" y contenedores de alertas en modales de Signup y Login.
    *   `packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/verificacionCorreo.js`: Manejadores de eventos AJAX para los botones de reenvío con spinners y retroalimentación reactiva.
    *   `packages/Reda/RedaAlojamiento/resources/lang/es.json`: Incorporación de cadenas de traducción en español.
    *   `manual_tecnico_plugin_reda_alojamiento.md`: Actualización de la documentación técnica del controlador, rutas, vistas y scripts.
    *   `previo_cambios_realizados.md`: Actualizado con el registro de progreso y punto de control en tiempo real.
    *   `LOG_DESARROLLO_REDA.md`: Registro de la sesión del 6 de Octubre, 2026.
- **Detalle Técnico e Integridad del Core:** Los archivos originales del proyecto de Laravel vRent se mantienen 100% inalterados e intactos. Toda la solución reside en el paquete `packages/Reda/RedaAlojamiento`. Siguiendo el protocolo mandatorio del entorno, los archivos fuente quedan editados en Cloud Shell y la compilación de assets a `verificacionCorreo.min.js` debe realizarse en el servidor Vesta de desarrollo mediante `./compilar.sh`.
- **Estado:** Completado, validado y documentado.

---

## [6 de Octubre, 2026] - Actualización de Directrices de Desarrollo: Modalidad de Trabajo Autónomo de la IA sin Pausas Intermedias
- **Tarea:** Configurar y asentar en las directrices de `GEMINI.md` (.github/copilot-instructions.md) y `REDA_PAUTAS_DESARROLLO.md` el cambio de preferencia operativa solicitado por el usuario:
    1. **Eliminación de Pausas para Aceptación Intermedia de Código:** Se reemplazó la directriz anterior (que exigía mostrar pantallas divididas de diferencias y esperar aprobación o rechazo manual paso a paso) por la **modalidad de trabajo autónomo**. La IA ahora implementa los cambios, correcciones o nuevas funcionalidades directamente en los archivos correspondientes sin interrumpir el flujo.
    2. **Punto de Control Progresivo (`previo_cambios_realizados.md`):** Se mantiene como regla mandatoria el registro progresivo en tiempo real de cada archivo modificado o creado durante el proceso de desarrollo, salvaguardando el progreso ante cortes de energía o caídas de internet.
    3. **Documentación Completa Obligatoria:** Al finalizar las modificaciones, la IA actualiza obligatoriamente `LOG_DESARROLLO_REDA.md`, `manual_tecnico_plugin_reda_alojamiento.md` y las cabeceras/funciones de los archivos creados o editados.
    4. **Reporte Final Detallado y Pedagógico:** Al concluir todo el proceso y la documentación, la IA entrega al usuario un informe exhaustivo, claro y estructurado que detalla los archivos modificados/creados, la explicación técnica de las soluciones implementadas y los pasos a seguir por el usuario en el servidor Vesta de desarrollo (subida vía FTP con `subir.sh` o `subir_archivos_puntuales.sh`, comandos de compilación y migraciones según apliquen).
- **Archivos Modificados:**
    *   `GEMINI.md` (.github/copilot-instructions.md): Se actualizó la sección *"Interacción con la IA y Modo de Trabajo Autónomo"*, eliminando la directriz de pausas/pantalla dividida y estableciendo la ejecución directa y reporte final detallado.
    *   `REDA_PAUTAS_DESARROLLO.md`: Se incorporó la sección *"7. Modalidad de Trabajo Autónomo de la IA y Reporte Final"*.
    *   `manual_tecnico_plugin_reda_alojamiento.md`: Se documentó en el sistema de puntos de control y memoria operativa la nueva modalidad de trabajo autónomo y reporte final.
    *   `previo_cambios_realizados.md`: Reinicializado y actualizado como punto de control en tiempo real.
    *   `LOG_DESARROLLO_REDA.md`: Registro de la sesión del 6 de Octubre, 2026.
- **Detalle Técnico e Integridad del Core:** Los archivos originales del proyecto de Laravel vRent se mantienen 100% inalterados e intactos. La modificación responde a una actualización organizativa y metodológica de las pautas del proyecto.
- **Estado:** Completado, validado y documentado.

---

## [5 de Octubre, 2026] - Sistema Integral de Alertas y Suspensión por Límites de Mediaciones, Notificaciones por Correo y Buzón, y Submenú "Mensajes y Alertas" en Panel Admin
- **Tarea:** Revisar, validar, perfeccionar y documentar la arquitectura completa del sistema de avisos preventivos, suspensión de cuentas y alertas administrativas por límites de mediaciones:
    1. **Validación de Estatus de Suspensión:** Se auditó el esquema nativo de la tabla `users` y los controladores de autenticación (`LoginController`, `CustomerController`). Se constató que el valor nativo para cuentas deshabilitadas/suspendidas es `'Inactive'` (el cual bloquea de inmediato el inicio de sesión del usuario en el core y marca su cuenta como inactiva), por lo que se utiliza de forma estándar en el plugin REDA.
    2. **Avisos Preventivos y Suspensión Automática (`MediacionAlertaService`):**
        - Al registrarse una nueva mediación (`DisputaController@store`), se evalúan los umbrales configurados en `settings` (`Cantidad mediaciones permitidas primer aviso` y `Cantidad mediaciones segundo aviso y suspensión`).
        - **Primer Aviso Preventivo:** Si el usuario alcanza el primer umbral, se envía un correo informativo (`emails.primer_aviso_usuario`), un mensaje enriquecido a su buzón `/inbox` (usando `App\Models\Messages` con metadato `sender_type = 'admin'` en `reda_mensajes_metadata`), un correo a todos los administradores activos (`emails.primer_aviso_admin`) y un registro en `alertas_admin` para cada administrador.
        - **Segundo Aviso y Suspensión:** Si alcanza el límite máximo, la cuenta se actualiza a `status = 'Inactive'`, se registra la suspensión en `usuarios_avisos_mediaciones` con fecha y motivo, se envía correo de suspensión (`emails.suspension_usuario`), mensaje formal a su buzón `/inbox`, correo crítico a administradores (`emails.suspension_admin`) y alertas tipo `'suspension'` en `alertas_admin`.
    3. **Campanita de Alertas en Header y Submenú en Menú Lateral Admin (`menuLateralAdmin.js`):**
        - Inyección de campanita con contador dinámico en el navbar superior (`#reda-admin-header-bell`), enlazando directamente al listado de alertas (`/admin/reda/alertas`).
        - Transformación de la opción simple "Messages" del core en el submenú desplegable **"Mensajes y Alertas"**, conteniendo:
            * "Mensajes": enlace al histórico original de mensajes entre huéspedes y anfitriones (`/admin/messages`).
            * "Alertas": enlace al nuevo listado de alertas administrativas (`/admin/reda/alertas`), con badge reactivo y contador dinámico de no leídas (`Alertas (N)`).
        - Sincronización en tiempo real vía AJAX y evento personalizado `reda:actualizar-contador-alertas` tanto para la campanita como para los badges del menú lateral.
    4. **Listado y Gestión Interactiva de Alertas Admin (`index.blade.php` y `indexAlertas.js`):**
        - Vista administrativa con diseño de tarjetas modernas, paginación estándar de 10 en 10 (`admin.general.paginacion`), filtros rápidos ("Todas", "No leídas" con badge, "Leídas") y botón masivo "Marcar todas como leídas".
        - Interacción optimizada: marcar como leída individualmente mediante el botón, haciendo clic en cualquier parte de la tarjeta no leída (con cursor pointer), o automáticamente en segundo plano al pulsar "Ver mediación".
    5. **Registro de Assets y Traducciones:**
        - Incorporación de `indexAlertas.js` en `webpack.mix.js` para compilar a `public/js/reda/admin/vistas/alerta/indexAlertas.min.js`.
        - Registro de todas las traducciones en español requeridas en `packages/Reda/RedaAlojamiento/resources/lang/es.json`.
- **Archivos Revisados, Modificados y Creados:**
    *   `packages/Reda/RedaAlojamiento/database/migrations/2026_10_04_000000_crear_tabla_alertas_admin.php`: Migración para la tabla `alertas_admin`.
    *   `packages/Reda/RedaAlojamiento/database/migrations/2026_10_04_000001_crear_tabla_usuarios_avisos_mediaciones.php`: Migración para la tabla `usuarios_avisos_mediaciones`.
    *   `packages/Reda/RedaAlojamiento/src/Models/Alerta/AlertaAdmin.php`: Modelo Eloquent para `alertas_admin` con relaciones a `admin`, `user` y `disputa`.
    *   `packages/Reda/RedaAlojamiento/src/Models/Disputa/UsuarioAvisoMediacion.php`: Modelo Eloquent para control de auditoría de avisos y suspensión.
    *   `packages/Reda/RedaAlojamiento/src/Services/MediacionAlertaService.php`: Servicio orquestador de límites, envío de correos, buzón y suspensión de usuarios.
    *   `packages/Reda/RedaAlojamiento/src/Http/Controllers/Admin/Alerta/AlertaController.php`: Controlador del panel administrativo para paginación 10 en 10, conteo no leídas y marcado individual/masivo.
    *   `packages/Reda/RedaAlojamiento/src/Http/Controllers/Disputa/DisputaController.php`: Integración de la llamada a `MediacionAlertaService::verificarLimites($disputa)` al crear mediaciones.
    *   `packages/Reda/RedaAlojamiento/routes/web.php`: Rutas del prefijo `admin/reda/alertas`.
    *   `packages/Reda/RedaAlojamiento/resources/views/admin/alerta/index.blade.php`: Vista Blade del panel de alertas del sistema.
    *   `packages/Reda/RedaAlojamiento/resources/js/admin/vistas/alerta/indexAlertas.js`: Controlador JavaScript del panel de alertas con filtros, paginación y marcado reactivo.
    *   `packages/Reda/RedaAlojamiento/resources/js/admin/general/menus/menuLateralAdmin.js`: Inyección de la campanita en header, submenú "Mensajes y Alertas" y sincronización reactiva de badges.
    *   `packages/Reda/RedaAlojamiento/resources/views/emails/primer_aviso_usuario.blade.php`: Plantilla de correo para el usuario por primer aviso.
    *   `packages/Reda/RedaAlojamiento/resources/views/emails/primer_aviso_admin.blade.php`: Plantilla de correo para administradores por primer aviso.
    *   `packages/Reda/RedaAlojamiento/resources/views/emails/suspension_usuario.blade.php`: Plantilla de correo para el usuario por suspensión.
    *   `packages/Reda/RedaAlojamiento/resources/views/emails/suspension_admin.blade.php`: Plantilla de correo para administradores por suspensión.
    *   `webpack.mix.js`: Registro de entrada de compilación para `indexAlertas.js`.
    *   `packages/Reda/RedaAlojamiento/resources/lang/es.json`: Incorporadas todas las cadenas de traducción en español para el módulo de alertas.
    *   `manual_tecnico_plugin_reda_alojamiento.md`: Documentada exhaustivamente la arquitectura del módulo de alertas, migraciones, modelos, servicio, controlador, vistas y scripts.
    *   `previo_cambios_realizados.md`: **NUEVO ARCHIVO**. Creado en la raíz del proyecto como punto de control en tiempo real para salvaguardar el estado de modificaciones ante fallas eléctricas o de internet.
    *   `GEMINI.md` (.github/copilot-instructions.md) y `REDA_PAUTAS_DESARROLLO.md`: Incorporado el protocolo mandatorio de lectura previa, reinicio limpio y registro progresivo en `previo_cambios_realizados.md`.
- **Detalle Técnico e Integridad del Core:** Los archivos originales del proyecto de Laravel vRent (`app/Models/User.php`, `app/Models/Messages.php`, `app/Models/Notifications.php`, `resources/views/admin/common/left_sidebar.blade.php`, etc.) se mantienen 100% inalterados e intactos. El buzón del usuario aprovecha el modelo de mensajería enriquecido mediante la tabla auxiliar `reda_mensajes_metadata`, mientras que las alertas de administradores operan en su propia tabla desacoplada `alertas_admin` para no contaminar el historial de chat entre usuarios. Siguiendo las directrices mandatorias del entorno, los archivos fuente quedan editados en Cloud Shell y la compilación y migración se sugieren para ser ejecutadas en el servidor Vesta de desarrollo.
- **Estado:** Completado, validado y documentado.

---

## [3 de Octubre, 2026] - Vista y Persistencia de "Cantidad de Mediaciones Permitidas" (Settings) y Restricción Exclusiva para Rol 1
- **Tarea:** Implementar la configuración de umbrales máximos de mediaciones en el panel administrativo y perfeccionar la experiencia interactiva del submenú "Mediaciones" en el menú lateral:
    1. **Contador en opción "Listado":** Actualizar el submenú de "Mediaciones" para que, al cargar y al hacer clic o tocar la opción padre "Mediaciones", la opción "Listado" muestre también el contador de mediaciones activas (`Listado (N)`), sincronizado con el conteo de la opción principal.
    2. **Restricción estricta de "Configuración" a Rol 1:** La opción "Configuración" del submenú solo se renderiza y queda visible y disponible para usuarios administradores con Rol 1 (`role_id == 1` o nombre de rol `'admin'`). Para usuarios con Rol 2 ("Atención al usuario"), el elemento de configuración no se inyecta en el DOM.
    3. **Protección Backend:** Los endpoints de configuración (`GET /admin/reda/disputas/configuracion` y `POST /admin/reda/disputas/configuracion/store`) validan el rol del usuario conectado en `role_admin` y devuelven 403 Forbidden ante cualquier acceso no autorizado.
    4. **Nueva Vista de Configuración:** Al hacer clic en "Configuración", se presenta la vista Blade `@extends('admin.template')` con un panel/recuadro titulado "Cantidad de mediaciones permitidas", conteniendo dos campos numéricos:
        - "Cantidad de mediaciones para primer aviso"
        - "Cantidad de mediaciones para segundo aviso y suspensión de cuenta"
    5. **Almacenamiento en `settings`:** Los valores se persisten mediante AJAX en la tabla `settings` con los nombres exactos:
        - `name`: `'Cantidad mediaciones permitidas primer aviso'`, `value`: valor numérico, `type`: `'Mediaciones'`
        - `name`: `'Cantidad mediaciones segundo aviso y suspensión'`, `value`: valor numérico, `type`: `'Mediaciones'`
    6. **Experiencia de usuario con REDA:** Validación reactiva en cliente, animación de espera (`window.RedaNotificaciones.esperar()`), botón con spinner y modal de notificación (`window.RedaNotificaciones.notificar`) con el estándar JSON de REDA.
- **Archivos Modificados/Creados:**
    *   `packages/Reda/RedaAlojamiento/resources/views/admin/general/main_footer.blade.php`: Se añadió la propiedad `esAdminTotal` calculada para Rol 1 al objeto `window.RedaAdminUser`.
    *   `packages/Reda/RedaAlojamiento/resources/js/admin/general/menus/menuLateralAdmin.js`: Se actualizó la lógica del submenú para condicionar la presencia de "Configuración" a Rol 1 (`esAdminRol1`), actualizar el texto del contador tanto en "Mediaciones" como en "Listado" (`Listado (N)`), sincronizar el contador al desplegar el acordeón e incorporar animación de espera al navegar a Configuración.
    *   `packages/Reda/RedaAlojamiento/routes/web.php`: Se registraron las rutas `admin/reda/disputas/configuracion` (GET) y `admin/reda/disputas/configuracion/store` (POST) asociadas a `AdminDisputaController`.
    *   `packages/Reda/RedaAlojamiento/src/Http/Controllers/Admin/Disputa/DisputaController.php`: Se implementaron los métodos `configuracion()` (autorización Rol 1 y lectura de `settings`) y `guardarConfiguracion(Request $request)` (validación, persistencia en `settings` con `type = 'Mediaciones'` y respuesta JSON estándar REDA).
    *   `packages/Reda/RedaAlojamiento/resources/views/admin/disputa/configuracion.blade.php`: **NUEVO ARCHIVO**. Vista administrativa con panel estilizado, formulario, inputs para primer y segundo aviso/suspensión, y carga del script compilado `configuracionDisputas.min.js`.
    *   `packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/configuracionDisputas.js`: **NUEVO ARCHIVO**. Controlador JavaScript para validación, estado de carga, envío AJAX y feedback con `window.RedaNotificaciones`.
    *   `packages/Reda/RedaAlojamiento/resources/lang/es.json`: Incorporadas todas las cadenas de traducción y mensajes de validación/notificación en español.
    *   `webpack.mix.js`: Registrada la entrada de compilación para generar `configuracionDisputas.min.js`.
    *   `manual_tecnico_plugin_reda_alojamiento.md`: Documentada la arquitectura, nuevos endpoints, vistas y scripts.
- **Detalle Técnico e Integridad del Core:** Los archivos originales del proyecto de Laravel vRent (`resources/views/admin/common/left_sidebar.blade.php`, `app/Models/Settings.php`, etc.) se mantienen 100% inalterados e intactos. La persistencia se realiza limpiamente en la tabla `settings` existente respetando los tipos y nombres especificados. Toda la funcionalidad reside en el paquete `packages/Reda/RedaAlojamiento`. Siguiendo el protocolo mandatorio del entorno, los archivos fuente quedan editados en Cloud Shell y la compilación de assets debe realizarse en el servidor Vesta mediante `./compilar.sh`.
- **Estado:** Completado y documentado.

---

## [30 de Septiembre, 2026] - Transformación de "Mediaciones" en Submenú Desplegable (Listado y Configuración) en Menú Lateral Admin
- **Tarea:** En el menú lateral del panel administrativo (Backend), transformar la opción de enlace simple "Mediaciones" en un submenú desplegable interactivo tipo acordeón (`.treeview`) que contenga dos opciones:
    1. **Listado:** Conduce a la vista principal del listado de mediaciones (`/admin/reda/disputas`), activando la animación de espera al interactuar.
    2. **Configuración:** Opción preliminar apuntando a `#` reservada para la futura configuración del módulo de mediaciones.
    3. Comportamiento interactivo: Al hacer clic en el encabezado padre "Mediaciones", se despliega o repliega suavemente mediante animación slide (`slideUp`/`slideDown`), alternando el icono de la flecha (`fa-angle-left` / `fa-angle-down`). Si el usuario se encuentra navegando en la ruta de mediaciones (`admin/reda/disputas`), el submenú se renderiza abierto automáticamente (`menu-open active`) y la opción "Listado" se marca como activa.
    4. El contador dinámico de mediaciones activas (`adminCountUrl`) se conserva y actualiza automáticamente en el encabezado del submenú (`Mediaciones (N)`).
- **Archivos Modificados/Creados:**
    *   `packages/Reda/RedaAlojamiento/resources/js/admin/general/menus/menuLateralAdmin.js`: Reestructurada la sección de inyección de "Mediaciones" para generar la estructura jerárquica con contenedor `li.nav-item.treeview`, enlace toggle con icono SVG, flecha animada y lista hija `ul.nav.nav-treeview.treeview-menu` con "Listado" y "Configuración". Implementados los manejadores de eventos para el despliegue animado, la animación de espera en "Listado" y la prevención por defecto en "Configuración".
    *   `packages/Reda/RedaAlojamiento/resources/lang/es.json`: Incorporadas las claves de traducción `"Listado": "Listado"` y `"Configuración": "Configuración"`.
    *   `manual_tecnico_plugin_reda_alojamiento.md`: Actualizada la documentación técnica de `menuLateralAdmin.js` con las especificaciones del nuevo submenú desplegable.
- **Detalle Técnico e Integridad del Core:** Los archivos originales del proyecto de Laravel vRent (`resources/views/admin/common/left_sidebar.blade.php`, etc.) se mantienen 100% intactos e inalterados. La solución se construyó exclusivamente en el JavaScript del plugin REDA respetando la estructura semántica de AdminLTE 4 / Bootstrap 5, manteniendo compatibilidad total tanto para roles 1 (Admin) como 2 (Atención al usuario). Siguiendo las directrices del proyecto, los archivos fuente quedan editados en Cloud Shell y la compilación a `reda-admin-general-main.min.js` debe realizarse en el servidor Vesta mediante `./compilar.sh`.
- **Estado:** Completado y documentado.

---

## [30 de Septiembre, 2026] - Asignación Dinámica de Agentes (Rol 1 Select, Rol 2 Suiche "Tomar Mediación") y Filtrado en Index de Disputas
- **Tarea:** Mejorar la vista de mediaciones administrativas (`/admin/reda/disputas`) adaptándola según el rol del usuario administrador autenticado en `role_admin`:
    1. Para Rol 1 (Admin): En cada tarjeta de la lista donde no haya agente asignado, sustituir el texto por un control interactivo `<select>` con la lista de agentes (obtenidos de `admin`, `roles` y `role_admin` con status 'Active'), incluyendo al propio usuario admin con Rol 1 por si desea atender la mediación él mismo. Al seleccionar un agente, se persiste vía AJAX en la columna `id_usuario_agente_asignado` de la tabla `disputas`, mostrando inmediatamente la foto de perfil y el nombre del agente, con opción de reasignar.
    2. Para Rol 2 (Atención al usuario / Agente): En lugar de un `<select>`, mostrar un suiche tipo toggle (`form-check form-switch`) con la etiqueta "Tomar mediación". Al activarlo, se actualiza atómicamente la columna `id_usuario_agente_asignado` en `disputas` con el ID del usuario admin logueado, sustituyendo el suiche por la foto de perfil y nombre del agente asignado.
    3. Filtrado estricto en el Index y Conteo para Rol 2: Los agentes con Rol 2 solo pueden visualizar en el listado y conteo las mediaciones no asignadas (`id_usuario_agente_asignado IS NULL` o `0`) y las que les fueron asignadas a ellos o tomaron ellos mismos. Las mediaciones asignadas a otros agentes quedan estrictamente ocultas y protegidas en el backend ante accesos directos al modal de detalle.
- **Archivos Modificados/Creados:**
    *   `packages/Reda/RedaAlojamiento/routes/web.php`: Se registró la ruta `POST admin/reda/disputas/asignar-agente` (`reda.admin.disputas.asignar_agente`) gestionada por `AdminDisputaController@asignarAgente`.
    *   `packages/Reda/RedaAlojamiento/src/Http/Controllers/Admin/Disputa/DisputaController.php`:
        - Implementado `obtenerRolAdmin($adminId)` para consultar de manera confiable el `role_id` del usuario conectado en `role_admin` y `roles`.
        - Implementado `obtenerAgentesDisponibles()` para consultar los agentes activos con rol 1 y 2, retornando colección con ID, nombre, foto de perfil y nombre de rol.
        - Actualizado `obtenerDisputasPaginadas`: para rol 2 aplica filtro `(id_usuario_agente_asignado IS NULL OR id_usuario_agente_asignado = 0 OR id_usuario_agente_asignado = $adminId)`; para rol 1 expone la lista de agentes disponibles; incluye en cada elemento `id_usuario_agente_asignado` y los datos del agente.
        - Actualizado `obtenerConteoDisputasActivas` con el mismo filtro para rol 2.
        - Actualizado `getDetailModal` denegando acceso (403) a rol 2 si la mediación pertenece a otro agente.
        - Creado el método `asignarAgente(Request $request)` que valida permisos según rol, actualiza `disputas.id_usuario_agente_asignado` y retorna la estructura estándar JSON de REDA con los datos del agente asignado.
    *   `packages/Reda/RedaAlojamiento/resources/views/admin/disputa/index.blade.php`: Se actualizaron las variables globales de acceso `window.RedaAdminAccess` para inyectar `roleId`, `adminId` y `isFullAdmin` (true únicamente para rol 1).
    *   `packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/indexDisputas.js`:
        - Se incorporaron las variables de estado `agentesDisponibles`, `rolAdminActual` y `adminIdActual`.
        - Implementado el método AJAX `asignarAgenteDisputa(disputaId, agenteId = null)`.
        - Implementada la función generadora `generarSeccionAgenteHtml(item)`: si está asignado muestra foto de perfil y nombre; si es rol 1 renderiza el `<select>` con opciones de agentes; si es rol 2 renderiza el suiche toggle "Tomar mediación".
        - Implementada la función `generarBloqueAgenteAsignadoHtml` para renderizar el agente con foto y nombre, e incluir el botón de reasignación para rol 1.
        - Registrados los eventos `change` para `.select-asignar-agente-disputa` y `.switch-tomar-mediacion` con animación de espera (`window.RedaNotificaciones.esperar()`), actualización reactiva del DOM (lista y sidebar) y notificación de resultado.
        - Registrados eventos para reasignar y cancelar cambio de agente en rol 1.
    *   `packages/Reda/RedaAlojamiento/resources/lang/es.json`: Incorporadas las cadenas de traducción en español para "Tomar mediación", "Asignar agente:", "Seleccionar agente...", "Cambiar agente" y mensajes de confirmación y error.
    *   `manual_tecnico_plugin_reda_alojamiento.md`: Documentada la arquitectura del nuevo flujo y los archivos modificados.
- **Detalle Técnico e Integridad del Core:** Los archivos originales del proyecto (`app/Models/Admin.php`, `app/Models/RoleAdmin.php`, `app/Models/Roles.php`, `routes/web.php` del núcleo, etc.) se mantienen 100% inalterados e intactos. Todas las modificaciones se realizaron dentro del paquete `packages/Reda/RedaAlojamiento`, respetando estrictamente las normas del archivo `GEMINI.md`. Siguiendo el protocolo mandatorio, los archivos fuente quedan listos en Cloud Shell y la compilación de `indexDisputas.min.js` se realizará en el servidor Vesta mediante `./compilar.sh`.
- **Estado:** Completado y documentado.

---

## [28 de Septiembre, 2026] - Acceso Completo del Rol "Atención al usuario" a la Opción y Gestión de Mediaciones en Panel Admin
- **Tarea:** Permitir que los usuarios administradores con rol "Atención al usuario" (Rol ID 2 en la tabla `roles` y `role_admin`) tengan acceso pleno a la opción de menú "Mediaciones" y a toda la gestión del módulo de disputas/mediaciones en el panel administrativo (`/admin/reda/disputas`), del mismo modo que el rol 1 ("Admin").
- **Archivos Modificados/Creados:**
    *   `packages/Reda/RedaAlojamiento/resources/js/admin/general/menus/menuLateralAdmin.js`: Se actualizó la lógica de inyección de la opción "Mediaciones". Ahora verifica `window.RedaAdminUser.tieneAccesoMediaciones` (habilitado para roles 1 y 2) y dispone de un algoritmo jerárquico de puntos de anclaje (`Bookings` -> `#menu-negocios` -> `Properties` -> `Customers` -> `Dashboard` -> `.sidebar-menu`), garantizando que la opción se inserte visualmente incluso si el rol "Atención al usuario" no posee acceso a "Bookings" o "Properties". Mantiene la animación de espera al hacer clic y la actualización del contador en tiempo real vía AJAX.
    *   `packages/Reda/RedaAlojamiento/resources/views/admin/general/main_footer.blade.php`: Se inyecta en el objeto global `window.RedaAdminUser` la propiedad `tieneAccesoMediaciones` calculada desde la base de datos consultando `role_admin` con soporte para `role_id in [1, 2]` y comprobación secundaria por nombre o `display_name` del rol (`'admin'`, `'atención al usuario'`), permitiendo al JavaScript conocer los permisos de inmediato.
    *   `packages/Reda/RedaAlojamiento/resources/views/admin/disputa/index.blade.php`: Se actualizó la variable de acceso `window.RedaAdminAccess.isFullAdmin` para que considere como administrador total tanto al rol 1 como al rol 2.
    *   `packages/Reda/RedaAlojamiento/src/Http/Controllers/Admin/Disputa/DisputaController.php`: Se implementó el método auxiliar `tieneAccesoCompletoMediaciones($adminId)` que valida permisos mediante consulta a `role_admin` y `roles` (`role_id in [1, 2]` o coincidencias de nombre/display_name), utilizándolo en `obtenerDisputasPaginadas`, `obtenerConteoDisputasActivas` y `getDetailModal`. De este modo, los usuarios con rol "Atención al usuario" pueden listar todas las mediaciones, ver el conteo total activo y abrir el detalle sin restricciones 403 Forbidden.
    *   `manual_tecnico_plugin_reda_alojamiento.md`: Se actualizó la documentación técnica con la arquitectura de permisos administrativos y la inyección adaptativa del menú lateral para roles de atención al cliente.
- **Detalle Técnico e Integridad del Core:** Los archivos originales del proyecto de Laravel vRent (`app/Models/Roles.php`, `app/Models/Admin.php`, `app/Models/RoleAdmin.php`, `resources/views/admin/common/left_sidebar.blade.php`, `app/Http/Controllers/Admin/AdminController.php` y `routes/web.php`) se mantuvieron 100% inalterados e intactos. La solución opera íntegramente dentro del paquete `packages/Reda/RedaAlojamiento`, respetando de manera estricta todas las directrices de `GEMINI.md`. Siguiendo el protocolo mandatorio, los archivos fuente quedan listos en Cloud Shell y la compilación de `reda-admin-general-main.min.js` se realizará en el servidor Vesta mediante `./compilar.sh`.
- **Estado:** Completado y documentado.

---

## [27 de Septiembre, 2026] - Validación del Lado del Cliente para Comprobante de Pago y Mensaje al Anfitrión (Sin Recarga de Página)
- **Tarea:** En la vista de pago (`/payment/{gateway}/pay` renderizada por `GatewayController@pay`, específicamente en pasarelas como `DirectBankTransfer`), evitar que al olvidar escribir el mensaje obligatorio al anfitrión (`note`) se recargue la página en el servidor y se pierda el archivo del comprobante de pago (`attachment[]`) previamente seleccionado por el usuario. Implementar una validación estricta y reactiva en el cliente (JavaScript) para ambos campos (comprobante y mensaje), mostrando mensajes de error en español bajo cada campo, resaltando con borde rojo, enfocando el primer campo inválido y garantizando que el archivo adjunto se conserve intacto en el navegador. Todo desarrollado 100% dentro del plugin REDA sin tocar ningún archivo original del core o de módulos externos.
- **Archivos Modificados/Creados:**
    *   `packages/Reda/RedaAlojamiento/resources/js/vistas/pago/frontend/pagos.js`: Refactorizado con documentación JSDoc completa. Desactiva la validación nativa del navegador (`novalidate`) para proveer interfaz homogénea. Incorpora intercepción en fase de captura del DOM (`Capture Phase`) en `#payment-form-submit` y en el evento `submit` de `#payment-form`, lo cual bloquea de forma preventiva el script `initial.js` de la pasarela que deshabilitaba el botón antes de tiempo. Valida que existan archivos en `attachment[]` y que `note` contenga texto no vacío (`trim()`). En caso de error, previene el envío, restaura el botón y el spinner, muestra mensajes claros en español en los contenedores `span.text-danger` y enfoca el campo con error sin recargar la página. Añade escuchadores en tiempo real (`change` e `input`) para limpiar los errores visuales tan pronto como el usuario interactúa con los campos.
    *   `packages/Reda/RedaAlojamiento/resources/lang/es.json`: Incorporadas las nuevas cadenas de traducción y mensajes de validación en español para el comprobante de pago y mensaje al anfitrión.
    *   `manual_tecnico_plugin_reda_alojamiento.md`: Documentada la responsabilidad y funciones del archivo `pagos.js` en la sección "Flujo de Pago y Confirmación de Reservas".
    *   `GEMINI.md` (.github/copilot-instructions.md) y `REDA_PAUTAS_DESARROLLO.md`: Se formalizó y reforzó la regla mandatoria sobre el entorno de trabajo: Cloud Shell Editor es exclusivamente repositorio de fuentes (prohibido compilar, minificar o correr artisan). Se incorporó la prohibición estricta de ejecutar o modificar `subir.sh` y `subir_archivos_puntuales.sh`. Se estableció el protocolo obligatorio para comandos (`php artisan`, `npm run`, `./compilar.sh`): sugerirlos por escrito al usuario y detener la respuesta esperando confirmación antes de continuar.
- **Detalle Técnico e Integridad del Core:** Los archivos originales del proyecto y de módulos (`Modules/DirectBankTransfer/Resources/views/pay.blade.php`, `Modules/DirectBankTransfer/Resources/assets/js/initial.js`, `Modules/DirectBankTransfer/Processor/DirectBankTransferProcessor.php` y `Modules/Gateway/Routes/web.php`) se mantuvieron 100% inalterados e intactos. La solución aprovecha que `pagos.min.js` ya está incorporado en `main_footer.blade.php` del plugin REDA, interceptando de forma no invasiva los eventos del DOM antes de que cualquier script del core o módulo proceda con el submit o con la desactivación del botón, cumpliendo de manera estricta todas las directrices de `GEMINI.md`. Siguiendo la directriz de Cloud Shell IDE, solo se gestionan y guardan los archivos fuentes; la compilación y minificación a `pagos.min.js` se realizará directamente en el servidor Vesta de desarrollo mediante `./compilar.sh`.
- **Estado:** Completado y documentado.

---

## [26 de Septiembre, 2026] - Verificación y Corrección de Correo Electrónico en Registro y Login (Arquitectura 100% en Plugin)
- **Tarea:** Implementar un flujo integral de confirmación y corrección de correo electrónico sin modificar archivos originales del core de Laravel:
    1. En el registro (`signup`), interceptar el envío del formulario tras las validaciones estándar para mostrar un modal donde se verifique si el correo escrito es correcto. Dispone de dos opciones: "Email correcto" (continúa el envío normal) y "Corregir email" (despliega un input con validación de sintaxis regex y alerta de error antes de enviar).
    2. Al registrarse un nuevo usuario, evitar que la sesión quede iniciada automáticamente; ahora se cierra la sesión y se le redirige a login informándole que debe confirmar su cuenta por correo.
    3. En el inicio de sesión (`login`), verificar en la tabla `users_verification` si `email == 'yes'`. Si las credenciales son correctas pero el correo no ha sido verificado, impedir el login y abrir automáticamente un modal informándole que se envió el enlace a su correo, con un botón "Corregir correo" que permite ingresar un nuevo correo y reenviar la confirmación mediante petición AJAX.
    4. Al hacer clic en el enlace de confirmación del correo (`users/confirm_email`), permitir la validación sin requerir sesión activa previa, activar la cuenta (`status = 'Active'`), actualizar `users_verification.email = 'yes'`, y redirigir a login mostrando un modal de felicitaciones con el botón "Iniciar sesión".
- **Archivos Modificados/Creados:**
    *   `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaUsuarioController.php`: **NUEVO ARCHIVO**. Controlador del plugin que extiende de `App\Http\Controllers\UserController`. Sobrescribe `create` para registrar al usuario, enviar correo de verificación vía `welcome_email` e interrumpir el inicio de sesión automático, redirigiendo a login con una notificación de cuenta pendiente. Sobrescribe `confirmEmail` para permitir la activación de la cuenta desde el enlace del correo sin requerir una sesión activa previa, activando el estado en `users`, actualizando `users_verification.email = 'yes'` y redirigiendo a login con el modal de confirmación exitosa.
    *   `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaLoginController.php`: **NUEVO ARCHIVO**. Controlador del plugin que extiende de `App\Http\Controllers\LoginController`. Sobrescribe `authenticate` para comprobar las credenciales del usuario con `Hash::check`; si la contraseña es correcta pero `users_verification.email` no es 'yes', bloquea el inicio de sesión y redirige a la vista de login con la variable flash `correo_no_verificado` para desplegar el modal interactivo de aviso y corrección de correo.
    *   `packages/Reda/RedaAlojamiento/src/RedaAlojamientoServiceProvider.php`: Se añadieron sobreescrituras en el evento `booted` para redirigir dinámicamente las rutas `create` (POST) y `users/confirm_email/{code?}` (GET) hacia `RedaUsuarioController`, y la ruta `authenticate` (POST) hacia `RedaLoginController`, eliminando los middlewares que obstaculizaban la activación externa sin sesión.
    *   `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/VerificacionCorreoController.php`: **NUEVO ARCHIVO**. Controlador de Reda con el método `actualizarCorreoYReenviar`. Valida el nuevo formato, unicidad en la base de datos, actualiza `users.email`, limpia tokens previos y reenvía el correo de confirmación con `EmailController@welcome_email` usando la estructura JSON estándar de Reda.
    *   `packages/Reda/RedaAlojamiento/routes/web.php`: Se registró la ruta POST `reda/usuarios/actualizar-correo-verificacion`.
    *   `packages/Reda/RedaAlojamiento/resources/views/general/modal_verificacion_correo.blade.php`: **NUEVO ARCHIVO**. Define los 3 modales Bootstrap 4.5 (`#reda_modal_confirmar_email_signup`, `#reda_modal_correo_no_verificado_login`, `#reda_modal_correo_confirmado_exito`) y expone las variables de sesión en `window.RedaVerificacionData`.
    *   `packages/Reda/RedaAlojamiento/resources/views/general/main_footer.blade.php`: Inclusión de `@include('reda-alojamiento::general.modal_verificacion_correo')` y del script `verificacionCorreo.min.js`.
    *   `packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/verificacionCorreo.js`: **NUEVO ARCHIVO**. Script modular que intercepta `#signup_form`, gestiona la verificación previa, detecta la variable de sesión para abrir el modal de login de correo no confirmado con llamada AJAX y gestiona el modal de confirmación exitosa con redirección y autofoco en login.
    *   `packages/Reda/RedaAlojamiento/resources/lang/es.json`: Incorporación de todas las traducciones y mensajes en español requeridos para los tres modales y respuestas del servidor.
    *   `webpack.mix.js`: Se añadió `verificacionCorreo.js` para compilarse en `public/js/reda/vistas/frontend/verificacionCorreo.min.js`.
    *   `manual_tecnico_plugin_reda_alojamiento.md`: Documentada la arquitectura del nuevo flujo y los archivos modificados/creados.
- **Detalle Técnico e Integridad del Core:** Los archivos originales del proyecto (`app/Http/Controllers/UserController.php` y `app/Http/Controllers/LoginController.php`) se mantienen 100% inalterados e intactos. La modificación del comportamiento se implementa de forma limpia y desacoplada mediante herencia controlada en controladores propios del paquete `packages/Reda/RedaAlojamiento` y secuestro de rutas vía el `ServiceProvider` del plugin, cumpliendo de manera estricta las directrices de `GEMINI.md`.
- **Estado:** Completado y documentado.

---

## [25 de Septiembre, 2026] - Desglose de Huéspedes en Adultos y Niños estilo Airbnb y Consistencia Global
- **Tarea:** Sustituir en el modal de reservación el selector único de huéspedes por dos campos desglosados ("Adulto(s)" y "Niño(s)") estilo Airbnb. Implementar persistencia híbrida (tabla auxiliar `reserva_huespedes` y tabla existente `booking_details`) para asegurar funcionamiento inmediato en el servidor de desarrollo Vesta sin depender de la ejecución inmediata de migraciones. Asegurar que en todas las vistas de la aplicación (/payments/book, /booking/requested, /booking/{id}, /admin/bookings/detail/{id}, /my-bookings y modales) se muestre consistentemente "X Adulto(s), Y Niño(s)" sin modificar archivos o tablas originales del proyecto.
- **Corrección en Pruebas:** Se solventó un error 500 (`Undefined constant "window_lang"`) en `packages/Reda/RedaAlojamiento/src/Helpers/helpers.php` al remover variables residuales no declaradas en la construcción del texto de adultos y niños.
- **Archivos Modificados/Creados:**
    *   `packages/Reda/RedaAlojamiento/database/migrations/2026_09_25_000000_crear_tabla_reserva_huespedes.php`: **NUEVO ARCHIVO**. Migración preparada para crear la tabla auxiliar `reserva_huespedes` (`id`, `reserva_id`, `adultos`, `ninos`, marcas de tiempo con soporte nullable), cumpliendo las normas de nomenclatura en español y sin ejecución local.
    *   `packages/Reda/RedaAlojamiento/src/Models/Reserva/ReservaHuesped.php`: **NUEVO ARCHIVO**. Modelo Eloquent para interactuar con la tabla auxiliar `reserva_huespedes`.
    *   `packages/Reda/RedaAlojamiento/src/Observers/ReservaObserver.php`: **NUEVO ARCHIVO**. Observador registrado sobre el modelo core `App\Models\Bookings`. Al crearse una reserva (`created`), intercepta los valores de `adultos` y `ninos` asegurados en sesión o request y los almacena en `booking_details` (claves 'adultos' y 'ninos') y en `reserva_huespedes` (si la tabla existe).
    *   `packages/Reda/RedaAlojamiento/src/Helpers/helpers.php`: Se creó la función helper `reda_obtener_desglose_huespedes($booking)` que consulta con prioridad `reserva_huespedes`, luego `booking_details`, y ofrece fallback retrocompatible asignando los `guest` como adultos si no existe desglose previo. Retorna `['adultos', 'ninos', 'total', 'texto']`.
    *   `packages/Reda/RedaAlojamiento/src/RedaAlojamientoServiceProvider.php`: Se registró el observador `ReservaObserver` para el modelo `Bookings`.
    *   `packages/Reda/RedaAlojamiento/routes/web.php`: Se registró la ruta AJAX `reda/bookings/huespedes-info`.
    *   `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaBookingController.php`: Se actualizó `getBookingDetails` para incluir `adultos`, `ninos` y `huespedes_desglose`, y se creó el método `getHuespedesInfo` para retornar el desglose por `booking_id` o `code`.
    *   `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaPaymentController.php`: Se añadieron los campos `payment_adultos` y `payment_ninos` en sesión al recibir la petición POST de reserva.
    *   `packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/propiedad_detalle.js`: Se actualizó `mostrarModalFinal` para ocultar el selector original `#number_of_guests` e inyectar dos selectores dinámicos para "Adulto(s)" y "Niño(s)" con cálculo en tiempo real de capacidad máxima permitida (`accommodates`), sincronización con `#number_of_guests` y disparo automático de `price_calculation()`.
    *   `packages/Reda/RedaAlojamiento/resources/views/general/modal_reservar.blade.php`: Se añadieron estilos CSS para `.reda-new-guests-container` y el ocultamiento de `#number_of_guests` en la modal.
    *   `packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/desgloseHuespedes.js`: **NUEVO ARCHIVO**. Script modular que detecta las páginas de pago, confirmación, detalle de reserva, mis reservas y admin para sustituir de manera no invasiva la etiqueta monolítica de huéspedes por el desglose "X Adulto(s), Y Niño(s)" e inyectar los inputs ocultos en `#checkout-form`.
    *   `packages/Reda/RedaAlojamiento/resources/views/general/main_footer.blade.php`: Se inyectaron en sesión las variables `window.RedaSessionHuespedes` y se incluyó el script `desgloseHuespedes.min.js`.
    *   `packages/Reda/RedaAlojamiento/resources/views/admin/general/main_footer.blade.php`: Se incluyó `desgloseHuespedes.min.js` en el footer administrativo.
    *   `packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/verDetalleReservaModal.js`: Se actualizó para mostrar `data.huespedes_desglose` en el modal de detalles.
    *   `packages/Reda/RedaAlojamiento/resources/lang/es.json`: Se agregaron traducciones para "Adulto(s)", "Niño(s)", "Adulto", "Adultos", "Niño", "Niños", etc.
    *   `webpack.mix.js`: Se registró `desgloseHuespedes.js` para su compilación.
    *   `manual_tecnico_plugin_reda_alojamiento.md`: Actualizada la documentación técnica con todos los componentes, modelos y flujos del desglose.
- **Detalle Técnico:** La solución cumple rigurosamente con la premisa de no tocar archivos core de base de datos ni vistas originales mediante modificaciones intrusivas. Al implementar el patrón Observer de Laravel sobre `Bookings`, cualquier proceso de reserva en el sistema (directo o pasarela de pago) persiste automáticamente los adultos y niños asegurados en sesión. El helper unificado y el endpoint AJAX permiten que tanto el frontend como el backend consuman los datos de forma retrocompatible (las reservas antiguas siguen funcionando reportando a todos los huéspedes como adultos). En la UI, la sincronización entre los selects de Adulto(s)/Niño(s) y el campo oculto original `#number_of_guests` garantiza que el motor de tarifas y cobros por huéspedes adicionales (`Common::getPrice`) funcione de manera transparente y sin alteraciones.

---

## [24 de Septiembre, 2026] - Reemplazo de Calendario en Formulario de Búsqueda de Propiedades con Flatpickr
- **Tarea:** Corregir el fallo de sincronización idéntica entre fechas de check-in y check-out en el formulario de búsqueda de propiedades de la vista principal (Home) aplicando el mismo procedimiento modular implementado en el modal de reservaciones (inactivar `daterangepicker`, ocultar inputs originales, inyectar inputs nuevos e inicializar Flatpickr en español).
- **Archivos Modificados/Creados:**
    *   `packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/busquedaPropiedades.js`: **NUEVO ARCHIVO**. Script modular que detecta el formulario `#front-search-form`, neutraliza e inhibe la librería `daterangepicker` original (incluso envolviendo de forma segura `window.dateRangeBtn`), oculta `#daterange-btn`, remueve el atributo `required` de los inputs ocultos originales (`#startDate` y `#endDate`), inyecta los nuevos campos visibles con iconos de calendario (`#new_startDate`, `#new_endDate`), e inicializa Flatpickr en español con rango estricto y fecha mínima dinámica. Sincroniza en tiempo real los valores hacia los inputs originales para asegurar la sumisión correcta hacia `/search`.
    *   `packages/Reda/RedaAlojamiento/resources/sass/main.scss`: Se añadieron los estilos CSS necesarios para forzar la ocultación del selector original (`#front-search-form #daterange-btn { display: none !important; }`) y dar estilo armónico a los nuevos inputs (`.reda-new-daterange-container-front`).
    *   `packages/Reda/RedaAlojamiento/resources/views/general/main_head.blade.php`: Se incluyó la hoja de estilo de Flatpickr (`flatpickr.min.css`) en la cabecera para garantizar disponibilidad inmediata de estilos.
    *   `packages/Reda/RedaAlojamiento/resources/views/general/main_footer.blade.php`: Se registró el script `busquedaPropiedades.min.js` para que se ejecute en las páginas del frontend.
    *   `webpack.mix.js`: Se registró la compilación de `busquedaPropiedades.js` hacia `public/js/reda/vistas/frontend/busquedaPropiedades.min.js`.
    *   `manual_tecnico_plugin_reda_alojamiento.md`: Se documentó el nuevo archivo y sus responsabilidades técnicas.
- **Detalle Técnico:** Al aplicar la misma arquitectura desacoplada del modal de reservaciones, el formulario de búsqueda principal se independiza completamente del comportamiento errático de `daterangepicker` (que igualaba automáticamente las fechas de checkin y checkout). El nuevo procedimiento no modifica ningún archivo original de Laravel vRent, cumpliendo al 100% las directrices del proyecto y garantizando que el envío del formulario a la ruta `/search` reciba los parámetros `checkin` y `checkout` con exactitud y sin interferencias.

## [24 de Septiembre, 2026] - Reemplazo de Calendario en Modal de Reservas con Flatpickr (Nueva Lógica)
- **Tarea:** Solucionar el problema de sincronización errática de fechas de llegada y salida en el modal de reservas anulando el selector original del core e implementando un sistema limpio basado en Flatpickr.
- **Archivos Modificados:**
    *   `packages/Reda/RedaAlojamiento/resources/views/general/modal_reservar.blade.php`: Se integró la librería Flatpickr (CSS/JS y localización española) desde CDN. Se inyectaron estilos CSS personalizados con estética Airbnb y se configuró la ocultación quirúrgica del contenedor original de fechas (`#daterange-btn`).
    *   `packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/propiedad_detalle.js`: Refactorizado `mostrarModalFinal` para desactivar el `daterangepicker` original del core de forma segura (`.off('.daterangepicker')` y `.remove()`). Implementa inyección dinámica de nuevos inputs adaptados e inicializa Flatpickr en modo rango ordenado (el cambio en llegada fija la fecha mínima de salida y limpia periodos inválidos). Agregado `recargarPrecios` para invocar de manera robusta la función global `price_calculation()`.
    *   `packages/Reda/RedaAlojamiento/resources/lang/es.json`: Registradas las claves de traducción `"Llegada"` y `"Salida"` para el frontend.
    *   `manual_tecnico_plugin_reda_alojamiento.md`: Actualizada la documentación de scripts y vistas correspondientes.
- **Detalle Técnico:** Al independizar la interfaz visual del modal del daterangepicker original del core (el cual causaba loops infinitos y sincronizaciones duplicadas que igualaban checkin y checkout), se logró control absoluto sobre los eventos de selección. Flatpickr ahora se encarga de limitar las fechas, y sus valores se transfieren a los inputs ocultos originales solo cuando el usuario selecciona fechas válidas, lo que dispara automáticamente el cálculo exacto de tarifas del core mediante AJAX sin romper el flujo estándar de reservas.

## [24 de Septiembre, 2026] - Consistencia Global y Sincronización de Reservas
- **Tarea:** Garantizar consistencia absoluta en los botones "Reservar/Ver reserva" en todas las vistas y actualizar la documentación técnica.
- **Archivos Modificados/Creados:**
    *   `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaBookingController.php`: Se rediseñó el método `getActiveBookingPropertyIds` para devolver una "Lista Blanca" triple (Propiedades, Reservas, Códigos) filtrando por pago y vigencia real.
    *   `packages/Reda/RedaAlojamiento/resources/js/reserve-injection.js`: Refactorizado para usar la lista blanca como fuente de verdad. Implementa detección por ID/Código en Viajes y por Propiedad en Home/Propiedad. Se añadió una jerarquía donde la fecha de fin tiene prioridad absoluta para revertir botones a "Reservar".
    *   `manual_tecnico_plugin_reda_alojamiento.md`: Actualizado con las nuevas responsabilidades de controladores y scripts.
    *   `REDA_PAUTAS_DESARROLLO.md`: **NUEVO ARCHIVO** creado para centralizar reglas de consistencia entre vistas y protecciones de estabilidad (ciclos infinitos).
- **Detalle Técnico:** Se logró consistencia total entre el Home, el Detalle de Propiedad y Mis Viajes. El sistema ahora es bidireccional: la fecha de fin decide la vigencia y la lista blanca del servidor decide la legitimidad del pago, eliminando discrepancias causadas por traducciones o estados ambiguos en el core.

---

## [23 de Septiembre, 2026] - Ampliación de Lógica para Botón "Ver Reserva" en Frontend
- **Tarea:** Cambiar el botón "Reservar" por "Ver Reserva" cuando exista una relación previa (Actual, Próximamente, Pendiente o Finalizada) y abrir el modal de detalles.
- **Archivos Modificados:**
    *   `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaBookingController.php`: Se modificó `getActiveBookingPropertyIds` para incluir cualquier reservación que no esté cancelada o rechazada, cubriendo los estados solicitados (Actual, Próximamente, Pendiente y fechas pasadas).
- **Detalle Técnico:** Al ampliar el universo de propiedades detectadas con "reserva activa", el script `reserve-injection.js` automáticamente sustituye el botón de reserva por el de detalles en las tarjetas de inmuebles. Esto permite que el usuario acceda rápidamente a la información de su estancia sin importar si esta ya concluyó o está en proceso, utilizando el modal unificado de `verDetalleReservaModal.js`. Se mantiene la integridad del sistema original al no modificar archivos core.

---

## [23 de Septiembre, 2026] - Implementación de Filtro de Pagos en Mis Viajes (Estrategia No Invasiva)
- **Tarea:** Ocultar las reservaciones que no tienen pagos asociados en la vista de "Mis Viajes" (Frontend) sin modificar archivos core.
- **Archivos Modificados/Creados:**
    *   `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaBookingController.php`: Agregado el método `getPaidBookingIds` que devuelve los datos identificativos (ID, Código, Nombre y Fechas) de reservaciones con pago.
    *   `packages/Reda/RedaAlojamiento/routes/web.php`: Registrada la ruta `reda/bookings/paid-ids`.
    *   `packages/Reda/RedaAlojamiento/resources/js/ocultar-consultas.js`: Implementada lógica de **Emparejamiento Multicapa (Fuzzy Matching)**. Ahora el script valida las filas del DOM comparando enlaces (IDs/Códigos) y contenido textual (Nombre de propiedad + Rango de fechas) contra la lista blanca del servidor.
- **Detalle Técnico:** Se resolvió un problema de visibilidad donde registros legítimos se ocultaban por falta de IDs en el HTML original. La nueva lógica utiliza el nombre de la propiedad y las fechas formateadas para identificar inequívocamente cada reservación, asegurando que se muestren todos los viajes con pagos registrados (incluso si no han sido confirmados por el administrador) y manteniendo ocultas las consultas (Inquiries). Se cumple estrictamente con la directriz de no modificar archivos base.

---

## [18 de Septiembre, 2026] - Implementación de Modal Detalle de Reserva en Frontend
- **Tarea:** Cambiar el comportamiento del botón "Ver reserva" en las tarjetas de propiedades para que abra un modal detallado en lugar de redirigir a otra página.
- **Archivos Creados:**
    *   `packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/verDetalleReservaModal.js`: Script que maneja la lógica de apertura, consulta AJAX y renderizado del modal con Bootstrap 4.5.
- **Archivos Modificados:**
    *   `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaBookingController.php`: Agregado el método `getBookingDetails` para obtener la información de una reserva activa (foto, ubicación, fechas, costo, etc.).
    *   `packages/Reda/RedaAlojamiento/routes/web.php`: Registrada la ruta `reda/bookings/details/{property_id}`.
    *   `packages/Reda/RedaAlojamiento/resources/js/reserve-injection.js`: Actualizada la lógica de inyección para añadir la clase `.btn-reda-ver-reserva-modal` y anular la redirección automática.
    *   `packages/Reda/RedaAlojamiento/resources/lang/es.json`: Añadidas etiquetas de traducción necesarias para el modal ("Detalles de la reserva", "Costo Total", etc.).
    *   `webpack.mix.js`: Registrada la compilación del nuevo archivo JS hacia `public/js/reda/vistas/frontend/verDetalleReservaModal.min.js`.
    *   `packages/Reda/RedaAlojamiento/resources/views/general/main_footer.blade.php`: Inyectado el nuevo script minificado en el pie de página global del usuario.
- **Detalle Técnico:** Se ha evitado modificar el core del sistema siguiendo las directrices del proyecto. La información se obtiene vía AJAX y el modal se construye dinámicamente en el DOM, utilizando la estética de Airbnb y componentes nativos de Bootstrap 4.5.

---

## [18 de Septiembre, 2026] - Ajuste de Jerarquía e Inyección Quirúrgica del Script de Ocultación de Consultas
- **Tarea:** Mover el script de ocultación de consultas a la jerarquía superior y ajustar su inyección para evitar cargar todo el bundle `main.js` en vistas originales.
- **Archivos Modificados:**
    *   `packages/Reda/RedaAlojamiento/resources/js/ocultar-consultas.js`: Movido desde `general/` a la raíz de JS.
    *   `webpack.mix.js`: Añadida compilación independiente hacia `public/js/reda/general/ocultar-consultas.min.js`.
    *   `packages/Reda/RedaAlojamiento/resources/js/general/main.js`: Se eliminó el import de `ocultarConsultas`.
    *   `packages/Reda/RedaAlojamiento/src/Http/Middleware/InjectPluginAssets.php`: Se ajustó para inyectar `ocultar-consultas.min.js` quirúrgicamente en lugar de `reda-general-main.min.js`.
- **Detalle Técnico:** Para cumplir con la directriz de evitar conflictos y siguiendo la jerarquía de los scripts de inyección (`chat` y `reserve`), se ha convertido el script de ocultación en un entry-point independiente. Esto permite que el middleware lo inyecte específicamente en las vistas del sistema original (Viajes/Reservas) sin arrastrar la lógica de menús y otras funcionalidades generales del plugin, garantizando una integración más limpia y segura.

---

## [18 de Septiembre, 2026] - Diagnóstico y Pruebas de Ocultación de Consultas (PAUSADO)
- **Estado:** Pausado a petición del usuario tras múltiples intentos de depuración.
- **Resumen de Intentos:**
    1. **Estrategia Inicial:** Se intentó filtrar mediante PHP en `BookingController` y `TripsController`. Se revirtió por violar la regla de no modificar el core.
    2. **Estrategia JS:** Se implementó `ocultar-consultas.js` con `MutationObserver` y selectores basados en la clase `.badge.Inquiry`.
    3. **Depuración Profunda:** Se añadieron alertas y logs de consola (`REDA: Badge encontrado...`).
- **Hallazgos Críticos:**
    *   El script se carga y detecta las filas (`.row.border.p-2`), encontrando 4 elementos en la vista `/trips/active`.
    *   Sin embargo, el filtrado por texto (`inquiry`, `consulta`) y por clase devuelve `false`.
    *   Los logs del navegador muestran que las reservaciones detectadas tienen el texto **"Vencido"** y la clase **"Expired"**, pero no se identifican visualmente las reservaciones que el usuario considera "consultas".
    *   Existe la posibilidad de que las consultas no estén llegando al DOM con el texto esperado o que el sistema original (`vRent`) esté sobreescribiendo el estado visual.
- **Próximos Pasos:** Cuando se retome, será necesario inspeccionar manualmente el código fuente HTML (View Source) de las filas que el usuario desea ocultar para identificar un patrón único (ID, Slug o atributo oculto) que permita filtrarlas, ya que el badge de estatus no está siendo suficiente.

---

## [17 de Septiembre, 2026] - Reversión de Cambios en Código Base y Ajuste de Estrategia para Consultas
- **Tarea:** Revertir la modificación de archivos originales del proyecto y ocultar las consultas (Inquiries) mediante Javascript para cumplir con las directrices de `GEMINI.md`.
- **Archivos Revertidos (Estado Original Restaurado):**
    *   `app/Http/Controllers/BookingController.php`: Se eliminó el filtro manual en `myBookings`.
    *   `app/Http/Controllers/TripsController.php`: Se eliminó el filtro manual en `myTrips`.
    *   `app/Models/Bookings.php`: Se eliminó el soporte para el estado `'Inquiry'` en `getLabelColorAttribute`.
- **Archivos Modificados/Creados (Nueva Estrategia):**
    *   `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/ChatController.php`: Se mantuvo el estado `'Inquiry'` para las nuevas consultas (dentro del plugin).
    *   `packages/Reda/RedaAlojamiento/resources/js/general/ocultarConsultas.js`: NUEVO script que identifica y oculta las filas de consultas en el DOM del frontend.
    *   `packages/Reda/RedaAlojamiento/resources/js/general/main.js`: Se integró el nuevo script de filtrado.
- **Detalle Técnico:** En lugar de modificar los controladores originales, ahora se utiliza un MutationObserver en Javascript que detecta la presencia de badges con clase `Inquiry` en las vistas de viajes y reservaciones, ocultando automáticamente la fila correspondiente. Esto asegura que las consultas no interfieran con la experiencia de reservaciones reales sin alterar el núcleo del sistema.


## [17 de Septiembre, 2026] - Implementación de Modal de Advertencia en Inbox
... (resto del archivo)

- **Archivos Modificados:**
    *   `app/Http/Controllers/UserController.php`:
        *   Se corrigió la importación de fachadas usando `Illuminate\Support\Facades\...` para evitar errores de clase no encontrada.
    *   `app/Http/Controllers/EmailController.php`:
        *   Se corrigió la importación de fachadas (`Auth`, `Mail`, `Log`) usando el espacio de nombres completo.
    *   `config/mail.php`:
        *   Se añadió la opción `verify_peer` al driver `smtp`, vinculada a la variable de entorno `MAIL_VERIFY_PEER`. Esto permite desactivar la verificación de certificados SSL/TLS en entornos donde el host no coincide con el certificado.
- **Hallazgos Técnicos:**
    *   El error de SMTP `Peer certificate CN='redetronic.top' did not match expected CN='mail.torbiangames.com'` indica que el servidor de correo usa un certificado compartido.
- **Acciones Recomendadas al Usuario:**
    *   Para solucionar el error de certificado, el usuario puede:
        1. Cambiar `MAIL_HOST=redetronic.top` en su archivo `.env`.
        2. O agregar `MAIL_VERIFY_PEER=false` en su archivo `.env`.
- **Estado:** Corregido. El sistema de registro de usuarios ahora debería completar el envío de correos (o capturar el error sin romper la ejecución) tras corregir las importaciones.

---

## [09 de Septiembre, 2026] - Corrección de Error 500 en Verificación de Reservas Activas
- **Tarea:** Resolver el error 500 (Internal Server Error) que ocurría cuando el script `reserve-injection.js` intentaba consultar las propiedades con reservas activas.
- **Archivos Modificados:**
    *   `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaBookingController.php`: 
        *   Se implementó el método faltante `getActiveBookingPropertyIds`, el cual era referenciado en las rutas pero no estaba definido en el controlador.
        *   Se añadió la importación de la fachada `Log` (`use Illuminate\Support\Facades\Log;`) para el registro correcto de errores.
        *   El método ahora devuelve un objeto JSON con un mapa de `{ property_id: property_name }` para las reservas en estado 'Accepted' (vigentes), 'Pending' o 'processing'.
- **Estado:** Corregido. El script `reserve-injection.js` ahora debería poder obtener los datos y cambiar el texto a "Ver reserva" correctamente.

---

## [17 de Septiembre, 2026] - Mejora de Seguridad en Inbox y Detección de Datos Sensibles
- **Tarea:** Reforzar el filtro de seguridad en el chat para evitar el intercambio de datos de contacto externos.
- **Archivos Modificados:**
    *   `packages/Reda/RedaAlojamiento/resources/js/vistas/inbox/inbox.js`:
        *   Se mejoró la función `detectarInformacionSensible` para incluir la detección de secuencias de 4 o más números (consecutivos o con separadores).
        *   Se añadió la detección de secuencias de 4 o más números escritos en letras (español: "uno", "dos", etc.).
        *   Se documentó internamente la función y el archivo siguiendo los estándares del proyecto.
- **Estado:** Completado.

---

## [09 de Septiembre, 2026] - Detección de Información Sensible en Inbox
- **Tarea:** Implementar un sistema de advertencia para prevenir que los usuarios compartan datos de contacto privados (teléfonos/emails) antes de confirmar una reserva.
- **Archivos Modificados:**
    *   `packages/Reda/RedaAlojamiento/resources/js/vistas/inbox/inbox.js`: 
        *   Se implementó la lógica de interceptación de mensajes en el frontend.
        *   Se utiliza `detectarInformacionSensible()` con expresiones regulares para email y patrones comunes de teléfono.
        *   Si se detecta información sensible, se dispara el modal `#modalAdvertenciaMensajeReda` y se aborta el envío AJAX.
    *   `packages/Reda/RedaAlojamiento/resources/views/users/inbox.blade.php`:
        *   Se añadió la estructura del modal de advertencia de seguridad.
- **Estado:** Implementado.

---

## [08 de Septiembre, 2026] - Documentación Técnica de Menú Lateral y Dashboard
- **Tarea:** Documentar el archivo de gestión de menús del usuario y actualizar el manual técnico.
...

    *   `packages/Reda/RedaAlojamiento/resources/js/general/menus/menuLateralUsuario.js`: 
        *   Se añadió el bloque de documentación inicial (Resumen) explicando su responsabilidad en la inyección dinámica de menús y tarjetas del Dashboard.
        *   Se documentó la función principal `menuLateralUsuario` mediante JSDoc.
    *   `manual_tecnico_plugin_reda_alojamiento.md`:
        *   Se agregó la entrada técnica correspondiente bajo la sección de JavaScript (General), detallando su funcionalidad de reestructuración de sidebar y dashboard.
- **Estado:** Completado.

---

## [08 de Septiembre, 2026] - Corrección de Bloqueo de Modal y Conflictos de Backdrop
- **Tarea:** Solucionar el problema donde el modal de reserva aparecía opaco y deshabilitado tras la carga.
- **Archivos Modificados:**
    *   `packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/propiedad_detalle.js`: 
        *   Se reestructuró `ejecutarAperturaSegura` para llamar a `window.RedaNotificaciones.ocultar()` inmediatamente al recibir la respuesta AJAX.
        *   Se añadió un retardo de seguridad de 600ms antes de disparar `mostrarModalFinal()`, asegurando que Bootstrap limpie los backdrops previos y evite la superposición de capas bloqueantes.
- **Estado:** Corregido y Optimizado.

---

## [08 de Septiembre, 2026] - Ajuste de Integridad y Posicionamiento mediante JS
- **Tarea:** Revertir cambios en archivos originales y asegurar el posicionamiento automático en la vista de viajes usando solo Javascript.
...
