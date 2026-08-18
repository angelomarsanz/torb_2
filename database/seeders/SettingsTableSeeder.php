<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('settings')->truncate();

        DB::table('settings')->insert([
        	['name' => 'name', 'value' => 'Vrent', 'type' => 'general'],
        	['name' => 'email', 'value' => 'stockpile@techvill.net', 'type' => 'general'],
            ['name' => 'logo', 'value' => 'logo.png', 'type' => 'general'],
            ['name' => 'favicon', 'value' => 'favicon.png', 'type' => 'general'],
            ['name' => 'head_code', 'value' => '', 'type' => 'general'],
            ['name' => 'default_currency', 'value' => 1, 'type' => 'general'],
            ['name' => 'default_language', 'value' => 1, 'type' => 'general'],
            ['name' => 'email_logo', 'value' => 'email_logo.png', 'type' => 'general'],

            ['name' => 'username', 'value' => env('USERNAME_PAYPAL', ''), 'type' => 'PayPal'],
            ['name' => 'password', 'value' => env('PASSWORD_PAYPAL', ''), 'type' => 'PayPal'],
            ['name' => 'signature', 'value' => env('SIGNATURE_PAYPAL', ''), 'type' => 'PayPal'],
            ['name' => 'mode', 'value' => 'sandbox', 'type' => 'PayPal'],
            ['name' => 'paypal_status', 'value' => '1', 'type' => 'PayPal'],

            ['name' => 'publishable', 'value' => env('PUBLISHABLEKEY_STRIPE', ''), 'type' => 'Stripe'],
            ['name' => 'secret', 'value' => env('CLIENTSECRET_STRIPE', ''), 'type' => 'Stripe'],
            ['name' => 'stripe_status', 'value' => '1', 'type' => 'Stripe'],


            ['name' => 'driver', 'value' => 'sendmail', 'type' => 'email'],
            ['name' => 'host', 'value' => 'mail.techvill.net', 'type' => 'email'],
            ['name' => 'port', 'value' => '587', 'type' => 'email'],
            ['name' => 'from_address', 'value' => 'stockpile@techvill.net', 'type' => 'email'],
            ['name' => 'from_name', 'value' => 'Vrent', 'type' => 'email'],
            ['name' => 'encryption', 'value' => 'tls', 'type' => 'email'],
            ['name' => 'username', 'value' => 'stockpile@techvill.net', 'type' => 'email'],
            ['name' => 'password', 'value' => 'nT4HD2XEdRUX', 'type' => 'email'],

            ['name' => 'facebook', 'value' => '#', 'type' => 'join_us'],
            ['name' => 'google_plus', 'value' => '#', 'type' => 'join_us'],
            ['name' => 'twitter', 'value' => '#', 'type' => 'join_us'],
            ['name' => 'linkedin', 'value' => '#', 'type' => 'join_us'],
            ['name' => 'pinterest', 'value' => '#', 'type' => 'join_us'],
            ['name' => 'youtube', 'value' => '#', 'type' => 'join_us'],
            ['name' => 'instagram', 'value' => '#', 'type' => 'join_us'],

            ['name' => 'key', 'value' => env('KEY_GOOGLEMAP', ''), 'type' => 'googleMap'],

            ['name' => 'client_id', 'value' => env('CLIENTID_GOOGLE', ''), 'type' => 'google'],
            ['name' => 'client_secret', 'value' => env('CLIENTSECRET_GOOGLE', ''), 'type' => 'google'],

            ['name' => 'client_id', 'value' => env('CLIENTID_FACEBOOK', ''), 'type' => 'facebook'],
            ['name' => 'client_secret', 'value' => env('CLIENTSECRET_FACEBOOK', ''), 'type' => 'facebook'],
            ['name' => 'email_status', 'value' => '0', 'type' => 'email'],
            ['name' => 'row_per_page', 'value' => '25', 'type' => 'preferences'],
            ['name' => 'date_separator', 'value' => '-', 'type' => 'preferences'],
            ['name' => 'date_format', 'value' => '2', 'type' => 'preferences'],
            ['name' => 'dflt_timezone', 'value' => 'Asia/Dhaka', 'type' => 'preferences'],
            ['name' => 'money_format', 'value' => 'before', 'type' => 'preferences'],
            ['name' => 'date_format_type', 'value' => 'mm-dd-yyyy', 'type' => 'preferences'],
            ['name' => 'front_date_format_type', 'value' => 'mm-dd-yy', 'type' => 'preferences'],
            ['name' => 'search_date_format_type', 'value' => 'm-d-yy', 'type' => 'preferences'],
            ['name' => 'min_search_price', 'value' => 1, 'type' => 'preferences'],
            ['name' => 'max_search_price', 'value' => 1000, 'type' => 'preferences'],
            ['name' => 'property_approval', 'value' => 'No', 'type' => 'preferences'],
            ['name' => 'facebook_login', 'value' => 1, 'type' => 'social'],
            ['name' => 'google_login', 'value' => 1, 'type' => 'social'],
            ['name' => 'recaptcha_preference', 'value' => 'disable', 'type' => 'preferences'],
            ['name' => 'recaptcha_key', 'value' => '', 'type' => 'google_reCaptcha'],
            ['name' => 'recaptcha_secret', 'value' => '', 'type' => 'google_reCaptcha'],

        ]);
    }
}
