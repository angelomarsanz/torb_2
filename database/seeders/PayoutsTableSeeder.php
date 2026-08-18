<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class PayoutsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payouts')->truncate();
    	
        DB::table('payouts')->insert([
            [
                'id' => 2,
                'booking_id' => 3,
                'user_id' => 4,
                'property_id' => 9,
                'user_type' => 'Host',
                'amount' => 160,
                'penalty_amount' => 0,
                'status' => 'Future',
                'currency_code' => 'USD',
                'created_at' => '2020-12-01 00:00:00',
                'updated_at' => '2020-12-01 00:00:00'
                ],           
                            
                [
                'id' => 4,
                'booking_id' => 6,
                'user_id' => 1,
                'property_id' => 7,
                'user_type' => 'Host',
                'amount' => 100,
                'penalty_amount' => 0,
                'status' => 'Future',
                'currency_code' => 'USD',
                'created_at' => '2020-12-01 00:00:00',
                'updated_at' => '2020-12-01 00:00:00'
                ],          
                            
                [
                'id' => 6,
                'booking_id' => 10,
                'user_id' => 1,
                'property_id' => 7,
                'user_type' => 'Host',
                'amount' => 100,
                'penalty_amount' => 0,
                'status' => 'Future',
                'currency_code' => 'USD',
                'created_at' => '2020-12-01 00:00:00',
                'updated_at' => '2020-12-01 00:00:00'
                ],
                                      
                [
                'id' => 7,
                'booking_id' => 11,
                'user_id' => 2,
                'property_id' => 6,
                'user_type' => 'Host',
                'amount' => 120,
                'penalty_amount' => 0,
                'status' => 'Future',
                'currency_code' => 'USD',
                'created_at' => '2020-12-02 00:00:00',
                'updated_at' => '2020-12-02 00:00:00'
                ],
                                   
                [
                'id' => 10,
                'booking_id' => 16,
                'user_id' => 1,
                'property_id' => 7,
                'user_type' => 'Host',
                'amount' => 100,
                'penalty_amount' => 0,
                'status' => 'Future',
                'currency_code' => 'USD',
                'created_at' => '2020-12-01 00:00:00',
                'updated_at' => '2020-12-01 00:00:00'
                ],
                                      
                [
                'id' => 11,
                'booking_id' => 17,
                'user_id' => 3,
                'property_id' => 15,
                'user_type' => 'Host',
                'amount' => 55,
                'penalty_amount' => 0,
                'status' => 'Future',
                'currency_code' => 'USD',
                'created_at' => '2021-06-24 10:10:17',
                'updated_at' => '2021-06-24 10:10:17'
                ],
                                    
                [
                'id' => 12,
                'booking_id' => 18,
                'user_id' => 3,
                'property_id' => 15,
                'user_type' => 'Host',
                'amount' => 110,
                'penalty_amount' => 0,
                'status' => 'Future',
                'currency_code' => 'USD',
                'created_at' => '2021-06-24 10:12:12',
                'updated_at' => '2021-06-24 10:12:12'
                ],        
                            
                [
                'id' => 13,
                'booking_id' => 19,
                'user_id' => 3,
                'property_id' => 15,
                'user_type' => 'Host',
                'amount' => 55,
                'penalty_amount' => 0,
                'status' => 'Future',
                'currency_code' => 'USD',
                'created_at' => '2021-06-24 10:16:20',
                'updated_at' => '2021-06-24 10:16:20'
                ],            
                            
                [
                'id' => 14,
                'booking_id' => 20,
                'user_id' => 1,
                'property_id' => 7,
                'user_type' => 'Host',
                'amount' => 200,
                'penalty_amount' => 0,
                'status' => 'Future',
                'currency_code' => 'USD',
                'created_at' => '2021-06-24 10:20:39',
                'updated_at' => '2021-06-24 10:20:39'
                ],
                                                     
                [
                'id' => 15,
                'booking_id' => 21,
                'user_id' => 1,
                'property_id' => 1,
                'user_type' => 'Host',
                'amount' => 5,
                'penalty_amount' => 0,
                'status' => 'Future',
                'currency_code' => 'USD',
                'created_at' => '2021-06-24 11:51:54',
                'updated_at' => '2021-06-24 11:51:54'
                ],
                                        
                [
                'id' => 16,
                'booking_id' => 21,
                'user_id' => 1,
                'property_id' => 1,
                'user_type' => 'Host',
                'amount' => 5,
                'penalty_amount' => 0,
                'status' => 'Future',
                'currency_code' => 'USD',
                'created_at' => '2021-06-24 11:53:06',
                'updated_at' => '2021-06-24 11:53:06'
                ],
                                       
                [
                'id' => 17,
                'booking_id' => 22,
                'user_id' => 2,
                'property_id' => 8,
                'user_type' => 'Host',
                'amount' => 200,
                'penalty_amount' => 0,
                'status' => 'Future',
                'currency_code' => 'USD',
                'created_at' => '2022-02-01 22:39:31',
                'updated_at' => '2022-02-01 22:39:31'
                ],
                                         
                [
                'id' => 18,
                'booking_id' => 26,
                'user_id' => 2,
                'property_id' => 8,
                'user_type' => 'Host',
                'amount' => 200,
                'penalty_amount' => 0,
                'status' => 'Future',
                'currency_code' => 'USD',
                'created_at' => '2022-02-01 22:47:43',
                'updated_at' => '2022-02-01 22:47:43'
                ],
        	]);
    }
}
