# Documentación de Archivos del Plugin RedaAlojamiento

## Modificaciones en Archivos del Core (Originales)
*Excepciones delimitadas con comentarios de Inicio/Fin para el plugin REDA.*

### Controladores (Admin)
- **app/Http/Controllers/Admin/SettingsController.php**
  Se modificó el método `email` para permitir el guardado manual del campo `email_status`. La lógica original dependía del éxito de un envío de prueba; con este cambio, se respeta la selección manual del administrador ("Active"/"Inactive") permitiendo la persistencia del sistema de correos incluso si la validación automática falla temporalmente.

### Controladores (General)
- **app/Http/Controllers/EmailController.php**
  Se inyectaron logs de depuración en el método `welcome_email` para rastrear la configuración de SMTP y el estado del envío. Se actualizó el método `sendPhpEmail` para redirigir errores y confirmaciones al log de Laravel en lugar de imprimirlos en pantalla (`echo`), mejorando la estabilidad de las respuestas AJAX.

### Vistas (Admin)
- **resources/views/admin/settings/email.blade.php**
  Inyección de un campo `select` para la columna `email_status`. Esta modificación habilita la interfaz de usuario para que el administrador pueda forzar el estado del sistema de correos desde el panel de configuración de email.

### Configuración
- **config/mail.php**
  Se añadió la opción `verify_peer` al transport `smtp` vinculada a la variable de entorno `MAIL_VERIFY_PEER`. Esta modificación permite desactivar opcionalmente la verificación de certificados SSL/TLS desde el archivo `.env`, facilitando la conexión a servidores SMTP con certificados compartidos o discrepancias de nombre de host (CN).

## Alojamientos (Hospedajes)

### JavaScript (Vistas)
- **packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/propiedad_detalle.js**
  Gestiona el sistema de reserva en modal y botones flotantes para la vista de detalle de propiedad (property.single). Implementa la lógica para ocultar el sidebar original, inyectar el botón flotante con animación y manejar la apertura del modal mediante el hash `#reservar`. Incluye una verificación proactiva de reservas activas para prevenir duplicidades. Se actualizó para desactivar el `daterangepicker` original del core, inyectar inputs de fecha adaptados y configurar una lógica robusta mediante **Flatpickr** (Llegada y Salida con rango dinámico y fecha mínima de salida), sincronizándose con los inputs ocultos del core y gatillando el recálculo automático de precios (`price_calculation`).
  **Actualización Desglose Huéspedes (Airbnb-style):** Oculta el selector `#number_of_guests` original dentro de la modal e inyecta dos selectores dinámicos para "Adulto(s)" y "Niño(s)". Sincroniza dinámicamente las opciones para respetar la capacidad máxima (`accommodates`), actualiza `#number_of_guests` con el total y gatilla el recálculo de precios y tarifas en tiempo real.

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
  Observador registrado en `RedaAlojamientoServiceProvider` para el modelo original `App\Models\Bookings`. Al crearse una reserva (`created`), intercepta los valores de adultos y niños asegurados en sesión o en la petición y los persiste tanto en la tabla original `booking_details` (`field = 'adultos'`, `field = 'ninos'`) como en la tabla auxiliar `reserva_huespedes` (si está migrada).

- **packages/Reda/RedaAlojamiento/src/Helpers/helpers.php**
  Se incorporó la función helper `reda_obtener_desglose_huespedes($booking)`. Implementa una jerarquía de consulta: primero `reserva_huespedes`, luego `booking_details` y como fallback retrocompatible para reservas antiguas asigna el total de `guest` a Adultos y `0` a Niños. Retorna array asociativo con `adultos`, `ninos`, `total` y `texto` formateado.

### Controladores
- **packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaPaymentController.php**
  Controlador extendido para gestionar el flujo de pagos y redirecciones de reserva. Asegura que los datos de la reserva se mantengan persistentes durante el proceso de login y valida si el usuario ya posee una reservación vigente antes de permitir el acceso al formulario de reserva, redirigiendo con alertas personalizadas si es necesario. Se actualizó para asegurar en sesión `payment_adultos` y `payment_ninos`.

