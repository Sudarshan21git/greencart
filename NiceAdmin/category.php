<?php
session_start();
include("../database/database.php");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
// delete 
if (isset($_GET['delete'])) {
    $category_id = $_GET['delete'];
    
    // First check if category exists
    $check_query = mysqli_query($conn, "SELECT * FROM categories WHERE category_id = $category_id");
    if (mysqli_num_rows($check_query) == 0) {
        $_SESSION['error'] = "Category not found or already deleted.";
        header("Location: category.php");
        exit();
    }

    // Delete the category
    $delete_query = mysqli_query($conn, "DELETE FROM categories WHERE category_id = $category_id");
    
    if ($delete_query) {
        $_SESSION['success'] = "Category deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete category: " . mysqli_error($conn);
    }
    
    header("Location: category.php");
    exit();
}

// Check for low stock products
$low_stock_query = mysqli_query($conn, "SELECT name FROM products WHERE stock_quantity < 5");
$low_stock_products = mysqli_fetch_all($low_stock_query, MYSQLI_ASSOC);
$low_stock_count = mysqli_num_rows($low_stock_query);

// Add Category Logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);
    $category_desc = trim($_POST['category_desc']);
    $category_image = $_FILES['category_image']['name'];
    $category_image_temp_name = $_FILES['category_image']['tmp_name'];
    $category_image_folder = '../img/' . $category_image;

    $errorMessage = "";

    if (empty($category_name) || empty($category_desc)) {
        $errorMessage = "Please fill in all fields!";
    } elseif (!preg_match("/^[a-zA-Z][a-zA-Z\s']{3,}$/", $category_name)) {
        $errorMessage = "Category must start with an alphabet and be at least 4 characters long.";
    } elseif (strlen($category_desc) < 1 || strlen($category_desc) > 200 || !preg_match("/^[a-zA-Z]/", $category_desc)) {
        $errorMessage = "Description must start with an alphabet and be between 1 and 200 characters.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM categories WHERE name = ?");
        $stmt->bind_param("s", $category_name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO categories (name, `description`, image) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $category_name, $category_desc, $category_image);
            $insert_success = $stmt->execute();

            if ($insert_success) {
                if (!empty($category_image)) {
                    move_uploaded_file($category_image_temp_name, $category_image_folder);
                }
                $_SESSION['success'] = "Category inserted successfully!";
                header("Location: category.php");
                exit();
            } else {
                $errorMessage = "Error inserting category!";
            }
        } else {
            $errorMessage = "Category already exists!";
        }
    }

    if (!empty($errorMessage)) {
        $_SESSION['error'] = $errorMessage;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Category GreenCart</title>
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

<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
        <a href="index.php" class="logo d-flex align-items-center">
        <img src="../img/logo.png" alt="">
            <span class="d-none d-lg-block">GreenCart Admin</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>

    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">
            <li class="nav-item dropdown pe-3">
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                    <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle">
                    <span class="d-none d-md-block ps-2">Admin</span>
                </a>
            </li>
        </ul>
    </nav>
</header>

<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item"><a class="nav-link collapsed" href="index.php"><i class="bi bi-grid"></i><span>Dashboard</span></a></li>
        <li class="nav-item"><a class="nav-link " href="category.php"><i class="bi-tags"></i><span>Category</span></a></li>
        <li class="nav-item"><a class="nav-link collapsed" href="product.php"><i class="bi-box-seam"></i><span>Product</span>
                <?php if ($low_stock_count > 0): ?>
                    <span class="badge bg-danger rounded-pill ms-auto"><?= $low_stock_count ?></span>
                <?php endif; ?></a></li> 
        <li class="nav-item"><a class="nav-link collapsed" href="contact.php"><i class="bi bi-phone"></i><span>Contact</span></a></li>
        <li class="nav-item"><a class="nav-link collapsed" href="user.php"><i class="bi bi-person"></i><span>User</span></a></li>
        <li class="nav-item"><a class="nav-link collapsed" href="order.php"><i class="bi bi-box"></i><span>Order</span></a></li>
        <li class="nav-item"><a class="nav-link collapsed" href="http:\\localhost\greencart\auth\logout_admin.php"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a></li>
    </ul>
</aside>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Category Management</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Category</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add New Category</h5>
                        
                        <!-- Display Messages -->
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
                            <?php unset($_SESSION['success']); ?>
                        <?php endif; ?>
                        
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>

                        <!-- Category Form -->
                        <form action="" class="add_category" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="category_name" class="form-label">Category Name</label>
                                <input type="text" id="category_name" name="category_name" class="form-control" placeholder="Enter the Category name" required>
                            </div>
                            <div class="mb-3">
                                <label for="category_desc" class="form-label">Category Description</label>
                                <textarea id="category_desc" name="category_desc" class="form-control" placeholder="Enter the Category description" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="category_image" class="form-label">Category Image (Optional)</label>
                                <input type="file" id="category_image" name="category_image" class="form-control" accept="image/png, image/jpg, image/jpeg">
                            </div>
                            <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
                        </form>

                        <h3 class="text-center mt-4">Category List</h3>
                        <p class="text-center text-muted">Here is the list of all available categories. You can edit or delete them as needed.</p>
                        
                        <table class="table table-striped table-bordered text-center table-hover-custom">
                            <thead class="custom-thead">
                                <tr>
                                    <th>Sl No.</th>
                                    <th>Category Image</th>
                                    <th>Category Name</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $category_product = mysqli_query($conn, "SELECT * FROM categories");
                                $serial_number = 1;
                                if (mysqli_num_rows($category_product) > 0) {
                                    while ($row = mysqli_fetch_assoc($category_product)) {
                                        echo "<tr>";
                                        echo "<td>{$serial_number}</td>";

                                        $image_src = !empty($row['image']) ? '../img/' . $row['image'] : 'img/default.png';
                                        echo "<td><img src='{$image_src}' alt='{$row['name']}' class='category-img' style='width: 150px; height: 150px; object-fit: cover; border-radius: 8px;'></td>";
                                        echo "<td>{$row['name']}</td>";
                                        echo "<td style='max-width: 200px; font-size: 14px;'>{$row['description']}</td>";
                                        echo "<td>
                                                <a href='update_category.php?id=" . urlencode($row['category_id']) . "' class='btn btn-warning btn-sm me-1'><i class='bi bi-pencil'></i></a>
                                                <a href='category.php?delete=" . urlencode($row['category_id']) . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this category?')\"><i class='bi bi-trash'></i></a>
                                              </td>";
                                        echo "</tr>";
                                        $serial_number++;
                                    }
                                } else {
                                    echo "<tr><td colspan='5'>No categories found.</td></tr>";
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

<!-- ======= Footer ======= -->
<footer id="footer" class="footer">
    <div class="copyright">
        &copy; Copyright <strong><span>GreenCart Admin</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
        Designed by <a href="https://bootstrapmade.com/">GreenCart Team</a>
    </div>
</footer>

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="assets/vendor/tinymce/tinymce.min.js"></script>

<!-- Template Main JS File -->
<script src="assets/js/main.js"></script>

</body>
</html>