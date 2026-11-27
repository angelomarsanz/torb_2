# Documentación de Archivos del Plugin RedaAlojamiento

## Plantillas de textos y mensaje de pago por transferencia

El permiso `manage_email_template` controla tanto la visibilidad del menú como todas las operaciones administrativas. «Mensaje al anfitrión» contiene dos grupos independientes: **Reserva inicial** (`mensaje_reserva`) y **Confirmación de pago** (`mensaje_anfitrion`). Las opciones se ordenan por `orden` e ID dentro de cada grupo; la primera se selecciona si el huésped no tiene una nota previa. Las plantillas ya existentes permanecen en confirmación. El nuevo grupo empieza vacío y, hasta que el administrador cree opciones, el huésped escribe su mensaje manualmente.

El editor guarda HTML, pero el huésped recibe y envía texto plano: se conservan saltos y viñetas, no los estilos visuales. Las variables son `{anfitrion}`, `{huesped}`, `{alojamiento}`, `{llegada}`, `{salida}`, `{adultos}`, `{ninos}`, `{total_personas}`, `{pais}` y `{metodo_pago}`. El total es la suma del mismo desglose de adultos y niños utilizado en el mensaje. El país y el método corresponden a los valores elegidos en la pantalla inicial y, en confirmación, a los datos guardados en sesión; si no existen se muestra «No seleccionado». En una solicitud sin selectores tampoco se reutilizan datos anteriores. Cuando el huésped modifica el texto, se considera personalizado y los cambios de selección no lo sobrescriben. Para una reserva existente se usa su desglose persistido; el helper preexistente considera a todos adultos si no existe dicho desglose. La nota admite hasta 10.000 caracteres.

En el grupo de reserva inicial, `{llegada}` y `{salida}` se muestran como `dd/mm/AAAA`, tanto si la sesión contiene fechas ISO como si usa el formato regional configurado. No se modifican las fechas de sesión ni de la reserva; el grupo de confirmación de pago conserva su formato anterior. Una fecha no reconocida se informa mediante el error habitual de carga de plantillas.

El formulario inicial `payments/book/{id?}` usa únicamente el grupo de reserva: su campo `message_to_host` se envía por el flujo habitual de creación o actualización de la reserva y aparece en el inbox como mensaje de reserva. La nota de transferencia usa el grupo de confirmación y genera un mensaje separado vinculado a los comprobantes de ese pago. `bookings.note` conserva hasta 191 caracteres para compatibilidad, y el texto íntegro de la nota de transferencia se almacena en `mensajes_pagos` y `messages`. El historial anterior no se reescribe. El inbox usa el ID real del mensaje, no el ID de reserva virtualizado, y autoriza cada enlace comprobando los participantes.

El procesador original coloca las imágenes en `public/uploads/booking`: el nuevo enlace verifica permisos, pero **los archivos siguen en almacenamiento público preexistente**, por lo que su URL original no queda protegida por este cambio. Asimismo, el procesador mueve archivos y el controlador original puede enviar correos antes del cierre de la transacción: una reversión de base de datos no puede deshacer esos efectos externos. Deben revisarse los archivos y correos si se produce un error de pago.

### `packages/Reda/RedaAlojamiento/database/migrations/2026_11_18_133000_crear_plantillas_y_mensajes_de_pago.php`
  `up()` crea las tablas auxiliares `plantillas_textos` y `mensajes_pagos`; sus campos son nulos salvo ID. `down()` impide la reversión si cualquiera contiene registros.

### `packages/Reda/RedaAlojamiento/src/Models/PlantillaTexto.php`
  Modelo de nombre, contenido HTML, contexto y orden de las opciones. Las constantes `CONTEXTO_RESERVA` y `CONTEXTO_PAGO` identifican los grupos sin cambiar tablas existentes.

### `packages/Reda/RedaAlojamiento/src/Models/MensajePago.php`
  Modelo del mensaje íntegro, reserva real, identificador del mensaje y nombres de comprobantes.

### `packages/Reda/RedaAlojamiento/src/Services/PlantillasTextoService.php`
  `opcionesPago($usarDatosPago)` verifica la reserva existente cuando corresponde, obtiene el desglose, suma adultos y niños para `{total_personas}` y filtra las plantillas según la pantalla. En reserva inicial usa `formatearFechaReserva()` para convertir las fechas válidas a `dd/mm/AAAA` sin alterar la sesión; el formato no reconocido produce un error explícito. Para confirmación obtiene el nombre del país y de la pasarela desde las selecciones guardadas en sesión y conserva las fechas originales; devuelve además el texto con las variables de pago para que el selector inicial pueda actualizarlas. `textoPlano()` elimina etiquetas y conserva saltos y viñetas.

### `packages/Reda/RedaAlojamiento/src/Http/Controllers/Admin/PlantillaTextoController.php`
  `autorizar()` comprueba el permiso; `index()` valida el contexto, pagina diez opciones y permite seleccionar una; `guardar()` valida y persiste dentro del contexto elegido; `eliminar()` valida el contexto y elimina únicamente la opción de ese grupo.

### `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/PlantillaPagoController.php`
  `opciones()` devuelve las opciones mediante la estructura JSON del plugin, sin caché; el parámetro `pantalla=confirmacion` selecciona las plantillas de pago y los datos de sesión; la pantalla inicial usa las de reserva. Informa y registra errores.

### `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/PasarelaPagoController.php`
  `makePayment()` valida la nota y las imágenes antes de subirlas; `getAllData()` captura los identificadores y datos de sesión antes de invocar la pasarela original, que los elimina al finalizar. Limita la nota de compatibilidad a 191 caracteres, identifica de forma inequívoca la reserva con los identificadores capturados y registra transaccionalmente el mensaje y los comprobantes. Si falla la transacción, restaura el contexto de la sesión para permitir volver al formulario; en caso de éxito conserva la limpieza original. Otras pasarelas conservan el comportamiento original.

### `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/ComprobantePagoController.php`
  `mostrar()` verifica el mensaje, la reserva, sus participantes y la ruta real antes de entregar un archivo, con cabeceras sin caché.

### `packages/Reda/RedaAlojamiento/resources/views/admin/plantillas/textos.blade.php`
  Vista adaptable con enlaces a los dos grupos independientes, listado paginado, selección y edición de opciones, orden y referencia de variables, incluidos `{pais}`, `{metodo_pago}` y `{total_personas}`; comunica que el mensaje se enviará en texto plano y advierte cuando el pago aún no tiene selección.

### `packages/Reda/RedaAlojamiento/resources/js/admin/vistas/plantillas/textos.js`
  `iniciarEditor()` configura CKEditor con negritas, cursivas, listas y alineación. El archivo gestiona confirmación de eliminación y espera al navegar o enviar.

### `packages/Reda/RedaAlojamiento/resources/js/admin/general/menus/menuLateralAdmin.js`
  `menuLateralAdmin()` transforma el acceso autorizado a correos en «Plantillas» con «Correos» y «Textos», conservando la URL original y el estado activo. Importa el SVG de documentos y utiliza la ruta del recurso generado por Laravel Mix como máscara, para heredar el color del enlace.

### `packages/Reda/RedaAlojamiento/resources/js/general/iconos/plantillas.svg`
  Ícono decorativo de documentos para el menú «Plantillas». Se integra mediante el JavaScript administrativo y se genera en `public/images` durante la compilación.

### `packages/Reda/RedaAlojamiento/resources/js/vistas/pago/frontend/pagos.js`
  `cargarPlantillas($campo, $boton)` consulta el grupo correspondiente con animación de espera para `message_to_host` en `#checkout-form` y para `note` en el formulario de transferencia. Elige la primera cuando el campo está vacío, conserva los textos recuperados o escritos mientras carga y permite volver al personalizado. Las opciones se recalculan al cambiar país o método de pago; solo se actualiza el campo si coincide con la opción seleccionada sin editar. Si falla la consulta muestra el error y deja disponible la escritura manual. `validarFormularioPago()` comprueba exclusivamente el comprobante y la longitud de la nota de transferencia; el formulario inicial conserva su validación original.
  `ajustarAlturaMensaje($campo)` adapta la altura de los mensajes de selección de pago y comprobante a su contenido tanto en móvil como en escritorio. Se recalcula al inicializar, cargar o cambiar la plantilla, editar el mensaje, cambiar país o método de pago, restaurar la página o cambiar el tamaño de pantalla; no altera el texto ni la validación.

### `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaInboxController.php`
  `adjuntarComprobantes()` vincula los comprobantes por ID de mensaje. `index()` y `message()` incorporan el vínculo al render inicial y a la carga dinámica.

### `packages/Reda/RedaAlojamiento/resources/views/users/messages.blade.php`
  Muestra la nota escapada con saltos y enlaces a los comprobantes asociados al mensaje nuevo.

### `packages/Reda/RedaAlojamiento/routes/web.php`
  Registra las rutas administrativas, de opciones para el pago y de comprobantes autenticados.

### `packages/Reda/RedaAlojamiento/src/RedaAlojamientoServiceProvider.php`
  En `boot()` sustituye únicamente las acciones de finalización y callback de pasarela por el controlador extendido del plugin.

### `packages/Reda/RedaAlojamiento/resources/sass/admin/main.scss` y `packages/Reda/RedaAlojamiento/resources/sass/main.scss`
  Definen respectivamente la distribución adaptable de tres columnas del editor, la máscara del ícono de plantillas que hereda el color del menú, y el ajuste de saltos y palabras del mensaje en inbox. El segundo es el SCSS frontend realmente compilado en este proyecto.

### `packages/Reda/RedaAlojamiento/resources/lang/es.json`
  Agrega los textos en español de la interfaz y los errores de plantillas y pago.

### `webpack.mix.js`
  Integra el nuevo JavaScript administrativo en la compilación del plugin. La migración, compilación y pruebas funcionales se realizan exclusivamente en Vesta.

## Información adicional en mediaciones

Solo el agente actualmente asignado puede crear solicitudes y reintentar los correos pendientes de su mediación. Una solicitud acepta demandante, demandado o ambos, texto, plazo entero positivo y un complemento opcional JPG, PNG, WebP o PDF de hasta 10 MB. Al guardar, el estado pasa a `Esperando respuesta` y el paso a `Solicitud de información adicional`. Las solicitudes ya vencidas no se reactivan; las que siguen vigentes reciben el plazo de la nueva solicitud. Una carga admite hasta cinco archivos de 10 MB cada uno y el estado continúa en espera.

Las rutas de los documentos se registran en tablas auxiliares; los archivos se almacenan en el disco `local`, bajo `storage/app/reda/mediaciones/informacion`, fuera de `storage/app/public`. Los complementos del agente son accesibles a los destinatarios de la solicitud; los documentos de respuesta solo a quien los cargó y al personal de soporte autorizado. La autorización se comprueba al descargar, y el plazo se verifica al abrir el formulario y nuevamente en la transacción de carga. El enlace por correo conduce al inbox después de autenticar al usuario. Se registra una alerta individual y un correo para el agente asignado en el momento de la carga.

Los correos pendientes se guardan en la base de datos y el agente asignado puede reintentarlos desde la tarjeta. El envío es síncrono; no hay reintento automático. Si el servidor falla después de enviar un correo pero antes de registrar su éxito, un reintento podría duplicarlo. El reinicio de mediaciones queda bloqueado mientras existan estas solicitudes para no borrar referencias ni dejar archivos privados huérfanos: su eliminación requiere diseñar previamente una limpieza segura y autorizada.

### `packages/Reda/RedaAlojamiento/database/migrations/2026_11_09_100000_crear_solicitudes_informacion_mediaciones.php`
  `up()` crea las tablas de solicitudes, destinatarios, archivos y correos; sus columnas nuevas son nulas salvo las claves ID. `down()` se niega a revertir mientras existan registros y elimina solo tablas vacías.

### `packages/Reda/RedaAlojamiento/src/Models/Disputa/SolicitudInformacion.php`
  Modelo de solicitudes y conversión de `vence_en` a fecha.

### `packages/Reda/RedaAlojamiento/src/Models/Disputa/DestinatarioInformacion.php`
  Modelo de destinatarios y vínculo con el mensaje privado.

### `packages/Reda/RedaAlojamiento/src/Models/Disputa/ArchivoInformacion.php`
  Modelo de archivos complementarios y respuestas privadas.

### `packages/Reda/RedaAlojamiento/src/Models/Disputa/CorreoInformacion.php`
  Modelo de correos pendientes y enviados, incluyendo el usuario que originó una carga.

### `packages/Reda/RedaAlojamiento/src/Http/Controllers/Disputa/InformacionAdicionalController.php`
  `crear()` valida roles, participantes y archivos, bloquea la disputa, renueva plazos vigentes y crea solicitud, mensajes y correos en una transacción. `acceso()` conserva el destino tras el login. `consultar()` y `cargar()` autorizan al destinatario y comprueban el plazo; `cargar()` registra documentos, alerta y correo de manera transaccional. `archivo()` y `archivoAdmin()` entregan únicamente documentos autorizados con cabeceras privadas. `reintentar()` reprocesa los correos pendientes; `enviarCorreos()` los bloquea individualmente, registra errores y conserva pendientes. `guardarArchivo()` utiliza el disco local privado y registra rutas para su limpieza ante errores de transacción; `autorizarAdmin()`, `autorizarDestinatario()` y `responder()` centralizan permisos y respuestas.

### `packages/Reda/RedaAlojamiento/resources/views/emails/informacion_mediacion.blade.php`
  Correo para destinatarios de solicitudes o para el agente cuando recibe documentos; usa el enlace autenticado o la vista de alertas.

### `packages/Reda/RedaAlojamiento/resources/js/vistas/inbox/informacionAdicional.js`
  `abrirSolicitud()` consulta autorización y vencimiento, presenta el modal, valida cantidades y tamaños y envía los archivos; `mostrarError()` presenta errores sin interpretar HTML. El enlace desde correo abre el mismo modal.

### `packages/Reda/RedaAlojamiento/resources/js/vistas/inbox/inbox.js`
  Importa el módulo de información adicional y evita abrir simultáneamente el aviso de privacidad cuando se entra desde un correo.

### `packages/Reda/RedaAlojamiento/resources/views/users/messages.blade.php`
  Presenta el botón «Cargar archivos solicitados» solo cuando el controlador vincula el mensaje al destinatario autenticado.