- **packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaBookingController.php**
  Controlador para consultas de reservas. Se enriqueció `getBookingDetails` para retornar `adultos`, `ninos` y `huespedes_desglose`. Se incorporó el método `getHuespedesInfo` que devuelve el desglose de huéspedes por `booking_id` o `code` para consumo de las vistas AJAX.

## Mediaciones (Disputas)
... (resto del archivo)

- **packages/Reda/RedaAlojamiento/resources/views/admin/disputa/index.blade.php**
  Vista de administración para el listado y gestión de mediaciones (Disputas). Esta interfaz permite a los administradores visualizar todos los casos de mediación, filtrarlos por estado, realizar seguimiento del progreso mediante una línea de tiempo y acceder a los chats de comunicación entre las partes involucradas.

### JavaScript (Vistas)
- **packages/Reda/RedaAlojamiento/resources/js/vistas/disputa/disputas/indexDisputas.js**
  Controlador de la vista de índice de disputas (Mediaciones). Este archivo gestiona la lógica del dashboard de mediaciones, incluyendo la carga paginada por estados, la renderización de la lista de casos, la línea de tiempo de progreso, el visor de medios (imágenes/PDF) y el chat específico de la mediación.

### JavaScript (Administración)
- **packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/indexDisputas.js**
  Controlador de administración para el panel de mediaciones (Disputas). Este archivo gestiona la lógica del dashboard de mediaciones para el administrador, permitiendo filtrar por estados, visualizar el progreso en la línea de tiempo, gestionar mensajes entre las partes y visualizar documentos adjuntos (fotos/PDFs).

- **packages/Reda/RedaAlojamiento/resources/js/general/ocultarConsultas.js**
  Script responsable de ocultar dinámicamente las consultas (Inquiries) y reservaciones sin pago asociado en las vistas de "Mis Viajes" y "Mis Reservas". Utiliza un MutationObserver optimizado con técnicas de *debouncing* y banderas de control para evitar ciclos infinitos de retroalimentación con otros scripts. Implementa una lógica de emparejamiento multicapa (ID, Código, Nombre y Fechas) contra una lista blanca obtenida del servidor para garantizar que solo se muestren registros con pagos legítimos.

- **packages/Reda/RedaAlojamiento/resources/js/general/reserve-injection.js**
  Script encargado de inyectar o actualizar el botón de "Reservar" / "Ver reserva" en las tarjetas de inmuebles de toda la aplicación (Home, Detalle de Propiedad y Mis Viajes). Utiliza una lógica centralizada basada en una "Lista Blanca" proporcionada por el servidor, asegurando consistencia absoluta entre las tres vistas. Implementa una jerarquía de decisión donde la vigencia de la fecha tiene prioridad máxima, permitiendo revertir botones a "Reservar" si la estancia ya concluyó. Incluye protecciones contra recursividad infinita mediante *debouncing*.

- **packages/Reda/RedaAlojamiento/resources/js/general/mensajes.js**
  Script de integración general para funcionalidades de mediación. Este archivo se encarga de inyectar la lógica de mediación en vistas preexistentes del proyecto principal, como el Inbox (chat general) y la barra lateral de detalles de reservación. Permite verificar disputas, solicitar nuevas mediaciones y enriquecer el chat original con información del plugin Reda.

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
  Controlador para la gestión de mediaciones (disputas). Maneja la lógica de negocio para crear, verificar y listar mediaciones. Incluye validaciones de estado de reserva para permitir mediaciones solo en reservaciones formales y no en simples consultas.

- **packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaBookingController.php**
  Controlador para gestiones relacionadas con las reservaciones dentro del ecosistema REDA. Proporciona métodos para verificar el estado de las reservas de un usuario, como la obtención de IDs de inmuebles con reservas activas y la recuperación de detalles completos (foto, ubicación, fechas, costos) para su visualización en componentes interactivos del frontend.

- **packages/Reda/RedaAlojamiento/src/Http/Controllers/General/ChatController.php**
  Controlador encargado de gestionar el inicio de conversaciones directas entre huéspedes y anfitriones desde la vista de propiedad. Implementa la lógica para buscar una conversación existente o crear un nuevo registro de tipo `Inquiry` (Consulta) en la tabla de reservaciones (`bookings`). Este estado permite que las consultas iniciales se mantengan separadas de las reservaciones reales en los listados de viajes y reservas del sistema.


