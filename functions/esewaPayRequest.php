<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Check login
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] == 1) {
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_SESSION['checkout_data'])) {
    header("Location: checkout.php");
    exit();
}

$checkoutData = $_SESSION['checkout_data'];
include '../database/database.php';

$first_name = $checkoutData['first_name'];
$last_name = $checkoutData['last_name'];
$email = $checkoutData['email'];
$phone = $checkoutData['phone'];
$address = $checkoutData['address'];
$payment_method = $checkoutData['payment_method'];
$order_total = $checkoutData['order_total'];
$cart_id = $checkoutData['cart_id'];
$order_number = uniqid("ORD"); // Unique ID

// ✅ Validation
if (
    empty($first_name) || empty($last_name) || empty($email) ||
    empty($phone) || empty($address) || empty($payment_method)
) {
    echo "<script>alert('Please fill all fields.'); window.location.href = '../user/cart.php';</script>";
    exit();
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $email)) {
    echo "<script>alert('Invalid Gmail address.'); window.location.href = '../user/cart.php';</script>";
    exit();
}
if (!preg_match('/^(98|97)\d{8}$/', $phone)) {
    echo "<script>alert('Invalid Nepali phone number.'); window.location.href = '../user/cart.php';</script>";
    exit();
}

// Save new checkout session with new order ID
$_SESSION['checkout_data']['order_number'] = $order_number;

// eSewa config
$esewa_url = "https://rc.esewa.com.np/epay/main"; // test URL
$scd = "EPAYTEST"; // test merchant code

$tAmt = $order_total;
$amt = $order_total;
$txAmt = 0;
$psc = 0;
$pdc = 0;

// Redirect URLs
$success_url = "http://localhost/greencart/functions/esewaPaymentSuccess.php";
$failure_url = "http://localhost/greencart/functions/esewaPaymentFailure.php";
?>

<form id="esewaForm" action="<?= $esewa_url ?>" method="POST">
    <input name="tAmt" type="hidden" value="<?= $tAmt ?>">
    <input name="amt" type="hidden" value="<?= $amt ?>">
    <input name="txAmt" type="hidden" value="<?= $txAmt ?>">
    <input name="psc" type="hidden" value="<?= $psc ?>">
    <input name="pdc" type="hidden" value="<?= $pdc ?>">
    <input name="scd" type="hidden" value="<?= $scd ?>">
    <input name="pid" type="hidden" value="<?= $order_number ?>">
    <input name="su" type="hidden" value="<?= $success_url ?>">
    <input name="fu" type="hidden" value="<?= $failure_url ?>">
</form>

<script>
    document.getElementById("esewaForm").submit();
</script>
