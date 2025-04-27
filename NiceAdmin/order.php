<?php
ob_start();
session_start();
include('../database/database.php');

// Handle Order Status Update
if (isset($_GET['approve']) && isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    $approve_query = "UPDATE orders SET status='approved' WHERE order_id=$order_id";
    if (mysqli_query($conn, $approve_query)) {
        $_SESSION['success'] = "Order approved.";
    } else {
        $_SESSION['error'] = "Failed to approve order.";
    }
    header("Location: order.php");
    exit();

} elseif (isset($_GET['decline']) && isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Restock products when declined
    $items_query = "SELECT product_id, quantity FROM order_items WHERE order_id = $order_id";
    $items_result = mysqli_query($conn, $items_query);

    if ($items_result) {
        while ($item = mysqli_fetch_assoc($items_result)) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            mysqli_query($conn, "UPDATE products SET stock_quantity = stock_quantity + $quantity WHERE product_id = $product_id");
        }
    }

    // Update the order status to declined
    $decline_query = "UPDATE orders SET status='declined' WHERE order_id=$order_id";
    mysqli_query($conn, $decline_query);

    $_SESSION['success'] = "Order declined and products restocked.";
    header("Location: order.php");
    exit();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Orders - GreenCart Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans|Nunito|Poppins" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .approve-btn {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-right: 5px;
        }
        .approve-btn:hover {
            background-color: #218838;
            color: white;
        }
        .decline-btn {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .decline-btn:hover {
            background-color: #c82333;
            color: white;
        }
        .badge {
            padding: 6px 10px;
            font-size: 14px;
            font-weight: normal;
        }
        .badge-approved {
            background-color: #28a745;
        }
        .badge-declined {
            background-color: #dc3545;
        }
        .badge-canceled {
            background-color: #6c757d;
        }
        .badge-pending {
            background-color: #ffc107;
            color: #212529;
        }
    </style>
</head>
<body>

<header class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
        <a href="index.php" class="logo d-flex align-items-center">
            <img src="../assets/img/logo.png" alt="">
            <span class="d-none d-lg-block">GreenCartAdmin</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>
</header>

<!-- Your exact sidebar implementation -->
<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link collapsed" href="index.php">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="category.php">
                <i class="bi bi-tags"></i>
                <span>Category</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="product.php">
                <i class="bi bi-box-seam"></i>
                <span>Product</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="contact.php">
                <i class="bi bi-phone"></i>
                <span>Contact</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="user.php">
                <i class="bi bi-person"></i>
                <span>User</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="order.php">
                <i class="bi bi-box"></i>
                <span>Order</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="../auth/logout_admin.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</aside>

<main id="main" class="main">
    <div class="pagetitle"><h1 class="text-success">Order Details</h1></div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <section class="section dashboard">
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-hover mt-4">
                    <thead class="table-success">
                        <tr>
                            <th>#Order ID</th>
                            <th>User Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Shipping Address</th>
                            <th>Products</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Placed At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
<?php
$order_query = mysqli_query($conn, "SELECT * FROM orders ORDER BY created_at DESC");
while ($order = mysqli_fetch_assoc($order_query)) {
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE user_id = {$order['user_id']}"));
    
    $items_result = mysqli_query($conn, "SELECT oi.quantity, p.name FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = {$order['order_id']}");
    $products = [];
    while ($item = mysqli_fetch_assoc($items_result)) {
        $products[] = $item['name'] . ' (' . $item['quantity'] . ')';
    }

    echo "<tr>
        <td>#{$order['order_id']}</td>
        <td>" . htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) . "</td>
        <td>" . htmlspecialchars($user['email']) . "</td>
        <td>" . htmlspecialchars($user['phone']) . "</td>
        <td>" . htmlspecialchars($order['shipping_address']) . "</td>
        <td>" . implode('<br>', $products) . "</td>
        <td>Rs. " . number_format($order['total']) . "</td>
        <td>" . htmlspecialchars($order['payment_method']) . "</td>
        <td><span class='badge badge-" . strtolower($order['status']) . "'>" . ucfirst($order['status']) . "</span></td>
        <td>" . htmlspecialchars($order['created_at']) . "</td>
        <td>";

    // Action column
    if ($order['status'] == 'pending') {
        echo "
            <form method='get' action='order.php'>
                <input type='hidden' name='order_id' value='{$order['order_id']}'>
                <button type='submit' name='approve' class='approve-btn'>Approve</button>
                <button type='submit' name='decline' class='decline-btn'>Decline</button>
            </form>";
    } elseif ($order['status'] == 'approved') {
        echo "<span class='badge badge-approved'>Approved</span>";
    } elseif ($order['status'] == 'declined') {
        echo "<span class='badge badge-declined'>Declined</span>";
    } else {
        echo "<span class='badge badge-canceled'>Canceled</span>";
    }
    echo "</td></tr>";
}
?>
</tbody>

                </table>
            </div>
        </div>
    </section>
</main>

<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>