## Mensajería (Inbox)

### Vistas
- **packages/Reda/RedaAlojamiento/resources/views/users/inbox.blade.php**
  Vista Blade principal para el Inbox unificado del plugin Reda. Define la interfaz de mensajería con sidebar de avatares duales (propiedad y participantes), contenedor de mensajes enriquecidos e indicadores visuales (badges) para mensajes pendientes de leer. Incluye modales de seguridad desacoplados del flujo de contenido principal para prevenir rupturas del contexto de apilamiento (stacking context): `modalAdvertenciaMensajeReda` (al detectar datos sensibles en el envío) y `modalAdvertenciaPrivacidadReda` (aviso preventivo al cargar la vista con control de persistencia en `sessionStorage`), además del overlay para ampliación de fotos (`reda-chat-zoom-overlay`).

### JavaScript (Vistas y General)
- **packages/Reda/RedaAlojamiento/resources/js/vistas/inbox/inbox.js**
  Controlador Javascript para la vista de Inbox personalizada. Gestiona la sincronización inmediata de la conversación activa detectando el parámetro `?id=...` en la URL para evitar retardos o llamadas artificiales a clics, carga dinámica de conversaciones mediante AJAX, envío de mensajes, navegación estilo WhatsApp en móviles, neutralización de conflictos y manejo dinámico de contadores de mensajes no leídos. Reubica dinámicamente los modales de seguridad como hijos directos de `body` (`.appendTo('body')`) para asegurar que el `modal-backdrop` de Bootstrap 4 nunca los eclipse, e implementa limpieza forzada de backdrops residuales en eventos `hidden.bs.modal`.
- **packages/Reda/RedaAlojamiento/resources/js/general/mensajes.js**
  Script de integración para mediaciones y mensajería. Gestiona la caja de mediación en `#booking`, modales de disputas y desplazamiento suave (`scrollIntoView`) en el sidebar sin interferir con la renderización inicial del servidor ni generar colisiones de modales de carga.
- **packages/Reda/RedaAlojamiento/resources/js/general/notificaciones.js**
  Módulo de notificaciones globales y animaciones de espera (`RedaNotificaciones`). Incorpora protección en `ocultar()` ante transiciones activas de apertura (`_isTransitioning`), garantizando que la ocultación se complete y se purguen backdrops huérfanos sin afectar modales interactivos abiertos en la vista.
- **packages/Reda/RedaAlojamiento/resources/js/chat-injection.js**
  Script encargado de la reubicación de la sección del anfitrión debajo del mapa en la vista de propiedad (`property.single`) e inyección del botón interactivo "Enviar mensaje". Inicia la conversación con `iniciarChat`, gestiona el estado de espera y redirige limpiamente al Inbox unificado (`/inbox?id=...`), excluyendo observadores de mutación innecesarios en la ruta de Inbox.

### Controladores
- **packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaInboxController.php**
  Controlador para la mensajería unificada. Gestiona el historial de chat enriquecido, marca de mensajes leídos, procesamiento de respuestas con virtualización de datos y obtención del conteo de mensajes no leídos. Implementa una lógica de ordenamiento por prioridad para destacar conversaciones con mensajes pendientes.

## Verificación de Correo (Signup y Login)

### Controladores
- **packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaUsuarioController.php**
  Controlador que extiende de `App\Http\Controllers\UserController` para desacoplar el flujo de registro y confirmación de correo de los archivos del core. Sobrescribe `create` para registrar al usuario, enviar el correo de verificación vía `welcome_email` e interrumpir el inicio de sesión automático, redirigiendo a login con una notificación de cuenta pendiente. Sobrescribe `confirmEmail` para permitir la activación de la cuenta desde el enlace del correo sin requerir una sesión activa previa, activando el estado en `users`, actualizando `users_verification.email = 'yes'` y redirigiendo a login con el modal de confirmación exitosa.

- **packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaLoginController.php**
  Controlador que extiende de `App\Http\Controllers\LoginController` para proteger el acceso al sistema. Sobrescribe `authenticate` para comprobar las credenciales del usuario; si la contraseña es correcta pero `users_verification.email` no es 'yes', bloquea el inicio de sesión y redirige a la vista de login con la variable flash `correo_no_verificado` para desplegar el modal interactivo de aviso y corrección de correo.

