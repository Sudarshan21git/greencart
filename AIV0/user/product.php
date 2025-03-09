<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
require_once 'config/database.php';

// Common functions
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function displayError($message) {
    return "<div class='error-message'>$message</div>";
}

function displaySuccess($message) {
    return "<div class='success-message'>$message</div>";
}

// Function to get product by ID
function getProductById($pdo, $productId) {
    $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.category_id 
            WHERE p.product_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$productId]);
    return $stmt->fetch();
}

// Function to get related products
function getRelatedProducts($pdo, $categoryId, $currentProductId, $limit = 4) {
    $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.category_id 
            WHERE p.category_id = ? AND p.product_id != ?
            ORDER BY RAND()
            LIMIT ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$categoryId, $currentProductId, $limit]);
    return $stmt->fetchAll();
}

// Function to get product reviews
function getProductReviews($pdo, $productId) {
    $sql = "SELECT r.*, u.first_name, u.last_name FROM reviews r 
            JOIN users u ON r.user_id = u.user_id 
            WHERE r.product_id = ? 
            ORDER BY r.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$productId]);
    return $stmt->fetchAll();
}

// Function to add a review
function addReview($pdo, $userId, $productId, $rating, $message) {
    // Insert review
    $sql = "INSERT INTO reviews (user_id, product_id, rating, message) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$userId, $productId, $rating, $message]);
    
    if ($result) {
        // Update product rating and review count
        updateProductRating($pdo, $productId);
        return true;
    }
    
    return false;
}

// Function to update product rating
function updateProductRating($pdo, $productId) {
    // Get average rating
    $sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as count FROM reviews WHERE product_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$productId]);
    $result = $stmt->fetch();
    
    // Update product
    $sql = "UPDATE products SET rating = ?, review_count = ? WHERE product_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$result['avg_rating'], $result['count'], $productId]);
    
    return true;
}

// Function to check if user has already reviewed a product
function hasUserReviewed($pdo, $userId, $productId) {
    $sql = "SELECT COUNT(*) as count FROM reviews WHERE user_id = ? AND product_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, $productId]);
    $result = $stmt->fetch();
    
    return $result['count'] > 0;
}

// Get product ID from URL
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// If no product ID or invalid, redirect to shop
if ($productId <= 0) {
    redirect('shop.php');
}

// Get product details
$product = getProductById($pdo, $productId);

// If product not found, redirect to shop
if (!$product) {
    redirect('shop.php');
}

// Get related products
$relatedProducts = getRelatedProducts($pdo, $product['category_id'], $productId);

// Get product reviews
$reviews = getProductReviews($pdo, $productId);

