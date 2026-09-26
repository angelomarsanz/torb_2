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
  Vista Blade principal para el Inbox unificado del plugin Reda. Define la interfaz de mensajería con sidebar de avatares duales (propiedad y participantes), contenedor de mensajes enriquecidos e indicadores visuales (badges) para mensajes pendientes de leer. Incluye modales de seguridad: `modalAdvertenciaMensajeReda` (al detectar datos sensibles en el envío) y `modalAdvertenciaPrivacidadReda` (aviso preventivo al cargar la vista).

### JavaScript (Vistas)
- **packages/Reda/RedaAlojamiento/resources/js/vistas/inbox/inbox.js**
  Controlador Javascript para la vista de Inbox personalizada. Gestiona la carga de conversaciones, envío de mensajes por AJAX, navegación estilo WhatsApp en móviles, neutralización de conflictos y manejo dinámico de contadores de mensajes no leídos al interactuar con el chat. Implementa un sistema de aviso preventivo al cargar la página (`modalAdvertenciaPrivacidadReda`) y un sistema de interceptación de mensajes mediante expresiones regulares para detectar y advertir sobre el envío de números de teléfono, correos electrónicos y secuencias de 4 o más números (tanto en dígitos como escritos en letras en español), reforzando la seguridad del usuario y evitando el intercambio de datos de contacto externos.

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
  Controlador encargado de la actualización y reenvío del correo de confirmación. Ofrece el método `actualizarCorreoYReenviar` que valida que la cuenta exista, que aún no esté verificada en `users_verification` (`email != 'yes'`), que el nuevo correo no esté registrado por otro usuario, actualiza el registro en la tabla `users`, limpia tokens previos y reenvía el correo de confirmación invocando `EmailController@welcome_email`.

### Vistas
- **packages/Reda/RedaAlojamiento/resources/views/general/modal_verificacion_correo.blade.php**
  Estructura de modales Bootstrap 4.5 e inyección de datos de sesión a JavaScript (`window.RedaVerificacionData`). Contiene:
  1. `#reda_modal_confirmar_email_signup`: Muestra el email ingresado en el registro y pregunta "¿Por favor verifique si la dirección de su correo es correcta?", con botones "Email correcto" y "Corregir email" (con formulario y validación).
  2. `#reda_modal_correo_no_verificado_login`: Alerta en login cuando el usuario tiene credenciales válidas pero su correo no ha sido verificado, con opción para corregir el email y reenviar el enlace vía AJAX.
  3. `#reda_modal_correo_confirmado_exito`: Mensaje de confirmación exitosa con botón para "Iniciar sesión" tras hacer clic en el enlace del correo.

### JavaScript (Vistas)
- **packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/verificacionCorreo.js**
  Controlador JavaScript del flujo de verificación y corrección de correo. Intercepta el formulario `#signup_form` previa validación con jQuery Validate y `ageValidate()`, abre el modal de confirmación antes del envío y actualiza el campo si se corrige. Detecta si la sesión contiene `correo_no_verificado` para desplegar el modal en login con la petición AJAX de actualización. Detecta `correo_confirmado_exitoso` para desplegar el modal de bienvenida y enfocar el login.