- **packages/Reda/RedaAlojamiento/src/Http/Controllers/General/VerificacionCorreoController.php**
  Controlador encargado de la verificación, actualización y reenvío del correo de confirmación de cuenta:
  - `actualizarCorreoYReenviar(Request $request, EmailController $emailController)`: Valida la cuenta existente y no verificada (`users_verification.email != 'yes'`), comprueba la disponibilidad del nuevo correo, actualiza `users.email`, purga tokens previos en `password_resets` y reenvía el correo de confirmación con `welcome_email`.
  - `reenviarCorreoVerificacion(Request $request, EmailController $emailController)`: Endpoint `POST reda/usuarios/reenviar-correo-verificacion` que permite reenviar el correo de verificación sin modificar la dirección registrada. Valida el formato del correo, busca la cuenta en la tabla `users` (devolviendo 404 pedagógico si no existe aún), confirma que la cuenta no esté validada previamente (400 si ya está activa), renueva el token en `password_resets` y reenvía el correo de confirmación invocando `EmailController@welcome_email`, retornando la estructura estándar JSON de REDA.

### Vistas
- **packages/Reda/RedaAlojamiento/resources/views/general/modal_verificacion_correo.blade.php**
  Estructura de modales Bootstrap 4.5 e inyección de datos de sesión a JavaScript (`window.RedaVerificacionData` con `rutaActualizarCorreo`, `rutaReenviarCorreo`, `correoNoVerificado`, `correoRegistradoPendiente` y `csrfToken`). Contiene:
  1. `#reda_modal_confirmar_email_signup`: Muestra el email ingresado en el registro y pregunta "¿Por favor verifique si la dirección de su correo es correcta?", disponiendo de 3 botones interactivos:
     - "Corregir email": despliega el input para editar la dirección antes de enviar.
     - "Re-enviar": botón con spinner que envía una petición asíncrona a `rutaReenviarCorreo` por si el usuario ya se había registrado previamente pero no le llegó el primer correo, mostrando alertas de éxito o aviso si aún no existe la cuenta.
     - "Email correcto": procede con el envío regular del formulario de registro.
  2. `#reda_modal_correo_no_verificado_login`: Alerta en login cuando el usuario tiene credenciales válidas o acaba de registrarse pero su correo no ha sido verificado, con botón "Re-enviar" directo y botón "Corregir correo" con formulario interactivo y reenvío asíncrono.
  3. `#reda_modal_correo_confirmado_exito`: Mensaje de confirmación exitosa con botón para "Iniciar sesión" tras hacer clic en el enlace del correo.

### JavaScript (Vistas)
- **packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/verificacionCorreo.js**
  Controlador JavaScript del flujo de verificación, reenvío y corrección de correo:
  - Intercepta el formulario `#signup_form` previa validación con jQuery Validate y `ageValidate()`, abre el modal de confirmación antes del envío y ofrece la opción de enviar, corregir o reenviar.
  - Gestiona el botón `#reda_btn_reenviar_email_signup` para reenviar el correo de confirmación vía AJAX hacia `rutaReenviarCorreo` con bloqueo del botón, animación de spinner y mensajes reactivos de éxito o error.
  - Detecta si la sesión contiene `correo_no_verificado` o `correo_registrado_pendiente` para desplegar el modal en login, permitiendo el reenvío inmediato con `#reda_btn_reenviar_correo_login` o la corrección de correo.
  - Detecta `correo_confirmado_exitoso` para desplegar el modal de bienvenida y enfocar el campo de login.

## Flujo de Pago y Confirmación de Reservas

