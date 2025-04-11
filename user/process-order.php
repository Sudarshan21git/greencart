<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Include database connection
include '../database/database.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];

    // Get form data
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);

    // Get active cart
    $cart_query = "SELECT * FROM cart WHERE user_id = $userId";
    $cart_result = mysqli_query($conn, $cart_query);

    if ($cart_result && mysqli_num_rows($cart_result) > 0) {
        $cart = mysqli_fetch_assoc($cart_result);
        $cart_id = $cart['cart_id'];

        // Calculate order total
        $total_query = "SELECT SUM(ci.quantity * p.price) AS total 
                        FROM cart_items ci 
                        JOIN products p ON ci.product_id = p.product_id 
                        WHERE ci.cart_id = $cart_id";
        $total_result = mysqli_query($conn, $total_query);
        $total_row = mysqli_fetch_assoc($total_result);
        $order_total = $total_row['total'] ?? 0;

        // Generate order number
        $order_number = uniqid('ORD-');

        // Shipping address
        $shipping_address = $address;

        // Create order
        $order_query = "INSERT INTO orders (user_id, order_number, status, subtotal, total, payment_method, shipping_address, created_at) 
                        VALUES ($userId, '$order_number', 'pending', $order_total, $order_total, '$payment_method', '$shipping_address', NOW())";

        if (mysqli_query($conn, $order_query)) {
            $order_id = mysqli_insert_id($conn);

            // Move items from cart to order items
            $items_query = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                            SELECT $order_id, ci.product_id, ci.quantity, p.price 
                            FROM cart_items ci 
                            JOIN products p ON ci.product_id = p.product_id 
                            WHERE ci.cart_id = $cart_id";

            if (mysqli_query($conn, $items_query)) {
                // Clear cart items
                mysqli_query($conn, "DELETE FROM cart_items WHERE cart_id = $cart_id");

                // Create new cart
                mysqli_query($conn, "INSERT INTO cart (user_id, created_at) VALUES ($userId, NOW())");

                // Store order info in session for confirmation page
                $_SESSION['order_id'] = $order_id;
                $_SESSION['order_number'] = $order_number;
                $_SESSION['order_total'] = $order_total;
                $_SESSION['order_date'] = date('Y-m-d H:i:s');

                // Redirect to confirmation page
                header("Location: order-confirmation.php");
                exit();
            } else {
                $error = "Error creating order items: " . mysqli_error($conn);
            }
        } else {
            $error = "Error creating order: " . mysqli_error($conn);
        }
    } else {
        $error = "Cart not found.";
    }

    // Handle errors
    $_SESSION['order_error'] = $error;
    header("Location: cart.php");
    exit();
}

// Redirect to cart if not a POST request
header("Location: cart.php");
exit();
?>
