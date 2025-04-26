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

include '../database/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Check if order information exists in session
if (!isset($_SESSION['order_id'])) {
    // If no order ID in session, check if it's in the URL (for bookmarking/sharing)
    if (isset($_GET['order_id']) && is_numeric($_GET['order_id'])) {
        $order_id = (int)$_GET['order_id'];
        
        // Verify this order belongs to the current user
        $userId = $_SESSION['user_id'];
        $order_query = "SELECT * FROM orders WHERE order_id = $order_id AND user_id = $userId";
        $order_result = mysqli_query($conn, $order_query);
        
        if ($order_result && mysqli_num_rows($order_result) > 0) {
            $order = mysqli_fetch_assoc($order_result);
            
            // Set session variables from database
            $_SESSION['order_id'] = $order_id;
            $_SESSION['order_email'] = $order['email'];
            $_SESSION['order_total'] = $order['total_amount'];
            $_SESSION['order_date'] = $order['created_at'];
        } else {
            // Order not found or doesn't belong to user
            $_SESSION['error'] = "Order not found";
            header("Location: account.php");
            exit();
        }
    } else {
        // No order information available
        $_SESSION['error'] = "No order information available";
        header("Location: cart.php");
        exit();
    }
}

// Get order information from session
$order_id = $_SESSION['order_id'];
$order_total = $_SESSION['order_total'];
$order_date = isset($_SESSION['order_date']) ? $_SESSION['order_date'] : date('Y-m-d H:i:s');

// Get order items from database
$order_items = [];
$items_query = "SELECT oi.*, p.name, p.image 
               FROM order_items oi 
               JOIN products p ON oi.product_id = p.product_id 
               WHERE oi.order_id = $order_id";
$items_result = mysqli_query($conn, $items_query);

if ($items_result) {
    while ($item = mysqli_fetch_assoc($items_result)) {
        $order_items[] = $item;
    }
}

// Get shipping information if available
$shipping_info = null;
$shipping_query = "SELECT * FROM orders WHERE order_id = $order_id";
$shipping_result = mysqli_query($conn, $shipping_query);

if ($shipping_result && mysqli_num_rows($shipping_result) > 0) {
    $shipping_info = mysqli_fetch_assoc($shipping_result);
}

// Clear order session variables after getting them (optional)
$clear_session = false; // Set to true to clear session after displaying
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <?php include_once '../includes/header.php'; ?>

    <!-- Order Confirmation -->
    <section class="confirmation-section">
        <div class="container">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="error-message">
                    <?php 
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                    ?>
                </div>
            <?php else: ?>
                <div class="confirmation-container">
                    <div class="confirmation-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    </div>
                    <h2>Thank You for Your Order!</h2>
                    <p>Your order has been placed successfully.</p>
                    
                    <div class="order-details">
                        <div class="order-detail">
                            <span>Order Number:</span>
                            <span id="order-number">#<?php echo $order_id; ?></span>
                        </div>
                        <div class="order-detail">
                            <span>Order Date:</span>
                            <span id="order-date"><?php echo date('M d, Y', strtotime($order_date)); ?></span>
                        </div>
                        <div class="order-detail">
                            <span>Order Total:</span>
                            <span id="order-total">Rs.<?php echo number_format($order_total); ?></span>
                        </div>
                        <div class="order-detail">
                            <span>Payment Method:</span>
                            <span id="payment-method">
                                <?php 
                                    // Get payment method from database
                                    $payment_query = "SELECT payment_method FROM orders WHERE order_id = $order_id";
                                    $payment_result = mysqli_query($conn, $payment_query);
                                    if ($payment_result && mysqli_num_rows($payment_result) > 0) {
                                        $payment = mysqli_fetch_assoc($payment_result);
                                        echo ucfirst($payment['payment_method']);
                                    } else {
                                        echo "Not specified";
                                    }
                                ?>
                            </span>
                        </div>
                    </div>
                    
                    <?php if (!empty($order_items)): ?>
                    <div class="order-items-summary">
                        <h3>Order Items</h3>
                        <div class="order-items-list">
                            <?php foreach ($order_items as $item): ?>
                            <div class="order-item">
                                <div class="order-item-image">
                                    <img src="../img/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                </div>
                                <div class="order-item-details">
                                    <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <div class="order-item-meta">
                                        <span>Quantity: <?php echo $item['quantity']; ?></span>
                                        <span>Price: Rs.<?php echo number_format($item['price']); ?></span>
                                    </div>
                                </div>
                                <div class="order-item-total">
                                    Rs.<?php echo number_format($item['price'] * $item['quantity']); ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="order-next-steps">
                        <h3>What's Next?</h3>
                        <ol>
                            <li>Your order is being processed.</li>
                            <li>Once processed, we'll prepare your items for shipping.</li>
                            <li>You'll receive an email when your order ships with tracking information.</li>
                            <li>Your plants will be delivered to your doorstep!</li>
                        </ol>
                    </div>
                    
                    <div class="order-actions">
                        <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
                        <a href="orders.php" class="btn btn-secondary">View My Account</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <?php include_once '../includes/footer.php'; ?>

    <?php if ($clear_session): ?>
    <script>
        // Clear order session variables after displaying
        <?php
            unset($_SESSION['order_id']);
            unset($_SESSION['order_email']);
            unset($_SESSION['order_total']);
            unset($_SESSION['order_date']);
        ?>
    </script>
    <?php endif; ?>
    
    <script>
        // Print order functionality
        function printOrder() {
            window.print();
        }
        
        // Add event listener to print button if it exists
        document.addEventListener('DOMContentLoaded', function() {
            const printBtn = document.getElementById('print-order');
            if (printBtn) {
                printBtn.addEventListener('click', printOrder);
            }
        });
    </script>
</body>
</html>