### JavaScript (Vistas)
- **packages/Reda/RedaAlojamiento/resources/js/vistas/pago/frontend/pagos.js**
  Controlador JavaScript del lado del cliente para el formulario de confirmación de pago (`#payment-form`). Implementa validación estricta y reactiva en el cliente cuando aplica la carga de comprobante de pago (`attachment[]`) y nota/mensaje al anfitrión (`note`), como ocurre en pasarelas de Transferencia Bancaria Directa (`DirectBankTransfer`). Utiliza intercepción en fase de captura del DOM (Capture Phase) en el botón de confirmación (`#payment-form-submit`) y en el evento `submit` del formulario para adelantarse a scripts de terceros que deshabilitan el botón de manera prematura. Si falta alguno de los campos obligatorios, cancela la propagación del evento, previene el envío, restaura el estado interactivo del botón y el spinner, y muestra mensajes de error en español estilizados en los contenedores correspondientes (`span.text-danger`), evitando la recarga del navegador y garantizando que el usuario no pierda el archivo del comprobante adjunto. Incluye limpiadores de errores en tiempo real en los eventos `change` e `input`, enfoca y desplaza la vista hacia el primer campo inválido, y activa la animación de espera ("Su reserva está siendo procesada") únicamente cuando el formulario es 100% válido.

## Administración y Menú Lateral (Backend)

### JavaScript (Administración General)
- **packages/Reda/RedaAlojamiento/resources/js/admin/general/menus/menuLateralAdmin.js**
  Orquesta la inyección reactiva y no invasiva de las opciones del plugin REDA en el menú lateral de AdminLTE: "Negocios", el submenú "Mediaciones" (con opciones desplegables de "Listado" y condicionalmente "Configuración" exclusivo para Rol 1) y "Soporte Técnico". Incorpora lógica adaptativa para la inyección de "Mediaciones": si el usuario conectado no posee el elemento `Bookings` en su menú lateral (como ocurre con el Rol 2 "Atención al usuario"), el script busca puntos de anclaje jerárquicos alternativos (`#menu-negocios`, `Properties`, `Customers`, `Dashboard` o el final del contenedor `.sidebar-menu`). Transforma "Mediaciones" en un elemento acordeón interactivo (`.treeview`) que despliega "Listado" (apunta al index de mediaciones con animación de espera) y "Configuración" (enlace que conduce a `/admin/reda/disputas/configuracion` con animación de espera, visible y disponible únicamente para usuarios con Rol 1). Al hacer clic o tocar en "Mediaciones", asegura que en la opción "Listado" también aparezca el contador dinámico de mediaciones activas (`Listado (N)`), sincronizado con el encabezado padre. Mantiene abierto el submenú y resalta la opción activa cuando la ruta coincide con `/admin/reda/disputas` o `/admin/reda/disputas/configuracion`, y actualiza en tiempo real los contadores vía AJAX.
  **Campanita de Alertas en Header y Submenú "Mensajes y Alertas":**
  1. **Campanita en Header:** Inyecta en el navbar superior del panel administrativo (`.navbar-nav.ms-auto`) la campanita `#reda-admin-header-bell` con su badge `#reda-admin-bell-badge`, la cual enlaza a `/admin/reda/alertas` y refleja en tiempo real el número de alertas no leídas.
  2. **Transformación de "Messages":** Reemplaza la opción de enlace simple "Messages" del core por el submenú interactivo "Mensajes y Alertas" (`#menu-mensajes`), que agrupa:
     - "Mensajes": Conduce a la vista original de histórico de mensajes (`/admin/messages`).
     - "Alertas": Conduce a la vista de alertas administrativas (`/admin/reda/alertas`), mostrando la insignia y contador de alertas no leídas (`Alertas (N)`).
  3. **Sincronización Reactiva:** Escucha el evento global `reda:actualizar-contador-alertas` y realiza sondeo periódico cada 60 segundos hacia `/admin/reda/alertas/count-no-leidas`, actualizando dinámicamente tanto la campanita superior como las insignias del menú lateral sin requerir recarga de página.

### Vistas (Administración General)
- **packages/Reda/RedaAlojamiento/resources/views/admin/general/main_footer.blade.php**
  Archivo maestro para la inyección de modales y scripts globales en el panel administrativo. Expone hacia el contexto global de JavaScript el objeto `window.RedaAdminUser` con el ID del administrador, su `roleId`, su nombre de rol, la bandera booleana `tieneAccesoMediaciones` (activa para los roles 1 "Admin" y 2 "Atención al usuario") y la propiedad `esAdminTotal` (true exclusivamente para usuarios con Rol 1 "Admin"), permitiendo que los scripts frontend conozcan el perfil del usuario autenticado sin llamadas adicionales.