### `packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaInboxController.php`
  `index()` y `message()` validan la reserva seleccionada, preservan la privacidad de mensajes y vinculan las solicitudes por destinatario con los mensajes correspondientes.

### `packages/Reda/RedaAlojamiento/src/Http/Controllers/Disputa/MensajeController.php`
  `getMessages()` exige que el usuario participe en la reserva antes de entregar el historial.

### `packages/Reda/RedaAlojamiento/src/Http/Controllers/Admin/Disputa/MensajeController.php`
  `getMessages()` verifica el rol y acceso a la mediación, evita mezclar solicitudes privadas de otras mediaciones y no marca como leídos los avisos destinados a participantes.

### `packages/Reda/RedaAlojamiento/src/Http/Controllers/Admin/Disputa/DisputaController.php`
  `obtenerDisputasPaginadas()` incorpora archivos privados autorizados, cantidad de correos pendientes y localización de la mediación en el filtro de espera.

### `packages/Reda/RedaAlojamiento/src/Http/Controllers/Disputa/DisputaController.php`
  `obtenerDisputasPaginadas()` muestra los archivos privados propios, filtra avisos ajenos del contador y reconoce el estado persistido en español.

### `packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/indexDisputas.js`
  `abrirSolicitudInformacion()` construye el formulario, controla asignación y muestra la confirmación y eventual renovación de plazos. Los manejadores cambian a la pestaña de espera y permiten reintentar correos. `generarListaAdjuntosHtml()` y `abrirMediaViewer()` reconocen y muestran archivos privados, y el chat escapa el texto de los mensajes.

### `packages/Reda/RedaAlojamiento/resources/js/vistas/disputa/disputas/indexDisputas.js`
  `generarListaAdjuntosHtml()` y `abrirMediaViewer()` muestran archivos propios, incluso PDF con rutas autenticadas; `renderizarMensajes()` escapa el contenido.

### `packages/Reda/RedaAlojamiento/resources/js/admin/vistas/alerta/indexAlertas.js`
  `renderizarAlertas()` escapa textos y datos de usuario antes de insertarlos en el HTML.

### `packages/Reda/RedaAlojamiento/src/Services/ReinicioMediacionesService.php`
  `reiniciar()` incluye las solicitudes de información en el reinicio, independientemente de su estado. Guarda en una cola durable las rutas privadas y elimina sus registros y mensajes inequívocos en la misma transacción que las mediaciones.

### `packages/Reda/RedaAlojamiento/src/Http/Controllers/Admin/Disputa/ReinicioMediacionesController.php`
  `reiniciar()` comunica el resultado exitoso con una advertencia si quedaron adjuntos pendientes de eliminar.

### `packages/Reda/RedaAlojamiento/routes/web.php`
  Declara rutas de consulta, carga y lectura autenticadas, acceso desde correo, creación administrativa y reintento de correo.

### `packages/Reda/RedaAlojamiento/resources/lang/es.json`
  Agrega textos del flujo, validaciones, vencimiento, reintento y advertencia de conservación.

## Reinicio de mediaciones de prueba

Herramienta destructiva y deshabilitada por defecto. Solo se muestra y ejecuta con `REDA_REINICIO_MEDIACIONES=true`, `APP_ENV` igual a `local`, `development` o `dev`, sesión administrativa y rol ID 1. No cambiar `APP_ENV` global de un servidor para forzar el acceso. El administrador debe disponer de un respaldo reciente y coordinar una ventana sin usuarios ni procesos de mediación o chat; no existe bloqueo automático de las demás operaciones. No elimina correos ya entregados.

- **packages/Reda/RedaAlojamiento/database/migrations/2026_11_07_000000_crear_tablas_reinicio_mediaciones.php**
  `up()` crea las tablas auxiliares `mensajes_de_mediacion` y `adjuntos_mediacion_pendientes`; `down()` impide revertir mientras existan marcadores o adjuntos pendientes. La migración debe aplicarse antes de desplegar los controladores y el servicio de mensajes modificados.
- **packages/Reda/RedaAlojamiento/src/Models/Disputa/MensajeDeMediacion.php**
  Modelo del marcador inequívoco de origen de cada mensaje nuevo de mediación.
- **packages/Reda/RedaAlojamiento/src/Models/Disputa/AdjuntoMediacionPendiente.php**
  Modelo para rutas y huellas SHA-256 de archivos pendientes de eliminar tras confirmar la transacción.
- **packages/Reda/RedaAlojamiento/src/Services/ReinicioMediacionesService.php**
  `usuariosElegibles()` filtra cuentas inactivas, no borradas y con antecedentes de segundo aviso o restricción. `reiniciar()` usa un bloqueo de MySQL para impedir reinicios simultáneos, valida la selección y elimina transaccionalmente disputas, solicitudes, destinatarios, registros de archivos y correos de información adicional, alertas de los tres tipos de mediación, avisos y mensajes identificados. Reactiva solo las cuentas seleccionadas y conserva reservas, ajustes y mensajes históricos ambiguos. `normalizarRuta()` y `archivoSeguro()` limitan las rutas públicas y privadas y rechazan enlaces simbólicos. `encolarAdjunto()` guarda rutas y huellas SHA-256 en la tabla de pendientes, incluso si la verificación falla; `procesarAdjuntos()` elimina después del commit únicamente archivos con huella verificable, conserva los fallos y los registra. Los archivos sin huella requieren revisión manual: no se eliminan automáticamente. Si existe `disputas_respaldo`, se conservan los adjuntos públicos por precaución y se informa el número. El borrado de archivos no es atómico con la base de datos; durante mantenimiento un reinicio posterior reintenta los pendientes con huella.
- **packages/Reda/RedaAlojamiento/src/Http/Controllers/Admin/Disputa/ReinicioMediacionesController.php**
  `autorizar()` comprueba bandera, entorno y rol real en cada petición; `index()` presenta las cuentas elegibles y `reiniciar()` valida la frase exacta y selección, registra el resultado y devuelve el formato estándar. Cuando los datos fueron reiniciados pero hay adjuntos pendientes, responde éxito con una advertencia y el número de pendientes; no atribuye todos los fallos exclusivamente a permisos.
- **packages/Reda/RedaAlojamiento/resources/views/admin/disputa/reinicio_mediaciones.blade.php**
  Presenta advertencias sobre solicitudes y adjuntos, selección voluntaria de cuentas, confirmación textual y modal adaptable. El antecedente no prueba por sí solo el motivo de inactividad; no se ofrecen cuentas borradas lógicamente.
- **packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/reinicioMediaciones.js**
  `solicitarReinicio()` normaliza la petición AJAX; el manejador exige la frase, impide cerrar el modal durante la operación, muestra espera y presenta el resultado con los contadores de solicitudes y archivos pendientes. Tras un error se debe actualizar la página y revisar el registro antes de reintentar.
- **packages/Reda/RedaAlojamiento/src/Http/Controllers/Disputa/MensajeController.php** y **packages/Reda/RedaAlojamiento/src/Http/Controllers/Admin/Disputa/MensajeController.php**
  `store()` guarda el mensaje y el marcador dentro de la misma transacción; el observador sigue creando los metadatos de remitente. Esto identifica los mensajes futuros creados desde las pantallas de mediación, no el chat normal.
- **packages/Reda/RedaAlojamiento/src/Services/MediacionAlertaService.php**
  `enviarMensajeBuzon()` guarda transaccionalmente el aviso privado, sus metadatos y el marcador de mediación.
- **packages/Reda/RedaAlojamiento/config/reda-alojamiento.php**, **packages/Reda/RedaAlojamiento/routes/web.php** y **packages/Reda/RedaAlojamiento/resources/views/admin/general/main_footer.blade.php**
  Declaran la bandera, las dos rutas y los datos de disponibilidad para el menú. La verificación del controlador es independiente de la visibilidad.
- **packages/Reda/RedaAlojamiento/resources/js/admin/general/menus/menuLateralAdmin.js** y **packages/Reda/RedaAlojamiento/resources/js/admin/general/main.js**
  Insertan el submenú «Pruebas aplicación» cuando el servidor lo permite e incluyen el controlador de la pantalla en el bundle administrativo existente. Al desplegarlo, el menú desplaza solamente `.sidebar-wrapper` si el enlace «Reiniciar mediaciones» queda por debajo de su área visible; mantiene `aria-expanded` sincronizado.
- **packages/Reda/RedaAlojamiento/resources/lang/es.json**
  Incluye los textos nuevos del modal, las advertencias y los resultados. Para el selector de pasos de mediaciones incluye su etiqueta, el mensaje para pasos no disponibles y los mensajes de la transición a revisión.

## Modificaciones en Archivos del Core (Originales)
*Excepciones delimitadas con comentarios de Inicio/Fin para el plugin REDA.*

### Controladores (Admin)
- **app/Http/Controllers/Admin/SettingsController.php**
  Se modificó el método `email` para permitir el guardado manual del campo `email_status`. La lógica original dependía del éxito de un envío de prueba; con este cambio, se respeta la selección manual del administrador ("Active"/"Inactive") permitiendo la persistencia del sistema de correos incluso si la validación automática falla temporalmente.

### Controladores (General)
- **app/Http/Controllers/EmailController.php**
  Se inyectaron logs de depuración en el método `welcome_email` para rastrear la configuración de SMTP y el estado del envío. Se actualizó el método `sendPhpEmail` para redirigir errores y confirmaciones al log de Laravel en lugar de imprimirlos en pantalla (`echo`), mejorando la estabilidad de las respuestas AJAX.

### Vistas (Admin)
- **packages/Reda/RedaAlojamiento/resources/views/admin/experiencia/tipos_de_negocios/opciones_tipos_de_negocios.blade.php**
  Vista administrativa para consultar, agregar, editar y eliminar tipos de negocios. Conserva su tabla para escritorio, sus tarjetas móviles y el modal `#modal-category`; el archivo `resources/sass/admin/main.scss` aplica espaciado y bordes suaves aislados a esta pantalla y al modal. Usa el mensaje de ayuda del identificador desde las traducciones JSON, explicando que los espacios se convierten en guiones bajos y que no se permiten acentos ni caracteres especiales. También mantiene el título del listado, el botón de alta y el título inicial del modal con la terminología «tipo de negocio», y exporta `RedaAlojamientoJson` para los textos dinámicos.
- **packages/Reda/RedaAlojamiento/resources/views/admin/experiencia/configuracion_planes.blade.php**
  Vista administrativa para configurar los requisitos de planes destacados y administrar los planes de negocio. Su presentación recibe espaciado lateral y padding adaptable desde `resources/sass/admin/main.scss`, con paneles ligeramente redondeados y estilos limitados a esta pantalla; la vista y su lógica JavaScript permanecen sin cambios.
- **resources/views/admin/settings/email.blade.php**
  Inyección de un campo `select` para la columna `email_status`. Esta modificación habilita la interfaz de usuario para que el administrador pueda forzar el estado del sistema de correos desde el panel de configuración de email.

### Traducciones (Admin)
- **packages/Reda/RedaAlojamiento/resources/lang/es.json**
  Contiene las etiquetas y mensajes en español para la gestión administrativa de tipos de negocios, incluyendo los títulos de alta/edición, confirmación de eliminación y notificaciones exitosas. La vista Blade y su JavaScript consumen estas cadenas mediante `__()` y `window.RedaAlojamientoJson`, sin depender de las antiguas traducciones PHP de «categoría».

### Configuración
- **config/mail.php**
  Se añadió la opción `verify_peer` al transport `smtp` vinculada a la variable de entorno `MAIL_VERIFY_PEER`. Esta modificación permite desactivar opcionalmente la verificación de certificados SSL/TLS desde el archivo `.env`, facilitando la conexión a servidores SMTP con certificados compartidos o discrepancias de nombre de host (CN).

## Alojamientos (Hospedajes)

### JavaScript (Vistas)
- **packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/propiedad_detalle.js**
  En `prepararModalReserva()`, ambos calendarios usan `disableMobile: true` para mantener el calendario de Flatpickr también en celular. Así, cuando no hay fechas ocupadas, no se sustituye el calendario de llegada por el selector nativo, que carece de `calendarContainer` y provocaba un error en `onReady` antes de abrir el formulario. La selección de fechas ocupadas y las reglas de salida no cambian.
  Gestiona el sistema de reserva en modal y botones flotantes para la vista de detalle de propiedad (property.single). Su método `normalizarTarjetaAnfitrion()` añade clases del plugin a la tarjeta resumen superior y a la imagen del anfitrión para conservar el avatar cuadrado en móvil. También oculta el sidebar original, inyecta el botón flotante con animación y maneja la apertura del modal mediante el hash `#reservar`. Incluye una verificación proactiva de reservas activas para prevenir duplicidades. Antes de abrir el formulario consulta los intervalos ocupados; `prepararModalReserva()` busca desde hoy el primer día libre y posiciona allí el calendario, permitiendo reservar el mismo día cuando esté disponible. Los rangos reservados siguen bloqueados al avanzar por meses posteriores, y las salidas se validan para impedir cruces entre estancias, conservando el día de checkout como límite válido. Los campos visibles usan el formato de Flatpickr, mientras que los inputs originales `#startDate` y `#endDate` reciben fechas ISO (`Y-m-d`) para evitar interpretaciones ambiguas por la preferencia regional de `setDateForDb()`. El checkout inicia vacío y `limpiarEstadoPrecios()` oculta los avisos y resultados heredados hasta contar con una estancia completa. `vigilarRespuestasPrecio()` compara las fechas de cada respuesta AJAX del core con las fechas actualmente elegidas: descarta el estado obsoleto si aún no hay salida y vuelve a solicitar el cálculo cuando la respuesta corresponde a una selección anterior. Esto corrige los avisos residuales de rangos iniciales, pero no sustituye la disponibilidad persistida del core. La auditoría encontró bloqueos residuales de propiedad 3 (31/10–20/11 y 21/11) vinculados por fechas y marcas de tiempo a los bookings 54 y 59 borrados lógicamente, y un bloqueo de propiedad 1 (12/11/2026) coincidente con el booking 65 eliminado lógicamente. El usuario confirmó en respaldos posteriores que las 22 fechas de propiedad 3 y la fecha 12/11 de propiedad 1 quedaron en estado `Available`. No se deben borrar bloqueos solo por solapamiento: `property_dates` no almacena el ID del booking. Las noches 26–29/11 de propiedad 3 vinculadas al booking 73 vigente y las fechas futuras de reservas activas permanecen bloqueadas. Si la consulta de disponibilidad falla, `notificarErrorFechasOcupadas()` informa al usuario y no abre el formulario. `restablecerEnvioAlRegresar()` escucha `pageshow` y, si el navegador restaura la página desde BFCache (`event.persisted`), habilita `#save_btn` y oculta su `.spinner`, preservando las fechas y los demás datos del formulario que el navegador restauró.
  **Actualización Desglose Huéspedes (Airbnb-style):** Oculta el selector `#number_of_guests` original dentro de la modal e inyecta dos selectores dinámicos para "Adulto(s)" y "Niño(s)". Sincroniza dinámicamente las opciones para respetar la capacidad máxima (`accommodates`), actualiza `#number_of_guests` con el total y gatilla el recálculo de precios y tarifas en tiempo real.

