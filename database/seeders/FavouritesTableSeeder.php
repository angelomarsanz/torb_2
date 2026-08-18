<?php
namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use DB;

class FavouritesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('favourites')->truncate();
        DB::table('favourites')->insert([
        		['property_id' => '7', 'user_id' => '2', 'status' => 'Active', 'created_at' => NULL, 'updated_at' => NULL],
        		['property_id' => '15', 'user_id' => '2', 'status' => 'Active', 'created_at' => NULL, 'updated_at' => NULL],
        		['property_id' => '11', 'user_id' => '1', 'status' => 'Active', 'created_at' => NULL, 'updated_at' => NULL],
        		['property_id' => '10', 'user_id' => '1', 'status' => 'Active', 'created_at' => NULL, 'updated_at' => NULL],
        		['property_id' => '6', 'user_id' => '1', 'status' => 'Active', 'created_at' => NULL, 'updated_at' => NULL],
        	]);
    }
}
