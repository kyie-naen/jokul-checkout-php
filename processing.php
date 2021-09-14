<?php
require_once('core.php');
date_default_timezone_set('Asia/Jakarta');

$data = json_decode(file_get_contents('php://input'));

// set invoice
$invoice = 'INV_'.time();

//set env
$getUrl = 'https://api-sandbox.doku.com';
$path = '/checkout/v1/payment';
$url = $getUrl . $path;

//set credentials
$clientid = 'MCH-0447-1270651441479';
$secretkey = 'SK-oqadmqpeZpMDa21lvEgo';

$requestid = rand(1, 100000); // You can use UUID or anything
$dateTime = gmdate("Y-m-d H:i:s");
$isoDateTime = date(DATE_ISO8601, strtotime($dateTime));
$requesttime = substr($isoDateTime, 0, 19) . "Z";

$requestBody = array (
'order' =>
    array (
        'amount' => $data->amount,
        'invoice_number' => $invoice,
        'currency' => 'IDR',
        'callback_url' => 'https://doku.com/',
        'line_items' =>
        array (
          0 =>
            array (
                'name' => 'DOKU T-Shirt',
                'price' => $data->amount,
                'quantity' => 1
            )
        ),
    ),
    'payment' =>
    array (
        'payment_due_date' => 120
    ),
    'customer' =>
    array (
        'id' => '123123123',
        'name' => 'Rizky',
        'email' => 'rizky.zulkarnaen@doku.com',
        'phone' => '6287805586273',
        'address' => 'Jakarta',
        'country' => 'ID',
    )
);

$digest = Jokul_Core::generateDigest($requestBody);
$signature = Jokul_Core::generateSignaturePost($clientid, $requestid, $requesttime, $secretkey, $path, $digest);

$result = Jokul_Core::hitApi($url, $clientid, $requestid, $requesttime, $signature, $requestBody);

if ($result['httpcode'] == 200) {
  $hasil = json_decode($result['responseJson']);
  echo $hasil->response->payment->url;
}
