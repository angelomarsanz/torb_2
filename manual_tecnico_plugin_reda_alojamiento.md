# Documentación de Archivos del Plugin RedaAlojamiento

## Reinicio de mediaciones de prueba

Herramienta destructiva y deshabilitada por defecto. Solo se muestra y ejecuta con `REDA_REINICIO_MEDIACIONES=true`, `APP_ENV` igual a `local`, `development` o `dev`, sesión administrativa y rol ID 1. No cambiar `APP_ENV` global de un servidor para forzar el acceso. El administrador debe disponer de un respaldo reciente y coordinar una ventana sin usuarios ni procesos de mediación o chat; no existe bloqueo automático de las demás operaciones. No elimina correos ya entregados.

- **packages/Reda/RedaAlojamiento/database/migrations/2026_11_07_000000_crear_tablas_reinicio_mediaciones.php**
  `up()` crea las tablas auxiliares `mensajes_de_mediacion` y `adjuntos_mediacion_pendientes`; `down()` impide revertir mientras existan marcadores o adjuntos pendientes. La migración debe aplicarse antes de desplegar los controladores y el servicio de mensajes modificados.
- **packages/Reda/RedaAlojamiento/src/Models/Disputa/MensajeDeMediacion.php**
  Modelo del marcador inequívoco de origen de cada mensaje nuevo de mediación.
- **packages/Reda/RedaAlojamiento/src/Models/Disputa/AdjuntoMediacionPendiente.php**
  Modelo para rutas y huellas SHA-256 de archivos pendientes de eliminar tras confirmar la transacción.
- **packages/Reda/RedaAlojamiento/src/Services/ReinicioMediacionesService.php**
  `usuariosElegibles()` filtra cuentas inactivas, no borradas y con antecedentes de segundo aviso o restricción. `reiniciar()` usa un bloqueo de MySQL para impedir reinicios simultáneos, valida de nuevo la selección y en una transacción elimina disputas, alertas `primer_aviso`/`suspension`, avisos y mensajes con marcador inequívoco; reactiva solo cuentas seleccionadas y conserva reservas y ajustes. Los mensajes históricos sin marcador permanecen por su origen ambiguo. `normalizarRuta()` y `archivoSeguro()` limitan las rutas y rechazan enlaces simbólicos. `procesarAdjuntos()` elimina después del commit solo archivos con la misma huella, mantiene la cola si falla y registra el error. Si existe `disputas_respaldo`, conserva todos los adjuntos por precaución; informa el número conservado. El borrado de archivos no es atómico con la base de datos. Un reinicio posterior, durante mantenimiento, reintenta los pendientes; antes de reintentar revisar el registro y no sustituir archivos pendientes.
- **packages/Reda/RedaAlojamiento/src/Http/Controllers/Admin/Disputa/ReinicioMediacionesController.php**
  `autorizar()` comprueba bandera, entorno y rol real en cada petición; `index()` presenta las cuentas elegibles y `reiniciar()` valida la frase exacta y selección, registra el resultado y devuelve el formato estándar. Un error posterior al commit puede dejar archivos pendientes.
- **packages/Reda/RedaAlojamiento/resources/views/admin/disputa/reinicio_mediaciones.blade.php**
  Presenta advertencias, selección voluntaria de cuentas, confirmación textual y modal adaptable. El antecedente no prueba por sí solo el motivo de inactividad; no se ofrecen cuentas borradas lógicamente.
- **packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/reinicioMediaciones.js**
  `solicitarReinicio()` normaliza la petición AJAX; el manejador exige la frase, impide cerrar el modal durante la operación, muestra espera y presenta el resultado. Tras un error se debe actualizar la página y revisar el registro antes de reintentar.
- **packages/Reda/RedaAlojamiento/src/Http/Controllers/Disputa/MensajeController.php** y **packages/Reda/RedaAlojamiento/src/Http/Controllers/Admin/Disputa/MensajeController.php**
  `store()` guarda el mensaje y el marcador dentro de la misma transacción; el observador sigue creando los metadatos de remitente. Esto identifica los mensajes futuros creados desde las pantallas de mediación, no el chat normal.