- **packages/Reda/RedaAlojamiento/resources/sass/main.scss**
  En la vista móvil de detalle de propiedad, mantiene el avatar de la tarjeta resumen superior cuadrado de 90 × 90 px con `object-fit: cover`. Las reglas se limitan a las clases que `propiedad_detalle.js` añade por JavaScript a esa tarjeta y a la imagen del anfitrión. También da un fondo gris, texto atenuado y cursor no disponible a los días ocupados que Flatpickr deshabilita en la modal de reservas. La clase temporal `reda-exito-mediacion` aumenta el título y el mensaje de la confirmación de mediación, sin alterar los demás avisos.

- **packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/desgloseHuespedes.js**
  Script encargado de actualizar dinámicamente y de forma no invasiva todas las vistas de la aplicación donde se muestra el conteo de huéspedes, sustituyendo etiquetas monolíticas ("X Guests") por el desglose detallado "X Adulto(s), Y Niño(s)":
  1. `/payments/book/{id}`: Actualiza la tarjeta de resumen e inyecta los campos ocultos `adultos` y `ninos` en `#checkout-form`.
  2. `/booking/requested`: Consulta vía AJAX a `huespedes-info` mediante el código de reserva y actualiza encabezados y resúmenes.
  3. `/booking/{id}`: Consulta vía AJAX y actualiza el detalle para el anfitrión.
  4. `/admin/bookings/detail/{id}`: Actualiza el renglón de noches y huéspedes en el panel de administración.
  5. `/my-bookings`: Enriquece el indicador de camas y huéspedes en las tarjetas de listado de viajes.

- **packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/verDetalleReservaModal.js**
  Script para abrir el modal con los detalles de una reserva activa del usuario. Se actualizó para mostrar en el renglón de "Huéspedes" el desglose detallado de adultos y niños recuperado desde `RedaBookingController@getBookingDetails`.

- **packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/busquedaPropiedades.js**
  Gestiona el control y selección de fechas en el formulario de búsqueda de propiedades (`#front-search-form`) en la vista principal (Home) y en el buscador de propiedades. Desactiva y neutraliza de forma segura la inicialización de `daterangepicker` del core para evitar que sobreescriba y sincronice involuntariamente las fechas de check-in y check-out con el mismo valor. Oculta el contenedor `#daterange-btn` e inyecta dinámicamente nuevos campos de fecha independientes (`#new_startDate`, `#new_endDate`) controlados por la librería **Flatpickr** con localización en español. Sincroniza en tiempo real los valores seleccionados con los campos originales (`#startDate`, `#endDate`) para que el envío del formulario procese correctamente los parámetros de búsqueda hacia `/search`.

### Vistas (Usuario/Frontend y Admin)
- **packages/Reda/RedaAlojamiento/resources/views/general/modal_reservar.blade.php**
  Define la estructura HTML de la modal de reservación del plugin RedaAlojamiento. Carga e integra la librería de calendarios Flatpickr (CSS/JS) desde CDN, inyecta estilos Airbnb-style para los nuevos campos de selección de fechas (Llegada y Salida) y para el desglose de huéspedes (Adultos y Niños), y oculta quirúrgicamente los selectores de rango y de huéspedes originales del core para evitar conflictos.

- **packages/Reda/RedaAlojamiento/resources/views/general/main_footer.blade.php**
  Archivo maestro del pie de página del usuario. Se actualizó para inyectar en `window.RedaSessionHuespedes` los valores de sesión de adultos y niños, y cargar el script `desgloseHuespedes.min.js`.

- **packages/Reda/RedaAlojamiento/resources/views/admin/general/main_footer.blade.php**
  Archivo maestro del pie de página del administrador. Se actualizó para cargar `desgloseHuespedes.min.js`.

### Base de Datos y Modelos
- **packages/Reda/RedaAlojamiento/database/migrations/2026_09_25_000000_crear_tabla_reserva_huespedes.php**
  Migración (no ejecutada en local) que define la tabla auxiliar `reserva_huespedes` con claves `id`, `reserva_id`, `adultos`, `ninos` y marcas de tiempo, cumpliendo con la nomenclatura en español y valores nulos por directriz REDA.

- **packages/Reda/RedaAlojamiento/src/Models/Reserva/ReservaHuesped.php**
  Modelo Eloquent para la tabla auxiliar `reserva_huespedes`. Ofrece relación con `App\Models\Bookings` y atributo calculado `total_huespedes`.

### Observadores y Helpers
- **packages/Reda/RedaAlojamiento/src/Observers/ReservaObserver.php**
  Observador registrado en `RedaAlojamientoServiceProvider` para el modelo original `App\Models\Bookings`. Al crearse una reserva (`created`), intercepta los valores de adultos y niños asegurados en sesión o en la petición y los persiste tanto en la tabla original `booking_details` (`field = 'adultos'`, `field = 'ninos'`) como en la tabla auxiliar `reserva_huespedes` (si está migrada). Los atributos de `BookingDetails` se asignan explícitamente porque el modelo original no permite asignación masiva; un fallo se registra en el log de Laravel.

- **packages/Reda/RedaAlojamiento/src/Helpers/helpers.php**
  Se incorporó la función helper `reda_obtener_desglose_huespedes($booking)`. Implementa una jerarquía de consulta: primero `reserva_huespedes`, luego `booking_details` y como fallback retrocompatible para reservas antiguas asigna el total de `guest` a Adultos y `0` a Niños. Retorna array asociativo con `adultos`, `ninos`, `total` y `texto` formateado.

### Controladores
- **packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaPaymentController.php**
  Controlador extendido para gestionar el flujo de pagos y redirecciones de reserva. Asegura que los datos de la reserva se mantengan persistentes durante el proceso de login y valida si el usuario ya posee una reservación vigente antes de permitir el acceso al formulario de reserva, redirigiendo con alertas personalizadas si es necesario. Se actualizó para asegurar en sesión `payment_adultos` y `payment_ninos`.

- **packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaBookingController.php**
  Controlador para consultas de reservas. Se enriqueció `getBookingDetails` para retornar `adultos`, `ninos` y `huespedes_desglose`. Se incorporó el método `getHuespedesInfo` que devuelve el desglose de huéspedes por `booking_id` o `code` para consumo de las vistas AJAX. El método `obtenerFechasOcupadas($idPropiedad)` consulta las reservas `Accepted`, `Pending`, `Processing` y `processing` con fecha de salida futura, excluye estados no ocupantes y retorna intervalos `[llegada, salida)` para que el día de checkout permanezca disponible como llegada de otra reserva.

### Rutas
- **packages/Reda/RedaAlojamiento/routes/web.php**
  La ruta autenticada `GET /reda/bookings/fechas-ocupadas/{idPropiedad}` dirige a `RedaBookingController@obtenerFechasOcupadas` y entrega al calendario del detalle de propiedad los intervalos de noches ocupadas. `POST /admin/reda/disputas/poner-en-revision` dirige a `DisputaController@ponerEnRevision` bajo el grupo administrativo existente.

- **packages/Reda/RedaAlojamiento/resources/lang/es.json**
  Incluye los textos de respuesta del endpoint de disponibilidad y el mensaje mostrado cuando no se pueden consultar las fechas; en ese caso el modal de reserva permanece cerrado.

## Mediaciones (Disputas)
... (resto del archivo)

- **packages/Reda/RedaAlojamiento/resources/views/admin/disputa/index.blade.php**
  Vista de administración para el listado y gestión de mediaciones (Disputas). Esta interfaz permite a los administradores visualizar todos los casos de mediación, filtrarlos por estado, realizar seguimiento del progreso mediante una línea de tiempo y acceder a los chats de comunicación entre las partes involucradas.

### JavaScript (Vistas)
- **packages/Reda/RedaAlojamiento/resources/js/vistas/disputa/disputas/indexDisputas.js**
  Controlador de la vista de índice de disputas (Mediaciones). Este archivo gestiona la lógica del dashboard de mediaciones, incluyendo la carga paginada por estados, la renderización de la lista de casos, la línea de tiempo de progreso, el visor de medios (imágenes/PDF) y el chat específico de la mediación. `generarTimelineHtml()` identifica los pasos por su valor español persistido o por su etiqueta traducida, manteniendo compatibilidad con valores existentes. Se eliminó una línea textual ajena al código dentro de `getFullUrl()` que impedía compilar el archivo.

### JavaScript (Administración)
- **packages/Reda/RedaAlojamiento/resources/js/admin/vistas/experiencia/opcionesTipoDeNegocios.js**
  Gestiona el alta, edición y eliminación de tipos de negocios en la vista administrativa. Utiliza las traducciones JSON para el título del modal, la confirmación de borrado y las notificaciones de resultado; los mensajes exitosos de crear y editar se muestran como «Tipo de negocio guardado correctamente», y el borrado como «Tipo de negocio eliminado correctamente». Traduce el mensaje legado de elemento inexistente al término actual sin cambiar rutas, nombres internos ni datos.

- **packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/indexDisputas.js**
  Controlador de administración para el panel de mediaciones (Disputas). Este archivo gestiona la lógica del dashboard de mediaciones para el administrador, permitiendo filtrar por estados, visualizar el progreso en la línea de tiempo, gestionar mensajes entre las partes y visualizar documentos adjuntos (fotos/PDFs). Identifica el paso por su valor español o etiqueta traducida y sincroniza la línea de tiempo tras asignar o tomar una mediación.

- **packages/Reda/RedaAlojamiento/resources/js/general/ocultarConsultas.js**
  Script responsable de ocultar dinámicamente las consultas (Inquiries) y reservaciones sin pago asociado en las vistas de "Mis Viajes" y "Mis Reservas". Utiliza un MutationObserver optimizado con técnicas de *debouncing* y banderas de control para evitar ciclos infinitos de retroalimentación con otros scripts. Implementa una lógica de emparejamiento multicapa (ID, Código, Nombre y Fechas) contra una lista blanca obtenida del servidor para garantizar que solo se muestren registros con pagos legítimos.

- **packages/Reda/RedaAlojamiento/resources/js/general/reserve-injection.js**
  Script encargado de inyectar o actualizar el botón de "Reservar" / "Ver reserva" en las tarjetas de inmuebles de toda la aplicación (Home, Detalle de Propiedad y Mis Viajes). Utiliza una lógica centralizada basada en una "Lista Blanca" proporcionada por el servidor, asegurando consistencia absoluta entre las tres vistas. Implementa una jerarquía de decisión donde la vigencia de la fecha tiene prioridad máxima, permitiendo revertir botones a "Reservar" si la estancia ya concluyó. Incluye protecciones contra recursividad infinita mediante *debouncing*.

- **packages/Reda/RedaAlojamiento/resources/js/general/mensajes.js**
  Script de integración general para funcionalidades de mediación. Este archivo se encarga de inyectar la lógica de mediación en vistas preexistentes del proyecto principal, como el Inbox (chat general) y la barra lateral de detalles de reservación. Permite verificar disputas, solicitar nuevas mediaciones y enriquecer el chat original con información del plugin Reda. `guardarMediacionReda()` conserva el `mensaje_usuario` recibido en errores HTTP, incluido el 403 de acciones restringidas. En `configurarEventosModal()` el resultado de la solicitud reemplaza el contenido del modal de espera sin ejecutar antes su cierre diferido; la notificación permanece abierta hasta que el usuario la cierre. En caso de éxito se aplica la clase `reda-exito-mediacion` para ampliar su texto y se retira al cerrar el modal compartido. Si no está disponible `notificar()`, se oculta la espera y se usa una alerta alternativa.

- **packages/Reda/RedaAlojamiento/resources/js/general/menus/menuLateralUsuario.js**
  Script para la gestión y reestructuración del menú lateral y dashboard del usuario. Este archivo se encarga de inyectar dinámicamente las opciones del plugin REDA en el sidebar (escritorio y móvil) y añadir la tarjeta de "Negocios" en el tablero principal (Dashboard). Implementa la lógica de submenús colapsables para Alojamientos y Reseñas, e integra el contador dinámico de mediaciones activas.

- **packages/Reda/RedaAlojamiento/resources/js/general/menus/menuPrincipal.js**
  Script para inyectar y gestionar el menú principal REDA en el navbar. Agrega accesos rápidos a Alojamientos, Comercios y Mensajes (Inbox) con contador dinámico de notificaciones no leídas obtenido mediante la función AJAX estandarizada.

- **packages/Reda/RedaAlojamiento/resources/js/general/iconos/notificacionesSvg.js**
  Ícono SVG para la campana de notificaciones. Diseño de campana convencional con forma de pera, ajustado en dimensiones y grosor de trazo para mantener la consistencia visual con los íconos de Alojamientos y Comercios del menú principal.

### JavaScript (AJAX)
- **packages/Reda/RedaAlojamiento/resources/js/general/ajax/obtenerConteoNoLeidos.js**
  Función AJAX para obtener el número de mensajes no leídos del usuario autenticado. Implementa el patrón de Promesas, maneja animaciones de espera mediante notificaciones.js y procesa errores siguiendo la estructura estandarizada de REDA.

- **packages/Reda/RedaAlojamiento/resources/js/general/menus/obtenerConteoViajes.js**
  Función AJAX para obtener el conteo de reservaciones activas (viajes) del usuario. Sigue la estructura de promesas y manejo de errores del plugin REDA para alimentar dinámicamente el dashboard.

