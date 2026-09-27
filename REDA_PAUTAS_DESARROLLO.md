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

---

## 6. Entorno de Trabajo y Prohibición de Compilación/Comandos en Cloud Shell Editor

- **Cloud Shell Editor es ÚNICAMENTE repositorio de código fuente:**
  - Solo se crean, modifican y guardan archivos fuente (`.js`, `.scss`, `.php`, `.blade.php`, `.json`, `.md`).
  - **PROHIBIDO COMPILAR / MINIFICAR:** NUNCA ejecutar `npm run dev`, `npm run prod`, `npx mix`, `webpack` ni generar archivos `.min.js` o `.min.css` en este entorno.
  - **PROHIBIDO EJECUTAR ARTISAN / MIGRACIONES:** NUNCA ejecutar `php artisan ...` en Cloud Shell. Las migraciones solo se redactan, no se ejecutan aquí.
- **Servidor Vesta de Desarrollo:**
  - Es el único lugar donde se suben los archivos fuente vía FTP (`subir.sh` o `subir_archivos_puntuales.sh`).
  - Es el único lugar donde se compila (`./compilar.sh`) y donde se ejecutan las migraciones (`sudo -u appvac php8.2 artisan migrate`) y las pruebas funcionales.
- **PROHIBIDO MODIFICAR O EJECUTAR LOS SCRIPTS DE SUBIDA (`subir.sh` y `subir_archivos_puntuales.sh`):**
  - La IA NUNCA debe ejecutar `./subir.sh` ni `./subir_archivos_puntuales.sh`.
  - La IA NUNCA debe modificar el contenido de `subir.sh` ni de `subir_archivos_puntuales.sh`. Esos scripts son de gestión exclusiva y manual del usuario.
- **PROTOCOLO DE COMANDOS (SUGERIR Y DETENER):**
  - La IA NUNCA debe ejecutar comandos de consola directamente (`php artisan ...`, `npm run ...`, `./compilar.sh`, etc.).
  - Siempre debe sugerir por escrito el comando que el usuario debe ejecutar en el servidor (ej: *"Por favor ejecute en el servidor Vesta: `sudo -u appvac php8.2 artisan migrate`"* o *"Por favor ejecute `./compilar.sh`"*).
  - La IA debe **DETENER su respuesta** en ese punto y esperar a que el usuario confirme la ejecución para reactivar y proseguir con el siguiente paso.


