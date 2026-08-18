<?php

use Illuminate\Support\Facades\Route;
use Infoamin\Installer\Http\Controllers\{
    WelcomeController,
    DatabaseController,
    RequirementsController,
    PermissionsController,
    UserController,
    FinalController
};

Route::group([
    'namespace' => 'Infoamin\Installer\Http\Controllers',
    'middleware' => ['web']
], function () {
    
    // Welcome route - only available if app is not installed
    if (!env('APP_INSTALL')) {
        Route::get('/', [WelcomeController::class, 'welcome']);
    }

    // Installation routes - protected by 'installed' middleware
    Route::group([
        'prefix' => 'install',
        'middleware' => ['web', 'installed']
    ], function () {
        // Requirements check
        Route::get('requirements', [RequirementsController::class, 'requirements']);
        
        // Permissions check
        Route::get('permissions', [PermissionsController::class, 'checkPermissions']);
        
        // Database configuration
        Route::get('database', [DatabaseController::class, 'create']);
        Route::post('database', [DatabaseController::class, 'store']);
        Route::get('seedmigrate/{type}', [DatabaseController::class, 'seedMigrate']);
        
        // User registration (if required)
        Route::get('register', [UserController::class, 'createUser']);
        Route::post('register', [UserController::class, 'storeUser']);
        
        // Final installation step
        Route::get('finish', [FinalController::class, 'finish']);
    });

    // Purchase code verification - only if not cached or secret not set
    if (!cache('a_s_k') || !env('INSTALL_APP_SECRET')) {
        Route::match(
            ['GET', 'POST'],
            'install/verify-envato-purchase-code',
            [PermissionsController::class, 'verifyPurchaseCode']
        );
    }

    // Cache clearing route
    Route::post('install/clear-cache', [PermissionsController::class, 'clearCache']);
});
