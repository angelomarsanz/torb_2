# Documentación de Archivos del Plugin RedaAlojamiento

## Alojamientos (Hospedajes)

### JavaScript (Vistas)
- **packages/Reda/RedaAlojamiento/resources/js/vistas/frontend/propiedad_detalle.js**
  Gestiona el sistema de reserva en modal y botones flotantes para la vista de detalle de propiedad. Implementa la lógica para ocultar el sidebar original, inyectar el botón flotante con animación y manejar la apertura del modal mediante el hash `#reservar`. Incluye una verificación proactiva de reservas activas para prevenir duplicidades.

### Controladores
- **packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaPaymentController.php**
  Controlador extendido para gestionar el flujo de pagos y redirecciones de reserva. Asegura que los datos de la reserva se mantengan persistentes durante el proceso de login y valida si el usuario ya posee una reservación vigente antes de permitir el acceso al formulario de reserva, redirigiendo con alertas personalizadas si es necesario.

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

### JavaScript (General)
- **packages/Reda/RedaAlojamiento/resources/js/reserve-injection.js**
  Script responsable de inyectar dinámicamente el botón de "Reservar" o "Ver reserva" en las tarjetas de inmuebles de la aplicación. Implementa lógica para detectar el `propertyId` y `slug`, verifica si el usuario está autenticado y consulta si existe una reserva activa (Accepted vigentes, Pending o processing) para cambiar el texto y enlace del botón de manera proactiva, incluyendo parámetros de alerta en la redirección.

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

### Controladores
- **packages/Reda/RedaAlojamiento/src/Http/Controllers/Disputa/DisputaController.php**
  Controlador para la gestión de mediaciones (disputas). Maneja la lógica de negocio para crear, verificar y listar mediaciones. Incluye validaciones de estado de reserva para permitir mediaciones solo en reservaciones formales y no en simples consultas.

- **packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaBookingController.php**
  Controlador para gestiones relacionadas con las reservaciones dentro del ecosistema REDA. Proporciona métodos para verificar el estado de las reservas de un usuario, como la obtención de IDs de inmuebles con reservas activas (aceptadas vigentes, pendientes o en proceso) para adaptar la interfaz de usuario dinámicamente.


## Mensajería (Inbox)

### Vistas
- **packages/Reda/RedaAlojamiento/resources/views/users/inbox.blade.php**
  Vista Blade principal para el Inbox unificado del plugin Reda. Define la interfaz de mensajería con sidebar de avatares duales (propiedad y participantes), contenedor de mensajes enriquecidos e indicadores visuales (badges) para mensajes pendientes de leer.

### JavaScript (Vistas)
- **packages/Reda/RedaAlojamiento/resources/js/vistas/inbox/inbox.js**
  Controlador Javascript para la vista de Inbox personalizada. Gestiona la carga de conversaciones, envío de mensajes por AJAX, navegación estilo WhatsApp en móviles, neutralización de conflictos y manejo dinámico de contadores de mensajes no leídos al interactuar con el chat.

### Controladores
- **packages/Reda/RedaAlojamiento/src/Http/Controllers/General/RedaInboxController.php**
  Controlador para la mensajería unificada. Gestiona el historial de chat enriquecido, marca de mensajes leídos, procesamiento de respuestas con virtualización de datos y obtención del conteo de mensajes no leídos. Implementa una lógica de ordenamiento por prioridad para destacar conversaciones con mensajes pendientes.
