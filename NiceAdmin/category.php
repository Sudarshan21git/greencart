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

<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item"><a class="nav-link collapsed" href="index.php"><i class="bi bi-grid"></i><span>Dashboard</span></a></li>
        <li class="nav-item"><a class="nav-link " href="category.php"><i class="bi-tags"></i><span>Category</span></a></li>
        <li class="nav-item"><a class="nav-link collapsed" href="product.php"><i class="bi-box-seam"></i><span>Product</span></a></li>
        <li class="nav-item"><a class="nav-link collapsed" href="contact.php"><i class="bi bi-phone"></i><span>Contact</span></a></li>
        <li class="nav-item"><a class="nav-link collapsed" href="user.php"><i class="bi bi-person"></i><span>User</span></a></li>
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
                        <h5 class="card-title">Category</h5>
                        <!-- backend -->
                        


                        <?php
include("../database/database.php");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

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
                header("Location: ".$_SERVER['PHP_SELF']."?success=1");
                exit();
            } else {
                $errorMessage = "Error inserting category!";
            }
        } else {
            $errorMessage = "Category already exists!";
        }
    }

    if (!empty($errorMessage)) {
        echo "<script>alert('$errorMessage');</script>";
    }
}
if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo "<script>alert('Category inserted successfully!');</script>";
}
?>

<!-- Category Form -->
<form action="" class="add_category" method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="category_name" class="form-label">Category Name</label>
        <input type="text" id="category_name" name="category_name" class="form-control" placeholder="Enter the Category name">
    </div>
    <div class="mb-3">
        <label for="category_desc" class="form-label">Category Description</label>
        <textarea id="category_desc" name="category_desc" class="form-control" placeholder="Enter the Category description" rows="3"></textarea>
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
        $category_product = mysqli_query($conn, "SELECT * FROM categories ");
        $serial_number = 1;
        if (mysqli_num_rows($category_product) > 0) {
            while ($row = mysqli_fetch_assoc($category_product)) {
                echo "<tr>";
                echo "<td>{$serial_number}</td>";

                $image_src = !empty($row['image']) ? '../img/' . $row['image'] : 'img/default.png';
                echo "<td><img src='{$image_src}' alt='{$row['name']}' class='category-img'></td>";

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

<!-- Style Section -->
<style>
    .custom-thead th {
        background-color: #006400 !important; /* dark green */
        color: white !important;
    }

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

    .table-hover-custom tbody tr:hover {
        background-color: #e6ffe6; /* light green on hover */
        transition: background-color 0.3s ease;
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
