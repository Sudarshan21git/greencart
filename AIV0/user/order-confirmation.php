<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('auth/login.php');
}

$userId = $_SESSION['user_id'];

// Check if order ID is provided
if (!isset($_GET['order_id'])) {
    redirect('index.php');
}

$orderId = (int)$_GET['order_id'];

// Get order details
$order = getOrderById($pdo, $orderId);

// Check if order exists and belongs to the user
if (!$order || $order['user_id'] != $userId) {
    redirect('index.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - GreenCart</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include_once 'includes/header.php'; ?>

    <section class="confirmation-section">
        <div class="container">
            <div class="confirmation-container">
                <div class="confirmation-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
                <h2>Thank You for Your Order!</h2>
                <p>Your order has been placed successfully. We've sent a confirmation email to <span id="confirmation-email"><?php echo $order['email']; ?></span>.</p>
                <div class="order-details">
                    <div class="order-detail">
                        <span>Order Number:</span>
                        <span id="order-number"><?php echo $order['order_id']; ?></span>
                    </div>
                    <div class="order-detail">
                        <span>Order Date:</span>
                        <span id="order-date"><?php echo date('F j, Y', strtotime($order['created_at'])); ?></span>
                    </div>
                    <div class="order-detail">
                        <span>Order Total:</span>
                        <span id="order-total">$<?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>
                    <div class="order-detail">
                        <span>Payment Method:</span>
                        <span id="payment-method"><?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?></span>
                    </div>
                </div>
                <p>We'll send you another email when your order ships.</p>
                <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        </div>
    </section>

    <?php include_once 'includes/footer.php'; ?>
    <script src="script.js"></script>
</body>
</html>