### Controladores (Administración)
- **packages/Reda/RedaAlojamiento/src/Http/Controllers/Admin/Disputa/DisputaController.php**
  Controlador para el panel de mediaciones en el backend. Gestiona la visualización, asignación y configuración de mediaciones según el rol del usuario conectado:
  - **Rol 1 (Admin):** Visualiza todas las mediaciones en el panel. Consulta y expone la colección de agentes disponibles (usuarios de la tabla `admin` con roles 1 y 2 activos). Procesa la asignación directa de cualquier agente a una mediación. Accede con exclusividad a los métodos `configuracion()` y `guardarConfiguracion()` para definir los umbrales de mediaciones permitidas.
  - **Rol 2 (Atención al usuario / Agente):** Aplica un filtro estricto en `obtenerDisputasPaginadas`, `obtenerConteoDisputasActivas` y `getDetailModal` para mostrar únicamente las mediaciones que no han sido asignadas o tomadas (`id_usuario_agente_asignado IS NULL` o `0`) y aquellas asignadas a él mismo. Las mediaciones asignadas a otros agentes quedan completamente ocultas y protegidas ante accesos no autorizados. Tiene restringido el acceso a la configuración (403 Forbidden).
  - **Método `asignarAgente`:** Endpoint `POST /admin/reda/disputas/asignar-agente` que actualiza la columna `id_usuario_agente_asignado` en la tabla `disputas`. Permite a usuarios de Rol 1 asignar cualquier agente o a sí mismos, y a usuarios de Rol 2 tomar mediaciones disponibles asignándose a sí mismos, retornando la estructura estándar JSON de REDA con los datos de avatar y nombre del agente.
  - **Métodos de Configuración (`configuracion` y `guardarConfiguracion`):** Endpoints `GET /admin/reda/disputas/configuracion` y `POST /admin/reda/disputas/configuracion/store` protegidos exclusivamente para Rol 1. Gestionan la recuperación y almacenamiento en la tabla `settings` de los parámetros con `type = 'Mediaciones'`: `'Cantidad mediaciones permitidas primer aviso'` y `'Cantidad mediaciones segundo aviso y suspensión'`.

### Vistas y Scripts de Administración
- **packages/Reda/RedaAlojamiento/resources/views/admin/disputa/index.blade.php**
  Vista principal del panel de mediaciones para el administrador. Expone las credenciales y perfil del usuario conectado en `window.RedaAdminAccess` (`roleId`, `adminId`, `isFullAdmin`).
- **packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/indexDisputas.js**
  Controlador del dashboard administrativo de mediaciones. Implementa la lógica reactiva de asignación:
  - Para Rol 1: Sustituye el texto del agente no asignado por un control `<select>` con los agentes disponibles y el propio admin. Al seleccionar, ejecuta la asignación vía AJAX con bloqueo de espera y renderiza la foto de perfil y nombre del agente asignado, con soporte para reasignación interactiva.
  - Para Rol 2: Sustituye el texto por un suiche tipo toggle (`form-check form-switch`) con la etiqueta "Tomar mediación". Al activarlo, toma la mediación vía AJAX con animación de espera y actualiza la tarjeta mostrando la foto de perfil y el nombre del agente.
  - Sincroniza dinámicamente la información del agente asignado en las tarjetas del listado, el panel lateral de detalles y el acordeón en dispositivos móviles.
- **packages/Reda/RedaAlojamiento/resources/views/admin/disputa/configuracion.blade.php**
  Vista Blade administrativa para la configuración de mediaciones permitidas, reservada para administradores con Rol 1. Renderiza un panel/recuadro titulado "Cantidad de mediaciones permitidas" que contiene dos campos de entrada numéricos: "Cantidad de mediaciones para primer aviso" y "Cantidad de mediaciones para segundo aviso y suspensión de cuenta". Integra validación y envío asíncrono con animación de espera hacia `route('reda.admin.disputas.configuracion.store')`.
- **packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/configuracionDisputas.js**
  Controlador JavaScript del formulario de configuración de mediaciones permitidas. Intercepta el evento submit, valida en el cliente que los valores sean números enteros no negativos y que el segundo aviso no sea menor que el primero, activa la animación de espera global (`window.RedaNotificaciones.esperar()`), envía los datos vía AJAX con token CSRF y presenta notificaciones reactivas de confirmación o error basadas en el formato estándar JSON de REDA.

