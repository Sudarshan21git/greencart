<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Components / Accordion - NiceAdmin Bootstrap Template</title>
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

    <!-- =======================================================
    * Template Name: NiceAdmin
    * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
    * Updated: Apr 20 2024 with Bootstrap v5.3.3
    * Author: BootstrapMade.com
    * License: https://bootstrapmade.com/license/
    ======================================================== -->
</head>

<body>

<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
        <a href="index.jsp" class="logo d-flex align-items-center">
            <img src="assets/img/logo.png" alt="">
            <span class="d-none d-lg-block">GreenCartAdmin</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">

            <li class="nav-item dropdown pe-3">

                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                    <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle">
                    <span class="d-none d-md-block  ps-2">Admin</span>
                </a><!-- End Profile Iamge Icon -->

            </li><!-- End Profile Nav -->

        </ul>
    </nav><!-- End Icons Navigation -->

</header><!-- End Header -->

<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link collapsed" href="index.php">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li><!-- End Dashboard Nav -->

        <li class="nav-item">
            <a class="nav-link" href="category.php">
                <i class="bi bi-file-earmark"></i>
                <span>Category</span>
            </a>
        </li><!-- End home Page features Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" href="product.php">
                <i class="bi bi-file-earmark"></i>
                <span>Product</span>
            </a>
        </li><!-- End Blank Page Nav -->

    </ul>

</aside><!-- End Sidebar-->

<main id="main" class="main">
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Category</h5>
                        <!-- backend -->
                        


                        <?php
include("../database/database.php");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $product_name = trim($_POST['product_name']);
    $product_desc = trim($_POST['product_desc']);
    $product_price = trim($_POST['product_price']);
    $product_stock_quantity =($_POST['product_stock_quantity']);
    $product_image = $_FILES['product_image']['name'];
    $product_image_temp_name = $_FILES['product_image']['tmp_name'];
    $product_image_folder = '../img/' . $product_image;

    $errorMessage = "";

    if (empty($product_name) || empty($product_price) || empty($product_stock_quantity) || empty($product_desc) || empty($product_image)) {
        $errorMessage = "Please fill in all fields!";
    } elseif (!preg_match("/^[a-zA-Z][a-zA-Z\s']{3,}$/", $product_name)){
        $errorMessage = "Product must start with an alphabet and be at least 4 characters long.";
    } else if (!is_numeric($product_price) || $product_price <= 0) {
        echo "Product price must be a positive number greater than 0";
    }else if (!is_numeric($product_stock_quantity) || $product_stock_quantity <= 0) {
        echo "Product stock must be a positive number greater than 0";
    }elseif (strlen($product_desc) < 1 || strlen($product_desc) > 200 || !preg_match("/^[a-zA-Z]/", $product_desc)) {
        $errorMessage = "Description must start with an alphabet and be between 1 and 200 characters.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM products WHERE name = ?");
        $stmt->bind_param("s", $product_name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO products (name, `description`, price, stock_quantity, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdis", $product_name, $product_desc, $product_price, $stock_quantity, $product_image);
            $insert_success = $stmt->execute();

            $insert_success = $stmt->execute();

            if ($insert_success) {
                if (!empty($product_image)) {
                    move_uploaded_file($product_image_temp_name, $product_image_folder);
                }
                header("Location: ".$_SERVER['PHP_SELF']."?success=1");
                exit();
            } else {
                $errorMessage = "Error inserting product!";
            }
        } else {
            $errorMessage = "Product already exists!";
        }
    }

    if (!empty($errorMessage)) {
        echo "<script>alert('$errorMessage');</script>";
    }
}
if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo "<script>alert('product  inserted successfully!');</script>";
}
?>

<!-- Category Form -->
<form action="" class="add_product" method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="product_name" class="form-label">product Name</label>
        <input type="text" id="product_name" name="product_name" class="form-control" placeholder="Enter the product name">
    </div>  <div class="mb-3">
        <label for="product_price" class="form-label">product Price</label>
        <input type="text" id="product_price" name="product_price" class="form-control" placeholder="Enter the product price">
    </div>  <div class="mb-3">
        <label for="product_stock_quantity" class="form-label"> product_stock_quantity</label>
        <input type="text" id="product_stock_quantity" name="product_stock_quantity" class="form-control" placeholder="Enter the product stock_quantity">
    </div>
    <div class="mb-3">
        <label for="product_desc" class="form-label">Product Description</label>
        <textarea id="product_desc" name="product_desc" class="form-control" placeholder="Enter the Product description" rows="3"></textarea>
    </div>
    <div class="mb-3">
        <label for="product_image" class="form-label">Product Image </label>
        <input type="file" id="product_image" name="product_image" class="form-control" accept="image/png, image/jpg, image/jpeg">
    </div>
    <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
</form>
<style>
    .category-img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
    }
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

<!-- form end  -->
</main><!-- End #main -->

<!-- ======= Footer ======= -->
<footer id="footer" class="footer">
    <div class="copyright">
        &copy; Copyright <strong><span>GreenCartAdmin</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
        <!-- All the links in the footer should remain intact. -->
        <!-- You can delete the links only if you purchased the pro version. -->
        <!-- Licensing information: https://bootstrapmade.com/license/ -->
        <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
    </div>
</footer><!-- End Footer -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="assets/vendor/tinymce/tinymce.min.js"></script>

<!-- Template Main JS File -->
<script src="assets/js/main.js"></script>

</body>

</html>
<!-- delete catgeory backend code  -->
<?php
include('../database/database.php');


if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $category_id = $_GET['delete'];

    // Prevent further deletion if the same category is being deleted (in case of multiple clicks)
    if (isset($_SESSION['delete_category']) && $_SESSION['delete_category'] == $category_id) {
        // This condition prevents a second deletion for the same category
        $_SESSION['error'] = "Category has already been deleted.";
        header("Location: category.php");
        exit();
    }

    // Delete the category from the database
    $delete_query = mysqli_query($conn, "DELETE FROM categories WHERE category_id = $category_id");

    if ($delete_query) {
        // Mark that the category has been deleted and store the ID
        $_SESSION['delete_category'] = $category_id;
        
        // Redirect back to the category.php page after successful deletion
        $_SESSION['success'] = "Category deleted successfully!";
    
        exit();
    } else {
        $_SESSION['error'] = "Failed to delete category.";
      
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid category ID.";
 
    exit();
}
?>
