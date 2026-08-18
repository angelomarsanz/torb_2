<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class WithdrawalsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('withdrawals')->truncate();

        DB::table('withdrawals')->insert([

            [
            'user_id' => 2,
            'payout_id' => 1,
            'currency_id' => 1,
            'payment_method_id' => 1,
            'uuid' => '5fc88754b59e8',
            'subtotal' => '100.00',
            'amount' => NULL,
            'email' => 'customer@techvill.net',
            'status' => 'Pending',
            'created_at' => '2020-12-02 00:00:00',
            'updated_at' => '2020-12-02 00:00:00'
            ],

            [
            'user_id' => 2,
            'payout_id' => 1,
            'currency_id' => 1,
            'payment_method_id' => 1,
            'uuid' => '5fc8875e08ac3',
            'subtotal' => '0.00',
            'amount' => '10.00',
            'email' => 'customer@techvill.net',
            'status' => 'Success',
            'created_at' => '2020-12-01 00:00:00',
            'updated_at' => '2020-12-01 00:00:00'
            ],

            [
            'user_id' => 3,
            'payout_id' => 2,
            'currency_id' => 1,
            'payment_method_id' => 1,
            'uuid' => '5fc8889bea1ba',
            'subtotal' => '0.00',
            'amount' => '11.00',
            'email' => 'william@techvill.net',
            'status' => 'Success',
            'created_at' => '2020-12-02 00:00:00',
            'updated_at' => '2020-12-02 00:00:00'
            ],

            [
            'user_id' => 3,
            'payout_id' => 2,
            'currency_id' => 1,
            'payment_method_id' => 1,
            'uuid' => '5fc888a2abf2f',
            'subtotal' => '0.00',
            'amount' => '10.00',
            'email' => 'william@techvill.net',
            'status' => 'Success',
            'created_at' => '2020-12-01 00:00:00',
            'updated_at' => '2020-12-01 00:00:00'
            ],

            [
            'user_id' => 4,
            'payout_id' => 3,
            'currency_id' => 1,
            'payment_method_id' => 1,
            'uuid' => '5fc8890b2893c',
            'subtotal' => '0.00',
            'amount' => '120.00',
            'email' => 'john@techvill.net',
            'status' => 'Success',
            'created_at' => '2020-12-02 00:00:00',
            'updated_at' => '2020-12-02 00:00:00'
            ],

            [
            'user_id' => 4,
            'payout_id' => 3,
            'currency_id' => 1,
            'payment_method_id' => 1,
            'uuid' => '5fc889239f198',
            'subtotal' => '150.00',
            'amount' => NULL,
            'email' => 'john@techvill.net',
            'status' => 'Pending',
            'created_at' => '2020-12-01 00:00:00',
            'updated_at' => '2020-12-01 00:00:00'
            ],

            [
            'user_id' => 4,
            'payout_id' => 3,
            'currency_id' => 1,
            'payment_method_id' => 1,
            'uuid' => '5fc8892f4faa6',
            'subtotal' => '244.00',
            'amount' => NULL,
            'email' => 'john@techvill.net',
            'status' => 'Pending',
            'created_at' => '2020-12-02 00:00:00',
            'updated_at' => '2020-12-02 00:00:00'
            ],

            [
            'user_id' => 1,
            'payout_id' => 4,
            'currency_id' => 1,
            'payment_method_id' => 1,
            'uuid' => '5fc88cc41ec14',
            'subtotal' => '0.00',
            'amount' => '10.00',
            'email' => 'test@techvill.net',
            'status' => 'Success',
            'created_at' => '2020-12-01 00:00:00',
            'updated_at' => '2020-12-01 00:00:00'
            ],

            [
            'user_id' => 1,
            'payout_id' => 4,
            'currency_id' => 1,
            'payment_method_id' => 1,
            'uuid' => '5fc898f1464a9',
            'subtotal' => '10.00',
            'amount' => NULL,
            'email' => 'test@techvill.net',
            'status' => 'Pending',
            'created_at' => '2020-12-02 00:00:00',
            'updated_at' => '2020-12-02 00:00:00'
            ],

            [
            'user_id' => 3,
            'payout_id' => 2,
            'currency_id' => 1,
            'payment_method_id' => 1,
            'uuid' => '60d3faaee4ff3',
            'subtotal' => '0.00',
            'amount' => '10.00',
            'email' => 'william@techvill.net',
            'status' => 'Success',
            'created_at' => '2021-06-24 10:23:26',
            'updated_at' => '2021-06-24 10:23:50'
            ]

        ]);
    }
}
