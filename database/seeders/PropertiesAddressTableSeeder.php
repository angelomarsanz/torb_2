<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class PropertiesAddressTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('property_address')->truncate();

        DB::table('property_address')->insert([

            [
            'property_id' => 1,
            'address_line_1' => 'New York City Hall, New York, NY 10007, USA',
            'address_line_2' => '851 8th Ave, New York, NY, US, 10019',
            'latitude' => '40.7127461',
            'longitude' => '-74.00597399999998',
            'city' => 'New York',
            'state' => 'New York',
            'country' => 'US',
            'postal_code' => '10007'
            ],

            [
            'property_id' => 2,
            'address_line_1' => 'MLC Centre, 108 King St, Sydney NSW 2000, Australia',
            'address_line_2' => NULL,
            'latitude' => '-33.8686949',
            'longitude' => '151.2092424',
            'city' => 'Sydney',
            'state' => 'New South Wales',
            'country' => 'AU',
            'postal_code' => '2000'
            ],

            [
            'property_id' => 3,
            'address_line_1' => '19 Rue de Rivoli, 75004 Paris, France',
            'address_line_2' => NULL,
            'latitude' => '48.8559431',
            'longitude' => '2.3573452000000543',
            'city' => 'Paris',
            'state' => 'Île-de-France',
            'country' => 'FR',
            'postal_code' => '75004'
            ],

            [
            'property_id' => 4,
            'address_line_1' => 'Passeig de Picasso, 26, 08003 Barcelona, Spain',
            'address_line_2' => NULL,
            'latitude' => '41.3866227',
            'longitude' => '2.184072199999946',
            'city' => 'Barcelona',
            'state' => 'Catalunya',
            'country' => 'ES',
            'postal_code' => '08003'
            ],

            [
            'property_id' => 5,
            'address_line_1' => '12 Stacey St, London WC2H, UK',
            'address_line_2' => NULL,
            'latitude' => '51.5142805',
            'longitude' => '-0.12846539999998186',
            'city' => 'London',
            'state' => 'England',
            'country' => 'GB',
            'postal_code' => 'WC2H'
            ],

            [
            'property_id' => 6,
            'address_line_1' => '240 7th St, San Francisco, CA 94103, USA',
            'address_line_2' => NULL,
            'latitude' => '37.7771788',
            'longitude' => '-122.40894029999998',
            'city' => 'SF',
            'state' => 'California',
            'country' => 'US',
            'postal_code' => '94103'
            ],

            [
            'property_id' => 7,
            'address_line_1' => '240 7th St, San Francisco, CA 94103, USA',
            'address_line_2' => NULL,
            'latitude' => '37.7771788',
            'longitude' => '-122.40894029999998',
            'city' => 'SF',
            'state' => 'California',
            'country' => 'US',
            'postal_code' => '94103'
            ],

            [
            'property_id' => 8,
            'address_line_1' => '12 Stacey St, London WC2H, UK',
            'address_line_2' => NULL,
            'latitude' => '51.5142805',
            'longitude' => '-0.12846539999998186',
            'city' => 'London',
            'state' => 'England',
            'country' => 'GB',
            'postal_code' => 'WC2H'
            ],

            [
            'property_id' => 9,
            'address_line_1' => 'S477, L4, 311, 315 Castlereagh St, Haymarket NSW 2000, Australia',
            'address_line_2' => NULL,
            'latitude' => '-33.879402129156325',
            'longitude' => '151.2082655317383',
            'city' => 'Haymarket',
            'state' => 'New South Wales',
            'country' => 'AU',
            'postal_code' => '2000'
            ],

            [
            'property_id' => 10,
            'address_line_1' => '33 Cox Ave, Bondi Beach NSW 2026, Australia',
            'address_line_2' => NULL,
            'latitude' => '-33.88973752585554',
            'longitude' => '151.26897126977542',
            'city' => 'Bondi Beach',
            'state' => 'New South Wales',
            'country' => 'AU',
            'postal_code' => '2026'
            ],



            [
            'property_id' => 11,
            'address_line_1' => 'Vanderbilt Av/Plaza St E, Brooklyn, NY 11238, USA',
            'address_line_2' => NULL,
            'latitude' => '40.67499800825174',
            'longitude' => '-73.9695376727417',
            'city' => 'New York',
            'state' => 'New York',
            'country' => 'US',
            'postal_code' => '11238'
            ],



            [
            'property_id' => 12,
            'address_line_1' => '11 Australia Ave, Sydney Olympic Park NSW 2127, Australia',
            'address_line_2' => NULL,
            'latitude' => '-33.8465088',
            'longitude' => '151.0722137',
            'city' => 'Sydney Olympic Park',
            'state' => 'New South Wales',
            'country' => 'AU',
            'postal_code' => '2127'
            ],

            [
            'property_id' => 13,
            'address_line_1' => '216 9th St, Jersey City, NJ 07302, USA',
            'address_line_2' => NULL,
            'latitude' => '40.728257472677235',
            'longitude' => '-74.04262250397949',
            'city' => 'Jersey City',
            'state' => 'New Jersey',
            'country' => 'US',
            'postal_code' => '07302'
            ],

            [
            'property_id' => 14,
            'address_line_1' => 'Newtown Station, Enmore Rd, Stand A, Newtown NSW 2042, Australia',
            'address_line_2' => NULL,
            'latitude' => '-33.8978149',
            'longitude' => '151.1785003',
            'city' => 'Newtown',
            'state' => 'New South Wales',
            'country' => 'AU',
            'postal_code' => '2042'
            ],

            [
            'property_id' => 15,
            'address_line_1' => '302 Freeman St, Brooklyn, NY 11222, USA',
            'address_line_2' => NULL,
            'latitude' => '40.734789095083435',
            'longitude' => '-73.94786324140627',
            'city' => 'New York',
            'state' => 'New York',
            'country' => 'US',
            'postal_code' => '11222'
            ],

            [
            'property_id' => 16,
            'address_line_1' => '14 St - Union Sq, New York, NY 10003, USA',
            'address_line_2' => NULL,
            'latitude' => '40.73524929022898',
            'longitude' => '-73.99065202210693',
            'city' => 'New York',
            'state' => 'New York',
            'country' => 'US',
            'postal_code' => '10003'
            ]
        ]);
    }
}
