<?php

// Configuración del plugin, incluida la habilitación explícita del reinicio en desarrollo.
return [

    /*
    |--------------------------------------------------------------------------
    | Reda Experiencia Module Configuration
    |--------------------------------------------------------------------------
    | Este es el archivo de configuración para el módulo Reda Experiencias.
    | Aquí puedes sobrescribir valores por defecto, prefijos, o ajustes de la base de datos.
    */

    'module_version' => '1.0',

    // Herramienta destructiva: también exige APP_ENV de desarrollo y rol administrativo 1.
    'reinicio_mediaciones_habilitado' => env('REDA_REINICIO_MEDIACIONES', false),

    // Puedes añadir aquí tus primeras configuraciones futuras, como:
    // 'api_prefix' => 'api/experiencias',
    // 'table_name' => 'reda_experiencias',

];