- **packages/Reda/RedaAlojamiento/src/Services/MediacionAlertaService.php**
  `enviarMensajeBuzon()` guarda transaccionalmente el aviso privado, sus metadatos y el marcador de mediación.
- **packages/Reda/RedaAlojamiento/config/reda-alojamiento.php**, **packages/Reda/RedaAlojamiento/routes/web.php** y **packages/Reda/RedaAlojamiento/resources/views/admin/general/main_footer.blade.php**
  Declaran la bandera, las dos rutas y los datos de disponibilidad para el menú. La verificación del controlador es independiente de la visibilidad.
- **packages/Reda/RedaAlojamiento/resources/js/admin/general/menus/menuLateralAdmin.js** y **packages/Reda/RedaAlojamiento/resources/js/admin/general/main.js**
  Insertan el submenú «Pruebas aplicación» cuando el servidor lo permite e incluyen el controlador de la pantalla en el bundle administrativo existente.
- **packages/Reda/RedaAlojamiento/resources/lang/es.json**
  Incluye los textos nuevos del modal, las advertencias y los resultados.

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
  Gestiona el sistema de reserva en modal y botones flotantes para la vista de detalle de propiedad (property.single). Su método `normalizarTarjetaAnfitrion()` añade clases del plugin a la tarjeta resumen superior y a la imagen del anfitrión para conservar el avatar cuadrado en móvil. También oculta el sidebar original, inyecta el botón flotante con animación y maneja la apertura del modal mediante el hash `#reservar`. Incluye una verificación proactiva de reservas activas para prevenir duplicidades. Antes de abrir el formulario consulta los intervalos ocupados; `prepararModalReserva()` busca desde hoy el primer día libre y posiciona allí el calendario, permitiendo reservar el mismo día cuando esté disponible. Los rangos reservados siguen bloqueados al avanzar por meses posteriores, y las salidas se validan para impedir cruces entre estancias, conservando el día de checkout como límite válido. Los campos visibles usan el formato de Flatpickr, mientras que los inputs originales `#startDate` y `#endDate` reciben fechas ISO (`Y-m-d`) para evitar interpretaciones ambiguas por la preferencia regional de `setDateForDb()`. El checkout inicia vacío y `limpiarEstadoPrecios()` oculta los avisos y resultados heredados hasta contar con una estancia completa. `vigilarRespuestasPrecio()` compara las fechas de cada respuesta AJAX del core con las fechas actualmente elegidas: descarta el estado obsoleto si aún no hay salida y vuelve a solicitar el cálculo cuando la respuesta corresponde a una selección anterior. Esto corrige los avisos residuales de rangos iniciales, pero no sustituye la disponibilidad persistida del core. La auditoría encontró bloqueos residuales de propiedad 3 (31/10–20/11 y 21/11) vinculados por fechas y marcas de tiempo a los bookings 54 y 59 borrados lógicamente, y un bloqueo de propiedad 1 (12/11/2026) coincidente con el booking 65 eliminado lógicamente. El usuario confirmó en respaldos posteriores que las 22 fechas de propiedad 3 y la fecha 12/11 de propiedad 1 quedaron en estado `Available`. No se deben borrar bloqueos solo por solapamiento: `property_dates` no almacena el ID del booking. Las noches 26–29/11 de propiedad 3 vinculadas al booking 73 vigente y las fechas futuras de reservas activas permanecen bloqueadas. Si la consulta de disponibilidad falla, `notificarErrorFechasOcupadas()` informa al usuario y no abre el formulario.
  **Actualización Desglose Huéspedes (Airbnb-style):** Oculta el selector `#number_of_guests` original dentro de la modal e inyecta dos selectores dinámicos para "Adulto(s)" y "Niño(s)". Sincroniza dinámicamente las opciones para respetar la capacidad máxima (`accommodates`), actualiza `#number_of_guests` con el total y gatilla el recálculo de precios y tarifas en tiempo real.

