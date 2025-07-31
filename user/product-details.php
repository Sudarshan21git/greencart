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

// Fetch reviews for this product
$reviews_query = "SELECT r.*, u.first_name, u.last_name 
                 FROM reviews r 
                 JOIN users u ON r.user_id = u.user_id 
                 WHERE r.product_id = ? 
                 ORDER BY r.created_at DESC";
$reviews_stmt = $conn->prepare($reviews_query);
$reviews_stmt->bind_param("i", $product_id);
$reviews_stmt->execute();
$reviews_result = $reviews_stmt->get_result();

// Check if user has purchased this product (to enable review submission)
$user_purchased = false;
if (isset($_SESSION['user_id'])) {
    $purchase_query = "SELECT COUNT(*) as purchase_count 
                      FROM orders o 
                      JOIN order_items oi ON o.order_id = oi.order_id 
                      WHERE o.user_id = ? AND oi.product_id = ? AND o.status = 'delivered'";
    $purchase_stmt = $conn->prepare($purchase_query);
    $purchase_stmt->bind_param("ii", $_SESSION['user_id'], $product_id);
    $purchase_stmt->execute();
    $purchase_result = $purchase_stmt->get_result();
    $purchase_data = $purchase_result->fetch_assoc();
    $user_purchased = $purchase_data['purchase_count'] > 0;
    
    // Check if user has already reviewed this product
    $user_reviewed_query = "SELECT review_id FROM reviews WHERE user_id = ? AND product_id = ?";
    $user_reviewed_stmt = $conn->prepare($user_reviewed_query);
    $user_reviewed_stmt->bind_param("ii", $_SESSION['user_id'], $product_id);
    $user_reviewed_stmt->execute();
    $user_reviewed_result = $user_reviewed_stmt->get_result();
    $user_already_reviewed = $user_reviewed_result->num_rows > 0;
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
                        <?php
                        switch(true) {
                                    case ($product['rating']>=5): echo '<span class="stars">★★★★★</span>'; break;
                                    case ($product['rating']>=4): echo '<span class="stars">★★★★☆</span>'; break;
                                    case ($product['rating']>=3): echo '<span class="stars">★★★☆☆</span>'; break;
                                    case ($product['rating']>=2): echo '<span class="stars">★★☆☆☆</span>'; break;
                                    case ($product['rating']>=1): echo '<span class="stars">★☆☆☆☆</span>'; break;
                                    default: echo 'No rating';
                                }
                        ?>
                        <span class="reviews-count">(<?php echo $product['review_count']; ?> reviews)</span>
                    </div>

                    <div class="product-details-product-price">Rs.<?php echo number_format($product['price'], 2) ?></div>

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
    
    <!-- Reviews Section -->
    <section class="reviews-section">
        <div class="container">
            <div class="reviews-container">
                <div class="reviews-header">
                    <h2 class="reviews-title">Customer Reviews</h2>
                    <?php if (isset($_SESSION['user_id']) && $user_purchased && !$user_already_reviewed): ?>
                        <a href="#write-review" class="btn btn-primary">Write a Review</a>
                    <?php endif; ?>
                </div>
                
                <?php if ($reviews_result->num_rows > 0): ?>
                    <div class="reviews-summary">
                    <div class="reviews-average"><?php echo $product['rating']; ?></div>
                    <div>
                        <div class="reviews-stars">
                            <?php
                            switch(true) {
                                    case ($product['rating']>=5): echo '<span class="stars">★★★★★</span>'; break;
                                    case ($product['rating']>=4): echo '<span class="stars">★★★★☆</span>'; break;
                                    case ($product['rating']>=3): echo '<span class="stars">★★★☆☆</span>'; break;
                                    case ($product['rating']>=2): echo '<span class="stars">★★☆☆☆</span>'; break;
                                    case ($product['rating']>=1): echo '<span class="stars">★☆☆☆☆</span>'; break;
                                    default: echo 'No rating';
                                }
                            ?>
                        </div>
                        <div class="reviews-count">Based on <?php echo $product['review_count']; ?> reviews</div>
                    </div>
                </div>
                    <div class="reviews-list">
                        <?php while ($review = $reviews_result->fetch_assoc()): ?>
                            <div class="review-card">
                                <div class="review-header">
                                    <div class="reviewer-info">
                                        <div class="reviewer-avatar">
                                            <?php echo strtoupper(substr($review['first_name'], 0, 1) . substr($review['last_name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="reviewer-name"><?php echo htmlspecialchars($review['first_name'] . ' ' . $review['last_name']); ?></div>
                                            <div class="review-date"><?php echo date('F d, Y', strtotime($review['created_at'])); ?></div>
                                        </div>
                                    </div>
                                    <div class="review-rating">
                                        <?php
                                        switch(true) {
                                    case ($review['rating']>=5): echo '<span class="star">★★★★★</span>'; break;
                                    case ($review['rating']>=4): echo '<span class="star">★★★★☆</span>'; break;
                                    case ($review['rating']>=3): echo '<span class="star">★★★☆☆</span>'; break;
                                    case ($review['rating']>=2): echo '<span class="star">★★☆☆☆</span>'; break;
                                    case ($review['rating']>=1): echo '<span class="star">★☆☆☆☆</span>'; break;
                                    default: echo 'No rating';
                                }
                                        ?>
                                    </div>
                                </div>
                                <div class="review-content">
                                    <?php echo nl2br(htmlspecialchars($review['message'])); ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-reviews">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                        <h3>No Reviews Yet</h3>
                        <p>Be the first to review this product</p>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['user_id']) && $user_purchased && !$user_already_reviewed): ?>
                    <div class="review-form" id="write-review">
                        <h3>Write a Review</h3>
                        <form action="../functions/submit-review.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                            
                            <div class="form-group">
                                <label>Your Rating</label>
                                <div class="rating-input">
                                    <input type="radio" name="rating" id="star5" value="5" required>
                                    <label for="star5">★</label>
                                    <input type="radio" name="rating" id="star4" value="4">
                                    <label for="star4">★</label>
                                    <input type="radio" name="rating" id="star3" value="3">
                                    <label for="star3">★</label>
                                    <input type="radio" name="rating" id="star2" value="2">
                                    <label for="star2">★</label>
                                    <input type="radio" name="rating" id="star1" value="1">
                                    <label for="star1">★</label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="review-comment">Your Review</label>
                                <textarea id="review-comment" name="comment" required placeholder="Share your experience with this product..."></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Submit Review</button>
                        </form>
                    </div>
                <?php elseif (isset($_SESSION['user_id']) && $user_already_reviewed): ?>
                    <div class="alert alert-info">
                        You have already reviewed this product. You can edit your review in your account dashboard.
                    </div>
                <?php elseif (isset($_SESSION['user_id']) && !$user_purchased): ?>
                    <div class="alert alert-info">
                        You can write a review after purchasing and receiving this product.
                    </div>
                <?php elseif (!isset($_SESSION['user_id'])): ?>
                    <div class="alert alert-info">
                        <a href="../auth/login.php">Log in</a> to write a review.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <div id="notification-container"></div>
    
    <?php include_once '../includes/footer.php'; ?>
    <script src="../js/script.js"></script>
</body>

</html>
