<?php
ob_start();
session_start();
include('../database/database.php');

// Check for low stock products
$low_stock_query = mysqli_query($conn, "SELECT name FROM products WHERE stock_quantity < 5");
$low_stock_products = mysqli_fetch_all($low_stock_query, MYSQLI_ASSOC);
$low_stock_count = mysqli_num_rows($low_stock_query);

// Initialize search query
$search_query = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Product Details - GreenCart Admin</title>

    <!-- Favicons -->
    <link href="../img/logo.png" rel="icon">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans|Nunito|Poppins" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">
    
    <style>
        .btn-sm {
            padding: 5px 10px;
            font-size: 14px;
        }
        .table-hover-custom tbody tr:hover {
            background-color: #e6ffe6;
            transition: background-color 0.3s ease;
        }
        thead th {
            background-color: green !important;
            color: white !important;
        }
        .low-stock {
            color: red;
            font-weight: bold;
        }
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1100;
        }
    </style>
</head>
<body>

<!-- Header -->
<header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
    <a href="" class="logo d-flex align-items-center">        <img src="../img/logo.png" alt="">
            <span class="d-none d-lg-block">GreenCart Admin</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>
</header>

<!-- Sidebar -->
<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item"><a class="nav-link collapsed" href="index.php"><i class="bi bi-grid"></i><span>Dashboard</span></a></li>
        <li class="nav-item"><a class="nav-link collapsed" href="category.php"><i class="bi-tags"></i><span>Category</span></a></li>
        <li class="nav-item">
            <a class="nav-link " href="product.php">
                <i class="bi-box-seam"></i>
                <span>Product</span>
                <?php if ($low_stock_count > 0): ?>
                    <span class="badge bg-danger rounded-pill ms-auto"><?= $low_stock_count ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item"><a class="nav-link collapsed" href="contact.php"><i class="bi bi-phone"></i><span>Contact</span></a></li>
        <li class="nav-item"><a class="nav-link collapsed" href="user.php"><i class="bi bi-person"></i><span>User</span></a></li>
        <li class="nav-item"><a class="nav-link collapsed" href="order.php"><i class="bi bi-box"></i><span>Order</span></a></li>
        <li class="nav-item"><a class="nav-link collapsed" href="http://localhost/greencart/auth/logout_admin.php"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a></li>
    </ul>
</aside>

<!-- Main Content -->
<main id="main" class="main">
    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">

                        <a href="product.php"><h5><i class="bi bi-arrow-left"></i> Back</h5></a>

                        <!-- Page Title -->
                        <h1 class="card-title text-center" style="color: green; font-size: 2.5rem;">Product Details</h1>

                        <!-- Messages -->
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                        <?php endif; ?>

                        <!-- Search Form -->
                        <form method="GET" class="d-flex justify-content-end mb-3">
                            <input type="text" name="search" class="form-control w-25 me-2" placeholder="Search product..." value="<?= htmlspecialchars($search_query); ?>">
                            <button type="submit" class="btn btn-success"><i class="bi bi-search"></i></button>
                        </form>

                        <p class="text-center text-muted">Here is the list of all Product Details. You can delete them as needed.</p>

                        <!-- Product Table -->
                        <table class="table table-striped table-bordered text-center align-middle table-hover-custom">
                            <thead>
                                <tr>
                                    <th>Sl No.</th>
                                    <th>Product Image</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th style="width: 150px;">Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!$conn) {
                                    die("Database connection failed: " . mysqli_connect_error());
                                }

                                $query = "
                                    SELECT p.product_id, p.name AS product_name, p.description AS product_desc, p.price, p.stock_quantity, p.image, c.name AS category_name
                                    FROM products p
                                    LEFT JOIN categories c ON p.category_id = c.category_id
                                ";

                                if (!empty($search_query)) {
                                    $query .= " WHERE p.name LIKE '%$search_query%' OR c.name LIKE '%$search_query%'";
                                }

                                $products = mysqli_query($conn, $query);
                                $serial_number = 1;

                                if (mysqli_num_rows($products) > 0) {
                                    while ($row = mysqli_fetch_assoc($products)) {
                                        echo "<tr>";
                                        echo "<td>{$serial_number}</td>";
                                        echo "<td><img src='../img/{$row['image']}' alt='{$row['product_name']}' style='max-width: 100px;'></td>";
                                        echo "<td>{$row['product_name']}</td>";
                                        echo "<td>{$row['category_name']}</td>";
                                        echo "<td>{$row['price']}</td>";
                                        echo "<td class='" . ($row['stock_quantity'] < 5 ? 'low-stock' : '') . "'>{$row['stock_quantity']}";
                                        if ($row['stock_quantity'] < 5) {
                                            echo " (Low Stock)";
                                        }
                                        echo "</td>";
                                        echo "<td style='font-size: 14px;'>{$row['product_desc']}</td>";
                                        echo "<td>
                                            <a href='view_product.php?delete=" . urlencode($row['product_id']) . "' class='btn btn-danger btn-sm me-1' onclick=\"return confirm('Are you sure you want to delete this product?')\"><i class='bi bi-trash'></i></a>
                                            <a href='update_product.php?edit=" . urlencode($row['product_id']) . "' class='btn btn-primary btn-sm'><i class='bi bi-pencil'></i></a>
                                        </td>";
                                        echo "</tr>";
                                        $serial_number++;
                                    }
                                } else {
                                    echo "<tr><td colspan='8'>No products found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>

                    </div>
                </div>

            </div>
        </div>
    </section>
</main>

<!-- Footer -->
<footer id="footer" class="footer">
    <div class="copyright">
        &copy; Copyright <strong><span>GreenCartAdmin</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
        Designed by <a href="https://bootstrapmade.com/">GreenCart Team</a>
    </div>
</footer>

<!-- Back to Top -->
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

<!-- JS Files -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/simple-datatables/simple-datatables.js"></script>

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

<!-- Delete Product Backend Code -->
<?php
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $product_id = $_GET['delete'];

    // Prevent double deletion
    if (isset($_SESSION['delete_product']) && $_SESSION['delete_product'] == $product_id) {
        $_SESSION['error'] = "Product has already been deleted.";
        header("Location: view_product.php");
        exit();
    }

    // Delete product from database
    $delete_query = mysqli_query($conn, "DELETE FROM products WHERE product_id = $product_id");

    if ($delete_query) {
        $_SESSION['delete_product'] = $product_id;
        $_SESSION['success'] = "Product deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete product.";
    }

    header("Location: view_product.php");
    exit();
}
?>