- **packages/Reda/RedaAlojamiento/resources/sass/main.scss**
  En la vista móvil de detalle de propiedad, mantiene el avatar de la tarjeta resumen superior cuadrado de 90 × 90 px con `object-fit: cover`. Las reglas se limitan a las clases que `propiedad_detalle.js` añade por JavaScript a esa tarjeta y a la imagen del anfitrión. También da un fondo gris, texto atenuado y cursor no disponible a los días ocupados que Flatpickr deshabilita en la modal de reservas.

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
  Controlador para consultas de reservas. Se enriqueció `getBookingDetails` para retornar `adultos`, `ninos` y `huespedes_desglose`. Se incorporó el método `getHuespedesInfo` que devuelve el desglose de huéspedes por `booking_id` o `code` para consumo de las vistas AJAX. El método `obtenerFechasOcupadas($idPropiedad)` consulta las reservas `Accepted`, `Pending`, `Processing` y `processing` con fecha de salida futura, excluye estados no ocupantes y retorna intervalos `[llegada, salida)` para que el día de checkout permanezca disponible como llegada de otra reserva.

### Rutas
- **packages/Reda/RedaAlojamiento/routes/web.php**
  La ruta autenticada `GET /reda/bookings/fechas-ocupadas/{idPropiedad}` dirige a `RedaBookingController@obtenerFechasOcupadas` y entrega al calendario del detalle de propiedad los intervalos de noches ocupadas.

- **packages/Reda/RedaAlojamiento/resources/lang/es.json**
  Incluye los textos de respuesta del endpoint de disponibilidad y el mensaje mostrado cuando no se pueden consultar las fechas; en ese caso el modal de reserva permanece cerrado.

## Mediaciones (Disputas)
... (resto del archivo)

- **packages/Reda/RedaAlojamiento/resources/views/admin/disputa/index.blade.php**
  Vista de administración para el listado y gestión de mediaciones (Disputas). Esta interfaz permite a los administradores visualizar todos los casos de mediación, filtrarlos por estado, realizar seguimiento del progreso mediante una línea de tiempo y acceder a los chats de comunicación entre las partes involucradas.

### JavaScript (Vistas)
- **packages/Reda/RedaAlojamiento/resources/js/vistas/disputa/disputas/indexDisputas.js**
  Controlador de la vista de índice de disputas (Mediaciones). Este archivo gestiona la lógica del dashboard de mediaciones, incluyendo la carga paginada por estados, la renderización de la lista de casos, la línea de tiempo de progreso, el visor de medios (imágenes/PDF) y el chat específico de la mediación.

### JavaScript (Administración)
- **packages/Reda/RedaAlojamiento/resources/js/admin/vistas/experiencia/opcionesTipoDeNegocios.js**
  Gestiona el alta, edición y eliminación de tipos de negocios en la vista administrativa. Utiliza las traducciones JSON para el título del modal, la confirmación de borrado y las notificaciones de resultado; los mensajes exitosos de crear y editar se muestran como «Tipo de negocio guardado correctamente», y el borrado como «Tipo de negocio eliminado correctamente». Traduce el mensaje legado de elemento inexistente al término actual sin cambiar rutas, nombres internos ni datos.

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
  Script de integración para mediaciones y mensajería. Gestiona la caja de mediación en `#booking`, modales de disputas y desplazamiento suave (`scrollIntoView`) en el sidebar sin interferir con la renderización inicial del servidor ni generar colisiones de modales de carga.
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
  Controlador JavaScript del lado del cliente para el formulario de confirmación de pago (`#payment-form`). Implementa validación estricta y reactiva en el cliente cuando aplica la carga de comprobante de pago (`attachment[]`) y nota/mensaje al anfitrión (`note`), como ocurre en pasarelas de Transferencia Bancaria Directa (`DirectBankTransfer`). Utiliza intercepción en fase de captura del DOM (Capture Phase) en el botón de confirmación (`#payment-form-submit`) y en el evento `submit` del formulario para adelantarse a scripts de terceros que deshabilitan el botón de manera prematura. Si falta alguno de los campos obligatorios, cancela la propagación del evento, previene el envío, restaura el estado interactivo del botón y el spinner, y muestra mensajes de error en español estilizados en los contenedores correspondientes (`span.text-danger`), evitando la recarga del navegador y garantizando que el usuario no pierda el archivo del comprobante adjunto. Incluye limpiadores de errores en tiempo real en los eventos `change` e `input`, enfoca y desplaza la vista hacia el primer campo inválido, y activa la animación de espera ("Su reserva está siendo procesada") únicamente cuando el formulario es 100% válido.

