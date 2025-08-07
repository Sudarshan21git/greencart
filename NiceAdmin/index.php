<?php
ob_start();
session_start();
include('../database/database.php');

// Check for low stock products
$low_stock_query = mysqli_query($conn, "SELECT name FROM products WHERE stock_quantity < 5");
$low_stock_products = mysqli_fetch_all($low_stock_query, MYSQLI_ASSOC);
$low_stock_count = mysqli_num_rows($low_stock_query);

// Fetch dashboard statistics
// Total customers (users who are not admin)
$total_customers_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE is_admin = 0");
$total_customers = mysqli_fetch_assoc($total_customers_query)['total'];

// Total orders
$total_orders_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders");
$total_orders = mysqli_fetch_assoc($total_orders_query)['total'];

// Total revenue (sum of all order totals)
$total_revenue_query = mysqli_query($conn, "SELECT SUM(total) as total FROM orders WHERE status != 'cancelled'");
$total_revenue_result = mysqli_fetch_assoc($total_revenue_query);
$total_revenue = $total_revenue_result['total'] ? $total_revenue_result['total'] : 0;

// Today's orders
$today_orders_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE DATE(created_at) = CURDATE()");
$today_orders = mysqli_fetch_assoc($today_orders_query)['total'];

// Today's revenue
$today_revenue_query = mysqli_query($conn, "SELECT SUM(total) as total FROM orders WHERE DATE(created_at) = CURDATE() AND status != 'cancelled'");
$today_revenue_result = mysqli_fetch_assoc($today_revenue_query);
$today_revenue = $today_revenue_result['total'] ? $today_revenue_result['total'] : 0;

// Recent orders (last 5 orders)
$recent_orders_query = mysqli_query($conn, "
    SELECT o.order_id, o.total, o.status, o.created_at, 
           CONCAT(u.first_name, ' ', u.last_name) as customer_name
    FROM orders o 
    JOIN users u ON o.user_id = u.user_id 
    ORDER BY o.created_at DESC 
    LIMIT 5
");
$recent_orders = mysqli_fetch_all($recent_orders_query, MYSQLI_ASSOC);

// Calculate percentage changes (simplified - you can make this more sophisticated)
$yesterday_orders_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)");
$yesterday_orders = mysqli_fetch_assoc($yesterday_orders_query)['total'];

$yesterday_revenue_query = mysqli_query($conn, "SELECT SUM(total) as total FROM orders WHERE DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND status != 'cancelled'");
$yesterday_revenue_result = mysqli_fetch_assoc($yesterday_revenue_query);
$yesterday_revenue = $yesterday_revenue_result['total'] ? $yesterday_revenue_result['total'] : 0;

// Calculate percentage changes
$orders_percentage = $yesterday_orders > 0 ? (($today_orders - $yesterday_orders) / $yesterday_orders) * 100 : 0;
$revenue_percentage = $yesterday_revenue > 0 ? (($today_revenue - $yesterday_revenue) / $yesterday_revenue) * 100 : 0;
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Dashboard - GreenCart</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="../img/logo.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

 <style>
      .toast-container {
            position: fixed;
            top:70px;
            right: 100px;
            z-index: 1100;
        }
 </style>

</head>

<body>

<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center">

  <div class="d-flex align-items-center justify-content-between">
    <a href="index.php" class="logo d-flex align-items-center">
    <img src="../img/logo.png" alt="">
      <span class="d-none d-lg-block">GreenCart Admin</span>
    </a>
    <i class="bi bi-list toggle-sidebar-btn"></i>
  </div><!-- End Logo -->

  <nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">

      <li class="nav-item dropdown">

      <li class="nav-item dropdown pe-3">

        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
          <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle">
          <span class="d-none d-md-block  ps-2">Admin</span>
        </a><!-- End Profile Iamge Icon -->
  </nav><!-- End Icons Navigation -->

</header><!-- End Header -->

