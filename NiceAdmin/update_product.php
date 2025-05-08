<?php
// Include the database connection file
include('../database/database.php');
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch categories from the database
$category_query = "SELECT category_id, name FROM categories";
$category_result = mysqli_query($conn, $category_query);

// Fetch product details through that id from the product table 
if (isset($_GET['edit'])) {
    $edit_id = (int) $_GET['edit'];
    $sql = "SELECT * FROM products WHERE product_id = '$edit_id'";
    $edit_query = mysqli_query($conn, $sql);

    if ($edit_query) {
        $fetch_data = mysqli_fetch_assoc($edit_query);
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Update logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_product'])) {
    $update_product_id = trim($_POST['update_product_id']);  // Hidden input for product ID
    $update_product_name = trim($_POST['update_product_name']);
    $update_product_desc = trim($_POST['update_product_desc']);
    $update_product_price = trim($_POST['update_product_price']);
    $update_product_stock_quantity = trim($_POST['update_product_stock_quantity']);
    $update_category_id = trim($_POST['update_product_category']);
    
    // Handling image upload
    $update_product_image = $_FILES['update_product_image']['name'];
    $update_product_image_temp_name = $_FILES['update_product_image']['tmp_name'];
    $update_product_image_folder = '../img/' . $update_product_image;

    // Validation
    if (empty($update_product_name) || empty($update_product_price) || empty($update_product_stock_quantity) || empty($update_product_desc) || empty($update_category_id)) {
        $errorMessage = "Please fill in all required fields!";
    } elseif (!preg_match("/^[a-zA-Z][a-zA-Z\s']{3,}$/", $update_product_name)) {
        $errorMessage = "Product name must start with a letter and be at least 4 characters long.";
    } elseif (!is_numeric($update_product_price) || $update_product_price <= 0) {
        $errorMessage = "Product price must be a positive number.";
    } elseif (!is_numeric($update_product_stock_quantity) || $update_product_stock_quantity <= 0) {
        $errorMessage = "Stock quantity must be a positive number.";
    } elseif (strlen($update_product_desc) < 1 || strlen($update_product_desc) > 200 || !preg_match("/^[a-zA-Z]/", $update_product_desc)) {
        $errorMessage = "Description must start with a letter and be between 1 and 200 characters.";
    } else {
        // Check if the product name already exists (excluding the current product ID)
        $stmt = $conn->prepare("SELECT * FROM products WHERE name = ? AND product_id != ?");
        $stmt->bind_param("si", $update_product_name, $update_product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            // Update query with placeholders
            if (!empty($update_product_image)) {
                $update_query = "UPDATE products SET category_id=?, name=?, description=?, price=?, stock_quantity=?, image=? WHERE product_id=?";
                $stmt = $conn->prepare($update_query);
                $stmt->bind_param("issdisi", $update_category_id, $update_product_name, $update_product_desc, $update_product_price, $update_product_stock_quantity, $update_product_image, $update_product_id);
            } else {
                // If no new image is uploaded, do not update the image column
                $update_query = "UPDATE products SET category_id=?, name=?, description=?, price=?, stock_quantity=? WHERE product_id=?";
                $stmt = $conn->prepare($update_query);
                $stmt->bind_param("issdii", $update_category_id, $update_product_name, $update_product_desc, $update_product_price, $update_product_stock_quantity, $update_product_id);
            }

            if ($stmt->execute()) {
                // Move uploaded image if a new image was provided
                if (!empty($update_product_image)) {
                    move_uploaded_file($update_product_image_temp_name, $update_product_image_folder);
                }
                $successMessage = "Product updated successfully!";
            } else {
                $errorMessage = "Error updating product!";
            }
        } else {
            $errorMessage = "Product name already exists!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Update Product</title>

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
    <li class="nav-item"><a class="nav-link collapsed" href="category.php"><i class="bi bi-tags"></i><span>Category</span></a></li>
    <li class="nav-item"><a class="nav-link" href="product.php"><i class="bi bi-box-seam"></i><span>Product</span></a></li>
    <li class="nav-item"><a class="nav-link collapsed" href="contact.php"><i class="bi bi-phone"></i><span>Contact</span></a></li>
    <li class="nav-item"><a class="nav-link collapsed" href="user.php"><i class="bi bi-person"></i><span>User</span></a></li>
    <li class="nav-item"><a class="nav-link collapsed" href="order.php"><i class="bi bi-box "></i><span>Order</span></a></li>
    <li class="nav-item"><a class="nav-link collapsed" href="http:\\localhost\greencart\auth\logout_admin.php"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a></li>

  </ul>
</aside><!-- End Sidebar -->

<main id="main" class="main">
  <section class="section">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
          <a href="view_product.php"><h5><i class="bi bi-arrow-left"></i> Back</h5></a>
          <div class="d-flex justify-content-between align-items-center">
         

  <h5 class="card-title text-center">Update Product</h5>
</div>


            <!-- Product Form -->
            <?php
            if (isset($errorMessage)) {
                echo '<div class="alert alert-danger mt-3">' . $errorMessage . '</div>';
            }
            if (isset($successMessage)) {
                echo '<div class="alert alert-success mt-3">' . $successMessage . '</div>';
            }
            ?>
            <form action="" method="post" enctype="multipart/form-data">
              <img src="../img/<?php echo isset($fetch_data['image']) ? htmlspecialchars($fetch_data['image']) : ''; ?>" alt="Current Image" class="previous_image img-fluid rounded" style="max-width: 150px; height: auto;"><br>
              <label class="form-label"><h5>Previous Image</h5></label><br>
              <input type="hidden" value="<?php echo isset($fetch_data['product_id']) ? $fetch_data['product_id'] : ''; ?>" name="update_product_id">

              <div class="mb-3">
                <label for="product_name" class="form-label">Product Name</label>
                <input type="text" id="product_name" name="update_product_name" class="form-control" value="<?php echo isset($fetch_data['name']) ? $fetch_data['name'] : ''; ?>" placeholder="Enter the product name">
              </div>
              <div class="mb-3">
                <label for="product_price" class="form-label">Product Price</label>
                <input type="text" id="product_price" name="update_product_price" class="form-control" value="<?php echo isset($fetch_data['price']) ? $fetch_data['price'] : ''; ?>" placeholder="Enter the product price">
              </div>
              <div class="mb-3">
                <label for="product_stock_quantity" class="form-label">Product Stock Quantity</label>
                <input type="text" id="product_stock_quantity" name="update_product_stock_quantity" class="form-control" value="<?php echo isset($fetch_data['stock_quantity']) ? $fetch_data['stock_quantity'] : ''; ?>" placeholder="Enter the product stock quantity">
              </div>
              <div class="mb-3">
                <label for="product_desc" class="form-label">Product Description</label>
                <textarea id="product_desc" name="update_product_desc" class="form-control" placeholder="Enter the product description" rows="3"><?php echo isset($fetch_data['description']) ? htmlspecialchars($fetch_data['description']) : ''; ?></textarea>
              </div>
              <div class="mb-3">
                <label for="product_category" class="form-label">Product Category</label>
                <select id="product_category" name="update_product_category" class="form-control">
                  <option value="">Choose</option>
                  <?php while ($row = mysqli_fetch_assoc($category_result)) { ?>
                    <option value="<?php echo $row['category_id']; ?>" <?php echo (isset($fetch_data['category_id']) && $fetch_data['category_id'] == $row['category_id']) ? 'selected' : ''; ?>> <?php echo $row['name']; ?> </option>
                  <?php } ?>
                </select>
              </div>
              <div class="mb-3">
                <label for="product_image" class="form-label">Product Image</label>
                <input type="file" id="product_image" name="update_product_image" class="form-control">
              </div>

              <div class="text-center">
                <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
              </div>
            </form>

       
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<!-- Vendor JS Files -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