## Administración y Menú Lateral (Backend)

### JavaScript (Administración General)
- **packages/Reda/RedaAlojamiento/resources/js/admin/general/menus/menuLateralAdmin.js**
  Orquesta la inyección reactiva y no invasiva de las opciones del plugin REDA en el menú lateral de AdminLTE. Construye "Negocios", "Mediaciones" y "Mensajes y Alertas" con la estructura nativa de navegación (`nav-item`, `nav-link`, `nav-icon` y texto en `p`) para mantener la misma sangría y alineación de las opciones originales. El submenú acordeón "Negocios" agrupa "Configurar planes", "Tipos de negocios" y "Atención al comercio", esta última conserva el acceso a `/admin/reda/general/soporte-tecnico`; todos sus enlaces muestran la animación de espera al navegar. También inyecta el submenú "Mediaciones", con opciones desplegables de "Listado" y condicionalmente "Configuración" exclusiva para Rol 1. Para "Mediaciones", si el usuario conectado no posee el elemento `Bookings` en su menú lateral (como ocurre con el Rol 2 "Atención al usuario"), el script busca puntos de anclaje jerárquicos alternativos (`#menu-negocios`, `Properties`, `Customers`, `Dashboard` o el final del contenedor `.sidebar-menu`). Al hacer clic o tocar en "Mediaciones", asegura que en la opción "Listado" también aparezca el contador dinámico de mediaciones activas (`Listado (N)`), sincronizado con el encabezado padre. Mantiene abierto el submenú y resalta la opción activa cuando la ruta coincide con `/admin/reda/disputas` o `/admin/reda/disputas/configuracion`, y actualiza en tiempo real los contadores vía AJAX.
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
  - **Método `asignarAgente`:** Endpoint `POST /admin/reda/disputas/asignar-agente` que actualiza la columna `id_usuario_agente_asignado` en la tabla `disputas`. Permite a usuarios de Rol 1 asignar cualquier agente o a sí mismos, y a usuarios de Rol 2 tomar mediaciones disponibles asignándose a sí mismos, retornando la estructura estándar JSON de REDA con los datos de avatar y nombre del agente.
  - **Métodos de Configuración (`configuracion` y `guardarConfiguracion`):** Endpoints `GET /admin/reda/disputas/configuracion` y `POST /admin/reda/disputas/configuracion/store` protegidos exclusivamente para Rol 1. Gestionan en `settings` las claves históricas `'Cantidad mediaciones permitidas primer aviso'` y `'Cantidad mediaciones segundo aviso y suspensión'`; los mensajes de validación describen el segundo umbral como restricciones, no como inactivación.

### Vistas y Scripts de Administración
- **packages/Reda/RedaAlojamiento/resources/views/admin/disputa/index.blade.php**
  Vista principal del panel de mediaciones para el administrador. Expone las credenciales y perfil del usuario conectado en `window.RedaAdminAccess` (`roleId`, `adminId`, `isFullAdmin`).
- **packages/Reda/RedaAlojamiento/resources/js/admin/vistas/disputa/indexDisputas.js**
  Controlador del dashboard administrativo de mediaciones. Implementa la lógica reactiva de asignación:
  - Para Rol 1: Sustituye el texto del agente no asignado por un control `<select>` con los agentes disponibles y el propio admin. Al seleccionar, ejecuta la asignación vía AJAX con bloqueo de espera y renderiza la foto de perfil y nombre del agente asignado, con soporte para reasignación interactiva.
  - Para Rol 2: Sustituye el texto por un suiche tipo toggle (`form-check form-switch`) con la etiqueta "Tomar mediación". Al activarlo, toma la mediación vía AJAX con animación de espera y actualiza la tarjeta mostrando la foto de perfil y el nombre del agente.
  - Sincroniza dinámicamente la información del agente asignado en las tarjetas del listado, el panel lateral de detalles y el acordeón en dispositivos móviles.
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
