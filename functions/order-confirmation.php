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

// Database connection
include("../database/database.php");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch order items with product details
$order_items_query = "SELECT oi.quantity, oi.price, p.name, p.image 
                      FROM order_items oi 
                      JOIN products p ON oi.product_id = p.product_id 
                      WHERE oi.order_id = $order_id";
$order_items_result = mysqli_query($conn, $order_items_query);

if (!$order_items_result) {
    $order_items_error = "Error fetching order items: " . mysqli_error($conn);
}

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
    <link rel="stylesheet" href="../css/order-confirmation.css">
    
    <link rel="icon" type="image/png" href="../img/logo.png">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Header -->
    <?php include_once '../includes/header.php'; ?>

    <!-- Confirmation Section -->
    <section class="confirmation-section">
        <div class="container">
            <div class="confirmation-container">
                <a href="generate-bill.php?order_id=<?php echo $order_id; ?>" class="download-btn" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7,10 12,15 17,10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Download Bill
                </a>
                <div class="confirmation-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
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

                <!-- Product Details Section -->
                <div class="product-details">
                    <h3>Order Items</h3>
                    <?php if (isset($order_items_error)): ?>
                        <p style="color: red;"><?php echo $order_items_error; ?></p>
                    <?php elseif (mysqli_num_rows($order_items_result) > 0): ?>
                        <?php while ($item = mysqli_fetch_assoc($order_items_result)): ?>
                            <div class="product-item">
                                <img src="../img/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="product-image">
                                <div class="product-info">
                                    <div class="product-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                    <div class="product-price">Price: Rs.<?php echo number_format($item['price']); ?></div>
                                    <div class="product-quantity">Quantity: <?php echo $item['quantity']; ?></div>
                                </div>
                                <div class="product-total">
                                    Rs.<?php echo number_format($item['price'] * $item['quantity']); ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No items found for this order.</p>
                    <?php endif; ?>
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