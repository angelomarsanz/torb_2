<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class PropertiesPriceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('property_price')->truncate();

        DB::table('property_price')->insert([

            [
            'property_id' => 1,
            'cleaning_fee' => 0,
            'guest_after' => 1,
            'guest_fee' => 0,
            'security_fee' => 0,
            'price' => 5,
            'weekend_price' => 0,
            'weekly_discount' => 0,
            'monthly_discount' => 0,
            'currency_code' => 'USD'
            ],

            [
            'property_id' => 2,
            'cleaning_fee' => 0,
            'guest_after' => 1,
            'guest_fee' => 0,
            'security_fee' => 0,
            'price' => 6,
            'weekend_price' => 0,
            'weekly_discount' => 0,
            'monthly_discount' => 0,
            'currency_code' => 'USD'
            ],

            [
            'property_id' => 3,
            'cleaning_fee' => 0,
            'guest_after' => 1,
            'guest_fee' => 0,
            'security_fee' => 0,
            'price' => 7,
            'weekend_price' => 0,
            'weekly_discount' => 0,
            'monthly_discount' => 0,
            'currency_code' => 'USD'
            ],

            [
            'property_id' => 4,
            'cleaning_fee' => 0,
            'guest_after' => 1,
            'guest_fee' => 0,
            'security_fee' => 0,
            'price' => 8,
            'weekend_price' => 0,
            'weekly_discount' => 0,
            'monthly_discount' => 0,
            'currency_code' => 'USD'
            ],

            [
            'property_id' => 5,
            'cleaning_fee' => 0,
            'guest_after' => 1,
            'guest_fee' => 0,
            'security_fee' => 0,
            'price' => 20,
            'weekend_price' => 0,
            'weekly_discount' => 0,
            'monthly_discount' => 0,
            'currency_code' => 'USD'
            ],

            [
            'property_id' => 6,
            'cleaning_fee' => 0,
            'guest_after' => 1,
            'guest_fee' => 0,
            'security_fee' => 0,
            'price' => 120,
            'weekend_price' => 0,
            'weekly_discount' => 0,
            'monthly_discount' => 0,
            'currency_code' => 'USD'
            ],

            [
            'property_id' => 7,
            'cleaning_fee' => 0,
            'guest_after' => 1,
            'guest_fee' => 0,
            'security_fee' => 0,
            'price' => 100,
            'weekend_price' => 0,
            'weekly_discount' => 0,
            'monthly_discount' => 0,
            'currency_code' => 'USD'
            ],

            [
            'property_id' => 8,
            'cleaning_fee' => 0,
            'guest_after' => 1,
            'guest_fee' => 0,
            'security_fee' => 0,
            'price' => 200,
            'weekend_price' => 0,
            'weekly_discount' => 0,
            'monthly_discount' => 0,
            'currency_code' => 'USD'
            ],

            [
            'property_id' => 9,
            'cleaning_fee' => 5,
            'guest_after' => 1,
            'guest_fee' => 0,
            'security_fee' => 5,
            'price' => 150,
            'weekend_price' => 0,
            'weekly_discount' => 0,
            'monthly_discount' => 0,
            'currency_code' => 'USD'
            ],

            [
            'property_id' => 10,
            'cleaning_fee' => 8,
            'guest_after' => 1,
            'guest_fee' => 0,
            'security_fee' => 0,
            'price' => 244,
            'weekend_price' => 0,
            'weekly_discount' => 0,
            'monthly_discount' => 0,
            'currency_code' => 'USD'
            ],

            [
            'property_id' => 11,
            'cleaning_fee' => 5,
            'guest_after' => 1,
            'guest_fee' => 0,
            'security_fee' => 0,
            'price' => 150,
            'weekend_price' => 0,
            'weekly_discount' => 0,
            'monthly_discount' => 0,
            'currency_code' => 'USD'
            ],

            [
            'property_id' => 12,
            'cleaning_fee' => 7,
            'guest_after' => 1,
            'guest_fee' => 0,
            'security_fee' => 0,
            'price' => 266,
            'weekend_price' => 0,
            'weekly_discount' => 0,
            'monthly_discount' => 0,
            'currency_code' => 'USD'
            ],

            [
            'property_id' => 13,
            'cleaning_fee' => 0,
            'guest_after' => 1,
            'guest_fee' => 0,
            'security_fee' => 0,
            'price' => 75,
            'weekend_price' => 0,
            'weekly_discount' => 0,
            'monthly_discount' => 0,
            'currency_code' => 'USD'
            ],

            [
            'property_id' => 14,
            'cleaning_fee' => 15,
            'guest_after' => 1,
            'guest_fee' => 0,
            'security_fee' => 0,
            'price' => 275,
            'weekend_price' => 0,
            'weekly_discount' => 0,
            'monthly_discount' => 0,
            'currency_code' => 'USD'
            ],

            [
            'property_id' => 15,
            'cleaning_fee' => 0,
            'guest_after' => 1,
            'guest_fee' => 20,
            'security_fee' => 0,
            'price' => 55,
            'weekend_price' => 0,
            'weekly_discount' => 0,
            'monthly_discount' => 0,
            'currency_code' => 'USD'
            ],

            [
            'property_id' => 16,
            'cleaning_fee' => 0,
            'guest_after' => 1,
            'guest_fee' => 0,
            'security_fee' => 0,
            'price' => 80,
            'weekend_price' => 0,
            'weekly_discount' => 0,
            'monthly_discount' => 0,
            'currency_code' => 'USD'
            ],


        ]);
    }
}
