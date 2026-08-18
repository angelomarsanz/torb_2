<?php

namespace Modules\Gateway\Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class GatewaysTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        DB::table('gateways')->delete();

        DB::table('gateways')->insert(array(
            0 =>
            array(
                'alias' => 'paypal',
                'name' => 'Paypal',
                'sandbox' => 1,
                'data' => '{"secretKey":"' . env('SECRETKEY_PAYPAL', '') . '","clientId":"' . env('CLIENTID_PAYPAL', '') . '","instruction":"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.","status":"1","sandbox":"1"}',
                'instruction' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'image' => 'thumbnail.png',
                'status' => 1,
            ),
            1 =>
            array(
                'alias' => 'stripe',
                'name' => 'Stripe',
                'sandbox' => 1,
                'data' => '{"clientSecret":"' . env('CLIENTSECRET_STRIPE', '') . '","publishableKey":"' . env('PUBLISHABLEKEY_STRIPE', '') . '","instruction":"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.","status":"1","sandbox":"1"}',
                'instruction' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'image' => 'thumbnail.png',
                'status' => 1,
            ),
            2 =>
            array(
                'alias' => 'directbanktransfer',
                'name' => 'DirectBankTransfer',
                'sandbox' => 1,
                'data' => '{"name":"DirectBankTransfer","account_name":"John Doe","iban":"6667 77637 32432","swift_code":"Test123","routing_no":"Test123","bank_name":"HSBC","branch_name":"Chicago","branch_city":"Chicago","branch_address":"123, Shicago , USA","country":"USA","logo":"hsbc.png","status":"1","instruction":"Make your payment directly into our bank account. please upload necessary attachment to verify the transaction. the admin will approve the transaction if it valid"}',
                'instruction' => 'Make your payment directly into our bank account. please upload necessary attachment to verify the transaction. the admin will approve the transaction if it valid.',
                'image' => 'thumbnail.png',
                'status' => 1,
            ),
            3 =>
            array(
                'alias' => 'flutterwave',
                'name' => 'Flutterwave',
                'sandbox' => 1,
                'data' => '{"secretKey":"' . env('SECRETKEY_FLUTTERWAVE', '') . '","publicKey":"' . env('PUBLICKEY_FLUTTERWAVE', '') . '","encryptionKey":"' . env('ENCRYPTIONKEY_FLUTTERWAVE', '') . '","instruction":"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua","status":"1"}',
                'instruction' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua',
                'image' => 'thumbnail.png',
                'status' => 1,
            ),
            
            4 =>
            array(
                'alias' => 'paystack',
                'name' => 'Paystack',
                'sandbox' => 1,
                'data' => '{"secretKey":"' . env('SECRETKEY_PAYSTACK', '') . '","publicKey":"' . env('PUBLICKEY_PAYSTACK', '') . '","instruction":"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.","status":"1","sandbox":"1"}',
                'instruction' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'image' => 'thumbnail.png',
                'status' => 1,
            ),

            5 =>
            array(
                'alias' => 'razorpay',
                'name' => 'Razorpay',
                'sandbox' => 1,
                'data' => '{"apiKey":"' . env('APIKEY_RAZORPAY', '') . '","apiSecret":"' . env('SECRETKEY_RAZORPAY', '') . '","instruction":"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.","status":"1","sandbox":"1"}',
                'instruction' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'image' => 'thumbnail.png',
                'status' => 1,
            ),
            6 =>
            array(
                'alias' => 'paytr',
                'name' => 'PayTR',
                'sandbox' => 1,
                'data' => '{"name":"PayTR","merchantId":"' . env('MERCHANTID_PAYTR', '') . '","merchantKey":"' . env('MERCHANTKEY_PAYTR', '') . '","merchantSalt":"' . env('MERCHANTSALT_PAYTR', '') . '","callbackURL":"' . url('gateway/paytr/callbackPaytr') .'","instruction":"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.","status":"1","sandbox":"1"}',
                'instruction' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'image' => 'thumbnail.png',
                'status' => 1,
            ),
            7 =>
            array(
                'alias' => 'yookassa',
                "name" => "YooKassa",
                "sandbox" => 1,
                "data" => '{"name":"YooKassa","storeId":"' . env('STOREID_YOOKASSA', '') . '","secretKey":"' . env('SECRETKEY_YOOKASSA', '') . '","instruction":"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.","status":"1","sandbox":"1"}',
                "instruction" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
                'image' => 'thumbnail.png',
                "status" => 1
            ),
        ));
    }
}
