<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class MessagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('messages')->truncate();
    	
        DB::table('messages')->insert( [
            [
            'id' => 1,
            'property_id' => 1,
            'booking_id' => 1,
            'sender_id' => 4,
            'receiver_id' => 1,
            'message' => 'Hey, I want to stay at this home.',
            'type_id' => 4,
            'read' => 1,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2020-12-03 00:14:57',
            'updated_at' => '2021-06-23 17:42:01'
            ],
                        
            [
            'id' => 3,
            'property_id' => 9,
            'booking_id' => 3,
            'sender_id' => 3,
            'receiver_id' => 4,
            'message' => 'Booking for Entire home/apt in Castlereagh St, Haymarket',
            'type_id' => 4,
            'read' => 0,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2020-12-03 00:18:06',
            'updated_at' => '2020-12-03 00:18:06'
            ],
                        
            [
            'id' => 4,
            'property_id' => 16,
            'booking_id' => 4,
            'sender_id' => 4,
            'receiver_id' => 3,
            'message' => 'Hey, I want to stay at this home.',
            'type_id' => 4,
            'read' => 1,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2020-12-03 00:19:16',
            'updated_at' => '2020-12-03 00:21:49'
            ],
                        
            [
            'id' => 6,
            'property_id' => 7,
            'booking_id' => 6,
            'sender_id' => 3,
            'receiver_id' => 1,
            'message' => 'Hellow, Paying for Dream house duplex villa mayrouba booking',
            'type_id' => 4,
            'read' => 1,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2020-12-03 00:21:22',
            'updated_at' => '2021-06-23 17:41:55'
            ],
                        
            [
            'id' => 8,
            'property_id' => 5,
            'booking_id' => 8,
            'sender_id' => 4,
            'receiver_id' => 2,
            'message' => 'I want to stay your home.',
            'type_id' => 4,
            'read' => 1,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2020-12-03 00:23:05',
            'updated_at' => '2021-06-23 17:48:32'
            ],
                        
            [
            'id' => 9,
            'property_id' => 3,
            'booking_id' => 9,
            'sender_id' => 4,
            'receiver_id' => 1,
            'message' => 'I want to stay your home',
            'type_id' => 4,
            'read' => 1,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2020-12-03 00:23:48',
            'updated_at' => '2021-06-23 17:41:51'
            ],
                        
            [
            'id' => 10,
            'property_id' => 7,
            'booking_id' => 10,
            'sender_id' => 2,
            'receiver_id' => 1,
            'message' => 'Booking for Dream House Duplex Villa Mayrouba',
            'type_id' => 4,
            'read' => 1,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2020-12-03 00:25:47',
            'updated_at' => '2021-06-23 17:44:56'
            ],
                        
            [
            'id' => 11,
            'property_id' => 6,
            'booking_id' => 11,
            'sender_id' => 1,
            'receiver_id' => 2,
            'message' => 'Hey, I want to stay at this home.',
            'type_id' => 4,
            'read' => 1,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2020-12-03 00:26:28',
            'updated_at' => '2021-06-23 17:47:58'
            ],
                        
            [
            'id' => 12,
            'property_id' => 10,
            'booking_id' => 12,
            'sender_id' => 1,
            'receiver_id' => 4,
            'message' => 'I want to book your apartment.',
            'type_id' => 4,
            'read' => 0,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2020-12-03 00:27:20',
            'updated_at' => '2020-12-03 00:27:20'
            ],
                        
             [
            'id' => 14,
            'property_id' => 11,
            'booking_id' => 14,
            'sender_id' => 1,
            'receiver_id' => 3,
            'message' => 'I want to stay at your home.',
            'type_id' => 4,
            'read' => 0,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2020-12-03 00:28:08',
            'updated_at' => '2020-12-03 00:28:08'
            ],
                        
            [
            'id' => 16,
            'property_id' => 6,
            'booking_id' => 11,
            'sender_id' => 2,
            'receiver_id' => 1,
            'message' => 'Ok We are ready to host you',
            'type_id' => 1,
            'read' => 1,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2020-12-03 00:30:27',
            'updated_at' => '2021-06-23 17:47:33'
            ],
                        
            [
            'id' => 17,
            'property_id' => 6,
            'booking_id' => 11,
            'sender_id' => 1,
            'receiver_id' => 2,
            'message' => 'thanks',
            'type_id' => 1,
            'read' => 1,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2020-12-03 00:31:13',
            'updated_at' => '2021-06-23 17:47:58'
            ],
                        
            [
            'id' => 18,
            'property_id' => 7,
            'booking_id' => 10,
            'sender_id' => 1,
            'receiver_id' => 2,
            'message' => 'Ok We are ready to host you',
            'type_id' => 1,
            'read' => 1,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2020-12-03 00:33:12',
            'updated_at' => '2021-06-23 17:45:49'
            ],
                        
            [
            'id' => 19,
            'property_id' => 1,
            'booking_id' => 1,
            'sender_id' => 1,
            'receiver_id' => 4,
            'message' => 'Ok We are ready to host you',
            'type_id' => 1,
            'read' => 0,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2020-12-03 00:33:23',
            'updated_at' => '2020-12-03 00:33:23'
            ],
                        
            [
            'id' => 20,
            'property_id' => 7,
            'booking_id' => 16,
            'sender_id' => 4,
            'receiver_id' => 1,
            'message' => 'Booking Dream house duplex villa mayrouba apartment',
            'type_id' => 4,
            'read' => 1,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2020-12-03 01:43:37',
            'updated_at' => '2021-06-23 17:41:58'
            ],
                        
            [
            'id' => 21,
            'property_id' => 7,
            'booking_id' => 10,
            'sender_id' => 2,
            'receiver_id' => 1,
            'message' => 'When will you arrived ?',
            'type_id' => 1,
            'read' => 1,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2021-06-23 17:42:59',
            'updated_at' => '2021-06-23 17:44:56'
            ],
                        
            [
            'id' => 22,
            'property_id' => 7,
            'booking_id' => 10,
            'sender_id' => 1,
            'receiver_id' => 2,
            'message' => 'We will arrive at 10 PM, what is your check in time ?',
            'type_id' => 1,
            'read' => 1,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2021-06-23 17:43:43',
            'updated_at' => '2021-06-23 17:45:49'
            ],
                        
            [
            'id' => 23,
            'property_id' => 7,
            'booking_id' => 10,
            'sender_id' => 2,
            'receiver_id' => 1,
            'message' => 'You can check in here from 12 A.M',
            'type_id' => 1,
            'read' => 1,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2021-06-23 17:44:44',
            'updated_at' => '2021-06-23 17:44:56'
            ],
                        
            [
            'id' => 24,
            'property_id' => 6,
            'booking_id' => 11,
            'sender_id' => 2,
            'receiver_id' => 1,
            'message' => 'When you will arrive ?',
            'type_id' => 1,
            'read' => 1,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2021-06-23 17:46:07',
            'updated_at' => '2021-06-23 17:47:33'
            ],
                        
            [
            'id' => 25,
            'property_id' => 6,
            'booking_id' => 11,
            'sender_id' => 1,
            'receiver_id' => 2,
            'message' => 'We will there on 10 PM',
            'type_id' => 1,
            'read' => 1,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2021-06-23 17:46:45',
            'updated_at' => '2021-06-23 17:47:58'
            ],
                        
            [
            'id' => 26,
            'property_id' => 6,
            'booking_id' => 11,
            'sender_id' => 1,
            'receiver_id' => 2,
            'message' => 'We will receive you from station',
            'type_id' => 1,
            'read' => 1,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2021-06-23 17:47:33',
            'updated_at' => '2021-06-23 17:47:58'
            ],
                        
            [
            'id' => 27,
            'property_id' => 15,
            'booking_id' => 17,
            'sender_id' => 2,
            'receiver_id' => 3,
            'message' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam et.',
            'type_id' => 4,
            'read' => 0,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2021-06-24 10:10:17',
            'updated_at' => '2021-06-24 10:10:17'
            ],
                        
            [
            'id' => 28,
            'property_id' => 15,
            'booking_id' => 18,
            'sender_id' => 2,
            'receiver_id' => 3,
            'message' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam et.',
            'type_id' => 4,
            'read' => 0,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2021-06-24 10:12:12',
            'updated_at' => '2021-06-24 10:12:12'
            ],
                        
            [
            'id' => 29,
            'property_id' => 15,
            'booking_id' => 19,
            'sender_id' => 2,
            'receiver_id' => 3,
            'message' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam et.',
            'type_id' => 4,
            'read' => 1,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2021-06-24 10:16:20',
            'updated_at' => '2021-06-24 10:28:35'
            ],
                        
            [
            'id' => 30,
            'property_id' => 7,
            'booking_id' => 20,
            'sender_id' => 2,
            'receiver_id' => 1,
            'message' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam et.',
            'type_id' => 4,
            'read' => 0,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2021-06-24 10:20:39',
            'updated_at' => '2021-06-24 10:20:39'
            ],
                        
            [
            'id' => 31,
            'property_id' => 15,
            'booking_id' => 19,
            'sender_id' => 3,
            'receiver_id' => 2,
            'message' => 'Thanks for booking us',
            'type_id' => 1,
            'read' => 1,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2021-06-24 10:27:45',
            'updated_at' => '2021-06-24 10:29:06'
            ],
                        
            [
            'id' => 32,
            'property_id' => 15,
            'booking_id' => 19,
            'sender_id' => 2,
            'receiver_id' => 3,
            'message' => 'You are welcome',
            'type_id' => 1,
            'read' => 1,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2021-06-24 10:28:06',
            'updated_at' => '2021-06-24 10:28:35'
            ],
                        
             [
            'id' => 33,
            'property_id' => 15,
            'booking_id' => 19,
            'sender_id' => 3,
            'receiver_id' => 2,
            'message' => 'Our checking time is 12 pm',
            'type_id' => 1,
            'read' => 1,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2021-06-24 10:28:35',
            'updated_at' => '2021-06-24 10:29:06'
            ],
                        
            [
            'id' => 34,
            'property_id' => 15,
            'booking_id' => 19,
            'sender_id' => 2,
            'receiver_id' => 3,
            'message' => 'We will be there before 12 pm, thanks for informing us',
            'type_id' => 1,
            'read' => 0,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2021-06-24 10:29:06',
            'updated_at' => '2021-06-24 10:29:06'
            ],
                        
            [
            'id' => 35,
            'property_id' => 1,
            'booking_id' => 21,
            'sender_id' => 3,
            'receiver_id' => 1,
            'message' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam et.',
            'type_id' => 4,
            'read' => 0,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2021-06-24 11:51:13',
            'updated_at' => '2021-06-24 11:51:13'
            ],
                        
            [
            'id' => 36,
            'property_id' => 1,
            'booking_id' => 21,
            'sender_id' => 1,
            'receiver_id' => 3,
            'message' => 'fsaasfd',
            'type_id' => 5,
            'read' => 0,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2021-06-24 11:51:54',
            'updated_at' => '2021-06-24 11:51:54'
            ],
                        
            [
            'id' => 37,
            'property_id' => 1,
            'booking_id' => 21,
            'sender_id' => 3,
            'receiver_id' => 1,
            'message' => 'sadasdf',
            'type_id' => 4,
            'read' => 0,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2021-06-24 11:53:06',
            'updated_at' => '2021-06-24 11:53:06'
            ],
                        
            [
            'id' => 42,
            'property_id' => 8,
            'booking_id' => 26,
            'sender_id' => 1,
            'receiver_id' => 2,
            'message' => 'Hi. Instant Booking',
            'type_id' => 4,
            'read' => 0,
            'archive' => 0,
            'star' => 0,
            'created_at' => '2022-02-01 22:43:05',
            'updated_at' => '2022-02-01 22:43:05'
            ]
        ]);
    }
}