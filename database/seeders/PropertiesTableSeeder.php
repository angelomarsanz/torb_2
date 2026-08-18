<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class PropertiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('properties')->truncate();
    	
        DB::table('properties')->insert([
            
            [
                'name' => 'Hampton Inn',
                'slug' => 'hampton-inn',
                'host_id' => 1,
                'bedrooms' => 10,
                'beds' => 10,
                'bed_type' => 1,
                'bathrooms' => 8.00,
                'amenities' => '1,2,4,5,7,9,10,29,30,31',
                'property_type' => 1,
                'space_type' => 1,
                'accommodates' => 16,
                'booking_type' => 'request',
                'cancellation' => 'Flexible',
                'status' => 'Listed',
                'recomended' => 1,
                'created_at' => NULL,
                'updated_at' => NULL
                ],
                                 
                [
                'name' => 'North Sydney Harbourview Hotel',
                'slug' => 'north-sydney-harbourview-hotel',
                'host_id' => 1,
                'bedrooms' => 10,
                'beds' => 15,
                'bed_type' => 2,
                'bathrooms' => 8.00,
                'amenities' => '1,3,4,5,6,7,8,9,10,27,28',
                'property_type' => 2,
                'space_type' => 2,
                'accommodates' => 15,
                'booking_type' => 'request',
                'cancellation' => 'Flexible',
                'status' => 'Listed',
                'recomended' => 1,
                'created_at' => NULL,
                'updated_at' => NULL
                ],    
                            
                [
                'name' => 'Hotel Paris Rivoli',
                'slug' => 'hotel-paris-rivoli',
                'host_id' => 1,
                'bedrooms' => 10,
                'beds' => 16,
                'bed_type' => 3,
                'bathrooms' => 8.00,
                'amenities' => '1,2,4,5,6,11,12,13,14,17,18,19,21',
                'property_type' => 3,
                'space_type' => 3,
                'accommodates' => 10,
                'booking_type' => 'request',
                'cancellation' => 'Flexible',
                'status' => 'Listed',
                'recomended' => 1,
                'created_at' => NULL,
                'updated_at' => NULL
                ],          
                            
                [
                'name' => 'K+K Picasso',
                'slug' => 'k-k-picasso',
                'host_id' => 2,
                'bedrooms' => 10,
                'beds' => 10,
                'bed_type' => 4,
                'bathrooms' => 8.00,
                'amenities' => '1,3,4,5,6,7,10,11,21,22,23,24,25,26,27,28,29',
                'property_type' => 5,
                'space_type' => 1,
                'accommodates' => 10,
                'booking_type' => 'request',
                'cancellation' => 'Flexible',
                'status' => 'Listed',
                'recomended' => 1,
                'created_at' => NULL,
                'updated_at' => NULL
                ],
                                     
                [
                'name' => 'CONTACT APEX HOTELS',
                'slug' => 'contact-apex-hotels',
                'host_id' => 2,
                'bedrooms' => 5,
                'beds' => 10,
                'bed_type' => 6,
                'bathrooms' => 8.00,
                'amenities' => '1,3,4,9,10,11,17,18,19,20,21',
                'property_type' => 6,
                'space_type' => 2,
                'accommodates' => 10,
                'booking_type' => 'request',
                'cancellation' => 'Flexible',
                'status' => 'Listed',
                'recomended' => 1,
                'created_at' => NULL,
                'updated_at' => NULL
                ],
                                   
                [
                'name' => 'City Center Inn & Suites',
                'slug' => 'city-center-inn-suites',
                'host_id' => 2,
                'bedrooms' => 5,
                'beds' => 8,
                'bed_type' => 7,
                'bathrooms' => 3.00,
                'amenities' => '17,18,19,20,23,24,29',
                'property_type' => 7,
                'space_type' => 3,
                'accommodates' => 8,
                'booking_type' => 'instant',
                'cancellation' => 'Flexible',
                'status' => 'Listed',
                'recomended' => 1,
                'created_at' => NULL,
                'updated_at' => NULL
                ],        
                            
                [
                'name' => 'Dream House Duplex Villa mayrouba',
                'slug' => 'dream-house-duplex-villa-mayrouba',
                'host_id' => 1,
                'bedrooms' => 5,
                'beds' => 8,
                'bed_type' => 7,
                'bathrooms' => 3.00,
                'amenities' => '17,18,19,20,23,24,29',
                'property_type' => 7,
                'space_type' => 3,
                'accommodates' => 8,
                'booking_type' => 'instant',
                'cancellation' => 'Flexible',
                'status' => 'Listed',
                'recomended' => 1,
                'created_at' => NULL,
                'updated_at' => NULL
                ],        
                            
                [
                'name' => 'Fresh & Airy Private Bushwick Bedroom',
                'slug' => 'fresh-airy-private-bushwick-bedroom',
                'host_id' => 2,
                'bedrooms' => 5,
                'beds' => 8,
                'bed_type' => 7,
                'bathrooms' => 3.00,
                'amenities' => '17,18,19,20,23,24,29',
                'property_type' => 7,
                'space_type' => 3,
                'accommodates' => 8,
                'booking_type' => 'instant',
                'cancellation' => 'Flexible',
                'status' => 'Listed',
                'recomended' => 1,
                'created_at' => NULL,
                'updated_at' => NULL
                ],     
                            
                [
                'name' => 'Entire home/apt in Castlereagh St, Haymarket',
                'slug' => 'entire-homeapt-in-castlereagh-st-haymarket',
                'host_id' => 4,
                'bedrooms' => 5,
                'beds' => 8,
                'bed_type' => 3,
                'bathrooms' => 4.00,
                'amenities' => '1,2,3,4,5,6,7,9,10,17,21,27,30',
                'property_type' => 1,
                'space_type' => 1,
                'accommodates' => 5,
                'booking_type' => 'instant',
                'cancellation' => 'Flexible',
                'status' => 'Listed',
                'recomended' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL
                ],
                
                            
                [
                'name' => 'Entire home/apt in 33 Cox Ave, Bondi Beach',
                'slug' => 'entire-homeapt-in-33-cox-ave-bondi-beach',
                'host_id' => 4,
                'bedrooms' => 5,
                'beds' => 8,
                'bed_type' => 1,
                'bathrooms' => 4.00,
                'amenities' => '1,2,3,4,6,7,8,9,17,21,30',
                'property_type' => 1,
                'space_type' => 1,
                'accommodates' => 8,
                'booking_type' => 'request',
                'cancellation' => 'Flexible',
                'status' => 'Listed',
                'recomended' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL
                ],          
                            
                [
                'name' => 'Gold Room in Old American Style House, Near Subway',
                'slug' => 'gold-room-in-old-american-style-house-near-subway',
                'host_id' => 3,
                'bedrooms' => 2,
                'beds' => 2,
                'bed_type' => 1,
                'bathrooms' => 1.00,
                'amenities' => '1,2,3,4,11,12,17,18,23,24,27,29',
                'property_type' => 1,
                'space_type' => 1,
                'accommodates' => 2,
                'booking_type' => 'request',
                'cancellation' => 'Flexible',
                'status' => 'Listed',
                'recomended' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL
                ],            
                            
                [
                'name' => 'Entire home/apt in Sydney Olympic Park',
                'slug' => 'entire-homeapt-in-sydney-olympic-park',
                'host_id' => 4,
                'bedrooms' => 5,
                'beds' => 5,
                'bed_type' => 1,
                'bathrooms' => 0.50,
                'amenities' => '1,2,3,4,7,8,17,30',
                'property_type' => 1,
                'space_type' => 1,
                'accommodates' => 1,
                'booking_type' => 'instant',
                'cancellation' => 'Flexible',
                'status' => 'Listed',
                'recomended' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL
                ],          
                            
                [
                'name' => 'Special Junior suite in the heart of NYC',
                'slug' => 'special-junior-suite-in-the-heart-of-nyc',
                'host_id' => 3,
                'bedrooms' => 3,
                'beds' => 3,
                'bed_type' => 1,
                'bathrooms' => 2.00,
                'amenities' => '3,4,7,8,11,12,15,16,21,22,28',
                'property_type' => 1,
                'space_type' => 1,
                'accommodates' => 3,
                'booking_type' => 'instant',
                'cancellation' => 'Flexible',
                'status' => 'Listed',
                'recomended' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL
                ],           
                            
                [
                'name' => 'Entire home/apt in Newtown',
                'slug' => 'entire-homeapt-in-newtown',
                'host_id' => 4,
                'bedrooms' => 8,
                'beds' => 14,
                'bed_type' => 1,
                'bathrooms' => 5.00,
                'amenities' => '1,2,3,4,5,6,7,8,17,19,24,30',
                'property_type' => 1,
                'space_type' => 1,
                'accommodates' => 12,
                'booking_type' => 'request',
                'cancellation' => 'Flexible',
                'status' => 'Listed',
                'recomended' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL
                ],           
                            
                [
                'name' => 'Comfortable apartment with East River Views',
                'slug' => 'comfortable-apartment-with-east-river-views',
                'host_id' => 3,
                'bedrooms' => 4,
                'beds' => 4,
                'bed_type' => 7,
                'bathrooms' => 4.00,
                'amenities' => '1,2,5,6,11,12,17,18,25,26,27,29,31',
                'property_type' => 8,
                'space_type' => 3,
                'accommodates' => 4,
                'booking_type' => 'instant',
                'cancellation' => 'Flexible',
                'status' => 'Unlisted',
                'recomended' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL
                ],            
                            
                [
                'name' => 'Nobel Experience',
                'slug' => 'nobel-experience',
                'host_id' => 3,
                'bedrooms' => 1,
                'beds' => 1,
                'bed_type' => 1,
                'bathrooms' => 1.00,
                'amenities' => '7,8,9,10,13,14,28',
                'property_type' => 2,
                'space_type' => 3,
                'accommodates' => 1,
                'booking_type' => 'request',
                'cancellation' => 'Flexible',
                'status' => 'Listed',
                'recomended' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL
                ]
        ]);
    }
}
