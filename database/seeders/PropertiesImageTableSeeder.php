<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class PropertiesImageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('property_photos')->truncate();

        DB::table('property_photos')->insert([

            [
            'property_id' => 1,
            'photo' => 'property-1.jpg',
            'cover_photo' => 1,
            'serial' => 0
            ],

            [
            'property_id' => 1,
            'photo' => 'property-2.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 1,
            'photo' => 'property-3.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 1,
            'photo' => 'property-4.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 1,
            'photo' => 'property-34.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 2,
            'photo' => 'property-6.jpg',
            'cover_photo' => 1,
            'serial' => 0
            ],

            [
            'property_id' => 2,
            'photo' => 'property-7.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 2,
            'photo' => 'property-8.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 2,
            'photo' => 'property-9.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 2,
            'photo' => 'property-35.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 3,
            'photo' => 'property-10.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 3,
            'photo' => 'property-11.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 3,
            'photo' => 'property-12.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 3,
            'photo' => 'property-13.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 3,
            'photo' => 'property-36.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 4,
            'photo' => 'property-14.jpg',
            'cover_photo' => 1,
            'serial' => 0
            ],

            [
            'property_id' => 4,
            'photo' => 'property-15.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 4,
            'photo' => 'property-16.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 4,
            'photo' => 'property-17.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 4,
            'photo' => 'property-37.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 5,
            'photo' => 'property-18.jpg',
            'cover_photo' => 1,
            'serial' => 0
            ],

            [
            'property_id' => 5,
            'photo' => 'property-19.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 5,
            'photo' => 'property-20.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 5,
            'photo' => 'property-21.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 5,
            'photo' => 'property-38.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 6,
            'photo' => 'property-22.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 6,
            'photo' => 'property-23.jpg',
            'cover_photo' => 1,
            'serial' => 0
            ],

            [
            'property_id' => 6,
            'photo' => 'property-24.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 6,
            'photo' => 'property-25.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 6,
            'photo' => 'property-39.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 7,
            'photo' => 'property-26.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 7,
            'photo' => 'property-27.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 7,
            'photo' => 'property-28.jpg',
            'cover_photo' => 1,
            'serial' => 0
            ],

            [
            'property_id' => 7,
            'photo' => 'property-29.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 7,
            'photo' => 'property-40.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 8,
            'photo' => 'property-30.jpg',
            'cover_photo' => 1,
            'serial' => 0
            ],

            [
            'property_id' => 8,
            'photo' => 'property-31.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 8,
            'photo' => 'property-32.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 8,
            'photo' => 'property-33.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 8,
            'photo' => 'property-41.jpg',
            'cover_photo' => 0,
            'serial' => 0
            ],

            [
            'property_id' => 9,
            'photo' => '1606973880_property-24.jpg',
            'cover_photo' => 0,
            'serial' => 1
            ],

            [
            'property_id' => 9,
            'photo' => '1606973880_property-33.jpg',
            'cover_photo' => 0,
            'serial' => 2
            ],

            [
            'property_id' => 9,
            'photo' => '1606973880_property-35.jpg',
            'cover_photo' => 0,
            'serial' => 3
            ],

            [
            'property_id' => 9,
            'photo' => '1606973880_property-38.jpg',
            'cover_photo' => 0,
            'serial' => 4
            ],

            [
            'property_id' => 9,
            'photo' => '1606973880_property-39.jpg',
            'cover_photo' => 1,
            'serial' => 5
            ],

            [
            'property_id' => 9,
            'photo' => '1606973880_property-41.jpg',
            'cover_photo' => 0,
            'serial' => 6
            ],

            [
            'property_id' => 10,
            'photo' => '1606974277_property-8.jpg',
            'cover_photo' => 1,
            'serial' => 1
            ],

            [
            'property_id' => 10,
            'photo' => '1606974277_property-9.jpg',
            'cover_photo' => 0,
            'serial' => 2
            ],

            [
            'property_id' => 10,
            'photo' => '1606974277_property-25.jpg',
            'cover_photo' => 0,
            'serial' => 3
            ],

            [
            'property_id' => 10,
            'photo' => '1606974277_property-41.jpg',
            'cover_photo' => 0,
            'serial' => 4
            ],

            [
            'property_id' => 11,
            'photo' => '1606974319_property-22.jpg',
            'cover_photo' => 1,
            'serial' => 1
            ],

            [
            'property_id' => 11,
            'photo' => '1606974327_property-11.jpg',
            'cover_photo' => 0,
            'serial' => 2
            ],

            [
            'property_id' => 11,
            'photo' => '1606974333_property-36.jpg',
            'cover_photo' => 0,
            'serial' => 3
            ],

            [
            'property_id' => 11,
            'photo' => '1606974341_property-15.jpg',
            'cover_photo' => 0,
            'serial' => 4
            ],

            [
            'property_id' => 11,
            'photo' => '1606974350_property-2.jpg',
            'cover_photo' => 0,
            'serial' => 5
            ],

            [
            'property_id' => 12,
            'photo' => '1606974428_property-6.jpg',
            'cover_photo' => 0,
            'serial' => 1
            ],

            [
            'property_id' => 12,
            'photo' => '1606974428_property-7.jpg',
            'cover_photo' => 1,
            'serial' => 2
            ],

            [
            'property_id' => 12,
            'photo' => '1606974428_property-8.jpg',
            'cover_photo' => 0,
            'serial' => 3
            ],

            [
            'property_id' => 12,
            'photo' => '1606974428_property-9.jpg',
            'cover_photo' => 0,
            'serial' => 4
            ],

            [
            'property_id' => 12,
            'photo' => '1606974428_property-35.jpg',
            'cover_photo' => 0,
            'serial' => 5
            ],

            [
            'property_id' => 13,
            'photo' => '1606974775_property-38.jpg',
            'cover_photo' => 1,
            'serial' => 1
            ],

            [
            'property_id' => 13,
            'photo' => '1606974785_property-39.jpg',
            'cover_photo' => 0,
            'serial' => 2
            ],

            [
            'property_id' => 13,
            'photo' => '1606974799_property-35.jpg',
            'cover_photo' => 0,
            'serial' => 3
            ],

            [
            'property_id' => 13,
            'photo' => '1606974806_property-11.jpg',
            'cover_photo' => 0,
            'serial' => 4
            ],

            [
            'property_id' => 13,
            'photo' => '1606974825_property-29.jpg',
            'cover_photo' => 0,
            'serial' => 5
            ],

            [
            'property_id' => 14,
            'photo' => '1606974869_property-18.jpg',
            'cover_photo' => 0,
            'serial' => 1
            ],

            [
            'property_id' => 14,
            'photo' => '1606974869_property-21.jpg',
            'cover_photo' => 0,
            'serial' => 2
            ],

            [
            'property_id' => 14,
            'photo' => '1606974869_property-29.jpg',
            'cover_photo' => 0,
            'serial' => 3
            ],

            [
            'property_id' => 14,
            'photo' => '1606974869_property-31.jpg',
            'cover_photo' => 1,
            'serial' => 4
            ],

            [
            'property_id' => 15,
            'photo' => '1606975259_property-4.jpg',
            'cover_photo' => 1,
            'serial' => 1
            ],

            [
            'property_id' => 15,
            'photo' => '1606975266_property-7.jpg',
            'cover_photo' => 0,
            'serial' => 2
            ],

            [
            'property_id' => 15,
            'photo' => '1606975273_property-37.jpg',
            'cover_photo' => 0,
            'serial' => 3
            ],

            [
            'property_id' => 15,
            'photo' => '1606975280_property-25.jpg',
            'cover_photo' => 0,
            'serial' => 4
            ],

            [
            'property_id' => 15,
            'photo' => '1606975287_property-27.jpg',
            'cover_photo' => 0,
            'serial' => 5
            ],

            [
            'property_id' => 16,
            'photo' => '1606975535_property-9.jpg',
            'cover_photo' => 1,
            'serial' => 1
            ],

            [
            'property_id' => 16,
            'photo' => '1606975544_property-28.jpg',
            'cover_photo' => 0,
            'serial' => 2
            ],

            [
            'property_id' => 16,
            'photo' => '1606975552_property-41.jpg',
            'cover_photo' => 0,
            'serial' => 3
            ],

            [
            'property_id' => 16,
            'photo' => '1606975562_property-14.jpg',
            'cover_photo' => 0,
            'serial' => 4
            ],

            [
            'property_id' => 16,
            'photo' => '1606975573_property-35.jpg',
            'cover_photo' => 0,
            'serial' => 5
            ],

            [
            'property_id' => 7,
            'photo' => 'property-7.jpg',
            'cover_photo' => 0,
            'serial' => 1
            ],

            [
            'property_id' => 3,
            'photo' => 'property-36.jpg',
            'cover_photo' => 1,
            'serial' => 1
            ],


        ]);
    }
}
