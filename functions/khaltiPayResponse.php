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
$order_total = $checkoutData['order_total'];
$order_number = $checkoutData['order_number'];
$cart_id = $checkoutData['cart_id'];
// Get the pidx from the URL
$pidx = $_GET['pidx'] ?? null;

if ($pidx) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://a.khalti.com/api/v2/epayment/lookup/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode(['pidx' => $pidx]),
        CURLOPT_HTTPHEADER => array(
            'Authorization: key live_secret_key_68791341fdd94846a146f0457ff7b455',
            'Content-Type: application/json',
        ),
    ));


    $response = curl_exec($curl);
    curl_close($curl);

    if ($response) {
        $responseArray = json_decode($response, true);
        switch ($responseArray['status']) {
            case 'Completed':
                // Create order
                $order_query = "INSERT INTO orders (user_id, order_number, status, subtotal, total, payment_method, shipping_address, created_at) 
                        VALUES ($userId, '$order_number', 'pending', $order_total, $order_total, '$payment_method', '$address', NOW())";

                if (mysqli_query($conn, $order_query)) {
                    $order_id = mysqli_insert_id($conn);

                    // Move items from cart to order items
                    $items_query = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                            SELECT $order_id, ci.product_id, ci.quantity, p.price 
                            FROM cart_items ci 
                            JOIN products p ON ci.product_id = p.product_id 
                            WHERE ci.cart_id = $cart_id";
                    if (mysqli_query($conn, $items_query)) {
                        // Update stock
                        $update_stock_query = "UPDATE products p
                        JOIN cart_items ci ON p.product_id = ci.product_id
                        SET p.stock_quantity = p.stock_quantity - ci.quantity
                        WHERE ci.cart_id = $cart_id";

                        mysqli_query($conn, $update_stock_query);

                        // Clear cart and reset session
                        mysqli_query($conn, "DELETE FROM cart_items WHERE cart_id = $cart_id");
                        mysqli_query($conn, "INSERT INTO cart (user_id, created_at) VALUES ($userId, NOW())");

                        $_SESSION['order_id'] = $order_id;
                        $_SESSION['order_number'] = $order_number;
                        $_SESSION['order_total'] = $order_total;
                        $_SESSION['order_date'] = date('Y-m-d H:i:s');

                        unset($_SESSION['checkout_data']);
                        unset($_SESSION['show_checkout']);

                        header("Location: order-confirmation.php");
                        exit();
                    } else {
                        $error = "Error creating order items: " . mysqli_error($conn);
                    }
                } else {
                    $error = "Error creating order: " . mysqli_error($conn);
                }

                // ❗ Only reached on failure
                $_SESSION['order_error'] = $error;
                $_SESSION['show_checkout'] = true;

                echo "<script>
        alert('" . addslashes($error) . "');
        window.location.href = '../user/cart.php';
    </script>";
                exit();

            case 'Expired':
            case 'User canceled':
                //here you can write your logic to update the database
                $_SESSION['transaction_msg'] = '<script>
                        Swal.fire({
                            icon: "error",
                            title: "Transaction failed.",
                            showConfirmButton: false,
                            timer: 1500
                        });
                    </script>';
                header("Location: ../user/cart.php");
                exit();
                break;
            default:
                //here you can write your logic to update the database
                $_SESSION['transaction_msg'] = '<script>
                        Swal.fire({
                            icon: "error",
                            title: "Transaction failed.",
                            showConfirmButton: false,
                            timer: 1500
                        });
                    </script>';
                header("Location: ../user/cart.php");
                exit();
                break;
        }
    }
}
