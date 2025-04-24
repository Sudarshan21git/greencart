<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is admin
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
    header("Location: 404.html");
    exit();
}

include_once '../includes/header.php';
include_once '../database/database.php';

if (!isset($_GET['id'])) {
    header("Location: 404.html");
    exit();
}

$product_id = $_GET['id'];
// Fetch product details from the database using the product ID
$qry = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($qry);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header("Location: 404.html");
    exit();
}

$category_id = $product['category_id'];
$qry2 = "SELECT name FROM categories WHERE category_id = ?";
$stmt2 = $conn->prepare($qry2);
$stmt2->bind_param("i", $category_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
$category = $result2->fetch_assoc();

if (!$category) {
    header("Location: 404.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name'] ?> - GreenCart</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <section class="product-details">
        <div class="container">
            <div class="product-details-product-container">
                <div class="product-details-product-images">
                    <div class="product-details-main-image">
                        <img src="../img/<?php echo $product['image'] ?>" alt="<?php echo $product['name'] ?>">
                    </div>
                </div>
                <div class="product-details-product-info">

                    <h1><?php echo $product['name'] ?></h1>

                    <div class="product-details-product-rating">
                        <span class="stars">★★★★★</span>
                    </div>

                    <div class="product-details-product-price">$<?php echo number_format($product['price'], 2) ?></div>

                    <div class="product-details-product-description">
                        <p><?php echo $product['description'] ?></p>
                    </div>

                    <?php if ($product['stock_quantity'] > 0): ?>
                        <div class="product-details-stock-status">
                            <?php if ($product['stock_quantity'] <= 5): ?>
                                <span class="in-stock" style="color: #ff9800;">Only <?php echo $product['stock_quantity'] ?> left in stock</span>
                            <?php else: ?>
                                <span class="in-stock">In Stock (<?php echo $product['stock_quantity'] ?>)</span>
                            <?php endif; ?>
                        </div>

                        <div class="quantity-selector">
                            <button type="button" class="quantity-btn decrease">-</button>
                            <input type="number" class="quantity-input" value="1" min="1" max="<?php echo $product['stock_quantity'] ?>">
                            <button type="button" class="quantity-btn increase">+</button>
                        </div>
                        <div class="stock-warning" id="stock-warning">
                            Maximum available: <?php echo $product['stock_quantity'] ?>
                        </div><br>
                        <button type="button" class="btn btn-add-cart" data-productid="<?php echo $product['product_id'] ?>">Add to Cart</button>

                    <?php else: ?>
                        <div class="product-details-stock-status">
                            <span class="out-of-stock">Out of Stock</span>
                        </div>
                        <button type="button" class="btn btn-add-cart" disabled>Out of Stock</button>
                    <?php endif; ?>

                    <div class="product-meta">
                        <div class="meta-item">
                            <span class="meta-label">Category:</span>
                            <a href="../shop.php?category=<?php echo $category_id; ?>"><?php echo $category['name']; ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <div id="notification-container"></div>
    
    <?php include_once '../includes/footer.php'; ?>
    <script src="../js/script.js"></script>
</body>

</html>
