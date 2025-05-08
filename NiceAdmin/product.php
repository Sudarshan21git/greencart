<?php
include("../database/database.php");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
// Check for low stock products
$low_stock_query = mysqli_query($conn, "SELECT name FROM products WHERE stock_quantity < 5");
$low_stock_products = mysqli_fetch_all($low_stock_query, MYSQLI_ASSOC);
$low_stock_count = mysqli_num_rows($low_stock_query);

// Initialize error and success message variables
$errorMessage = "";
$successMessage = "";

// Fetch categories from the database
$category_query = "SELECT category_id, name FROM categories";
$category_result = mysqli_query($conn, $category_query);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $product_name = trim($_POST['product_name']);
    $product_desc = trim($_POST['product_desc']);
    $product_price = trim($_POST['product_price']);
    $product_stock_quantity = trim($_POST['product_stock_quantity']);
    $category_id = trim($_POST['product_category']);
    $product_image = $_FILES['product_image']['name'];
    $product_image_temp_name = $_FILES['product_image']['tmp_name'];
    $product_image_folder = '../img/' . $product_image;

    if (empty($product_name) || empty($product_price) || empty($product_stock_quantity) || empty($product_desc) || empty($product_image) || empty($category_id)) {
        $errorMessage = "Please fill in all fields!";
    } elseif (!preg_match("/^[a-zA-Z][a-zA-Z\s']{3,}$/", $product_name)) {
        $errorMessage = "Product name must start with an alphabet and be at least 4 characters long.";
    } elseif (!is_numeric($product_price) || $product_price <= 0) {
        $errorMessage = "Product price must be a positive number greater than 0";
    } elseif (!is_numeric($product_stock_quantity) || $product_stock_quantity <= 0) {
        $errorMessage = "Product stock must be a positive number greater than 0";
    } elseif (strlen($product_desc) < 1 || strlen($product_desc) > 200 || !preg_match("/^[a-zA-Z]/", $product_desc)) {
        $errorMessage = "Description must start with an alphabet and be between 1 and 200 characters.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM products WHERE name = ?");
        $stmt->bind_param("s", $product_name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $sql="INSERT INTO products (category_id, name, description, price, stock_quantity, image) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issdis", $category_id, $product_name, $product_desc, $product_price, $product_stock_quantity, $product_image);
            $insert_success = $stmt->execute();

            if ($insert_success) {
                if (!empty($product_image)) {
                    move_uploaded_file($product_image_temp_name, $product_image_folder);
                }
                $successMessage = "Product inserted successfully!";
            } else {
                $errorMessage = "Error inserting product!";
            }
        } else {
            $errorMessage = "Product already exists!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Product GreenCart</title>
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
            <img src="../img/logo.png" alt="">
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
        <li class="nav-item"><a class="nav-link collapsed" href="order.php"><i class="bi bi-box "></i><span>Order</span></a></li>
        <li class="nav-item"><a class="nav-link collapsed" href="http:\\localhost\greencart\auth\logout_admin.php"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a></li>

    </ul>
</aside><!-- End Sidebar-->
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Product Management</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Product</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title mb-0">Add New Product</h5>
                            <a href="view_product.php" class="btn btn-outline-primary">
                                <i class="bi bi-eye-fill"></i> View Products
                            </a>
                        </div>

                        <?php
                        // Display error/success messages
                        if (!empty($errorMessage)) {
                            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="bi bi-exclamation-octagon me-1"></i>'
                                    . $errorMessage .
                                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                  </div>';
                        }
                        if (!empty($successMessage)) {
                            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="bi bi-check-circle me-1"></i>'
                                    . $successMessage .
                                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                  </div>';
                        }
                        ?>

                        <!-- Product Form -->
                        <form action="" class="row g-3 needs-validation" method="post" enctype="multipart/form-data" novalidate>
                            <div class="col-md-6">
                                <label for="product_name" class="form-label">Product Name *</label>
                                <input type="text" class="form-control" id="product_name" name="product_name" required>
                                <div class="invalid-feedback">
                                    Please enter a product name.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="product_price" class="form-label">Price Rs. *</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="product_price" name="product_price" step="0.01" min="0" required>
                                    <div class="invalid-feedback">
                                        Please enter a valid price.
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="product_stock_quantity" class="form-label">Stock Quantity *</label>
                                <input type="number" class="form-control" id="product_stock_quantity" name="product_stock_quantity" min="0" required>
                                <div class="invalid-feedback">
                                    Please enter stock quantity.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="product_category" class="form-label">Category *</label>
                                <select class="form-select" id="product_category" name="product_category" required>
                                    <option value="" selected disabled>Choose category...</option>
                                    <?php 
                                    $category_result = mysqli_query($conn, "SELECT * FROM categories");
                                    while ($row = mysqli_fetch_assoc($category_result)) { 
                                    ?>
                                        <option value="<?php echo $row['category_id']; ?>"><?php echo $row['name']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback">
                                    Please select a category.
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="product_desc" class="form-label">Description</label>
                                <textarea class="form-control" id="product_desc" name="product_desc" rows="3" style="min-height: 100px;"></textarea>
                            </div>

                            <div class="col-md-6">
                                <label for="product_image" class="form-label">Product Image</label>
                                <input class="form-control" type="file" id="product_image" name="product_image" accept="image/png, image/jpg, image/jpeg">
                          
                            </div>

                            <div class="col-12">
                                <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
                                <button type="reset" class="btn btn-outline-secondary">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<!-- End #main -->

<!-- Footer -->
<footer id="footer" class="footer">
    <div class="copyright">
        &copy; Copyright <strong><span>GreenCartAdmin</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
        Designed by <a href="https://bootstrapmade.com/">GreenCart Team</a>
    </div>
</footer><!-- End Footer -->

<!-- JS Files -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="assets/js/main.js"></script>

</body>

</html>
