<?php
ob_start();
session_start();
include('../database/database.php');
// Check for low stock products
$low_stock_query = mysqli_query($conn, "SELECT name FROM products WHERE stock_quantity < 5");
$low_stock_products = mysqli_fetch_all($low_stock_query, MYSQLI_ASSOC);
$low_stock_count = mysqli_num_rows($low_stock_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>User GreenCart</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
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
</head>

<body>

<!-- Header -->
<header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
        <a href="index.jsp" class="logo d-flex align-items-center">
            <img src="assets/img/logo.png" alt="">
            <span class="d-none d-lg-block">GreenCart Admin</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">
            <li class="nav-item dropdown pe-3">
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                    <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle">
                    <span class="d-none d-md-block ps-2">Admin</span>
                </a><!-- End Profile Image Icon -->
            </li><!-- End Profile Nav -->
        </ul>
    </nav><!-- End Icons Navigation -->
</header><!-- End Header -->

<!-- Sidebar -->
<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item"><a class="nav-link collapsed" href="index.php"><i class="bi bi-grid"></i><span>Dashboard</span></a></li>
        <li class="nav-item"><a class="nav-link collapsed" href="category.php"><i class="bi-tags"></i><span>Category</span></a></li>
        <li class="nav-item">
            <a class="nav-link collapsed " href="product.php">
                <i class="bi-box-seam"></i>
                <span>Product</span>
                <?php if ($low_stock_count > 0): ?>
                    <span class="badge bg-danger rounded-pill ms-auto"><?= $low_stock_count ?></span>
                <?php endif; ?>
            </a>
        </li>        <li class="nav-item"><a class="nav-link collapsed" href="contact.php"><i class="bi bi-phone"></i><span>Contact</span></a></li>
        <li class="nav-item"><a class="nav-link " href="user.php"><i class="bi bi-person"></i><span>User</span></a></li>
        <li class="nav-item"><a class="nav-link collapsed" href="order.php"><i class="bi bi-box "></i><span>Order</span></a></li>
        <li class="nav-item"><a class="nav-link collapsed" href="http:\\localhost\greencart\auth\logout_admin.php"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a></li>

    </ul>
</aside><!-- End Sidebar-->

    <main id="main" class="main">
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">User Details</h5>

                            <!-- Session Messages -->
                            <?php if (isset($_SESSION['success'])): ?>
                                <div class="alert alert-success"><?php echo $_SESSION['success'];
                                                                    unset($_SESSION['success']); ?></div>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger"><?php echo $_SESSION['error'];
                                                                unset($_SESSION['error']); ?></div>
                            <?php endif; ?>

                            <p class="text-center text-muted">Here is the list of all UserDetail. You can delete them as needed.</p>
                            <table class="table table-striped table-bordered text-center">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone.no</th>
                                        <th>Address</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!$conn) {
                                        die("Database connection failed: " . mysqli_connect_error());
                                    }

                                    $user = mysqli_query($conn, "SELECT * FROM users");
                                    $serial_number = 1;

                                    if (mysqli_num_rows($user) > 0) {
                                        while ($row = mysqli_fetch_assoc($user)) {
                                            echo "<tr>";
                                            echo "<td>{$serial_number}</td>";
                                            echo "<td>" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['phone'] ?? 'N/A') . "</td>";
                                            echo "<td>" . htmlspecialchars($row['address'] ?? 'N/A') . "</td>";

                                            echo "<td>";
                                            if ($row['is_admin'] == 0) {
                                                echo "<a href='user.php?delete=" . urlencode($row['user_id']) . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this user?')\"><i class='bi bi-trash'></i></a>";
                                            } else {
                                                echo "<span class='badge bg-primary'>Admin</span>";
                                            }
                                            echo "</td>";

                                            echo "</tr>";
                                            $serial_number++;
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>No users found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>

                            <style>
                                .btn-sm {
                                    padding: 5px 10px;
                                    font-size: 14px;
                                }
                            </style>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">
        <div class="copyright">
            &copy; Copyright <strong><span>GreenCartAdmin</span></strong>. All Rights Reserved
        </div>
        <div class="credits">
            Designed by <a href="https://bootstrapmade.com/">GreenCart Team</a>
        </div>
    </footer><!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="assets/js/main.js"></script>

</body>

</html>

<!-- Delete Contact Message Backend Code -->
<?php
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $user_id = $_GET['delete'];

    // Prevent double deletion
    if (isset($_SESSION['delete_message']) && $_SESSION['delete_message'] == $user_id) {
        $_SESSION['error'] = "user has already been deleted.";
        header("Location:user.php");
        exit();
    }

    // Delete message from database
    $delete_query = mysqli_query($conn, "DELETE FROM users WHERE user_id=$user_id");

    if ($delete_query) {
        $_SESSION['delete_message'] = $user_id;
        $_SESSION['success'] = "User deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete message.";
    }

    header("Location: user.php");
    exit();
}
?>