### JavaScript (Vistas)
- **packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/verDetalleReservaModal.js**
  Script encargado de gestionar la apertura y renderizado del modal de detalles de reservación. Intercepta los clics en los botones de "Ver reserva", solicita la información detallada al servidor mediante AJAX y construye dinámicamente un modal con estética de Airbnb. Incluye lógica de manejo de errores robusta con notificaciones traducidas.

- **packages/Reda/RedaAlojamiento/resources/js/general/reserve-injection.js**
  Script responsable de inyectar dinámicamente el botón de "Reservar" o "Ver reserva" en las tarjetas de inmuebles de la aplicación. Implementa lógica para detectar el `propertyId` y `slug`, verifica si el usuario está autenticado y consulta si existe una reserva activa para cambiar el texto proactivamente. En el caso de "Ver reserva", configura el botón para abrir un modal detallado en lugar de realizar una redirección.

### Controladores
- **packages/Reda/RedaAlojamiento/src/Http/Controllers/Disputa/DisputaController.php**
  Controlador para la gestión de mediaciones (disputas). Maneja la lógica de negocio para crear, verificar y listar mediaciones. Incluye validaciones de estado de reserva para permitir mediaciones solo en reservaciones formales y no en simples consultas. `obtenerDisputasPaginadas()` filtra revisión tanto por el valor español persistido como por el traducido.

- **packages/Reda/RedaAlojamiento/src/Http/Controllers/Disputa/MensajeController.php**
  Controlador de mensajería de mediaciones. `getMessages($booking_id)` carga el historial compartido y filtra los mensajes de administrador con destinatario individual para que solo los reciba ese usuario; los mensajes grupales (`receiver_id = 0`) siguen visibles para ambos participantes. La actualización de lectura aplica el mismo filtro. `store(Request $request)` valida y almacena mensajes de los participantes.

- **packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaBookingController.php**
  Controlador para gestiones relacionadas con las reservaciones dentro del ecosistema REDA. Proporciona métodos para verificar el estado de las reservas de un usuario, como la obtención de IDs de inmuebles con reservas activas y la recuperación de detalles completos (foto, ubicación, fechas, costos) para su visualización en componentes interactivos del frontend.

- **packages/Reda/RedaAlojamiento/src/Http/Controllers/General/ChatController.php**
  Controlador encargado de gestionar el inicio de conversaciones directas entre huéspedes y anfitriones desde la vista de propiedad. Implementa la lógica para buscar una conversación existente o crear un nuevo registro de tipo `Inquiry` (Consulta) en la tabla de reservaciones (`bookings`). Este estado permite que las consultas iniciales se mantengan separadas de las reservaciones reales en los listados de viajes y reservas del sistema.


## Mensajería (Inbox)

### Vistas
- **packages/Reda/RedaAlojamiento/resources/views/users/inbox.blade.php**
  Vista Blade principal para el Inbox unificado del plugin Reda. Define la interfaz de mensajería con sidebar de avatares duales (propiedad y participantes), contenedor de mensajes enriquecidos e indicadores visuales (badges) para mensajes pendientes de leer. Maneja de forma segura participantes ausentes y mensajes sin reserva relacionada, evitando acceder a relaciones inexistentes en el sidebar. Incluye modales de seguridad desacoplados del flujo de contenido principal para prevenir rupturas del contexto de apilamiento (stacking context): `modalAdvertenciaMensajeReda` (al detectar datos sensibles en el envío) y `modalAdvertenciaPrivacidadReda` (aviso preventivo al cargar la vista con control de persistencia en `sessionStorage`), además del overlay para ampliación de fotos (`reda-chat-zoom-overlay`).
- **packages/Reda/RedaAlojamiento/resources/views/users/messages.blade.php**
  Subvista Blade que estructura la conversación activa y el pie de entrada de mensajes (`.message-footer`). Presenta las burbujas enriquecidas con roles, nombres y horas, y un pie interactivo simétrico compuesto por el campo de texto `.cht_msg` y el botón circular `.send-btn` con ícono de avioncito de papel (`fa-paper-plane`), perfectamente centrados verticalmente y alineados mediante Flexbox.

### JavaScript (Vistas y General)
- **packages/Reda/RedaAlojamiento/resources/js/vistas/inbox/inbox.js**
  Controlador Javascript para la vista de Inbox personalizada. Gestiona la sincronización inmediata de la conversación activa detectando el parámetro `?id=...` en la URL para evitar retardos o llamadas artificiales a clics, carga dinámica de conversaciones mediante AJAX, envío de mensajes, navegación estilo WhatsApp en móviles, neutralización de conflictos y manejo dinámico de contadores de mensajes no leídos. Reubica dinámicamente los modales de seguridad como hijos directos de `body` (`.appendTo('body')`) para asegurar que el `modal-backdrop` de Bootstrap 4 nunca los eclipse, e implementa limpieza forzada de backdrops residuales en eventos `hidden.bs.modal`.
- **packages/Reda/RedaAlojamiento/resources/js/general/mensajes.js**
  Script de integración para mediaciones y mensajería. Gestiona la caja de mediación en `#booking`, modales de disputas y desplazamiento suave (`scrollIntoView`) en el sidebar sin interferir con la renderización inicial del servidor. Al enviar una solicitud, convierte la espera en el resultado sin cerrarla previamente, evitando que el cierre diferido oculte el mensaje de error o éxito.
- **packages/Reda/RedaAlojamiento/resources/js/general/notificaciones.js**
  Módulo de notificaciones globales y animaciones de espera (`RedaNotificaciones`). Incorpora protección en `ocultar()` ante transiciones activas de apertura (`_isTransitioning`), garantizando que la ocultación se complete y se purguen backdrops huérfanos sin afectar modales interactivos abiertos en la vista.
- **packages/Reda/RedaAlojamiento/resources/js/chat-injection.js**
  Script de chat encargado de reubicar la sección "Sobre el anfitrión" debajo del mapa en la vista de propiedad (`property.single`) e inyectar el botón interactivo "Enviar mensaje". Inicia la conversación con `iniciarChat`, gestiona el estado de espera y redirige limpiamente al Inbox unificado (`/inbox?id=...`), excluyendo observadores de mutación innecesarios en la ruta de Inbox. El ajuste visual de la tarjeta resumen superior pertenece a `propiedad_detalle.js`.

### Controladores
- **packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaInboxController.php**
  Controlador para la mensajería unificada. Gestiona el historial de chat enriquecido, procesamiento de respuestas con virtualización de datos y obtención del conteo de mensajes no leídos. Filtra los mensajes administrativos privados por `receiver_id` al construir la lista del Inbox y el historial, y aplica el mismo criterio antes de marcar mensajes como leídos; mantiene visibles los mensajes grupales (`receiver_id = 0`). Omite del sidebar los mensajes cuya reserva ya no está relacionada y selecciona como conversación inicial una reserva ya cargada y vigente, tolerando registros huérfanos que pueden quedar tras eliminaciones manuales.

## Verificación de Correo (Signup y Login)

### Controladores
- **packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaUsuarioController.php**
  Controlador que extiende de `App\Http\Controllers\UserController` para desacoplar el flujo de registro y confirmación de correo de los archivos del core. Sobrescribe `create` para registrar al usuario, enviar el correo de verificación vía `welcome_email` e interrumpir el inicio de sesión automático, mostrando la notificación de registro exitoso sin activar el modal de correo pendiente. Sobrescribe `confirmEmail` para permitir la activación de la cuenta desde el enlace del correo sin requerir una sesión activa previa, activando el estado en `users`, actualizando `users_verification.email = 'yes'` y redirigiendo a login con el modal de confirmación exitosa.

- **packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaLoginController.php**
  Controlador que extiende de `App\Http\Controllers\LoginController` para proteger el acceso al sistema. Sobrescribe `authenticate` para comprobar las credenciales del usuario; si la contraseña es correcta pero `users_verification.email` no es 'yes', bloquea el inicio de sesión y redirige a la vista de login con la variable flash `correo_no_verificado` para desplegar el modal interactivo de aviso y corrección de correo.

- **packages/Reda/RedaAlojamiento/src/Http/Controllers/General/VerificacionCorreoController.php**
  Controlador encargado de la verificación, actualización y reenvío del correo de confirmación de cuenta:
  - `actualizarCorreoYReenviar(Request $request, EmailController $emailController)`: Valida la cuenta existente y no verificada (`users_verification.email != 'yes'`), comprueba la disponibilidad del nuevo correo, actualiza `users.email`, purga tokens previos en `password_resets` y reenvía el correo de confirmación con `welcome_email`.
  - `reenviarCorreoVerificacion(Request $request, EmailController $emailController)`: Endpoint `POST reda/usuarios/reenviar-correo-verificacion` que permite reenviar el correo de verificación sin modificar la dirección registrada. Valida el formato del correo, busca la cuenta en la tabla `users` (devolviendo 404 pedagógico si no existe aún), confirma que la cuenta no esté validada previamente (400 si ya está activa), renueva el token en `password_resets` y reenvía el correo de confirmación invocando `EmailController@welcome_email`, retornando la estructura estándar JSON de REDA.

### Vistas
- **packages/Reda/RedaAlojamiento/resources/views/general/modal_verificacion_correo.blade.php**
  Estructura de modales Bootstrap 4.5 e inyección de datos de sesión a JavaScript (`window.RedaVerificacionData` con `rutaActualizarCorreo`, `rutaReenviarCorreo`, `correoNoVerificado` y `csrfToken`). Contiene:
  1. `#reda_modal_confirmar_email_signup`: Muestra el email ingresado en el registro y pregunta "¿Por favor verifique si la dirección de su correo es correcta?", disponiendo de 3 botones interactivos:
     - "Corregir email": despliega el input para editar la dirección antes de enviar.
     - El botón "Re-enviar" se mantiene oculto durante esta etapa, ya que el usuario todavía no ha completado el registro.
     - "Email correcto": procede con el envío regular del formulario de registro.
  2. `#reda_modal_correo_no_verificado_login`: Alerta en login únicamente cuando el usuario intenta acceder con credenciales válidas pero su correo no ha sido verificado, con botón "Re-enviar" directo y botón "Corregir correo" con formulario interactivo y reenvío asíncrono. Al completar cualquiera de esas acciones, el aviso inicial se sustituye por la confirmación de envío; la corrección conserva el mensaje al regresar automáticamente al paso inicial.
  3. `#reda_modal_correo_confirmado_exito`: Mensaje de confirmación exitosa con botón para "Iniciar sesión" tras hacer clic en el enlace del correo.

### JavaScript (Vistas)
- **packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/verificacionCorreo.js**
  Controlador JavaScript del flujo de verificación, reenvío y corrección de correo:
  - Intercepta el formulario `#signup_form` previa validación con jQuery Validate y `ageValidate()`, abre el modal de confirmación antes del envío y permite confirmar o corregir el correo; el botón de reenvío del modal está oculto.
  - Detecta únicamente `correo_no_verificado`, establecido tras un intento de inicio de sesión con credenciales válidas, para desplegar el modal en login y permitir reenvío o corrección.
  - En caso de reenvío exitoso, oculta el texto genérico de correo pendiente y muestra solo la confirmación de reenvío. Al corregir el correo, conserva la confirmación después del cambio temporizado al paso inicial; cancelar también mantiene el estado de éxito y no restaura un aviso contradictorio.
  - Detecta `correo_confirmado_exitoso` para desplegar el modal de bienvenida y enfocar el campo de login.

## Flujo de Pago y Confirmación de Reservas

### JavaScript (Vistas)
- **packages/Reda/RedaAlojamiento/resources/js/vistas/pago/frontend/pagos.js**
  Controlador JavaScript del lado del cliente para el formulario inicial (`#checkout-form`) y el de confirmación (`#payment-form`). Comparte `cargarPlantillas()` para insertar el selector en el campo de mensaje de cada formulario, sin sustituir el texto ya escrito y sin cambiar la validación original del formulario inicial. Implementa validación estricta y reactiva en el cliente cuando aplica la carga de comprobante de pago (`attachment[]`) y nota/mensaje al anfitrión (`note`), como ocurre en pasarelas de Transferencia Bancaria Directa (`DirectBankTransfer`). Utiliza intercepción en fase de captura del DOM (Capture Phase) en el botón de confirmación (`#payment-form-submit`) y en el evento `submit` del formulario para adelantarse a scripts de terceros que deshabilitan el botón de manera prematura. Si falta alguno de los campos obligatorios, cancela la propagación del evento, previene el envío, restaura el estado interactivo del botón y el spinner, y muestra mensajes de error en español estilizados en los contenedores correspondientes (`span.text-danger`), evitando la recarga del navegador y garantizando que el usuario no pierda el archivo del comprobante adjunto. Incluye limpiadores de errores en tiempo real en los eventos `change` e `input`, enfoca y desplaza la vista hacia el primer campo inválido, y activa la animación de espera ("Su reserva está siendo procesada") únicamente cuando el formulario es 100% válido.

## Administración y Menú Lateral (Backend)

