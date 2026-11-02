# Registro Previo de Cambios en Curso

## Estado actual
- **Fecha:** 2 de noviembre de 2026
- **Tarea:** Añadir a `.github/copilot-instructions.md` el procedimiento para encontrar el JavaScript apropiado antes de alterar HTML de vistas originales.
- **Alcance:** Incorporar una pauta breve junto a las reglas existentes de vistas y registrar el cambio.
- **Avance:** Añadida en "Directrices del proyecto" la secuencia para reutilizar un script específico relacionado, revisar el JavaScript general del plugin y, si no hay uno idóneo, crear e integrar un archivo dedicado. Se prohíbe expresamente poner lógica de una vista en scripts de otro propósito.
- **Archivos modificados:**
  - `.github/copilot-instructions.md`: nueva directriz de selección de JavaScript para alterar HTML original.
  - `LOG_DESARROLLO_REDA.md`: registro del cambio y su validación.
- **Validación:** La directriz quedó en la sección indicada; `git diff --check` pasó.
- **Estado:** Completado.
