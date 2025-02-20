<?php
session_start();
include '../include/db.php';

// Get logged-in user ID
$userId = $_SESSION['id'] ?? null;

// Sorting logic
$orderBy = 'id';
$sortOption = $_GET['sort'] ?? 'default';
if ($sortOption === 'price_asc') {
    $orderBy = 'price ASC';
} elseif ($sortOption === 'price_desc') {
    $orderBy = 'price DESC';
}

// Filtering logic
$minPrice = $_GET['min_price'] ?? 0;
$maxPrice = $_GET['max_price'] ?? 10000;

// Add to Cart logic
$successMessage = "";
if (isset($_POST['add_to_cart'])) {
    if (!$userId) {
        header("Location: loginpage/login.php");
        exit;
    }
    
    $product_id = $_POST['product_id'];
    $product_quantity = 1;

    // Check if product is already in cart
    $check_cart = mysqli_query($conn, "SELECT * FROM cart WHERE productId = $product_id AND userId = $userId");
    
    if (mysqli_num_rows($check_cart) > 0) {
        $successMessage = '<div class="alert alert-warning text-center">Product already added to cart.</div>';
    } else {
        $add_to_cart = mysqli_query($conn, "INSERT INTO cart (userId, productId, quantity) VALUES ('$userId', '$product_id', '$product_quantity')");
        if ($add_to_cart) {
            $successMessage = '<div class="alert alert-success text-center">Product added to cart successfully.</div>';
        }
    }
}

// Fetch filtered and sorted products
$sql = "SELECT id, name, price, image, stock, `desc` FROM products WHERE price BETWEEN $minPrice AND $maxPrice ORDER BY $orderBy";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Page</title>
    <link rel="stylesheet" type="text/css" href="../include/nav.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="..." crossorigin="anonymous" />


   
</head>
<body>
<!-- navbar -->
<?php include "../include/nav.php"; ?>
<!-- nav bar -->

<!-- Success Message -->
<?= $successMessage ?>
 <!-- search bar  -->
 <div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form method="GET" action="search.php" class="input-group">
                <div class="input-group-prepend">
                </div>
                <input type="text" class="form-control" name="search" placeholder="Search for your product..." required>
                <div class="input-group-append">
                </div>
                <button type="submit" class="btn btn-success">Search</button>
            </form>
        </div>
    </div>
</div>


<!-- Filters & Sorting -->
<div class="container mt-4">
    <div class="row">
        <!-- Sidebar (Sorting & Price Filter) -->
        <div class="col-md-3">
            <form method="GET" action="">
                <label class="fw-bold">Sort By:</label>
                <select name="sort" class="form-select" onchange="this.form.submit()">
                    <option value="default" <?= $sortOption === 'default' ? 'selected' : '' ?>>Default</option>
                    <option value="price_asc" <?= $sortOption === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_desc" <?= $sortOption === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                </select>
            </form>

            <form method="GET" action="">
                <label class="fw-bold mt-3">Filter by Price:</label>
                <div class="d-flex justify-content-between">
                    <span>Rs. <span id="minPriceDisplay"><?= $minPrice ?></span></span>
                    <span>Rs. <span id="maxPriceDisplay"><?= $maxPrice ?></span></span>
                </div>
                <input type="range" class="form-range" id="minPrice" name="min_price" min="0" max="10000" step="100" value="<?= $minPrice ?>" oninput="updatePrice()">
                <input type="range" class="form-range" id="maxPrice" name="max_price" min="0" max="10000" step="100" value="<?= $maxPrice ?>" oninput="updatePrice()">
                <button type="submit" class="btn btn-success w-100 mt-2">Apply</button>
            </form>
        </div>

        <!-- Product Display -->
        <div class="col-md-9">
            <div class="row">
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow">
                        <img src="../img/<?php echo $row['image']; ?>" class="card-img-top" alt="">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row['name']; ?></h5>
                            <p class="card-text">Price: Rs.<?php echo $row['price']; ?></p>
                            <p class="card-text">Stock: <?php echo $row['stock']; ?></p>
                            <p class="card-text"><?php echo $row['desc']; ?></p>
                            <form action="" method="post">
                                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                <?php if ($row['stock'] > 0) { ?>
                                    <button type="submit" class="btn btn-success w-100" name="add_to_cart">Add to Cart</button>
                                <?php } else { ?>
                                    <p class="text-danger text-center">Out of Stock</p>
                                <?php } ?>
                            </form>
                        </div>
                    </div>
                </div>
                <?php
                    }
                } else {
                    echo "<p class='text-center'>No products available.</p>";
                }
                ?>
            </div>
        </div>
    </div>
</div>

<script>
    function updatePrice() {
        document.getElementById('minPriceDisplay').innerText = document.getElementById('minPrice').value;
        document.getElementById('maxPriceDisplay').innerText = document.getElementById('maxPrice').value;
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
