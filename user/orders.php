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

// Get all orders
$orders_query = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$orders_stmt = mysqli_prepare($conn, $orders_query);
mysqli_stmt_bind_param($orders_stmt, "i", $user_id);
mysqli_stmt_execute($orders_stmt);
$orders_result = mysqli_stmt_get_result($orders_stmt);
$orders = [];
while ($order = mysqli_fetch_assoc($orders_result)) {
    $order_id = $order['order_id'];
    
    // Get order items with product details
    $items_query = "SELECT oi.*, p.name, p.price, p.image 
                    FROM order_items oi 
                    JOIN products p ON oi.product_id = p.product_id 
                    WHERE oi.order_id = ?";
    $items_stmt = mysqli_prepare($conn, $items_query);
    mysqli_stmt_bind_param($items_stmt, "i", $order_id);
    mysqli_stmt_execute($items_stmt);
    $items_result = mysqli_stmt_get_result($items_stmt);
    
    $order_items = [];
    while ($item = mysqli_fetch_assoc($items_result)) {
        $order_items[] = $item;
    }
    
    $order['items'] = $order_items;
    $orders[] = $order;
    
    mysqli_stmt_close($items_stmt);
}
mysqli_stmt_close($orders_stmt);

// Handle order cancellation
$success_message = '';
$error_message = '';

if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $order_id = (int)$_GET['cancel'];
    
    // Check if order belongs to user and is in a cancellable state
    $check_order_query = "SELECT * FROM orders WHERE order_id = ? AND user_id = ? AND (status = 'pending' OR status = 'processing')";
    $check_order_stmt = mysqli_prepare($conn, $check_order_query);
    mysqli_stmt_bind_param($check_order_stmt, "ii", $order_id, $user_id);
    mysqli_stmt_execute($check_order_stmt);
    $order_result = mysqli_stmt_get_result($check_order_stmt);
    $order_detail = mysqli_fetch_assoc($order_result);
    
    if ($order_detail) {
        // Update order status to cancelled
        $cancel_query = "UPDATE orders SET status = 'cancelled' WHERE order_id = ?";
        $cancel_stmt = mysqli_prepare($conn, $cancel_query);
        mysqli_stmt_bind_param($cancel_stmt, "i", $order_id);
        
        if (mysqli_stmt_execute($cancel_stmt)) {
            $success_message = "Order #".$order_detail['order_number']." has been cancelled successfully.";
            
            // Refresh orders list with items
            $orders = [];
            $orders_query = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
            $orders_stmt = mysqli_prepare($conn, $orders_query);
            mysqli_stmt_bind_param($orders_stmt, "i", $user_id);
            mysqli_stmt_execute($orders_stmt);
            $orders_result = mysqli_stmt_get_result($orders_stmt);
            
            while ($order = mysqli_fetch_assoc($orders_result)) {
                $order_id = $order['order_id'];
                
                // Get order items with product details
                $items_query = "SELECT oi.*, p.name, p.price, p.image 
                                FROM order_items oi 
                                JOIN products p ON oi.product_id = p.product_id 
                                WHERE oi.order_id = ?";
                $items_stmt = mysqli_prepare($conn, $items_query);
                mysqli_stmt_bind_param($items_stmt, "i", $order_id);
                mysqli_stmt_execute($items_stmt);
                $items_result = mysqli_stmt_get_result($items_stmt);
                
                $order_items = [];
                while ($item = mysqli_fetch_assoc($items_result)) {
                    $order_items[] = $item;
                }
                
                $order['items'] = $order_items;
                $orders[] = $order;
                
                mysqli_stmt_close($items_stmt);
            }
            mysqli_stmt_close($orders_stmt);
        } else {
            $error_message = "Failed to cancel order. Please try again.";
        }
        
        mysqli_stmt_close($cancel_stmt);
    } else {
        $error_message = "Order cannot be cancelled. It may be already shipped or delivered.";
    }
    
    mysqli_stmt_close($check_order_stmt);
}

// Close database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - GreenCart</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/account-styles.css">
    <link rel="stylesheet" href="../css/orders-styles.css">
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Header -->
    <?php include_once "../includes/header.php"; ?>

    <!-- Orders Section -->
    <section class="account-section">
        <div class="container">
            <div class="account-header">
                <h1 class="section-title">My Orders</h1>
                <p>Track and manage your orders</p>
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
                            <li><a href="dashboard.php">Dashboard</a></li>
                            <li><a href="account.php">Account Settings</a></li>
                            <li><a href="orders.php" class="active">My Orders</a></li>
                            <li><a href="reviews.php">My Reviews</a></li>
                            <li><a href="../auth/logout.php">Logout</a></li>
                        </ul>
                    </nav>
                </div>

                <!-- Orders Content -->
                <div class="account-content">
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success">
                            <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-error">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (count($orders) > 0): ?>
                        <div class="orders-list">
                            <?php foreach ($orders as $order): ?>
                                <div class="order-card">
                                    <div class="order-header">
                                        <div class="order-id">
                                            <h3>Order #<?php echo $order['order_number']; ?></h3>
                                            <span class="order-date"><?php echo date('F d, Y', strtotime($order['created_at'])); ?></span>
                                        </div>
                                        <div class="order-status <?php echo strtolower($order['status']); ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </div>
                                    </div>
                                    
                                    <div class="order-summary">
                                        <div class="order-info">
                                            <div class="info-item">
                                                <span class="info-label">Total Amount:</span>
                                                <span class="info-value">Rs.<?php echo number_format($order['total'], 2); ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Payment Method:</span>
                                                <span class="info-value"><?php echo ucfirst($order['payment_method']); ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Shipping Address:</span>
                                                <span class="info-value"><?php echo $order['shipping_address']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Order Items -->
                                    <div class="order-items">
                                        <h4>Order Items</h4>
                                        <?php foreach ($order['items'] as $item): ?>
                                            <div class="order-item">
                                                <div class="order-item-image">
                                                    <img src="../img/<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                                </div>
                                                <div class="order-item-details">
                                                    <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                                    <div class="order-item-meta">
                                                        <span>Quantity: <?php echo $item['quantity']; ?></span>
                                                        <span>Price: Rs.<?php echo number_format($item['price'], 2); ?></span>
                                                        <span>Total: Rs.<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <div class="order-actions">
                                        <?php if ($order['status'] === 'pending' || $order['status'] === 'processing'): ?>
                                            <a href="orders.php?cancel=<?php echo $order['order_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this order?')">Cancel Order</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                            </div>
                            <h3>No Orders Yet</h3>
                            <p>You haven't placed any orders yet. Start shopping to place your first order!</p>
                            <a href="shop.php" class="btn btn-primary">Start Shopping</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include_once "../includes/footer.php"; ?>
    <script src="../js/script.js"></script>
    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            if (alerts.length > 0) {
                setTimeout(() => {
                    alerts.forEach(alert => {
                        alert.style.opacity = '0';
                        setTimeout(() => {
                            alert.style.display = 'none';
                        }, 500);
                    });
                }, 5000);
            }
        });
    </script>
</body>
</html>
