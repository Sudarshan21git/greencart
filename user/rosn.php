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

// Fetch product details
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

// Fetch category
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

// Get rating summary
$ratingSummary = getProductRatingSummary($product_id);
$averageRating = round($ratingSummary['average'] ?? 0, 1);
$reviewCount = $ratingSummary['count'] ?? 0;

// Get product reviews
$reviews = getProductReviews($product_id, 5);

// Rating functions
function getProductRatingSummary($product_id) {
    global $conn;
    $query = "SELECT AVG(rating) as average, COUNT(*) as count 
              FROM reviews 
              WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getProductReviews($product_id, $limit = 5) {
    global $conn;
    $query ="SELECT r.review_id, r.rating, r.message, r.created_at, 
    CONCAT(u.first_name, ' ', u.last_name) as user_name
FROM reviews r
JOIN users u ON r.user_id = u.user_id
WHERE r.product_id = ?
ORDER BY r.created_at DESC
LIMIT ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $product_id, $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function renderStars($rating) {
    $fullStars = floor($rating);
    $hasHalfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
    
    $html = '';
    for ($i = 0; $i < $fullStars; $i++) {
        $html .= '<i class="fas fa-star text-warning"></i>';
    }
    if ($hasHalfStar) {
        $html .= '<i class="fas fa-star-half-alt text-warning"></i>';
    }
    for ($i = 0; $i < $emptyStars; $i++) {
        $html .= '<i class="far fa-star text-warning"></i>';
    }
    return $html;
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

// Calculate average rating
$avg_rating_query = "SELECT AVG(rating) as avg_rating, COUNT(*) as review_count 
                    FROM reviews 
                    WHERE product_id = ?";
$avg_rating_stmt = $conn->prepare($avg_rating_query);
$avg_rating_stmt->bind_param("i", $product_id);
$avg_rating_stmt->execute();
$avg_rating_result = $avg_rating_stmt->get_result();
$rating_data = $avg_rating_result->fetch_assoc();

$avg_rating = $rating_data['avg_rating'] ? round($rating_data['avg_rating'], 1) : 0;
$review_count = $rating_data['review_count'];

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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .product-details-container {
            margin-top: 30px;
            margin-bottom: 50px;
        }
        .product-image {
            max-height: 500px;
            object-fit: contain;
            width: 100%;
        }
        .product-title {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .price {
            font-size: 1.8rem;
            font-weight: 700;
            color: #28a745;
            margin: 20px 0;
        }
        .stock-status {
            font-weight: 500;
            margin-bottom: 20px;
        }
        .in-stock {
            color: #28a745;
        }
        .low-stock {
            color: #ffc107;
        }
        .out-of-stock {
            color: #dc3545;
        }
        .quantity-selector {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .quantity-input {
            width: 60px;
            text-align: center;
            margin: 0 10px;
        }
        .reviews-section {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }
        .review-item {
            padding: 20px 0;
            border-bottom: 1px solid #eee;
        }
        .review-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .review-user {
            font-weight: 600;
            margin-right: 15px;
        }
        .review-date {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .review-message {
            line-height: 1.6;
            color: #495057;
        }
        .rating-display {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .average-rating {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0 10px;
        }
        .review-count {
            color: #6c757d;
        }
    </style>
</head>

<body>
    

    <div class="container product-details-container">
        <div class="row">
            <!-- Product Images -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <img src="../img/<?php echo $product['image'] ?>" class="card-img-top product-image" alt="<?php echo $product['name'] ?>">
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-md-6">
                <h1 class="product-title"><?php echo $product['name'] ?></h1>
                
                <!-- Rating Display -->
                <div class="rating-display">
                    <div class="me-2">
                        <?php echo renderStars($averageRating); ?>
                    </div>
                    <span class="average-rating"><?php echo $averageRating ?></span>
                    <span class="review-count">(<?php echo $reviewCount ?> reviews)</span>
                </div>

                <div class="price">Rs.<?php echo number_format($product['price']) ?></div>

                <div class="product-description mb-4">
                    <p><?php echo $product['description'] ?></p>
                </div>

                <!-- Stock Status -->
                <?php if ($product['stock_quantity'] > 0): ?>
                    <div class="stock-status <?php echo $product['stock_quantity'] <= 5 ? 'low-stock' : 'in-stock' ?>">
                        <?php if ($product['stock_quantity'] <= 5): ?>
                            <i class="fas fa-exclamation-circle"></i> Only <?php echo $product['stock_quantity'] ?> left in stock!
                        <?php else: ?>
                            <i class="fas fa-check-circle"></i> In Stock (<?php echo $product['stock_quantity'] ?> available)
                        <?php endif; ?>
                    </div>

                    <!-- Quantity Selector -->
                    <div class="quantity-selector mb-4">
    <button type="button" class="btn btn-outline-secondary quantity-btn decrease">-</button>
    <input type="number" class="form-control quantity-input" value="1" min="1" max="<?php echo $product['stock_quantity'] ?>">
    <button type="button" class="btn btn-outline-secondary quantity-btn increase" >+</button>
</div>


                    <div class="alert alert-warning stock-warning" id="stock-warning" style="display: none;">
                        Maximum available: <?php echo $product['stock_quantity'] ?>
                    </div>

                    <button type="button" class="btn btn-success btn-lg btn-add-cart" data-productid="<?php echo $product['product_id'] ?>">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>

                <?php else: ?>
                    <div class="stock-status out-of-stock mb-4">
                        <i class="fas fa-times-circle"></i> Out of Stock
                    </div>
                    <button type="button" class="btn btn-secondary btn-lg" disabled>
                        <i class="fas fa-shopping-cart"></i> Out of Stock
                    </button>
                <?php endif; ?>

                <!-- Product Meta -->
                <div class="product-meta mt-4">
                    <div class="d-flex align-items-center mb-2">
                        <span class="fw-bold me-2">Category:</span>
                        <a href="../shop.php?category=<?php echo $category_id; ?>" class="text-decoration-none">
                            <?php echo $category['name']; ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>

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
                    <div class="reviews-average"><?php echo $avg_rating; ?></div>
                    <div>
                        <div class="reviews-stars">
                            <?php
                            // Display stars based on average rating
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $avg_rating) {
                                    echo '<span class="stars">★</span>';
                                } else if ($i - 0.5 <= $avg_rating) {
                                    echo '<span class="stars half">★</span>';
                                } else {
                                    echo '<span class="stars">☆</span>';
                                }
                            }
                            ?>
                        </div>
                        <div class="reviews-count">Based on <?php echo $review_count; ?> reviews</div>
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
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $review['rating']) {
                                                echo '<span class="star">★</span>';
                                            } else {
                                                echo '<span class="star">☆</span>';
                                            }
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

    </div>
    
    <div id="notification-container"></div>
    
    <?php include_once '../includes/footer.php'; ?>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/script.js"></script>
    
    <script>
        // Quantity selector functionality
        document.querySelectorAll('.quantity-btn').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.parentNode.querySelector('.quantity-input');
                let value = parseInt(input.value);
                
                if (this.classList.contains('decrease')) {
                    if (value > 1) {
                        input.value = value - 1;
                    }
                } else if (this.classList.contains('increase')) {
                    const max = parseInt(input.max);
                    if (value < max) {
                        input.value = value + 1;
                    } else {
                        document.getElementById('stock-warning').style.display = 'block';
                        setTimeout(() => {
                            document.getElementById('stock-warning').style.display = 'none';
                        }, 3000);
                    }
                }
            });
        });
    </script>
</body>

</html>