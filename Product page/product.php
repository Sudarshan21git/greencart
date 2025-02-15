<?php
// Start the session
session_start();
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
}

// Include the database connection
include '../db.php';

// Sorting logic
$orderBy = 'id'; // Default sorting
$sortOption = isset($_GET['sort']) ? $_GET['sort'] : 'default';

if ($sortOption === 'price_asc') {
    $orderBy = 'price';
} elseif ($sortOption === 'price_desc') {
    $orderBy = 'price';
}

// Filtering logic using range
$minPrice = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$maxPrice = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 10000; // Default max price

// Fetch all products from the database
$result = mysqli_query($conn, "SELECT id, name, price, image, stock, `desc` FROM products") or die("Query Failed");

$filteredProducts = [];

// Apply filtering manually
while ($row = mysqli_fetch_assoc($result)) {
    if ($row['price'] >= $minPrice && $row['price'] <= $maxPrice) {
        $filteredProducts[] = $row;
    }
}

// Sorting after filtering
if ($sortOption === 'price_asc') {
    usort($filteredProducts, function ($a, $b) {
        return $a['price'] - $b['price']; // Ascending order
    });
} elseif ($sortOption === 'price_desc') {
    usort($filteredProducts, function ($a, $b) {
        return $b['price'] - $a['price']; // Descending order
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nursery Website</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
</head>
<body>

<?php include '../include/nav.php'; ?>

<!-- Main Container -->
<div class="container mt-4">
    <div class="row">
        <!-- Sidebar Section (Filters & Sorting) -->
        <div class="col-md-3">
            <!-- Sort By -->
            <form method="GET" action="">
                <label class="fw-bold">Sort By:</label>
                <select name="sort" class="form-select" onchange="this.form.submit()">
                    <option value="default" <?= $sortOption === 'default' ? 'selected' : '' ?>>Default</option>
                    <option value="price_asc" <?= $sortOption === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_desc" <?= $sortOption === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                </select>
            </form>

            <!-- Price Range Filter -->
            <form method="GET" action="">
                <label class="fw-bold mt-3">Filter by Price:</label>
                <div class="d-flex justify-content-between">
                    <span>Rs. <span id="minPriceDisplay"><?= $minPrice ?></span></span>
                    <span>Rs. <span id="maxPriceDisplay"><?= $maxPrice ?></span></span>
                </div>
                <input type="range" class="form-range" id="minPrice" name="min_price" min="0" max="10000" step="100" value="<?= $minPrice ?>" oninput="updatePriceDisplay()">
                <input type="range" class="form-range" id="maxPrice" name="max_price" min="0" max="10000" step="100" value="<?= $maxPrice ?>" oninput="updatePriceDisplay()">
                <button class="btn btn-primary w-100 mt-2" type="submit">Apply Filter</button>
            </form>
        </div>

        <!-- Product Display Section -->
        <div class="col-md-9">
            <div class="row">
                <?php
                if (!empty($filteredProducts)) {
                    foreach ($filteredProducts as $row) {
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow">
                        <img src="../img/<?php echo $row['image']; ?>" class="card-img-top" alt="">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row['name']; ?></h5>
                            <p class="card-text">Price: Rs.<?php echo $row['price']; ?></p>
                            <p class="card-text">Stock: <?php echo $row['stock']; ?></p>
                            <p class="card-text">Description: <?php echo $row['desc']; ?></p>
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
                    echo "<p class='text-center'>No products available in this price range.</p>";
                }
                ?>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to Update Range Display -->
<script>
    function updatePriceDisplay() {
        document.getElementById('minPriceDisplay').textContent = document.getElementById('minPrice').value;
        document.getElementById('maxPriceDisplay').textContent = document.getElementById('maxPrice').value;
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