## Sistema de Alertas y Suspensiones por Límites de Mediaciones

### Base de Datos y Migraciones
- **packages/Reda/RedaAlojamiento/database/migrations/2026_10_04_000000_crear_tabla_alertas_admin.php**
  Migración que crea la tabla auxiliar `alertas_admin` para almacenar las notificaciones del sistema dirigidas a los administradores:
  - Campos: `id`, `admin_id` (nullable, relación con tabla `admin`), `titulo`, `mensaje`, `tipo` ('primer_aviso', 'suspension', 'general'), `disputa_id` (nullable), `user_id` (nullable, relación con `users`), `leido` (booleano con valor predeterminado 0), `fecha_lectura` (timestamp nullable) y marcas de tiempo (`created_at`, `updated_at`).
  - Cumple estrictamente con las directrices de nomenclatura en español y soporte nullable para todas las columnas excepto la clave primaria.
- **packages/Reda/RedaAlojamiento/database/migrations/2026_10_04_000001_crear_tabla_usuarios_avisos_mediaciones.php**
  Migración que crea la tabla auxiliar `usuarios_avisos_mediaciones` como registro de auditoría y control de umbrales para prevenir envíos duplicados de correos o suspensiones reiteradas:
  - Campos: `id`, `user_id` (relación foránea con `users`), `conteo_mediaciones`, `primer_aviso_enviado` (booleano), `fecha_primer_aviso` (timestamp nullable), `segundo_aviso_enviado` (booleano), `fecha_segundo_aviso` (timestamp nullable), `cuenta_suspendida` (booleano), `fecha_suspension` (timestamp nullable), `motivo` (texto descriptivo) y marcas de tiempo.

### Modelos Eloquent
- **packages/Reda/RedaAlojamiento/src/Models/Alerta/AlertaAdmin.php**
  Modelo Eloquent para la tabla `alertas_admin`. Define los campos asignables en `$fillable`, casteo booleano para `leido` y timestamp para `fecha_lectura`. Establece las relaciones `admin()` (`App\Models\Admin`), `usuario()` (`App\Models\User`) y `disputa()` (`Reda\RedaAlojamiento\Models\Disputa\Disputa`).
- **packages/Reda/RedaAlojamiento/src/Models/Disputa/UsuarioAvisoMediacion.php**
  Modelo Eloquent para la tabla `usuarios_avisos_mediaciones`. Gestiona el estado de control de avisos y suspensión por usuario, con conversiones de tipo para banderas booleanas y fechas, y relación `usuario()` con `App\Models\User`.

### Servicios y Lógica de Negocio
- **packages/Reda/RedaAlojamiento/src/Services/MediacionAlertaService.php**
  Servicio centralizado que orquesta la verificación automática de umbrales cada vez que se registra una nueva mediación (`DisputaController@store`).
  - **Recuperación de Configuración:** Consulta en la tabla `settings` los parámetros `Cantidad mediaciones permitidas primer aviso` y `Cantidad mediaciones segundo aviso y suspensión`.
  - **Evaluación de Partes Involucradas:** Determina la contraparte y el iniciador de la mediación y calcula el conteo total acumulado de mediaciones para cada uno.
  - **Disparo de Primer Aviso:** Si el conteo alcanza o supera el umbral de primer aviso y no ha sido notificado previamente:
    1. Registra la emisión del aviso en `usuarios_avisos_mediaciones` con fecha y hora actual.
    2. Envía correo preventivo al usuario (`emails.primer_aviso_usuario`).
    3. Envía un mensaje formal al buzón `/inbox` del usuario utilizando `App\Models\Messages` con metadato `sender_type = 'admin'` (`MensajeMetadata`).
    4. Envía correo informativo a todos los administradores activos (`emails.primer_aviso_admin`).
    5. Genera un registro en `alertas_admin` para cada administrador activo (`tipo = 'primer_aviso'`).
  - **Disparo de Suspensión (Segundo Aviso):** Si el conteo alcanza o supera el umbral máximo de suspensión:
    1. Actualiza el estatus nativo del usuario a `'Inactive'` en la tabla `users` (bloqueando de inmediato su inicio de sesión en el sistema core).
    2. Registra la suspensión y motivo en `usuarios_avisos_mediaciones`.
    3. Envía correo de notificación de suspensión al usuario (`emails.suspension_usuario`).
    4. Envía mensaje explicativo de suspensión al buzón `/inbox` del usuario.
    5. Envía correo de alerta crítica a todos los administradores activos (`emails.suspension_admin`).
    6. Genera un registro en `alertas_admin` para cada administrador (`tipo = 'suspension'`).