<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-grid"></i><span>Dashboard</span></a></li>
        <li class="nav-item"><a class="nav-link collapsed" href="category.php"><i class="bi-tags"></i><span>Category</span></a></li>
        <li class="nav-item">
            <a class="nav-link collapsed " href="product.php">
                <i class="bi-box-seam"></i>
                <span>Product</span>
                <?php if ($low_stock_count > 0): ?>
                    <span class="badge bg-danger rounded-pill ms-auto"><?= $low_stock_count ?></span>
                <?php endif; ?>
            </a>
        </li> 
        <li class="nav-item"><a class="nav-link collapsed" href="contact.php"><i class="bi bi-phone"></i><span>Contact</span></a></li>
        <li class="nav-item"><a class="nav-link collapsed" href="user.php"><i class="bi bi-person"></i><span>User</span></a></li>
        <li class="nav-item"><a class="nav-link collapsed" href="order.php"><i class="bi bi-box "></i><span>Order</span></a></li>
        <li class="nav-item"><a class="nav-link collapsed" href="Review.php"><i class="bi bi-chat-dots"></i><span>Review</span></a></li>
        <li class="nav-item"><a class="nav-link collapsed" href="http:\\localhost\greencart\auth\logout_admin.php" onclick="conofirm_logout()"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a></li>
    </ul>
</aside>

<main id="main" class="main">

  <section class="section dashboard">
    <div class="row">

      <!-- Left side columns -->
      <div class="col-lg-8">
        <div class="row">

          <!-- Sales Card -->
          <div class="col-xxl-4 col-md-6">
            <div class="card info-card sales-card">

              <div class="card-body">
                <h5 class="card-title">Orders <span>| Today</span></h5>

                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-cart"></i>
                  </div>
                  <div class="ps-3">
                    <h6><?= $today_orders ?></h6>
                    <?php if ($orders_percentage >= 0): ?>
                      <span class="text-success small pt-1 fw-bold"><?= number_format($orders_percentage, 1) ?>%</span>
                    <?php else: ?>
                      <span class="text-danger small pt-1 fw-bold"><?= number_format(abs($orders_percentage), 1) ?>%</span>
                    <?php endif; ?>
                    <span class="text-muted small pt-2 ps-1">
                      <?= $orders_percentage >= 0 ? 'increase' : 'decrease' ?>
                    </span>
                  </div>
                </div>
              </div>

            </div>
          </div><!-- End Sales Card -->

          <!-- Revenue Card -->
          <div class="col-xxl-4 col-md-6">
            <div class="card info-card revenue-card">

              <div class="card-body">
                <h5 class="card-title">Revenue <span>| Today</span></h5>

                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-currency-dollar"></i>
                  </div>
                  <div class="ps-3">
                    <h6>Rs. <?= number_format($today_revenue, 2) ?></h6>
                    <?php if ($revenue_percentage >= 0): ?>
                      <span class="text-success small pt-1 fw-bold"><?= number_format($revenue_percentage, 1) ?>%</span>
                    <?php else: ?>
                      <span class="text-danger small pt-1 fw-bold"><?= number_format(abs($revenue_percentage), 1) ?>%</span>
                    <?php endif; ?>
                    <span class="text-muted small pt-2 ps-1">
                      <?= $revenue_percentage >= 0 ? 'increase' : 'decrease' ?>
                    </span>
                  </div>
                </div>
              </div>

            </div>
          </div><!-- End Revenue Card -->

          <!-- Customers Card -->
          <div class="col-xxl-4 col-xl-12">

            <div class="card info-card customers-card">

              <div class="card-body">
                <h5 class="card-title">Total Customers</h5>

                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-people"></i>
                  </div>
                  <div class="ps-3">
                    <h6><?= $total_customers ?></h6>
                    <span class="text-muted small pt-2">Registered customers</span>
                  </div>
                </div>

              </div>
            </div>

          </div><!-- End Customers Card -->

          <!-- Recent Sales -->
          <div class="col-12">
            <div class="card recent-sales overflow-auto">

              <div class="card-body">
                <h5 class="card-title">Recent Orders <span>| Latest 5</span></h5>

                <table class="table table-borderless datatable">
                  <thead>
                    <tr>
                      <th scope="col">Order ID</th>
                      <th scope="col">Customer</th>
                      <th scope="col">Amount</th>
                      <th scope="col">Status</th>
                      <th scope="col">Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($recent_orders)): ?>
                      <?php foreach ($recent_orders as $order): ?>
                        <tr>
                          <td>#<?= $order['order_id'] ?></td>
                          <td><?= htmlspecialchars($order['customer_name']) ?></td>
                          <td>Rs. <?= number_format($order['total'], 2) ?></td>
                          <td>
                            <?php 
                            $status_class = '';
                            switch($order['status']) {
                              case 'pending': $status_class = 'badge bg-warning'; break;
                              case 'processing': $status_class = 'badge bg-info'; break;
                              case 'shipped': $status_class = 'badge bg-primary'; break;
                              case 'delivered': $status_class = 'badge bg-success'; break;
                              case 'cancelled': $status_class = 'badge bg-danger'; break;
                              default: $status_class = 'badge bg-secondary';
                            }
                            ?>
                            <span class="<?= $status_class ?>"><?= ucfirst($order['status']) ?></span>
                          </td>
                          <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="5" class="text-center">No recent orders found</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>

              </div>

            </div>
          </div><!-- End Recent Sales -->

        </div>
      </div><!-- End Left side columns -->

      <!-- Right side columns -->
      <div class="col-lg-4">

        <!-- Recent Activity -->
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Summary <span>| Overview</span></h5>

            <div class="activity">
              <div class="d-flex">
                <div class="flex-shrink-0">
                  <div class="activity-item">
                    <i class="bi bi-circle-fill activity-badge text-success align-self-start"></i>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <div class="activity-content">
                    <strong>Total Orders:</strong> <?= $total_orders ?>
                  </div>
                </div>
              </div>

              <div class="d-flex">
                <div class="flex-shrink-0">
                  <div class="activity-item">
                    <i class="bi bi-circle-fill activity-badge text-info align-self-start"></i>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <div class="activity-content">
                    <strong>Total Revenue:</strong> Rs. <?= number_format($total_revenue, 2) ?>
                  </div>
                </div>
              </div>

              <div class="d-flex">
                <div class="flex-shrink-0">
                  <div class="activity-item">
                    <i class="bi bi-circle-fill activity-badge text-warning align-self-start"></i>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <div class="activity-content">
                    <strong>Today's Orders:</strong> <?= $today_orders ?>
                  </div>
                </div>
              </div>

              <div class="d-flex">
                <div class="flex-shrink-0">
                  <div class="activity-item">
                    <i class="bi bi-circle-fill activity-badge text-primary align-self-start"></i>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <div class="activity-content">
                    <strong>Today's Revenue:</strong> Rs. <?= number_format($today_revenue, 2) ?>
                  </div>
                </div>
              </div>

            </div>

          </div>
        </div><!-- End Recent Activity -->

      </div><!-- End Right side columns -->

    </div>
  </section>

