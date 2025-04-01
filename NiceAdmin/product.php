<?php
include("../database/database.php");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

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
            $stmt = $conn->prepare("INSERT INTO products (category_id, name, description, price, stock_quantity, image) VALUES (?, ?, ?, ?, ?, ?)");
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
</head>

<body>

<!-- Header -->
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
        <li class="nav-item"><a class="nav-link " href="product.php"><i class="bi-box-seam"></i><span>Product</span></a></li>
        <li class="nav-item"><a class="nav-link collapsed" href="contact.php"><i class="bi bi-phone"></i><span>Contact</span></a></li>
    </ul>
</aside><!-- End Sidebar-->
<main id="main" class="main">
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Add Product</h5>
                            <a href="view_product.php" class="btn btn-secondary">View Products</a>
                        </div>

                        <?php
                        if ($errorMessage != "") {
                            echo "<div class='alert alert-danger'>$errorMessage</div>";
                        }
                        if ($successMessage != "") {
                            echo "<div class='alert alert-success'>$successMessage</div>";
                        }
                        ?>

                        <!-- Product Form -->
                        <form action="" class="add_product" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="product_name" class="form-label">Product Name</label>
                                <input type="text" id="product_name" name="product_name" class="form-control" placeholder="Enter the product name">
                            </div>
                            <div class="mb-3">
                                <label for="product_price" class="form-label">Product Price</label>
                                <input type="text" id="product_price" name="product_price" class="form-control" placeholder="Enter the product price">
                            </div>
                            <div class="mb-3">
                                <label for="product_stock_quantity" class="form-label">Product Stock Quantity</label>
                                <input type="text" id="product_stock_quantity" name="product_stock_quantity" class="form-control" placeholder="Enter the product stock quantity">
                            </div>
                            <div class="mb-3">
                                <label for="product_desc" class="form-label">Product Description</label>
                                <textarea id="product_desc" name="product_desc" class="form-control" placeholder="Enter the product description" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="product_category" class="form-label">Product Category</label>
                                <select id="product_category" name="product_category" class="form-control">
                                    <option value="">Choose</option>
                                    <?php while ($row = mysqli_fetch_assoc($category_result)) { ?>
                                        <option value="<?php echo $row['category_id']; ?>"> <?php echo $row['name']; ?> </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="product_image" class="form-label">Product Image</label>
                                <input type="file" id="product_image" name="product_image" class="form-control" accept="image/png, image/jpg, image/jpeg">
                            </div>
                            <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
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
        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
    </div>
</footer><!-- End Footer -->

<!-- JS Files -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="assets/js/main.js"></script>

</body>

</html>