### JavaScript (Administración General)
- **packages/Reda/RedaAlojamiento/resources/js/admin/general/menus/menuLateralAdmin.js**
  Orquesta la inyección reactiva y no invasiva de las opciones del plugin REDA en el menú lateral de AdminLTE. Construye "Negocios", "Mediaciones" y "Mensajes y Alertas" con la estructura nativa de navegación (`nav-item`, `nav-link`, `nav-icon` y texto en `p`) para mantener la misma sangría y alineación de las opciones originales. El submenú acordeón "Negocios" agrupa "Configurar planes", "Tipos de negocios" y "Atención al comercio", esta última conserva el acceso a `/admin/reda/general/soporte-tecnico`; todos sus enlaces muestran la animación de espera al navegar. También inyecta el submenú "Mediaciones", con opciones desplegables de "Listado" y condicionalmente "Configuración" exclusiva para Rol 1. Para "Mediaciones", si el usuario conectado no posee el elemento `Bookings` en su menú lateral (como ocurre con el Rol 2 "Atención al usuario"), el script busca puntos de anclaje jerárquicos alternativos (`#menu-negocios`, `Properties`, `Customers`, `Dashboard` o el final del contenedor `.sidebar-menu`). Al hacer clic o tocar en "Mediaciones", asegura que en la opción "Listado" también aparezca el contador dinámico de mediaciones activas (`Listado (N)`), sincronizado con el encabezado padre. Mantiene abierto el submenú y resalta la opción activa cuando la ruta coincide con `/admin/reda/disputas` o `/admin/reda/disputas/configuracion`, y actualiza en tiempo real los contadores vía AJAX. En «Pruebas aplicación», al terminar de abrir, desplaza la barra lateral solo lo necesario para mostrar «Reiniciar mediaciones» si se encuentra fuera de su borde inferior; sincroniza el estado accesible y evita acumular animaciones en clics rápidos.
  **Campanita de Alertas en Header y Submenú "Mensajes y Alertas":**
  1. **Campanita en Header:** Inyecta en el navbar superior del panel administrativo (`.navbar-nav.ms-auto`) la campanita `#reda-admin-header-bell` con su badge `#reda-admin-bell-badge`, la cual enlaza a `/admin/reda/alertas` y refleja en tiempo real el número de alertas no leídas.
  2. **Transformación de "Messages":** Reemplaza la opción de enlace simple "Messages" del core por el submenú interactivo "Mensajes y Alertas" (`#menu-mensajes`), que agrupa:
     - "Mensajes": Conduce a la vista original de histórico de mensajes (`/admin/messages`).
     - "Alertas": Conduce a la vista de alertas administrativas (`/admin/reda/alertas`), mostrando la insignia y contador de alertas no leídas (`Alertas (N)`).
     - El submenú está disponible para roles 1 y 2; si el rol 2 no tiene el enlace nativo "Messages" en el DOM, se inserta después de "Mediaciones", "Negocios" o "Properties", en ese orden de preferencia.
  3. **Sincronización Reactiva:** Escucha el evento global `reda:actualizar-contador-alertas` y realiza sondeo periódico cada 60 segundos hacia `/admin/reda/alertas/count-no-leidas`, actualizando dinámicamente tanto la campanita superior como las insignias del menú lateral sin requerir recarga de página.

### Vistas (Administración General)
- **packages/Reda/RedaAlojamiento/resources/views/admin/general/main_footer.blade.php**
  Archivo maestro para la inyección de modales y scripts globales en el panel administrativo. Expone hacia el contexto global de JavaScript el objeto `window.RedaAdminUser` con el ID del administrador, su `roleId`, su nombre de rol, la bandera booleana `tieneAccesoMediaciones` (activa para los roles 1 "Admin" y 2 "Atención al usuario") y la propiedad `esAdminTotal` (true exclusivamente para usuarios con Rol 1 "Admin"), permitiendo que los scripts frontend conozcan el perfil del usuario autenticado sin llamadas adicionales.

### Controladores (Administración)
- **packages/Reda/RedaAlojamiento/src/Http/Controllers/Admin/Disputa/DisputaController.php**
  Controlador para el panel de mediaciones en el backend. Gestiona la visualización, asignación y configuración de mediaciones según el rol del usuario conectado:
  - **Rol 1 (Admin):** Visualiza todas las mediaciones en el panel. Consulta y expone la colección de agentes disponibles (usuarios de la tabla `admin` con roles 1 y 2 activos). Procesa la asignación directa de cualquier agente a una mediación. Accede con exclusividad a los métodos `configuracion()` y `guardarConfiguracion()` para definir los umbrales de mediaciones permitidas.
  - **Rol 2 (Atención al usuario / Agente):** Aplica un filtro estricto en `obtenerDisputasPaginadas`, `obtenerConteoDisputasActivas` y `getDetailModal` para mostrar únicamente las mediaciones que no han sido asignadas o tomadas (`id_usuario_agente_asignado IS NULL` o `0`) y aquellas asignadas a él mismo. Las mediaciones asignadas a otros agentes quedan completamente ocultas y protegidas ante accesos no autorizados. Tiene restringido el acceso a la configuración (403 Forbidden).
  - **Método `asignarAgente`:** Endpoint `POST /admin/reda/disputas/asignar-agente` que guarda en `disputas` el `id_usuario_agente_asignado` y el valor literal `Asignación a agente` en `paso_actual`. Permite a usuarios de Rol 1 asignar o reasignar cualquier agente o a sí mismos, y a usuarios de Rol 2 tomar mediaciones disponibles asignándose a sí mismos. Retorna en la estructura JSON estándar el paso persistido y los datos de avatar y nombre del agente. No modifica mediaciones históricas hasta que se ejecuta una asignación.
  - **Método `ponerEnRevision`:** valida el administrador y el ID, bloquea la fila en una transacción y guarda los valores literales `En revisión` en `estado` y `paso_actual`. El rol 1 puede modificar cualquier mediación; el rol 2 solo las visibles para él (sin asignar o asignadas a él). Devuelve la estructura JSON estándar y registra los errores. No impone restricciones adicionales sobre el estado previo.
  - **Método `obtenerDisputasPaginadas`:** para revisión acepta tanto el estado español persistido como el traducido; con `mostrar_id` localiza la página del caso visible, ordenando por fecha de actualización e ID descendentes, sin modificar los controles normales de paginación.
  - **Métodos de Configuración (`configuracion` y `guardarConfiguracion`):** Endpoints `GET /admin/reda/disputas/configuracion` y `POST /admin/reda/disputas/configuracion/store` protegidos exclusivamente para Rol 1. Gestionan en `settings` las claves históricas `'Cantidad mediaciones permitidas primer aviso'` y `'Cantidad mediaciones segundo aviso y suspensión'`; los mensajes de validación describen el segundo umbral como restricciones, no como inactivación.

### Vistas y Scripts de Administración
- **packages/Reda/RedaAlojamiento/resources/views/admin/disputa/index.blade.php**
  Vista principal del panel de mediaciones para el administrador. Expone las credenciales y perfil del usuario conectado en `window.RedaAdminAccess` (`roleId`, `adminId`, `isFullAdmin`).
- **packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/indexDisputas.js**
  Controlador del dashboard administrativo de mediaciones. Implementa la lógica reactiva de asignación:
  - Para Rol 1: Sustituye el texto del agente no asignado por un control `<select>` con los agentes disponibles y el propio admin. Al seleccionar, ejecuta la asignación vía AJAX con bloqueo de espera y renderiza la foto de perfil y nombre del agente asignado, con soporte para reasignación interactiva.
  - Para Rol 2: Sustituye el texto por un suiche tipo toggle (`form-check form-switch`) con la etiqueta "Tomar mediación". Al activarlo, toma la mediación vía AJAX con animación de espera y actualiza la tarjeta mostrando la foto de perfil y el nombre del agente.
  - `actualizarAsignacionMediacion()` sincroniza el agente y el paso devueltos por el servidor con el selector de la tarjeta, el panel lateral de la mediación seleccionada y el acordeón móvil abierto. Si se selecciona después otra mediación, se utiliza su propio paso actualizado. `generarTimelineHtml()` reconoce el valor persistido en español o su etiqueta traducida.
  - `obtenerPasosMediacion()` suministra los mismos seis pasos a la línea de tiempo y a `renderizarLista()`. `generarOpcionesPasos()` selecciona el valor recibido de `paso_actual` o su etiqueta traducida, incluyendo los dos pasos iniciales deshabilitados; si el valor es nulo o desconocido, muestra «Paso actual no disponible» y emite una advertencia en consola sin escoger otro paso. `ponerMediacionEnRevision()` envía la transición con CSRF; el selector muestra espera, restaura el valor y notifica ante errores, o activa la pestaña de revisión y llama a `cargarMediaciones()` con el ID para ubicar y seleccionar la mediación. Las otras opciones habilitadas aún no ejecutan cambios e informan que no están disponibles. Si el caso ya no figura en la página, se muestra una advertencia. En escritorio «Actualizado» aparece al inicio de la tercera columna; en móvil permanece al final de los datos.
  - `renderizarCabeceraMediacion()` muestra también el estado antes del ID en el lateral de escritorio y el detalle móvil.
- **packages/Reda/RedaAlojamiento/resources/views/admin/disputa/configuracion.blade.php**
  Vista Blade administrativa para la configuración de mediaciones permitidas, reservada para administradores con Rol 1. Incluye los umbrales de primer aviso y de segundo aviso con restricciones de acciones; conserva los identificadores y las claves de almacenamiento existentes. Integra validación y envío asíncrono con animación de espera hacia `route('reda.admin.disputas.configuracion.store')`.
- **packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/configuracionDisputas.js**
  Controlador JavaScript del formulario de configuración de mediaciones permitidas. Añade la clase `reda-admin-configuracion-mediaciones` al `body` cuando existe el formulario para limitar los estilos a esta pantalla. Intercepta el evento submit, valida en el cliente que los valores sean números enteros no negativos y que el segundo aviso no sea menor que el primero, usa terminología de restricciones, activa la animación de espera global (`window.RedaNotificaciones.esperar()`), envía los datos vía AJAX con token CSRF y presenta notificaciones basadas en el formato estándar JSON de REDA.
  `desactivarCargando()` restablece el botón sin cerrar el modal de espera. `mostrarResultado()` transforma ese mismo modal en una notificación de éxito o error para evitar que los temporizadores de `ocultar()` la cierren inmediatamente; si no está disponible `notificar()`, oculta la espera cuando sea posible y muestra una alerta alternativa.

## Sistema de Alertas y Restricciones por Límites de Mediaciones

### Base de Datos y Migraciones
- **packages/Reda/RedaAlojamiento/database/migrations/2026_10_04_000000_crear_tabla_alertas_admin.php**
  Migración que crea la tabla auxiliar `alertas_admin` para almacenar las notificaciones del sistema dirigidas a los administradores:
  - Campos: `id`, `admin_id` (nullable, relación con tabla `admin`), `titulo`, `mensaje`, `tipo` ('primer_aviso', 'suspension', 'general'), `disputa_id` (nullable), `user_id` (nullable, relación con `users`), `leido` (booleano con valor predeterminado 0), `fecha_lectura` (timestamp nullable) y marcas de tiempo (`created_at`, `updated_at`).
  - Cumple estrictamente con las directrices de nomenclatura en español y soporte nullable para todas las columnas excepto la clave primaria.
- **packages/Reda/RedaAlojamiento/database/migrations/2026_10_04_000001_crear_tabla_usuarios_avisos_mediaciones.php**
  Migración que crea la tabla auxiliar `usuarios_avisos_mediaciones` como registro de auditoría y control de umbrales para prevenir envíos duplicados de correos o suspensiones reiteradas:
  - Campos: `id`, `user_id` (relación foránea con `users`), `conteo_mediaciones`, `primer_aviso_enviado` (booleano), `fecha_primer_aviso` (timestamp nullable), `segundo_aviso_enviado` (booleano), `fecha_segundo_aviso` (timestamp nullable), `cuenta_suspendida` (booleano), `fecha_suspension` (timestamp nullable), `motivo` (texto descriptivo) y marcas de tiempo.
- **packages/Reda/RedaAlojamiento/database/migrations/2026_10_18_000002_habilitar_mensajes_para_rol_atencion_usuario.php**
  Migración de datos que asigna el permiso existente `manage_messages` al rol 2 (Atención al usuario), si están registrados tanto el rol como el permiso. Esto habilita las rutas originales `/admin/messages` protegidas por dicho permiso para que el enlace "Mensajes" del submenú funcione realmente; no crea ni altera tablas.

### Modelos Eloquent
- **packages/Reda/RedaAlojamiento/src/Models/Alerta/AlertaAdmin.php**
  Modelo Eloquent para la tabla `alertas_admin`. Define los campos asignables en `$fillable`, casteo booleano para `leido` y timestamp para `fecha_lectura`. Establece las relaciones `admin()` (`App\Models\Admin`), `usuario()` (`App\Models\User`) y `disputa()` (`Reda\RedaAlojamiento\Models\Disputa\Disputa`).
- **packages/Reda/RedaAlojamiento/src/Models/Disputa/UsuarioAvisoMediacion.php**
  Modelo Eloquent para la tabla `usuarios_avisos_mediaciones`. La columna histórica `cuenta_suspendida = 1` identifica restricciones activas, incluso en registros históricos; no implica que deba cambiarse el estado de inicio de sesión. Conserva fechas, motivo y relación `usuario()`.

### Integración y protección de acciones
- **packages/Reda/RedaAlojamiento/resources/lang/es.json**
  Incluye los textos de configuración, bloqueo, primer y segundo aviso sobre restricciones de acciones, sin cambiar las claves históricas de configuración.
- **packages/Reda/RedaAlojamiento/src/RedaAlojamientoServiceProvider.php**
  En `boot()` agrega el middleware de restricciones al grupo `web`, después del inicio de sesión, sin modificar las rutas originales; mantiene las integraciones existentes del plugin.
- **packages/Reda/RedaAlojamiento/src/Http/Middleware/RestringirAccionesPorMediaciones.php**
  `handle()` comprueba el indicador histórico del usuario autenticado antes de permitir crear propiedades y negocios, iniciar mediaciones, comenzar reservas o procesar solicitudes de retiro. Bloquea también las entradas de la pasarela para impedir el acceso directo; devuelve JSON REDA con código 403 a AJAX o redirige con un aviso a los formularios normales. No bloquea el listado de retiros, la edición de elementos existentes, las mediaciones en curso ni los callbacks de pagos.
  Cuando `payments/create_booking` prepara un pago con pasarela mientras el usuario aún no está restringido, `identificarPago()` vincula en sesión el usuario, propiedad, fechas y método. El middleware permite abrir y completar esa pasarela si la restricción aparece después; los callbacks y webhooks siguen disponibles para conciliar pagos ya iniciados. Las sesiones preparadas antes de desplegar esta modificación no tienen esa marca: se deben completar antes del despliegue. Esta medida no altera registros de reservas o pagos existentes.
  Los cuatro registros históricos marcados en el respaldo del 06/11/2026 11:25 quedan sujetos a restricciones si su usuario accede. Dos cuentas no borradas estaban inactivas, una estaba activa y otra borrada lógicamente; no se reactiva ni restaura ninguna automáticamente. Antes de reactivar se debe verificar cada motivo de inactividad en Vesta.

