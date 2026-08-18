<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class PropertiesStepTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('property_steps')->truncate();

        DB::table('property_steps')->insert([

            [
            'property_id' => 1,
            'basics' => 1,
            'description' => 1,
            'location' => 1,
            'photos' => 1,
            'pricing' => 1,
            'booking' => 1
            ],

            [
            'property_id' => 2,
            'basics' => 1,
            'description' => 1,
            'location' => 1,
            'photos' => 1,
            'pricing' => 1,
            'booking' => 1
            ],

            [
            'property_id' => 3,
            'basics' => 1,
            'description' => 1,
            'location' => 1,
            'photos' => 1,
            'pricing' => 1,
            'booking' => 1
            ],

            [
            'property_id' => 4,
            'basics' => 1,
            'description' => 1,
            'location' => 1,
            'photos' => 1,
            'pricing' => 1,
            'booking' => 1
            ],

            [
            'property_id' => 5,
            'basics' => 1,
            'description' => 1,
            'location' => 1,
            'photos' => 1,
            'pricing' => 1,
            'booking' => 1
            ],

            [
            'property_id' => 6,
            'basics' => 1,
            'description' => 1,
            'location' => 1,
            'photos' => 1,
            'pricing' => 1,
            'booking' => 1
            ],

            [
            'property_id' => 7,
            'basics' => 1,
            'description' => 1,
            'location' => 1,
            'photos' => 1,
            'pricing' => 1,
            'booking' => 1
            ],

            [
            'property_id' => 8,
            'basics' => 1,
            'description' => 1,
            'location' => 1,
            'photos' => 1,
            'pricing' => 1,
            'booking' => 1
            ],

            [
            'property_id' => 9,
            'basics' => 1,
            'description' => 1,
            'location' => 1,
            'photos' => 1,
            'pricing' => 1,
            'booking' => 1
            ],

            [
            'property_id' => 10,
            'basics' => 1,
            'description' => 1,
            'location' => 1,
            'photos' => 1,
            'pricing' => 1,
            'booking' => 1
            ],

            [
            'property_id' => 11,
            'basics' => 1,
            'description' => 1,
            'location' => 1,
            'photos' => 1,
            'pricing' => 1,
            'booking' => 1
            ],

            [
            'property_id' => 12,
            'basics' => 1,
            'description' => 1,
            'location' => 1,
            'photos' => 1,
            'pricing' => 1,
            'booking' => 1
            ],

            [
            'property_id' => 13,
            'basics' => 1,
            'description' => 1,
            'location' => 1,
            'photos' => 1,
            'pricing' => 1,
            'booking' => 1
            ],

            [
            'property_id' => 14,
            'basics' => 1,
            'description' => 1,
            'location' => 1,
            'photos' => 1,
            'pricing' => 1,
            'booking' => 1
            ],

            [
            'property_id' => 15,
            'basics' => 1,
            'description' => 1,
            'location' => 1,
            'photos' => 1,
            'pricing' => 1,
            'booking' => 1
            ],

            [
            'property_id' => 16,
            'basics' => 1,
            'description' => 1,
            'location' => 1,
            'photos' => 1,
            'pricing' => 1,
            'booking' => 1
            ],

        ]);
    }
}