### Controladores
- **packages/Reda/RedaAlojamiento/src/Http/Controllers/Admin/Alerta/AlertaController.php**
  Controlador del panel administrativo para la gestión de alertas del sistema.
  - `index()`: Renderiza la vista principal `reda-alojamiento::admin.alerta.index`.
  - `obtenerAlertasPaginadas(Request $request)`: Retorna las alertas en bloques de 10 en 10 (`paginate(10)`) vía AJAX con filtros por estado ('todos', 'no_leidas', 'leidas'), incluyendo el HTML de la barra de paginación renderizado con `admin.general.paginacion` y el conteo de no leídas.
  - `obtenerConteoNoLeidas()`: Retorna el conteo actual de alertas no leídas del administrador conectado para alimentar la campanita del header y la insignia del menú lateral.
  - `marcarLeida($id)`: Marca una alerta individual como leída (`leido = 1`, `fecha_lectura = now()`) y devuelve el conteo actualizado.
  - `marcarTodasLeidas()`: Marca de forma masiva todas las alertas no leídas del administrador actual como leídas y reinicia el contador a 0.

### Vistas y Plantillas
- **packages/Reda/RedaAlojamiento/resources/views/admin/alerta/index.blade.php**
  Vista administrativa que presenta el panel de Alertas del Sistema. Incorpora botones de filtrado rápido ("Todas", "No leídas" con badge dinámico, "Leídas"), botón de acción masiva "Marcar todas como leídas" con spinner de carga, contenedor asíncrono para las tarjetas de alertas y contenedor inferior para la paginación de 10 en 10.
- **packages/Reda/RedaAlojamiento/resources/views/emails/primer_aviso_usuario.blade.php**
  Plantilla de correo electrónico en español dirigida al usuario, advirtiendo de manera clara y cordial que ha acumulado mediaciones y que se encuentra próximo al límite de suspensión. Incluye botón directo a su buzón de mensajes.
- **packages/Reda/RedaAlojamiento/resources/views/emails/primer_aviso_admin.blade.php**
  Plantilla de correo dirigida al equipo de administradores, resumiendo los datos del usuario, el número de mediaciones acumuladas y un enlace directo a la mediación en el panel administrativo.
- **packages/Reda/RedaAlojamiento/resources/views/emails/suspension_usuario.blade.php**
  Plantilla de correo notificando la suspensión inmediata de la cuenta por haber alcanzado el límite máximo de mediaciones estipulado, detallando los efectos de la sanción y proporcionando enlace de contacto a soporte.
- **packages/Reda/RedaAlojamiento/resources/views/emails/suspension_admin.blade.php**
  Plantilla de correo para administradores con carácter de prioridad crítica, notificando la suspensión automática de la cuenta de usuario, con ficha técnica completa y enlace al visor de alertas del panel.

### JavaScript (Vistas y Clientes)
- **packages/Reda/RedaAlojamiento/resources/js/admin/vistas/alerta/indexAlertas.js**
  Controlador JavaScript del panel de alertas:
  - Carga asíncrona con animación de espera (`window.RedaNotificaciones.esperar()`).
  - Renderizado dinámico de tarjetas con código de colores según tipo (advertencia para primer aviso, peligro para suspensión).
  - Paginación interactiva de 10 en 10 sin recargar página.
  - Filtrado en cliente por estado (Todas / No leídas / Leídas).
  - Marcado reactivo de alertas como leídas tanto al pulsar el botón "Marcar como leída", como al hacer clic directamente en cualquier parte de una tarjeta no leída o al pulsar el botón "Ver mediación".
  - Marcado masivo interactivo con spinner mediante `#btn-marcar-todas-leidas`.
  - Disparo del evento global `reda:actualizar-contador-alertas` que sincroniza de inmediato la campanita superior y el submenú lateral.

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





