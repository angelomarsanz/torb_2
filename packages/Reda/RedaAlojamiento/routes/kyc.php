<?php

/**
 * Resumen: Rutas del sistema KYC.
 *  - POST /webhooks/didit/kyc : receptor público de Didit. Sin grupo "web" (sin sesión ni CSRF); la
 *    autenticidad la garantiza la firma HMAC. Limitado por tasa.
 *  - /kyc/*                   : endpoints del usuario autenticado (verificación con Didit, verificación manual
 *    y descarga protegida de sus propios documentos).
 *  - /admin/reda/kyc/*        : panel administrativo (guard admin, permiso manage_kyc).
 * Nota: la ruta comodín GET {name} del núcleo solo captura una única parte de la URL, por eso la futura
 * pantalla /kyc deberá registrarse antes de ella o bajo un prefijo de dos partes.
 */

use Illuminate\Support\Facades\Route;
use Reda\RedaAlojamiento\Http\Controllers\Admin\Kyc\KycAdminController;
use Reda\RedaAlojamiento\Http\Controllers\Kyc\DiditWebhookController;
use Reda\RedaAlojamiento\Http\Controllers\Kyc\KycDocumentoController;
use Reda\RedaAlojamiento\Http\Controllers\Kyc\KycUsuarioController;

Route::post('webhooks/didit/kyc', [DiditWebhookController::class, 'recibir'])
    ->middleware('throttle:120,1,kyc_webhook')
    ->name('reda.kyc.didit.webhook');

Route::middleware(['web', 'locale', 'auth'])->prefix('kyc')->as('reda.kyc.')->group(function () {
    Route::post('didit/iniciar', [KycUsuarioController::class, 'iniciarDidit'])
        ->middleware('throttle:10,1,kyc_didit')
        ->name('didit.iniciar');

    Route::get('retorno', [KycUsuarioController::class, 'retorno'])->name('retorno');

    Route::get('estado', [KycUsuarioController::class, 'estado'])->name('estado');

    Route::get('manual/opciones', [KycUsuarioController::class, 'opcionesManual'])->name('manual.opciones');

    Route::get('manual/solicitud', [KycUsuarioController::class, 'solicitudManual'])->name('manual.solicitud');

    Route::post('manual', [KycUsuarioController::class, 'enviarManual'])
        ->middleware('throttle:6,1,kyc_manual')
        ->name('manual.enviar');

    Route::post('manual/correccion', [KycUsuarioController::class, 'reenviarCorreccion'])
        ->middleware('throttle:6,1,kyc_correccion')
        ->name('manual.correccion');

    Route::get('documentos/{documento}', [KycDocumentoController::class, 'descargar'])
        ->whereNumber('documento')
        ->middleware('throttle:30,1,kyc_documentos')
        ->name('documentos.descargar');
});

/*
|--------------------------------------------------------------------------
| Panel administrativo KYC (guard admin). Cada ruta exige el permiso manage_kyc; el controlador y KycService
| lo vuelven a verificar. Las acciones modifican datos solo por POST (con CSRF) y devuelven JSON estándar.
|--------------------------------------------------------------------------
*/
Route::prefix('admin/reda/kyc')->middleware(['web', 'guest:admin', 'permission:manage_kyc'])->as('reda.admin.kyc.')->group(function () {
    Route::get('/', [KycAdminController::class, 'index'])->name('index');
    Route::get('documentos/{documento}', [KycAdminController::class, 'documento'])
        ->whereNumber('documento')->middleware('throttle:120,1,kyc_admin_documentos')->name('documento');

    Route::middleware('throttle:60,1,kyc_admin_acciones')->group(function () {
        Route::get('{id}', [KycAdminController::class, 'show'])->whereNumber('id')->name('detalle');
        Route::post('{id}/aprobar', [KycAdminController::class, 'aprobar'])->whereNumber('id')->name('aprobar');
        Route::post('{id}/rechazar', [KycAdminController::class, 'rechazar'])->whereNumber('id')->name('rechazar');
        Route::post('{id}/solicitar-correccion', [KycAdminController::class, 'solicitarCorreccion'])->whereNumber('id')->name('correccion');
        Route::post('{id}/observacion', [KycAdminController::class, 'observacion'])->whereNumber('id')->name('observacion');
        Route::post('{id}/retencion', [KycAdminController::class, 'retencion'])->whereNumber('id')->name('retencion');
        Route::post('{id}/revelar-documento', [KycAdminController::class, 'revelarDocumento'])->whereNumber('id')->name('revelar');
    });
});
