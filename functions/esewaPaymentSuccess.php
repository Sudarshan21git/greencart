<?php
session_start();
include '../database/database.php';

if (!isset($_GET['oid']) || !isset($_GET['amt']) || !isset($_GET['refId'])) {
    die("Invalid eSewa response.");
}

$oid = $_GET['oid'];
$amt = $_GET['amt'];
$refId = $_GET['refId'];

$url = "https://rc.esewa.com.np/epay/transrec";
$data = [
    'amt' => $amt,
    'scd' => 'EPAYTEST',
    'pid' => $oid,
    'rid' => $refId
];

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));

$response = curl_exec($curl);
curl_close($curl);

$xml = simplexml_load_string($response);
if (isset($xml->response_code) && $xml->response_code == 'Success') {
    // ✅ Mark order as complete in DB
    echo "✅ Payment Verified for Order ID: " . htmlspecialchars($oid);
    // You can redirect to order confirmation page or process DB entry here
} else {
    echo " Payment verification failed.";
    echo "<pre>"; print_r($xml); echo "</pre>";
}
