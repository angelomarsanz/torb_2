#!/bin/bash

# --- CONFIGURACIÓN DE SUBIDA PUNTUAL ---
# Lista aquí las rutas de TODOS los archivos específicos que quieres subir.
# Pueden ser del proyecto original o de los módulos REDA.
# Para no subir archivos, escribir la palabra "Ninguno".
ARCHIVOS_PHP_PUNTUALES=(
    "packages/Reda/RedaAlojamiento/src/Events/VerificacionKycEliminada.php"
    "packages/Reda/RedaAlojamiento/src/Services/Kyc/KycService.php"
    "packages/Reda/RedaAlojamiento/src/Services/Kyc/KycDocumentoService.php"
    "packages/Reda/RedaAlojamiento/src/Services/Kyc/NotificacionesKycService.php"
    "packages/Reda/RedaAlojamiento/src/Services/Kyc/ConsultaKycAdmin.php"
    "packages/Reda/RedaAlojamiento/src/Support/EtiquetasKyc.php"
    "packages/Reda/RedaAlojamiento/src/Http/Controllers/Admin/Kyc/KycAdminController.php"
    "packages/Reda/RedaAlojamiento/routes/kyc.php"
    "packages/Reda/RedaAlojamiento/src/RedaAlojamientoServiceProvider.php"
    "packages/Reda/RedaAlojamiento/resources/views/admin/kyc/index.blade.php"
    "packages/Reda/RedaAlojamiento/resources/views/admin/kyc/detalle.blade.php"
    "packages/Reda/RedaAlojamiento/resources/views/admin/kyc/eliminadas.blade.php"
    "packages/Reda/RedaAlojamiento/resources/views/admin/kyc/historial.blade.php"
    "packages/Reda/RedaAlojamiento/resources/views/admin/kyc/partials/pestanas.blade.php"
    "packages/Reda/RedaAlojamiento/resources/js/admin/vistas/kyc/detalleKyc.js"
    "packages/Reda/RedaAlojamiento/resources/js/admin/vistas/kyc/eliminarVerificacionKyc.js"
    "packages/Reda/RedaAlojamiento/resources/sass/admin/main.scss"
    "packages/Reda/RedaAlojamiento/resources/lang/es.json"
    )