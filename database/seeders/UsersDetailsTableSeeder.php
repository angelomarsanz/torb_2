<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class UsersDetailsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_details')->truncate();

        DB::table('user_details')->insert([

            [
            'user_id' => 3,
            'field' => 'date_of_birth',
            'value' => '1993-12-21'
            ],

            [
            'user_id' => 4,
            'field' => 'date_of_birth',
            'value' => '1982-1-18'
            ],

            [
            'user_id' => 3,
            'field' => 'live',
            'value' => 'New York'
            ],

            [
            'user_id' => 3,
            'field' => 'about',
            'value' => 'My name is William Smith, and I love to travel. My biggest passion is traveling the world and experiencing new adventures and cultures. I live for it.'
            ],

            [
            'user_id' => 1,
            'field' => 'gender',
            'value' => 'Male'
            ],

            [
            'user_id' => 1,
            'field' => 'live',
            'value' => 'Washington'
            ],

            [
            'user_id' => 1,
            'field' => 'about',
            'value' => "I'm a 26 year-old entrepreneur from just outside of Washington D.C. I was born to a single mother in a lower-middle-class family. Growing up, life was hard. My mother had type-2 diabetes, high blood pressure, and bipolar disorder. That was the perfect storm for me to have an abusive upbringing. I was abused physically, emotionally, verbally…. no child should have to endure what I went through, but I really do believe that it made me stronger in a weird way."
            ],

            [
            'user_id' => 1,
            'field' => 'date_of_birth',
            'value' => '1987-12-19'
            ],

            [
            'user_id' => 2,
            'field' => 'live',
            'value' => 'Sydney'
            ],

            [
            'user_id' => 2,
            'field' => 'about',
            'value' => 'I am actually from a third world country, In 2003 i came to Sydney to study for bachelors degree in Electronics engineering.\r\n\r\nI found about Sydny only from Internet and applied to the university and moved with just 1000 US dollars. Without knowing anyone in Denmark. and with knowledge that i will not get more money.'
            ],

            [
            'user_id' => 2,
            'field' => 'date_of_birth',
            'value' => '1958-11-18'
            ],

            [
            'user_id' => 4,
            'field' => 'live',
            'value' => 'Jersey City'
            ],

            [
            'user_id' => 4,
            'field' => 'about',
            'value' => "I'm a world traveler who's been traveling since I was 7 years and have been to a few destinations around this planet since then."
            ]

        ]);
    }
}
