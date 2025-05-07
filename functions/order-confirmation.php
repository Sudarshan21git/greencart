<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
else if ($_SESSION['is_admin'] == 1) {
    header("Location: 404.html");
}

// Check if order information is available in session
if (!isset($_SESSION['order_id']) || !isset($_SESSION['order_number']) || !isset($_SESSION['order_total'])) {
    header("Location: ../user/cart.php");
    exit();
}

// Get order information from session
$order_id = $_SESSION['order_id'];
$order_number = $_SESSION['order_number'];
$order_total = $_SESSION['order_total'];
$order_date = $_SESSION['order_date'];

// Format the date
$formatted_date = date('F j, Y, g:i a', strtotime($order_date));

// Get user email
$user_email = $_SESSION['email'];

// Clear order information from session after displaying
unset($_SESSION['order_id']);
unset($_SESSION['order_number']);
unset($_SESSION['order_total']);
unset($_SESSION['order_date']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - GreenCart</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/cart-styles.css">
    <link rel="icon" type="image/png" href="../img/logo.png">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .confirmation-section {
            padding: 60px 0;
            background-color: #f9f9f9;
        }
        
        .confirmation-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .confirmation-icon {
            color: #4CAF50;
            margin-bottom: 20px;
        }
        
        .confirmation-container h2 {
            color: #333;
            margin-bottom: 20px;
        }
        
        .order-details {
            margin: 30px 0;
            text-align: left;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
        }
        
        .order-detail {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .order-detail:last-child {
            border-bottom: none;
        }
        
        .confirmation-container .btn {
            margin-top: 20px;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include_once '../includes/header.php'; ?>
    
    <!-- Confirmation Section -->
    <section class="confirmation-section">
        <div class="container">
            <div class="confirmation-container">
                <div class="confirmation-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
                <h2>Thank You for Your Order!</h2>
                <p>Your order has been placed successfully.</p>
                <div class="order-details">
                    <div class="order-detail">
                        <span>Order Number:</span>
                        <span><?php echo htmlspecialchars($order_number); ?></span>
                    </div>
                    <div class="order-detail">
                        <span>Order Date:</span>
                        <span><?php echo htmlspecialchars($formatted_date); ?></span>
                    </div>
                    <div class="order-detail">
                        <span>Order Total:</span>
                        <span>Rs.<?php echo number_format($order_total); ?></span>
                    </div>
                </div>
                <div class="btn-group">
                    <a href="../user/shop.php" class="btn btn-primary">Continue Shopping</a>
                    <a href="../user/orders.php" class="btn btn-secondary">View Your Orders</a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <?php include_once '../includes/footer.php'; ?>
    
    <script src="../js/script.js"></script>
</body>
</html>