### Servicios y Lógica de Negocio
- **packages/Reda/RedaAlojamiento/src/Services/MediacionAlertaService.php**
  Servicio centralizado que orquesta la verificación automática de umbrales cada vez que se registra una nueva mediación (`DisputaController@store`).
  - **Recuperación de Configuración:** Consulta en la tabla `settings` los parámetros `Cantidad mediaciones permitidas primer aviso` y `Cantidad mediaciones segundo aviso y suspensión`.
  - **Evaluación de Partes Involucradas:** Calcula individualmente el conteo total de mediaciones del turista y del anfitrión, sin filtrar por estado ni resultado y sin depender de quién inició la disputa. El conteo por resultados es un requerimiento independiente pendiente.
  - **Disparo de Primer Aviso:** Si el conteo alcanza o supera el umbral de primer aviso y no ha sido notificado previamente:
    1. Registra la emisión del aviso en `usuarios_avisos_mediaciones` con fecha y hora actual.
    2. Envía correo preventivo al usuario evaluado (`emails.primer_aviso_usuario`).
    3. Envía un mensaje privado al buzón `/inbox` del usuario evaluado usando `receiver_id` con su propio ID y metadato `sender_type = 'admin'` (`MensajeMetadata`).
    4. Envía correo informativo a todos los administradores activos (`emails.primer_aviso_admin`).
    5. Genera un registro en `alertas_admin` para cada administrador activo (`tipo = 'primer_aviso'`).
  - **Segundo aviso y restricciones:** Si el segundo límite es mayor que cero y el conteo lo alcanza o supera, guarda `cuenta_suspendida = 1`, fechas y motivo sin modificar `users.status`. Envía avisos al usuario y administradores y conserva el tipo histórico `suspension` para las alertas. Las restricciones continúan vigentes mientras el indicador permanezca marcado; cambiar el umbral no las levanta automáticamente. Si el segundo límite es cero, no se activa la restricción ni se anuncia un límite de cero en el primer aviso.

### Controladores
- **packages/Reda/RedaAlojamiento/src/Http/Controllers/Admin/Alerta/AlertaController.php**
  Controlador del panel administrativo para la gestión de alertas del sistema.
  - `index()`: Renderiza la vista principal `reda-alojamiento::admin.alerta.index`.
  - `obtenerAlertasPaginadas(Request $request)`: Retorna las alertas en bloques de 10 en 10 (`paginate(10)`) vía AJAX con filtros por estado ('todos', 'no_leidas', 'leidas'), incluyendo el HTML de la barra de paginación renderizado con `admin.general.paginacion` y el conteo de no leídas.
  - `obtenerConteoNoLeidas()`: Retorna el conteo actual de alertas no leídas del administrador conectado para alimentar la campanita del header y la insignia del menú lateral.
  - `marcarLeida($id)`: Marca una alerta individual como leída (`leido = 1`, `fecha_lectura = now()`) y devuelve el conteo actualizado.
  - `marcarTodasLeidas()`: Marca de forma masiva todas las alertas no leídas del administrador actual como leídas y reinicia el contador a 0.

### Estilos (Admin)
- **packages/Reda/RedaAlojamiento/resources/sass/admin/main.scss**
  En la pantalla de configuración de planes administrativos, el selector `#configuracion_planes_container + .content-wrapper` aplica márgenes laterales y padding interno adaptables a encabezados, paneles, pestañas y cajas. El selector `#modal-plan` da al modal de agregar/editar planes un borde ligeramente redondeado, separa encabezado, contenido y pie, y agrega espaciado a las secciones de opciones de pago y beneficios. Para las opciones de tipos de negocios, `#opciones_tipos_de_negocios + .content-wrapper` aplica márgenes, padding y radios suaves a la página, panel, tabla y tarjetas móviles; `#modal-category` estiliza el modal de agregar/editar. Los estilos de ambos módulos reducen sus espacios en pantallas móviles y permanecen aislados del resto del administrador.
  La clase `reda-admin-soporte-tecnico` delimita los márgenes y el padding del listado y detalle de atención al comercio, y los bordes suavemente redondeados de sus paneles, alertas, modal de búsqueda, modal de gestión, confirmaciones y notificaciones. En el detalle, añade 8 px entre la flecha y el texto del botón «Volver al listado». Ajusta también los espacios en móvil, la separación entre filtros y el ajuste de los botones de gestión; los modales compartidos mantienen su estilo original en otras páginas. Los selectores con `!important` contrarrestan exclusivamente utilidades Bootstrap existentes en los modales.
  En `#modal_busqueda_soporte`, las filas de filtros tienen separación horizontal y vertical adaptable, las etiquetas y campos disponen de mayor espacio y los controles presentan bordes discretos. La lupa flotante queda en el pie del modal, que reserva espacio lateral para evitar superponer los filtros y los botones de cancelar o limpiar.
  En configuración de mediaciones, `.reda-admin-configuracion-mediaciones` delimita márgenes del encabezado y contenido, padding de la tarjeta y separación entre etiquetas, campos y textos de ayuda. Tarjeta, campos y botón tienen radios suaves, con espacios reducidos para móvil; los selectores `!important` contrarrestan las utilidades Bootstrap de la tarjeta.
  En el listado de mediaciones, `.reda-select-paso-mediacion` conserva el ancho disponible de su propia línea y usa altura de 28 px y letra de 12 px. Reserva 2,5 rem a la derecha para la flecha y aplica recorte con puntos suspensivos al texto largo, sin alterar los valores ni afectar otros módulos.

### JavaScript (Atención al comercio)
- **packages/Reda/RedaAlojamiento/resources/js/admin/general/soporte_tecnico/indexSoporteTecnico.js**
  El script del listado añade `reda-admin-soporte-tecnico` al `body` cuando existe `#index_soporte_tecnico`. `inicializarSoporteTecnico()` conserva la búsqueda, los filtros, la navegación y las animaciones de espera del listado y de la paginación.
- **packages/Reda/RedaAlojamiento/resources/js/admin/general/soporte_tecnico/showSoporteTecnico.js**
  El script de detalle añade la misma clase cuando existe `#show_soporte_tecnico`, permitiendo aislar también los modales compartidos. `inicializarDetalleSoporte()` conserva la gestión del ticket y las confirmaciones de mantener o eliminar una reseña; las peticiones y sus respuestas no se modificaron.

### Vistas y Plantillas
- **packages/Reda/RedaAlojamiento/resources/views/admin/alerta/index.blade.php**
  Vista administrativa que presenta el panel de Alertas del Sistema. Incorpora botones de filtrado rápido ("Todas", "No leídas" con badge dinámico, "Leídas"), botón de acción masiva "Marcar todas como leídas" con spinner de carga, contenedor asíncrono para las tarjetas de alertas y contenedor inferior para la paginación de 10 en 10.
- **packages/Reda/RedaAlojamiento/resources/views/emails/primer_aviso_usuario.blade.php**
  Plantilla de primer aviso con el límite de restricciones y las cinco acciones afectadas cuando dicho límite está habilitado. Incluye botón al buzón.
- **packages/Reda/RedaAlojamiento/resources/views/emails/primer_aviso_admin.blade.php**
  Plantilla de primer aviso para administradores con el conteo, límite de restricciones y enlace al panel.
- **packages/Reda/RedaAlojamiento/resources/views/emails/suspension_usuario.blade.php**
  Plantilla de segundo aviso: informa las cinco acciones restringidas, que no se modifica el acceso a la cuenta y cómo contactar a soporte. Se conserva el nombre histórico del archivo.
- **packages/Reda/RedaAlojamiento/resources/views/emails/suspension_admin.blade.php**
  Plantilla para administradores que informa las restricciones, los datos de la mediación y que no se modificó el estado de acceso. Se conserva el nombre histórico del archivo.

### JavaScript (Vistas y Clientes)
- **packages/Reda/RedaAlojamiento/resources/js/admin/vistas/alerta/indexAlertas.js**
  Controlador JavaScript del panel de alertas:
  - Carga asíncrona con animación de espera (`window.RedaNotificaciones.esperar()`).
  - Cierra la animación en `finally` con `window.RedaNotificaciones.ocultar()`, la función disponible en el módulo global, tanto al completar como al fallar la petición AJAX de paginación.
  - Renderizado dinámico de tarjetas con código de colores según tipo (advertencia para primer aviso, peligro para suspensión).
  - Paginación interactiva de 10 en 10 sin recargar página.
  - Filtrado en cliente por estado (Todas / No leídas / Leídas).
  - Marcado reactivo de alertas como leídas tanto al pulsar el botón "Marcar como leída", como al hacer clic directamente en cualquier parte de una tarjeta no leída o al pulsar el botón "Ver mediación".
  - Marcado masivo interactivo con spinner mediante `#btn-marcar-todas-leidas`.
  - Disparo del evento global `reda:actualizar-contador-alertas` que sincroniza de inmediato la campanita superior y el submenú lateral.

## Verificación de Identidad (KYC) con Didit — Fase 2: Base de datos, enum y modelos

Estado: en implementación por fases. Esta fase solo crea la base de datos, el enum, la configuración y los modelos; el bloqueo de operaciones, Didit, los controladores y las interfaces llegan en fases posteriores. La tabla `users_verification` (verificación social) no se modifica ni se elimina porque otros controladores dependen de ella; el nuevo KYC es el único mecanismo que autorizará publicaciones, negocios y reservas nuevas.

### Configuración y enums
- **packages/Reda/RedaAlojamiento/config/kyc.php**
  Configuración del KYC cargada con `mergeConfigFrom` como `config('kyc.*')`. Contiene el interruptor de emergencia `bloqueo_habilitado` (`KYC_ENFORCEMENT`), la decisión D1 `bloquear_edicion_publicados`, el país predeterminado `VE`, los parámetros de Didit (tolerancia de 300 s del webhook, confirmación por API antes de aprobar, `vendor_data_por_intento`, intervalo de consulta de respaldo) y la **tabla de equivalencia única** entre estados de Didit (comparación estricta) y estados internos. Define los tipos de documento (cédula de identidad, pasaporte, licencia de conducir, permiso de residencia y documento extranjero) con su código de Didit y si exigen reverso y selfie, los límites de archivos (JPG, JPEG, PNG y PDF, 5 MB, cifrado), los límites diarios de intentos y la **retención** de documentos (90 días tras aprobar, 30 tras rechazo, fallo o expiración, configurable e independiente de la aprobación, sin borrar si hay procesos pendientes). Las credenciales de Didit no se guardan aquí.
- **packages/Reda/RedaAlojamiento/src/Enums/EstadoKyc.php**
  Enum con los estados `not_started`, `pending`, `in_progress`, `manual_review`, `verified`, `rejected`, `failed` y `expired` (valores en inglés, casos en español). Es la única definición de estados y transiciones: `transicionesPermitidas()`, `puedePasarA()`, `esAbierto()`, `autorizaOperaciones()` (solo `verified`) y `permitenNuevoIntento()`. Un fallo automático (`failed`) nunca se confunde con un rechazo manual (`rejected`); desde ambos se puede iniciar una verificación manual o un nuevo intento. Como Didit permite que un revisor cambie una decisión, `failed` puede pasar a `verified` y `verified` puede pasar a `failed`, `in_progress` o `expired` (revocación); estas reglas generales no autorizan acciones administrativas, que KycService restringe por método.
- **packages/Reda/RedaAlojamiento/src/Enums/MetodoKyc.php**
  Enum con los métodos `didit` y `manual`.

### Base de Datos y Migraciones (aditivas; solo se crean, no se ejecutan desde el IDE)
- **packages/Reda/RedaAlojamiento/database/migrations/2026_11_19_100000_crear_tabla_solicitudes_kyc.php**
  Tabla `solicitudes_kyc`: un registro por intento (historial; nunca se sobrescribe). Guarda método, estado, número de intento, datos de Didit (`didit_session_id` único, `didit_estado_original` literal, `didit_resultado` saneado, `didit_ultimo_timestamp`), datos personales (documento, fecha de nacimiento y dirección cifrados), fechas, decisión administrativa y control de retención. `clave_abierta` (UNIQUE) vale `user_id` mientras la solicitud está abierta y `NULL` al cerrarse: impide dos solicitudes abiertas por usuario a nivel de base de datos.
- **packages/Reda/RedaAlojamiento/database/migrations/2026_11_19_100100_crear_tabla_usuarios_kyc.php**
  Tabla `usuarios_kyc`: estado efectivo de autorización de cada usuario (una fila por usuario, `user_id` UNIQUE). Es la única tabla que consultará el bloqueo. Un usuario sin fila equivale a `not_started`. Incluye `solicitud_activa_id`, `verificado_en`, `verificado_por_admin_id`, `expira_en` y `version`.
- **packages/Reda/RedaAlojamiento/database/migrations/2026_11_19_100200_crear_tabla_documentos_kyc.php**
  Tabla `documentos_kyc`: metadatos del archivo (tipo, ruta en el disco privado, MIME detectado, tamaño, `sha256`, cifrado, estado vigente/reemplazado/eliminado). No guarda el archivo.
- **packages/Reda/RedaAlojamiento/database/migrations/2026_11_19_100300_crear_tabla_eventos_kyc.php**
  Tabla `eventos_kyc`: auditoría inmutable. `clave_idempotencia` (UNIQUE) evita reprocesar eventos de Didit.
- **packages/Reda/RedaAlojamiento/database/migrations/2026_11_19_100400_crear_permiso_manage_kyc.php**
  Migración de datos idempotente: crea el permiso `manage_kyc` si no existe y lo asigna solo al rol llamado `admin` que ya posee `manage_admin` (no asume que el id sea 1). Si no encuentra ese rol, registra una advertencia y no asigna nada. No usa `truncate`, no toca `PermissionsTableSeeder` y limpia la caché `permissions`. Todas las claves foráneas hacia `users` usan `restrictOnDelete` para conservar el historial.

### Modelos Eloquent (src/Models/Kyc)
- **SolicitudKyc.php**
  Modelo de `solicitudes_kyc`. Cifra `numero_documento`, `fecha_nacimiento` y `direccion_residencia`; al asignar el número normaliza (mayúsculas, solo letras y números), calcula un hash HMAC-SHA256 con `APP_KEY` para búsquedas exactas (`hashesCandidatosDocumento()` tolera la letra V/E de la cédula) y guarda los últimos 4 caracteres para `numero_documento_enmascarado`. Oculta los campos sensibles en `toArray()` y `toJson()`. Relaciones: `usuario()`, `administrador()`, `documentos()`, `documentosVigentes()`, `eventos()`.
