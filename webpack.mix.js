const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    // Copy AdminLTE 4 CSS
    .copy('node_modules/admin-lte/dist/css/adminlte.min.css', 'public/backend/dist/css/adminlte.min.css')
    .copy('node_modules/admin-lte/dist/css/adminlte.min.css.map', 'public/backend/dist/css/adminlte.min.css.map')
    // Copy AdminLTE 4 JS
    .copy('node_modules/admin-lte/dist/js/adminlte.min.js', 'public/backend/dist/js/adminlte.min.js')
    .copy('node_modules/admin-lte/dist/js/adminlte.min.js.map', 'public/backend/dist/js/adminlte.min.js.map');
