<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->truncate();

        DB::table('users')->insert([

            [
            'first_name' => 'test',
            'last_name' => 'user',
            'email' => 'test@techvill.net',
            'phone' => '2015550124',
            'formatted_phone' => '+12015550124',
            'carrier_code' => '1',
            'default_country' => 'us',
            'password' => '$2y$10$kLKUKTJo.3ErEr6T/6pz/eUY.SqA1AbH83eYwh2W5F0cwoXJelwzO',
            'profile_image' => 'profile.png',
            'balance' => 0,
            'status' => 'Active',
            'remember_token' => NULL,
            'created_at' => NULL,
            'updated_at' => NULL
            ],
            
            
                        
            [
            'first_name' => 'customer',
            'last_name' => 'user',
            'email' => 'customer@techvill.net',
            'phone' => '2015550125',
            'formatted_phone' => '+12015550125',
            'carrier_code' => '1',
            'default_country' => 'us',
            'password' => '$2y$10$.1D.yMTt3DJl2vgMNsnIPuRgJn/VHmPm.ixyHf5AJHATFSIDcY35K',
            'profile_image' => 'profile.jpg',
            'balance' => 0,
            'status' => 'Active',
            'remember_token' => NULL,
            'created_at' => NULL,
            'updated_at' => NULL
            ],
            
            
                        
            [
            'first_name' => 'William',
            'last_name' => 'Smith',
            'email' => 'william@techvill.net',
            'phone' => '2015559123',
            'formatted_phone' => '+12015559123',
            'carrier_code' => '1',
            'default_country' => 'us',
            'password' => '$2y$10$A59X3xGYont3uTIg2BMqEu/skFDSKud6cn.kFM.Wz05uTm.1oVWFO',
            'profile_image' => 'profile_1606977863.png',
            'balance' => 0,
            'status' => 'Active',
            'remember_token' => 'xmrk9NtKWrJX48O9tIoVfNGO5PEJVC8D8MBP9Y8DihhtzOIugg662cLdgAyM',
            'created_at' => NULL,
            'updated_at' => NULL
            ],
            
            
                        
            [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@techvill.net',
            'phone' => '2017772727',
            'formatted_phone' => '+12017772727',
            'carrier_code' => '1',
            'default_country' => 'us',
            'password' => '$2y$10$Pi4y686ZYzXZ/NyAKvAijeQrMggROT2eFoABC4l.R6iPaL2SIL0gC',
            'profile_image' => 'profile_1606977391.png',
            'balance' => 0,
            'status' => 'Active',
            'remember_token' => NULL,
            'created_at' => NULL,
            'updated_at' => NULL
            ],

        ]);
    }
}