- **UsuarioKyc.php**
  Modelo de `usuarios_kyc`. `esVerificado()` exige estado `verified` y que `expira_en` no haya vencido.
- **DocumentoKyc.php**
  Modelo de `documentos_kyc` con las constantes de tipo (`frente`, `reverso`, `selfie`) y estado.
- **EventoKyc.php**
  Modelo de auditoría: inmutable, `updating` y `deleting` siempre lanzan excepción.
- **ProtegeEscrituraKyc.php**
  Trait que bloquea `creating`, `updating` y `deleting` fuera de la ventana autorizada. Las actualizaciones masivas con `Model::where()->update()` no disparan eventos, por lo que los servicios KYC deben escribir siempre mediante instancias de modelo.
- **packages/Reda/RedaAlojamiento/src/Services/Kyc/GuardiaEscrituraKyc.php**
  `ejecutar()` abre una ventana de escritura (reentrante y siempre cerrada con `finally`); solo KycService y KycDocumentoService la usarán. Garantiza que KycService sea el único responsable de cambiar estados.

### Integración
- **packages/Reda/RedaAlojamiento/src/RedaAlojamientoServiceProvider.php**
  `register()` carga `config/kyc.php` y `config/didit.php` (esta última en `services.didit`) con `mergeConfigFrom` y registra `DiditService` y `KycService` como singletons. `boot()` carga `routes/kyc.php`, registra `KycSincronizarCommand` y lo programa cada 10 minutos sin solaparse.
- **packages/Reda/RedaAlojamiento/resources/lang/es.json**
  Etiquetas de los estados, métodos y tipos de documento del KYC y los mensajes del flujo con Didit.

## Verificación de Identidad (KYC) con Didit — Fase 3: Integración con Didit

Estado: en implementación por fases. Esta fase añade el cliente de Didit, el servicio central, el webhook firmado, los endpoints del usuario para la verificación automática y la consulta de respaldo. Todavía no bloquea ninguna operación (Fase 6) ni tiene pantallas (Fase 7) ni verificación manual (Fase 4).

### Configuración
- **packages/Reda/RedaAlojamiento/config/didit.php**
  Se fusiona en `config('services.didit')` sin editar `config/services.php`. Lee solo de `.env`: `DIDIT_API_URL`, `DIDIT_API_KEY`, `DIDIT_WORKFLOW_ID`, `DIDIT_WEBHOOK_SECRET` y `DIDIT_ENVIRONMENT` (`sandbox` o `production`). Las credenciales nunca van a la base de datos, JavaScript, HTML, respuestas ni logs. Como se fusiona en `register()`, funciona con `config:cache`.

### Servicios (src/Services/Kyc)
- **DiditService.php**
  Cliente de la API oficial: `crearSesion()` (`POST /v3/session/` con `x-api-key`, `workflow_id`, `vendor_data`, `callback`, `callback_method=both`, `language=es` y `metadata`) y `obtenerDecision()` (`GET /v3/session/{id}/decision/`). Valida el UUID antes de armar la URL, exige https, no sigue redirecciones (no reenvía la API key), devuelve solo mensajes seguros en `DiditException` (reintentable ante red, 429 y 5xx). `sanearDecision()` reduce el resultado a estados por característica sin datos personales ni imágenes. `entornoEsperado()` traduce `DIDIT_ENVIRONMENT` a `sandbox`/`live`.
- **DiditException.php**
  Excepción con código HTTP y bandera `reintentable`.
- **FirmaWebhookDidit.php**
  Verificación criptográfica pura del webhook. Acepta `X-Signature-V2` (JSON canónico: claves ordenadas, objetos `{}` conservados, Unicode y barras sin escapar) y `X-Signature` (bytes exactos), también las cabeceras `-Previous` de la rotación de secreto; compara con `hash_equals`, exige `X-Timestamp` dentro de 300 s y fuerza `serialize_precision=-1`. NO acepta `X-Signature-Simple` (obsoleta: solo autentica el sobre). Validada contra la implementación de referencia de Didit en Python.
- **KycService.php**
  ÚNICO responsable de cambiar estados. Lectura: `estaVerificado()` (la consulta que usará el bloqueo, con caché por petición), `estadoDe()`, `solicitudActiva()`, `resumenParaUsuario()`. Escritura: `iniciarDidit()`, `procesarWebhookDidit()` y `sincronizarConDidit()`; todas devuelven la estructura estándar `success/message/mensaje_usuario/respuesta/code`.
  - **Concurrencia:** cada cambio ocurre en una transacción que bloquea (`FOR UPDATE`) la fila del usuario en `usuarios_kyc` (creada antes con `INSERT IGNORE`). Solicitud, estado efectivo y evento de auditoría se escriben juntos. Nunca se llama a Didit dentro de una transacción con bloqueo: `iniciarDidit()` reserva la solicitud, crea la sesión fuera de la transacción y guarda el `session_id`; si Didit falla, la solicitud nueva se cierra como `failed` y el usuario puede reintentar de inmediato.
  - **Webhook:** solo `status.updated` y `data.updated` de sesiones KYC. Comprueba que la sesión exista, que `vendor_data` coincida con el de la solicitud, que el `environment` sea el configurado, mapea el estado con la tabla `kyc.didit.mapa_estados` (comparación estricta) y, antes de aprobar, confirma con la API que la decisión sea `Approved` para ese mismo `vendor_data` (`kyc.didit.confirmar_aprobacion_por_api`). Si la API de Didit no responde devuelve 503 para que Didit reintente.
  - **Idempotencia y orden:** clave `didit:{event_id}` única en `eventos_kyc`, comprobada con el bloqueo tomado. Solo procesa la solicitud activa (`ignorado_sesion_antigua`) y descarta eventos cuyo `created_at` sea anterior al último aplicado (`ignorado_evento_antiguo`; se usa `created_at`, no `timestamp`, porque este se refresca en cada reintento de Didit). Las transiciones inválidas se registran y se ignoran.
  - **Consulta de respaldo:** `sincronizarConDidit()` usa el mismo camino que el webhook, con intervalo mínimo de 30 s por solicitud. No registra evento si no hay cambio.
  - **Avisos:** tras confirmar la transacción emite `EstadoKycCambiado` (punto de enganche de las notificaciones de la Fase 8).
- **GuardiaEscrituraKyc.php**
  Ventana de escritura autorizada (ver Fase 2).

### Eventos
- **packages/Reda/RedaAlojamiento/src/Events/EstadoKycCambiado.php**
  Evento sin datos personales (usuario, solicitud, estados, método, origen) emitido con `DB::afterCommit`.

### Controladores y rutas
- **packages/Reda/RedaAlojamiento/src/Http/Controllers/Kyc/DiditWebhookController.php**
  `recibir()` atiende `POST /webhooks/didit/kyc`: verifica la firma sobre el cuerpo original antes de leer su contenido; 401 si es inválida (solo se registra el motivo y la IP), 413 si supera 2 MB, 503 si falta el secreto (Didit reintenta) y delega en `KycService`.
- **packages/Reda/RedaAlojamiento/src/Http/Controllers/Kyc/KycUsuarioController.php**
  `iniciarDidit()` (`POST kyc/didit/iniciar`: JSON con `respuesta.url` si es AJAX, redirección a Didit si no), `retorno()` (`GET kyc/retorno`: ignora `status` y `verificationSessionId` de la URL, consulta a Didit y muestra el estado real guardado) y `estado()` (`GET kyc/estado`: resumen sin identificadores de Didit, sin caché). El usuario sale siempre de la sesión.
- **packages/Reda/RedaAlojamiento/routes/kyc.php**
  El webhook no usa el grupo `web` (sin sesión ni CSRF) y tiene `throttle:120,1`; las rutas `kyc/*` usan `web`, `locale` y `auth`. La ruta comodín `GET {name}` del núcleo solo captura una parte de la URL: la futura pantalla `/kyc` deberá registrarse antes de ella.

### Comandos
- **packages/Reda/RedaAlojamiento/src/Console/Commands/KycSincronizarCommand.php**
  `reda:kyc-sincronizar [--forzar]` consulta a Didit las verificaciones `in_progress` o `pending` con más de 5 minutos. Se programa cada 10 minutos (requiere `schedule:run` en el cron del servidor).

### Modelos
- **EventoKyc.php**
  Se añadieron las constantes de resultado `ignorado_estado_desconocido`, `ignorado_entorno`, `ignorado_sesion_desconocida`, `ignorado_evento_antiguo`, `rechazado_vendor_data` y `confirmacion_fallida`.

## Verificación de Identidad (KYC) con Didit — Fase 4: Verificación manual y documentos privados

Estado: en implementación por fases. Esta fase añade la verificación manual (datos personales y documentos), el almacenamiento privado cifrado, la descarga protegida, la política de autorización y la retención. Todavía no hay pantallas (Fase 7), panel administrativo (Fase 5), bloqueo de operaciones (Fase 6) ni notificaciones (Fase 8).

### Configuración y soporte
- **packages/Reda/RedaAlojamiento/config/kyc.php**
  Se ampliaron `documentos` (`ruta` desde `KYC_RUTA_PRIVADA`, `tipos_solo_imagen`, `lado_minimo_px`, `lado_maximo_px`, `megapixeles_maximo`, `calidad_jpeg`) y se añadió `manual.edad_minima` (18).
- **packages/Reda/RedaAlojamiento/src/Support/PaisesIso.php**
  Lista ISO 3166-1 alfa-2 usada para validar países (la tabla `country` del proyecto solo contiene Venezuela). Venezuela (`VE`) es el valor predeterminado de los formularios sin impedir otros países.
- **packages/Reda/RedaAlojamiento/src/RedaAlojamientoServiceProvider.php**
  Registra el disco privado `kyc_privado` (driver local, sin URL, permisos 0600/0700; carpeta `storage/app/private/kyc` o `KYC_RUTA_PRIVADA`), los singletons de los nuevos servicios, las políticas (`Gate::policy`) y el comando `reda:kyc-depurar` programado a diario a las 03:30.
  IMPORTANTE: la raíz web de este proyecto es la raíz del código (el `.htaccess` raíz solo bloquea archivos ocultos), por lo que `storage/` es alcanzable por URL. Por eso los documentos se guardan cifrados con nombre UUID y con un `.htaccess` de denegación en su carpeta, y se recomienda definir `KYC_RUTA_PRIVADA` fuera de `public_html`.

### Servicios (src/Services/Kyc)
- **KycDocumentoService.php**
  Valida cada archivo en el servidor: extensión (jpg, jpeg, png, pdf), tamaño (5 MB), tipo MIME REAL con `finfo` que debe coincidir con la extensión (MIME falso), imágenes legibles con límites de dimensiones y memoria, rechazo de código PHP o `<script>` incrustado y recodificación con GD que elimina metadatos, comentarios y datos añadidos (aplica antes la orientación EXIF). Los PDF se inspeccionan fuera de los flujos binarios y dentro de los flujos de objetos comprimidos (`ObjStm`) y se rechazan los que contengan `/JavaScript`, `/JS`, `/Launch`, `/EmbeddedFile`, `/OpenAction`, `/AA`, `/XFA`, `/URI`, `/Encrypt` y similares, incluso ofuscados con `#xx`; la selfie solo admite imágenes. `guardarCifrado()` guarda el archivo cifrado (AES-256 con APP_KEY) con ruta `AAAA/MM/uuid.enc`; `registrarDocumento()` conserva el historial (el vigente pasa a `reemplazado`); `leer()` descifra y comprueba la huella sha256 y que la ruta cumpla el patrón; `registrarAcceso()` audita cada consulta; `eliminarDocumentosDeSolicitud()` borra los archivos conservando las filas y la auditoría. No decide permisos (los decide `KycPolicy`).
- **DocumentoKycInvalidoException.php**
  Excepción con el campo del formulario y un mensaje seguro.
- **ValidadorKycManual.php**
  Normaliza y valida nombres y apellidos (letras, como figuran en el documento), tipo y número de documento, países ISO, fecha de nacimiento (formato `AAAA-MM-DD`, mayoría de edad), ciudad, dirección sin etiquetas, correo (por defecto el del perfil) y la aceptación del tratamiento de datos. Indica qué documentos exige cada tipo (`frente` siempre; `reverso` y `selfie` según `kyc.tipos_documento`).
- **KycService.php** (ampliado)
  `enviarManual()`: valida, guarda los archivos cifrados fuera de la transacción y, con el bloqueo de la fila del usuario, crea la solicitud `manual_review`; impide duplicados (estado abierto o UNIQUE de `clave_abierta`), aplica el límite diario, descarta los documentos que el tipo no exige y borra los archivos escritos si la operación falla. Si había una verificación Didit en proceso la reemplaza (queda `failed` en el historial y no genera un aviso intermedio; un `Approved` tardío de esa sesión se ignora). Marca en la auditoría la coincidencia del número de documento con otro usuario (señal para el revisor, sin bloquear).
  `reenviarCorreccion()`: atiende una corrección pendiente sin cambiar de estado, exige algún cambio, conserva los documentos reemplazados y audita solo los nombres de los campos modificados. `solicitudParaCorreccion()` entrega los datos propios para precargar el formulario. `coincidenciasDocumento()` y `resumenParaUsuario()` (ahora con `puede_solicitar_manual` y los documentos vigentes por id y tipo).
- **RetencionKycService.php**
  Política de retención separada de la aprobación. Calcula la fecha de eliminación = cierre + plazo por estado (`kyc.retencion`, 90 días tras aprobar y 30 tras rechazo, fallo o expiración; `null` = nunca) y elimina solo archivos de solicitudes cerradas. No elimina si el usuario tiene una mediación activa (estado distinto de Cerrado/Cerrada), una cuenta restringida por mediaciones o `retencion_suspendida`. Los plazos se leen de la configuración en cada ejecución. Nunca cambia el estado de verificación.

### Autorización
- **packages/Reda/RedaAlojamiento/src/Policies/KycPolicy.php**
  `verDocumento`, `verSolicitud` y `gestionar`. El usuario solo accede a lo suyo (comparando `user_id` del documento y de la solicitud). El administrador necesita estar activo y tener `manage_kyc` mediante `UserPermission` (un administrador con otros permisos, como el rol Atención a usuario, no puede ver documentos). Un documento eliminado no lo ve nadie.

