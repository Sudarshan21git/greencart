<?php
include("../database/database.php");

$fetch_data = null;
$success_message = "";

if (isset($_POST['update_category'])) {
    $update_category_id = $_POST['update_category_id'];
    $update_category_name = $_POST['update_category_name'];
    $update_category_desc = $_POST['update_category_desc'];

    // Check if category name already exists (excluding the current one)
    $nameCheck = "SELECT * FROM categories WHERE name ='$update_category_name' AND category_id != $update_category_id";
    $nameCheckResult = mysqli_query($conn, $nameCheck);

    if ($nameCheckResult->num_rows > 0) {
        echo "<div class='alert alert-danger'>Category with the same name already exists.</div>";
    } else {
        if (!preg_match("/^[a-zA-Z][a-zA-Z\s']*$/", $update_category_name)) {
            echo "<div class='alert alert-danger'>Category name must start with an alphabet and contain only letters, spaces, and apostrophes.</div>";
        } else if (strlen($update_category_desc) < 1 || strlen($update_category_desc) > 200 || !preg_match("/^[a-zA-Z]/", $update_category_desc)) {
            echo "<div class='alert alert-danger'>Category description must start with an alphabet and be between 1 and 200 characters long.</div>";
        } else {
            // Check if a new image is uploaded
            if (!empty($_FILES['update_category_image']['name'])) {
                $image_name = $_FILES['update_category_image']['name'];
                $image_tmp_name = $_FILES['update_category_image']['tmp_name'];
                $image_folder = "../img/" . $image_name;

                // Move new image to uploads folder
                move_uploaded_file($image_tmp_name, $image_folder);

                // Update query with new image
                $update_query = "UPDATE categories SET name='$update_category_name', description='$update_category_desc', image='$image_name' WHERE category_id=$update_category_id";
            } else {
                // Update query without changing image
                $update_query = "UPDATE categories SET name='$update_category_name', description='$update_category_desc' WHERE category_id=$update_category_id";
            }

            $update_result = mysqli_query($conn, $update_query);

            if ($update_result) {
                $success_message = "Category updated successfully.";
                // Display the success message on the same page
            } else {
                echo "<div class='alert alert-danger'>Error updating category: " . mysqli_error($conn) . "</div>";
            }
        }
    }
}

if (isset($_GET['id'])) {
    $edit_id = (int) $_GET['id'];
    $edit_query = mysqli_query($conn, "SELECT * FROM categories WHERE category_id = '$edit_id'");

    if ($edit_query) {
        $fetch_data = mysqli_fetch_assoc($edit_query);
    } else {
        echo "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>update_category GreenCart Team</title>
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
    <div class="pagetitle">
        <h1>Update Category</h1>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-6 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-center">Edit Category Details</h5>

                        <form action="" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="update_category_id" value="<?php echo isset($fetch_data['category_id']) ? $fetch_data['category_id'] : ''; ?>">

    <div class="mb-3">
        <label class="form-label">Category Name</label>
        <input type="text" class="form-control" required name="update_category_name" value="<?php echo isset($fetch_data['name']) ? $fetch_data['name'] : ''; ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Category Description</label>
        <textarea class="form-control" required name="update_category_desc"><?php echo isset($fetch_data['description']) ? $fetch_data['description'] : ''; ?></textarea>
    </div>

  <!-- Show Previous Image -->
<div class="mb-3">
    <label class="form-label">Previous Image</label><br>
    <?php if (!empty($fetch_data['image'])): ?>
        <img src="../img/<?php echo $fetch_data['image']; ?>" alt="Category Image" width="150">
    <?php else: ?>
        <p>No image available</p>
    <?php endif; ?>
</div>


    <!-- Upload New Image -->
    <div class="mb-3">
        <label class="form-label">Upload New Image</label>
        <input type="file" class="form-control" name="update_category_image">
    </div>

    <div class="text-center">
        <button type="submit" class="btn btn-success" name="update_category">Update</button>
        <input type="reset" id="close-edit" class="btn btn-secondary" value="Cancel">


    </div>
</form>


                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