</main><!-- End #main -->

<!-- ======= Footer ======= -->
<footer id="footer" class="footer">
  <div class="copyright">
    &copy; Copyright <strong><span>GreenCart</span></strong>. All Rights Reserved
  </div>
  <div class="credits">
    Designed by <a href="https://bootstrapmade.com/">GreenCart Team</a>
  </div>
</footer><!-- End Footer -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
<!-- Toast Notification for Low Stock -->
<div class="toast-container">
    <?php if (!empty($low_stock_products)): ?>
        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
            <div class="toast-header bg-warning">
                <strong class="me-auto">Low Stock Alert</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <p>The following products are running low on stock:</p>
                <ul>
                    <?php foreach ($low_stock_products as $product): ?>
                        <li><?= htmlspecialchars($product['name']) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>
</div>
<!-- Vendor JS Files -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="assets/vendor/tinymce/tinymce.min.js"></script>

<!-- Template Main JS File -->
<script src="assets/js/main.js"></script>
<script>
// Check for low stock and show alert
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($low_stock_products)): ?>
        const productNames = <?php echo json_encode(array_column($low_stock_products, 'name')); ?>;
        const message = "Warning! Low stock for products:\n" + productNames.join("\n");
        
        // Show browser alert
        alert(message);
        
        // Initialize Bootstrap toasts
        const toastElList = [].slice.call(document.querySelectorAll('.toast'));
        const toastList = toastElList.map(function(toastEl) {
            return new bootstrap.Toast(toastEl);
        });
    <?php endif; ?>
});
</script>

</body>

</html>