### Controladores y rutas
- **Http/Controllers/Kyc/KycUsuarioController.php** (ampliado)
  `opcionesManual()` (tipos, países, límites), `enviarManual()` y `reenviarCorreccion()` (multipart; detectan que el cuerpo superó `post_max_size` y responden 413), `solicitudManual()` (datos propios solo si hay corrección pendiente). El usuario sale siempre de la sesión.
- **Http/Controllers/Kyc/KycDocumentoController.php**
  `GET kyc/documentos/{id}`: lee el archivo, lo descifra, verifica su integridad y lo entrega con `nosniff`, sin caché, CSP restrictiva y sin referer; las imágenes en línea y los PDF como adjunto con nombre genérico. Un documento ajeno o inexistente responde 404 (IDOR) y cada consulta queda en la auditoría.
- **routes/kyc.php**
  Nuevas rutas `kyc/manual/opciones`, `kyc/manual/solicitud`, `POST kyc/manual`, `POST kyc/manual/correccion` y `kyc/documentos/{documento}`. Cada límite de tasa lleva su propio prefijo (`kyc_webhook`, `kyc_didit`, `kyc_manual`, `kyc_correccion`, `kyc_documentos`): sin prefijo, `throttle` comparte el contador entre todas las rutas del mismo usuario.

### Comandos
- **Console/Commands/KycDepurarCommand.php**
  `reda:kyc-depurar [--simular]` aplica la retención y muestra el resumen; `--simular` no modifica nada.
- **Console/Commands/KycAuditarRutasCommand.php**
  `reda:kyc-auditar-rutas [--json]` recorre todas las rutas de los controladores vigilados (`kyc.controladores_vigilados`) y verifica que cada una esté clasificada en `operaciones_restringidas` (con middleware `kyc.verificado`) o en `rutas_permitidas` (con justificación). Falla (código 1) si queda alguna ruta sin clasificar o si una restringida no tiene el middleware. Ejecutarlo en Vesta tras cada actualización del núcleo.

### Bloqueo de operaciones (Fase 6)
Un usuario sin KYC verificado no puede publicar ni reservar. El bloqueo se aplica en 4 capas independientes (la interfaz nunca es la barrera): (1) middleware por **acción** de controlador (no por URI), (2) observadores de modelos, (3) Gate `kyc-verificado` y (4) ventana de reserva en sesión. El interruptor `KYC_ENFORCEMENT` apaga las capas 1 y 2 sin tocar código.
- **Exceptions/KycNoVerificadoException.php**
  Excepción de dominio lanzada por las capas de modelo cuando un usuario no verificado intenta una operación restringida.
- **Services/Kyc/BloqueoKyc.php**
  Decide si una operación se bloquea (usuario autenticado del guard `users`, fuera de consola y de `/admin`, respetando `KYC_ENFORCEMENT`) y abre/consulta la ventana de reserva (`session('kyc_reserva_autorizada')`, 45 min, solo abierta por un usuario verificado).
- **Services/Kyc/RespuestaBloqueoKyc.php**
  Construye la respuesta de bloqueo con el texto aprobado: JSON 403 con `respuesta.codigo='kyc_requerido'` y la URL de verificación para AJAX; redirección a `users/edit-verification` con mensaje flash para peticiones normales.
- **Services/Kyc/ClasificadorOperacionKyc.php**
  Resuelve la etiqueta de una ruta (`null`, `reserva_nueva`, `activacion`, `borrador`, `edicion`) a partir de `operaciones_restringidas`. `reserva_nueva` solo se permite si la reserva es propia y está en estado `Processing` (se comprueba en la base).
- **Services/Kyc/AplicadorMiddlewareKyc.php**
  Aplica el manifiesto de `config/kyc.php` a las rutas ya registradas añadiéndoles el middleware `kyc.verificado`. Se ejecuta en un callback `booted` al final de `boot()` para ver también las rutas que el plugin reemplaza (`PaymentController@index`, `gateway/pay/{gateway}/complete` y `/callback`); por eso el manifiesto usa los nombres de acción finales y conserva los originales en `acciones_alternativas`.
- **Services/Kyc/AuditorRutasKyc.php**
  Lógica del comando de auditoría: lista rutas vigiladas y las clasifica (restringida, permitida con justificación, sin clasificar).
- **Http/Middleware/EnsureKycVerified.php**
  Alias `kyc.verificado`. Si la operación es restringida y el usuario no está verificado, responde con `RespuestaBloqueoKyc` (el bloqueo es del servidor, no de la interfaz).
- **Observers/BloqueoKycObserver.php**
  Observador de `Properties`, `Bookings`, `Experiencia` y `ActividadExperiencia`. Bloquea crear/activar publicaciones y crear/confirmar reservas (el chat crea `Bookings` como consulta, con `status='Inquiry'` o vacío en versiones anteriores, y se permite; convertirla en reserva real se bloquea en `updating`).
- **config/kyc.php** (ampliado): `url_seccion`, `reservas`, `controladores_vigilados` (incluye `PasarelaPagoController`, hueco que encontró la auditoría), `operaciones_restringidas`, `acciones_alternativas` y `rutas_permitidas`.

### Panel administrativo KYC (Fase 5)
Se integra en el panel actual (guard `admin`, layout `admin.template`) bajo `admin/reda/kyc`. Exige el permiso `manage_kyc` en 3 capas: middleware de la ruta, `KycAdminController::autorizar()` (Gate `gestionar`, que además exige administrador activo) y `KycService` (lo vuelve a comprobar antes de escribir). Todas las decisiones pasan por `KycService`, único escritor del estado.
- **Services/Kyc/ConsultaKycAdmin.php**
  Consultas de solo lectura: `listar()` (10 por página; grupos pendientes, en proceso, verificados, rechazados, fallidos, expirados; método didit/manual; búsqueda por nombre, correo o número de documento exacto mediante su hash HMAC, con `LIKE ... ESCAPE` para neutralizar comodines), `contadores()` y `detalle()`.
- **Http/Controllers/Admin/Kyc/KycAdminController.php**
  `index`, `show` (cabecera `no-store`; registra la consulta con antirrebote), `aprobar`, `rechazar`, `solicitarCorreccion`, `observacion`, `retencion`, `revelarDocumento` y `documento` (descarga protegida con auditoría del administrador). Respuestas JSON con la estructura estándar.
- **Services/Kyc/KycService.php** (ampliado)
  `aprobarManual()` (exige manual en revisión, sin corrección pendiente, con datos y documentos obligatorios), `rechazarManual()` (motivo obligatorio de 5 a 1000 caracteres; se permite aunque haya una corrección sin atender), `solicitarCorreccion()`, `agregarObservacion()` (historial interno con tope), `suspenderRetencion()`, `revelarNumeroDocumento()` (auditado) y `registrarConsultaAdmin()`. Las decisiones sobre solicitudes Didit se rechazan con 409. Dos administradores simultáneos: la segunda decisión recibe 409 (bloqueo de la fila `usuarios_kyc`).
- **Support/EtiquetasKyc.php**
  Textos y clases visuales traducibles para estados, métodos, orígenes y tipos de evento del historial.
- **Events/CorreccionKycSolicitada.php**
  Evento emitido tras confirmar la transacción de una solicitud de corrección; lo usará la Fase 8 (notificaciones).
- **resources/views/admin/kyc/index.blade.php**
  Listado: tabla en escritorio y tarjetas en móvil, pestañas con contadores, filtros, búsqueda y paginación `admin.general.paginacion`.
- **resources/views/admin/kyc/detalle.blade.php**
  Detalle: datos (documento enmascarado), documentos con miniatura y descarga protegida, resultado de Didit, observaciones, acciones y historial. Todo escapado con `{{ }}`.
- **resources/views/admin/general/main_footer.blade.php** (modificado)
  Añade `tieneAccesoKyc` y `kycPendientes` a `window.RedaAdminUser` para mostrar el menú solo a quien tiene `manage_kyc`.
- **resources/js/admin/vistas/kyc/** (`indexKyc.js`, `detalleKyc.js`, `peticionAdminKyc.js`, `aprobarSolicitudKyc.js`, `rechazarSolicitudKyc.js`, `solicitarCorreccionKyc.js`, `agregarObservacionKyc.js`, `alternarRetencionKyc.js`, `revelarNumeroDocumentoKyc.js`)
  Peticiones AJAX con la estructura del proyecto y animación "Espera"; el modal de rechazo y de corrección exige el texto; el número de documento se revela bajo demanda.
- **resources/js/admin/general/menus/menuKycAdmin.js**
  Inyecta en el menú lateral la entrada "Verificación KYC" (con insignia de pendientes) cuando `tieneAccesoKyc` es verdadero.
- **resources/sass/admin/main.scss** (ampliado): clases `.reda-kyc-*`. **webpack.mix.js** (ampliado): entradas `indexKyc.min.js` y `detalleKyc.min.js`.

### Interfaz del usuario KYC (Fase 7)
Se inyecta con JavaScript en la pantalla existente "Verificaciones" (`users/edit-verification`) sin modificar su vista Blade: la tarjeta "Verificación de identidad" se coloca antes de las verificaciones de correo, Facebook y Google. El script solo se carga en esa pantalla (condición en `views/general/main_footer.blade.php`). El bloqueo de publicar y reservar sigue siendo del servidor; esta interfaz solo guía al usuario. Se compila con `BUILD_ALOJAMIENTO=true` a `public/js/reda/vistas/frontend/kyc/verificacionIdentidad.min.js`.
- **resources/js/vistas/frontend/kyc/verificacionIdentidad.js**
  Punto de entrada. Inyecta el contenedor, carga el estado y maneja los botones (`data-kyc-accion`): `didit` (pide la sesión y navega solo a URL `https://`), `manual` y `corregir` (cargan las opciones y abren el formulario) y `actualizar`. Mientras el estado es `pending` o `in_progress` consulta cada 20 s (solo con la pestaña visible y hasta 45 veces). Cada acción del usuario muestra la animación de espera.
- **resources/js/vistas/frontend/kyc/renderizarTarjetaKyc.js**
  Genera el HTML de la tarjeta según el estado: verificado, solicitud en revisión (con la lista de documentos enviados y su enlace protegido), corrección pendiente (muestra lo pedido), Didit abierto o en revisión, rechazada (con el motivo), fallida, expirada o sin verificar. Todo se escapa; el texto del administrador se muestra con `white-space: pre-wrap`, nunca como HTML.
- **resources/js/vistas/frontend/kyc/formularioManualKyc.js**
  Modal del formulario manual (nuevo o de corrección). Los documentos que pide dependen del tipo elegido (el pasaporte no tiene reverso). Valida en el navegador lo básico (obligatorios, mayoría de edad, formato y tamaño de archivo; la selfie solo admite imágenes), muestra vista previa, y los errores 422 del servidor aparecen junto a cada campo. En corrección precarga los datos, no pide el consentimiento de nuevo y deja los documentos como opcionales.
- **resources/js/vistas/frontend/kyc/peticionUsuarioKyc.js**
  Petición AJAX común (normal y multipart) que siempre devuelve la estructura estándar, con mensajes claros para 401/419 (sesión), 413 (archivos grandes) y 429 (demasiados intentos).
- **consultarEstadoKyc.js**, **iniciarVerificacionDiditKyc.js**, **obtenerOpcionesManualKyc.js**, **obtenerSolicitudCorreccionKyc.js**, **enviarSolicitudManualKyc.js**
  Una función por endpoint (`GET /kyc/estado`, `POST /kyc/didit/iniciar`, `GET /kyc/manual/opciones`, `GET /kyc/manual/solicitud`, `POST /kyc/manual` o `/kyc/manual/correccion`).
- **resources/js/vistas/frontend/kyc/utilidadesKyc.js**
  `t()` (traducción con respaldo), `escapar()`, `mensajeSeguro()` y `formatearTamano()`.
- **resources/sass/main.scss** (ampliado): clases `.reda-kyc-*` del frontend (tarjeta, opciones táctiles, formulario, espacios de archivo); en celular el formulario ocupa toda la pantalla y las opciones se apilan.
- **Http/Controllers/Kyc/KycUsuarioController.php** (modificado): `retorno()` y el fallo de `iniciarDidit()` sin AJAX ahora redirigen a la pantalla de verificaciones (`kyc.url_seccion`) en lugar del dashboard.

## Sistema de Puntos de Control y Memoria Operativa

### Archivos de Control en Tiempo Real
- **previo_cambios_realizados.md**
  Archivo ubicado en la raíz del proyecto que actúa como punto de control en tiempo real durante procesos de desarrollo en curso. Su propósito es prevenir la pérdida de contexto o avances inacabados ante eventualidades como cortes de suministro eléctrico o pérdidas de conectividad a Internet.
  - **Protocolo de Lectura:** Al inicio de cada sesión o antes de iniciar modificaciones, la IA lo lee obligatoriamente para verificar si existió una interrupción previa y en qué punto exacto quedaron los cambios.
  - **Reinicio Limpio:** Tras refrescar la memoria, se limpia e inicializa con la nueva tarea activa para evitar basura acumulada de sesiones anteriores.
  - **Registro Progresivo:** Durante tareas extensas o con múltiples archivos, la IA registra progresivamente cada archivo modificado o creado a medida que avanza, salvaguardando el progreso antes de continuar al siguiente paso.

### Modalidad de Trabajo Autónomo y Reporte Final
- **Directrices en GEMINI.md y REDA_PAUTAS_DESARROLLO.md:**
  Define el modelo operativo de trabajo autónomo de la IA ante solicitudes de desarrollo y mantenimiento en el proyecto:
  - **Ejecución Directa y Continua:** La IA aplica todas las modificaciones y creaciones de código fuente de forma directa y autónoma, sin realizar pausas intermedias ni requerir aceptación/rechazo manual paso a paso por parte del usuario.
  - **Salvaguarda Progresiva:** Mantiene actualizado en tiempo real el archivo `previo_cambios_realizados.md` durante el proceso de desarrollo.
  - **Documentación Completa y Reporte Exhaustivo:** Al culminar todas las modificaciones, la IA actualiza obligatoriamente `LOG_DESARROLLO_REDA.md`, este manual técnico y las cabeceras correspondientes, entregando al usuario un informe final claro, pedagógico y detallado con los archivos intervenidos, las soluciones implementadas y las instrucciones pertinentes para el servidor Vesta de desarrollo.