// Handle review submission
$reviewMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_review') {
    if (!isLoggedIn()) {
        redirect('login.php?redirect=product.php?id=' . $productId);
    }
    
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $message = isset($_POST['message']) ? sanitize($_POST['message']) : '';
    
    if ($rating < 1 || $rating > 5) {
        $reviewMessage = displayError('Please select a valid rating (1-5).');
    } elseif (empty($message)) {
        $reviewMessage = displayError('Please enter a review message.');
    } elseif (hasUserReviewed($pdo, $_SESSION['user_id'], $productId)) {
        $reviewMessage = displayError('You have already reviewed this product.');
    } else {
        if (addReview($pdo, $_SESSION['user_id'], $productId, $rating, $message)) {
            $reviewMessage = displaySuccess('Your review has been submitted successfully!');
            // Refresh product and reviews
            $product = getProductById($pdo, $productId);
            $reviews = getProductReviews($pdo, $productId);
        } else {
            $reviewMessage = displayError('There was an error submitting your review. Please try again.');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> - GreenCart</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include_once 'includes/header.php'; ?>

    <!-- Product Details -->
    <section class="product-details">
        <div class="container">
            <div class="product-container">
                <div class="product-images">
                    <div class="main-image">
                        <img src="img/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                    </div>
                </div>
                <div class="product-info">
                    <nav class="breadcrumb">
                        <a href="index.php">Home</a> &gt;
                        <a href="shop.php">Shop</a> &gt;
                        <a href="shop.php?category=<?php echo urlencode($product['category_name']); ?>"><?php echo $product['category_name']; ?></a> &gt;
                        <span><?php echo $product['name']; ?></span>
                    </nav>
                    
                    <h1><?php echo $product['name']; ?></h1>
                    
                    <div class="product-rating">
                        <span class="stars">
                            <?php 
                            $rating = $product['rating'];
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $rating) {
                                    echo '★';
                                } elseif ($i - 0.5 <= $rating) {
                                    echo '★';
                                } else {
                                    echo '☆';
                                }
                            }
                            ?>
                        </span>
                        <span class="reviews">(<?php echo $product['review_count']; ?> reviews)</span>
                    </div>
                    
                    <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                    
                    <div class="product-description">
                        <p><?php echo $product['description']; ?></p>
                    </div>
                    
                    <div class="stock-status">
                        <?php if ($product['stock_quantity'] > 0): ?>
                        <span class="in-stock">In Stock (<?php echo $product['stock_quantity']; ?>)</span>
                        <?php else: ?>
                        <span class="out-of-stock">Out of Stock</span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($product['stock_quantity'] > 0): ?>
                    <form method="POST" action="cart.php" class="add-to-cart-form">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <div class="quantity-selector">
                            <button type="button" class="quantity-btn decrease">-</button>
                            <input type="number" name="quantity" class="quantity-input" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>">
                            <button type="button" class="quantity-btn increase">+</button>
                        </div>
                        <button type="submit" class="btn btn-primary">Add to Cart</button>
                    </form>
                    <?php endif; ?>
                    
                    <div class="product-meta">
                        <div class="meta-item">
                            <span class="meta-label">Category:</span>
                            <a href="shop.php?category=<?php echo urlencode($product['category_name']); ?>"><?php echo $product['category_name']; ?></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product Tabs -->
            <div class="product-tabs">
                <div class="tabs-header">
                    <button class="tab-btn active" data-tab="description">Description</button>
                    <button class="tab-btn" data-tab="reviews">Reviews (<?php echo count($reviews); ?>)</button>
                    <button class="tab-btn" data-tab="care">Care Instructions</button>
                </div>
                <div class="tabs-content">
                    <div class="tab-panel active" id="description">
                        <h3>Product Description</h3>
                        <p><?php echo $product['description']; ?></p>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla facilisi. Sed euismod, nisl vel ultricies lacinia, nisl nisl aliquam nisl, eget aliquam nisl nisl sit amet nisl. Sed euismod, nisl vel ultricies lacinia, nisl nisl aliquam nisl, eget aliquam nisl nisl sit amet nisl.</p>
                    </div>
                    <div class="tab-panel" id="reviews">
                        <h3>Customer Reviews</h3>
                        
                        <?php echo $reviewMessage; ?>
                        
                        <?php if (isLoggedIn() && !hasUserReviewed($pdo, $_SESSION['user_id'], $productId)): ?>
                        <div class="review-form">
                            <h4>Write a Review</h4>
                            <form method="POST" action="product.php?id=<?php echo $productId; ?>">
                                <input type="hidden" name="action" value="add_review">
                                
                                <div class="form-group">
                                    <label>Your Rating</label>
                                    <div class="rating-selector">
                                        <input type="radio" id="star5" name="rating" value="5">
                                        <label for="star5">★</label>
                                        <input type="radio" id="star4" name="rating" value="4">
                                        <label for="star4">★</label>
                                        <input type="radio" id="star3" name="rating" value="3">
                                        <label for="star3">★</label>
                                        <input type="radio" id="star2" name="rating" value="2">
                                        <label for="star2">★</label>
                                        <input type="radio" id="star1" name="rating" value="1">
                                        <label for="star1">★</label>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="message">Your Review</label>
                                    <textarea id="message" name="message" rows="5" required></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Submit Review</button>
                            </form>
                        </div>
                        <?php elseif (!isLoggedIn()): ?>
                        <div class="login-to-review">
                            <p>Please <a href="login.php?redirect=product.php?id=<?php echo $productId; ?>">login</a> to write a review.</p>
                        </div>
                        <?php endif; ?>
                        
                        <div class="reviews-list">
                            <?php if (empty($reviews)): ?>
                            <p>No reviews yet. Be the first to review this product!</p>
                            <?php else: ?>
                            <?php foreach ($reviews as $review): ?>
                            <div class="review-item">
                                <div class="review-header">
                                    <div class="reviewer-name"><?php echo $review['first_name'] . ' ' . $review['last_name']; ?></div>
                                    <div class="review-date"><?php echo date('F j, Y', strtotime($review['created_at'])); ?></div>
                                </div>
                                <div class="review-rating">
                                    <?php 
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $review['rating'] ? '★' : '☆';
                                    }
                                    ?>
                                </div>
                                <div class="review-content">
                                    <p><?php echo $review['message']; ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="tab-panel" id="care">
                        <h3>Plant Care Instructions</h3>
                        <div class="care-instructions">
                            <div class="care-item">
                                <div class="care-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v6"></path><path d="M12 18v4"></path><path d="m4.93 10.93 4.24 4.24"></path><path d="m14.83 8.93 4.24-4.24"></path><path d="m14.83 14.83 4.24 4.24"></path><path d="m4.93 12.93 4.24-4.24"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                </div>
                                <h4>Light</h4>
                                <p>Place in bright, indirect sunlight. Avoid direct sun which can burn the leaves.</p>
                            </div>
                            <div class="care-item">
                                <div class="care-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c4.97 0 9-2.69 9-6s-4.03-6-9-6-9 2.69-9 6 4.03 6 9 6z"></path><path d="M12 16v-6"></path><path d="M9 3h6l3 7H6l3-7z"></path></svg>
                                </div>
                                <h4>Water</h4>
                                <p>Water when the top inch of soil feels dry. Ensure good drainage and avoid waterlogging.</p>
                            </div>
                            <div class="care-item">
                                <div class="care-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12h20"></path><path d="M12 2v20"></path></svg>
                                </div>
                                <h4>Humidity</h4>
                                <p>Prefers higher humidity. Mist regularly or place on a pebble tray with water.</p>
                            </div>
                            <div class="care-item">
                                <div class="care-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                </div>
                                <h4>Temperature</h4>
                                <p>Thrives in temperatures between 65-80°F (18-27°C). Avoid cold drafts and sudden temperature changes.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Related Products -->
            <?php if (!empty($relatedProducts)): ?>
            <div class="related-products">
                <h2>You May Also Like</h2>
                <div class="products-grid">
                    <?php foreach ($relatedProducts as $relatedProduct): ?>
                    <div class="product-card" data-product-id="<?php echo $relatedProduct['product_id']; ?>">
                        <div class="product-image">
                            <img src="img/<?php echo $relatedProduct['image']; ?>" alt="<?php echo $relatedProduct['name']; ?>">
                        </div>
                        <div class="product-info">
                            <h3><?php echo $relatedProduct['name']; ?></h3>
                            <div class="product-rating">
                                <span class="stars">
                                    <?php 
                                    $rating = $relatedProduct['rating'];
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $rating) {
                                            echo '★';
                                        } elseif ($i - 0.5 <= $rating) {
                                            echo '★';
                                        } else {
                                            echo '☆';
                                        }
                                    }
                                    ?>
                                </span>
                                <span class="reviews">(<?php echo $relatedProduct['review_count']; ?>)</span>
                            </div>
                            <div class="product-price">$<?php echo number_format($relatedProduct['price'], 2); ?></div>
                            <form method="POST" action="cart.php">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $relatedProduct['product_id']; ?>">
                                <input  value="<?php echo $relatedProduct['product_id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-add-cart">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include_once 'includes/footer.php'; ?>
    
    <script>
        // Tab switching
        document.addEventListener('DOMContentLoaded', function() {
            const tabBtns = document.querySelectorAll('.tab-btn');
            const tabPanels = document.querySelectorAll('.tab-panel');
            
            tabBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Remove active class from all buttons and panels
                    tabBtns.forEach(b => b.classList.remove('active'));
                    tabPanels.forEach(p => p.classList.remove('active'));
                    
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    // Show corresponding panel
                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                });
            });
            
            // Quantity selector
            const decreaseBtn = document.querySelector('.quantity-btn.decrease');
            const increaseBtn = document.querySelector('.quantity-btn.increase');
            const quantityInput = document.querySelector('.quantity-input');
            
            if (decreaseBtn && increaseBtn && quantityInput) {
                decreaseBtn.addEventListener('click', function() {
                    let value = parseInt(quantityInput.value);
                    if (value > 1) {
                        quantityInput.value = value - 1;
                    }
                });
                
                increaseBtn.addEventListener('click', function() {
                    let value = parseInt(quantityInput.value);
                    let max = parseInt(quantityInput.getAttribute('max'));
                    if (value < max) {
                        quantityInput.value = value + 1;
                    }
                });
            }
        });
    </script>
</body>
</html>