<?php
    return [
    /*
    |--------------------------------------------------------------------------
    | Google Map API key
    |--------------------------------------------------------------------------
    | If you get RefererNotAllowedMapError, add your URLs in Google Cloud Console:
    | 1. Go to https://console.cloud.google.com/google/maps-apis/credentials
    | 2. Edit your API key → Application restrictions → HTTP referrers
    | 3. Add: http://localhost/vrent-web/* and https://yourdomain.com/*
    */
        'google_map_key' => env('GOOGLE_MAP_KEY'),

        'google_geocode_map_key' => env('GOOGLE_MAP_GEOCODE_KEY'),


    
     /*
    |--------------------------------------------------------------------------
    | APP Version
    |--------------------------------------------------------------------------
    */
        'app_version'   => env('APP_VERSION', '4.3.1'),

    /*
    |--------------------------------------------------------------------------
    | Google reCaptcha V2
    |--------------------------------------------------------------------------
    */
    'reCaptchaKey'     => env('GOOGLE_RECAPTCHA_KEY'),

    'reCaptchaSecret'  => env('GOOGLE_RECAPTCHA_SECRET'),

    'app_demo' => env('APP_DEMO', false),
    ]
?>