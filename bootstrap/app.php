<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function () {
            // Load web routes with namespace to support string syntax
            Route::middleware('web')
                ->namespace('App\Http\Controllers')
                ->group(base_path('routes/web.php'));
            
            // Load API routes with namespace to support string syntax
            Route::prefix('api')
                ->middleware('api')
                ->namespace('App\Http\Controllers')
                ->group(base_path('routes/api.php'));
            
            // Load module routes
            foreach (app('modules')->allEnabled() as $module) {
                if (file_exists(module_path($module->getName(), '/Routes/web.php'))) {
                    Route::middleware('web')
                        ->group(module_path($module->getName(), '/Routes/web.php'));
                }

                if (file_exists(module_path($module->getName(), '/Routes/api.php'))) {
                    Route::prefix('api')
                        ->middleware('api')
                        ->group(module_path($module->getName(), '/Routes/api.php'));
                }
            }
        },
        channels: __DIR__.'/../routes/channels.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up'
    )
    ->withMiddleware(function ($middleware) {
        // Register middleware aliases from Kernel.php
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'no_auth' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'guest' => \App\Http\Middleware\Guest::class,
            'locale' => \App\Http\Middleware\Locale::class,
            'checkUserRoutesPermissions' => \App\Http\Middleware\checkUserRoutesPermissions::class,
            'social_login' => \App\Http\Middleware\SocialLoginCheck::class,
        ]);
    })
    ->withExceptions(function ($exceptions) {
        // Exception handling configuration can be added here if needed
    })
    ->create();
