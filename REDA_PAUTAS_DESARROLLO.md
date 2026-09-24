# Pautas de Desarrollo y Notas Técnicas - Plugin REDA Alojamiento

Este archivo contiene directrices críticas, observaciones técnicas y reglas de negocio que deben tenerse en cuenta al realizar modificaciones en el código para asegurar la estabilidad, consistencia e integridad del sistema.

---

## 1. Consistencia de Botones "Reservar" / "Ver reserva"

### Contexto
El botón de reservación se inyecta dinámicamente en tres vistas principales:
1.  **Index/Home:** Tarjetas de propiedades recomendadas.
2.  **Detalle de Propiedad:** Vista individual de cada alojamiento.
3.  **Mis Viajes (`/trips/active`):** Listado de reservaciones del usuario.

### Directrices Críticas
- **Fuente de Verdad Única:** Cualquier cambio en la lógica de decisión debe originarse en el servidor, específicamente en `RedaBookingController@getActiveBookingPropertyIds`. Este método genera la "Lista Blanca" que el frontend utiliza.
- **Sincronización Bidireccional:** El script `reserve-injection.js` debe procesar los datos de la misma manera para las tres vistas. Nunca implementes lógica de filtrado por texto del DOM en una vista que no esté respaldada por la lógica del servidor.
- **Prioridad de la Fecha:** La vigencia de la estancia (`end_date`) tiene precedencia absoluta. Si la fecha ya pasó, el botón DEBE revertirse a "Reservar" (o mantenerse como tal), ignorando estatus de "Pendiente" que puedan persistir en la base de datos.
- **Exclusión de Consultas:** Se consideran "Consultas" (Inquiries) y deben ser ignoradas para el texto "Ver reserva" todos aquellos registros que no tengan un pago asociado (`transaction_id` o `payment_method_id`).

---

## 2. Estabilidad del Frontend (MutationObservers)

### Observaciones Técnicas
- **Ciclos Infinitos:** Existe un alto riesgo de recursividad infinita entre `ocultar-consultas.js` y `reserve-injection.js` ya que ambos modifican el DOM y se observan mutuamente.
- **Protecciones Obligatorias:**
    - Usar técnicas de **Debouncing** (mínimo 300-500ms) para agrupar cambios del DOM.
    - Implementar banderas de estado (`isScanning`, `isFiltering`) para evitar ejecuciones concurrentes.
    - Configurar los observadores para ignorar cambios en sus propios contenedores (`.reda-reserve-btn`, `#reda-empty-state-message`).

---

## 3. Manejo de Traducciones en JavaScript

- **Global RedaAlojamientoJson:** Siempre utiliza `window.RedaAlojamientoJson` para acceder a las traducciones.
- **Robustez:** Siempre proporciona un *fallback* en español literal si la clave no existe:
  `const texto = window.RedaAlojamientoJson["Clave"] || "Texto por defecto en español";`
- **Contexto de Error:** Asegúrate de que las variables de traducción estén disponibles dentro de los *callbacks* de AJAX y manejadores de eventos.

---

## 4. Integridad del Core (Laravel vRent)

- **Relaciones:** El modelo `Bookings` del núcleo utiliza `currency` (en singular). No uses `currencies` en consultas de Eloquent o accesos a propiedades.
- **No Modificar Originales:** Sigue estrictamente la regla de inyectar funcionalidad vía Middleware o JavaScript. Si es inevitable modificar un archivo original, delimita el cambio con:
  `// INICIO PLUGIN REDA` ... `// FIN PLUGIN REDA`.

---

## 5. Documentación Obligatoria

- Cada nueva función o archivo debe registrarse en:
    1.  `manual_tecnico_plugin_reda_alojamiento.md` (Descripción funcional/técnica).
    2.  `LOG_DESARROLLO_REDA.md` (Resumen del avance diario).
    3.  Este archivo (`REDA_PAUTAS_DESARROLLO.md`) si introduce una nueva regla de consistencia o precaución técnica.
