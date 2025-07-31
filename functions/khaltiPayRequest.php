<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
} else if ($_SESSION['is_admin'] == 1) {
    header("Location: 404.html");
}

// Include database connection
include '../database/database.php';
$userId = $_SESSION['user_id'];

if ($_SESSION['checkout_data'] == null) {
    header("Location: checkout.php");
    exit();
}
$checkoutData = $_SESSION['checkout_data'];

$first_name = $checkoutData['first_name'];
$last_name = $checkoutData['last_name'];
$email = $checkoutData['email'];
$phone = $checkoutData['phone'];
$address = $checkoutData['address'];
$payment_method = $checkoutData['payment_method'];
$order_number = $checkoutData['order_number'];
$order_total = $checkoutData['order_total'];

$error_message = "";

if (empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($address) || empty($payment_method)) {
    $error_message = "Please fill all the Fields";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $email)) {
    $error_message = "Invalid email format or not a Gmail address!";
} else if (!preg_match('/^(98|97)\d{8}$/', $phone)) {
    $error_message = "Enter a valid phone number!";
}

// If there are validation errors
if (!empty($error_message)) {
    $_SESSION['validation_error'] = $error_message;
    $_SESSION['show_checkout'] = true;

    // Redirect back to cart page with JavaScript alert
    echo "<script>
            function redirectWithAlert() {
                alert('" . addslashes($error_message) . "');
                window.location.href = '../user/cart.php';
            }
            redirectWithAlert();
        </script>";
    exit();
}

$postFields = array(
    "return_url" => "http://localhost/greencart/functions/khaltiPayResponse.php",
    "website_url" => "http://localhost/greencart/",
    "amount" => $order_total . "00",
    "purchase_order_id" => $order_number,
    "purchase_order_name" => $order_number,
    "customer_info" => array(
        "name" => $first_name . " " . $last_name,
        "email" => $email,
        "phone" => $phone
    )
);

$jsonData = json_encode($postFields);

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://a.khalti.com/api/v2/epayment/initiate/',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => $jsonData,
    CURLOPT_HTTPHEADER => array(
        'Authorization: key live_secret_key_68791341fdd94846a146f0457ff7b455',
        'Content-Type: application/json',
    ),
));

$response = curl_exec($curl);

if (curl_errno($curl)) {
    echo 'Error:' . curl_error($curl);
} else {
    $responseArray = json_decode($response, true);

    if (isset($responseArray['error'])) {
        echo 'Error: ' . $responseArray['error'];
    } elseif (isset($responseArray['payment_url'])) {
        // Redirect the user to the payment page
        header('Location: ' . $responseArray['payment_url']);
        exit();
    } else {
        echo 'Unexpected response: ' . $response;
    }
}

curl_close($curl);
