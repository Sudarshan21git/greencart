<?php
// Start session if not already started
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

// Include database connection
include '../database/database.php';

// Get user information
$user_id = $_SESSION['user_id'];

// Get recent orders
$orders_query = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";
$orders_stmt = mysqli_prepare($conn, $orders_query);
mysqli_stmt_bind_param($orders_stmt, "i", $user_id);
mysqli_stmt_execute($orders_stmt);
$orders_result = mysqli_stmt_get_result($orders_stmt);
$recent_orders = [];
while ($order = mysqli_fetch_assoc($orders_result)) {
    $recent_orders[] = $order;
}
mysqli_stmt_close($orders_stmt);

// Get order count
$order_count_query = "SELECT COUNT(*) as total FROM orders WHERE user_id = ?";
$order_count_stmt = mysqli_prepare($conn, $order_count_query);
mysqli_stmt_bind_param($order_count_stmt, "i", $user_id);
mysqli_stmt_execute($order_count_stmt);
$order_count_result = mysqli_stmt_get_result($order_count_stmt);
$order_count = mysqli_fetch_assoc($order_count_result)['total'];
mysqli_stmt_close($order_count_stmt);

// Get recent reviews
$reviews_query = "SELECT r.*, p.name as product_name, p.image as product_image 
                 FROM reviews r 
                 JOIN products p ON r.product_id = p.product_id 
                 WHERE r.user_id = ? 
                 ORDER BY r.created_at DESC LIMIT 3";
$reviews_stmt = mysqli_prepare($conn, $reviews_query);
mysqli_stmt_bind_param($reviews_stmt, "i", $user_id);
mysqli_stmt_execute($reviews_stmt);
$reviews_result = mysqli_stmt_get_result($reviews_stmt);
$recent_reviews = [];
while ($review = mysqli_fetch_assoc($reviews_result)) {
    $recent_reviews[] = $review;
}
mysqli_stmt_close($reviews_stmt);

// Get review count
$review_count_query = "SELECT COUNT(*) as total FROM reviews WHERE user_id = ?";
$review_count_stmt = mysqli_prepare($conn, $review_count_query);
mysqli_stmt_bind_param($review_count_stmt, "i", $user_id);
mysqli_stmt_execute($review_count_stmt);
$review_count_result = mysqli_stmt_get_result($review_count_stmt);
$review_count = mysqli_fetch_assoc($review_count_result)['total'];
mysqli_stmt_close($review_count_stmt);

// Get cart items count
$cart_query = "SELECT COUNT(ci.cart_item_id) as item_count 
              FROM cart c 
              JOIN cart_items ci ON c.cart_id = ci.cart_id 
              WHERE c.user_id = ? AND c.status = 'active'";
$cart_stmt = mysqli_prepare($conn, $cart_query);
mysqli_stmt_bind_param($cart_stmt, "i", $user_id);
mysqli_stmt_execute($cart_stmt);
$cart_result = mysqli_stmt_get_result($cart_stmt);
$cart_data = mysqli_fetch_assoc($cart_result);
$cart_count = $cart_data ? $cart_data['item_count'] : 0;
mysqli_stmt_close($cart_stmt);

// Close database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - GreenCart</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/account-styles.css">
    <link rel="stylesheet" href="../css/dashboard-styles.css">
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Header -->
    <?php include_once "../includes/header.php"; ?>

    <!-- Dashboard Section -->
    <section class="account-section">
        <div class="container">
            <div class="account-header">
                <h1 class="section-title">My Dashboard</h1>
                <p>Welcome back, <?php echo htmlspecialchars($_SESSION['fname']); ?>!</p>
            </div>

            <div class="account-container">
                <!-- Account Sidebar -->
                <div class="account-sidebar">
                    <div class="user-info">
                        <div class="user-avatar">
                            <span><?php echo strtoupper(substr($_SESSION['fname'], 0, 1) . substr($_SESSION['lname'], 0, 1)); ?></span>
                        </div>
                        <div class="user-details">
                            <h3><?php echo htmlspecialchars($_SESSION['fname'] . ' ' . $_SESSION['lname']); ?></h3>
                            <p><?php echo htmlspecialchars($_SESSION['email']); ?></p>
                        </div>
                    </div>

                    <nav class="account-nav">
                        <ul>
                            <li><a href="dashboard.php" class="active">Dashboard</a></li>
                            <li><a href="account.php">Account Settings</a></li>
                            <li><a href="orders.php">My Orders</a></li>
                            <li><a href="reviews.php">My Reviews</a></li>
                            <li><a href="../auth/logout.php">Logout</a></li>
                        </ul>
                    </nav>
                </div>

                <!-- Dashboard Content -->
                <div class="account-content">
                    <div class="dashboard-stats">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                            </div>
                            <div class="stat-info">
                                <h3>Orders</h3>
                                <p class="stat-value"><?php echo $order_count; ?></p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            </div>
                            <div class="stat-info">
                                <h3>Reviews</h3>
                                <p class="stat-value"><?php echo $review_count; ?></p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                            </div>
                            <div class="stat-info">
                                <h3>Cart</h3>
                                <p class="stat-value"><?php echo $cart_count; ?> items</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="dashboard-sections">
                        <div class="dashboard-section">
                            <div class="section-header">
                                <h2>Recent Orders</h2>
                                <a href="orders.php" class="view-all">View All</a>
                            </div>
                            
                            <?php if (count($recent_orders) > 0): ?>
                            <div class="dashboard-orders">
                                <?php foreach ($recent_orders as $order): ?>
                                <div class="dashboard-order">
                                    <div class="order-header">
                                        <div class="order-id">Order #<?php echo $order['order_id']; ?></div>
                                        <div class="order-date"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></div>
                                    </div>
                                    <div class="order-details">
                                        <div class="order-status <?php echo strtolower($order['status']); ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </div>
                                        <div class="order-total">Rs.<?php echo number_format($order['total_amount'], 2); ?></div>
                                    </div>
                                    <div class="order-actions">
                                        <a href="order-details.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm">View Details</a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <div class="empty-state">
                                <p>You haven't placed any orders yet.</p>
                                <a href="../shop.php" class="btn btn-primary">Start Shopping</a>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="dashboard-section">
                            <div class="section-header">
                                <h2>Recent Reviews</h2>
                                <a href="reviews.php" class="view-all">View All</a>
                            </div>
                            
                            <?php if (count($recent_reviews) > 0): ?>
                            <div class="dashboard-reviews">
                                <?php foreach ($recent_reviews as $review): ?>
                                <div class="dashboard-review">
                                    <div class="review-product">
                                        <img src="../img/<?php echo $review['product_image']; ?>" alt="<?php echo $review['product_name']; ?>">
                                        <span><?php echo $review['product_name']; ?></span>
                                    </div>
                                    <div class="review-rating">
                                        <?php 
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo $i <= $review['rating'] ? '★' : '☆';
                                        }
                                        ?>
                                    </div>
                                    <div class="review-date"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <div class="empty-state">
                                <p>You haven't written any reviews yet.</p>
                                <a href="../shop.php" class="btn btn-primary">Shop Products</a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include_once "../includes/footer.php"; ?>
    <script src="../js/script.js"></script>
</body>
</html>
