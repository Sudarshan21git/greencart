<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
require_once '../database/database.php';

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

// Get featured products

function getFeaturedProducts($pdo) {
    $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.category_id 
            WHERE p.is_featured = 1
            ORDER BY p.created_at DESC
            LIMIT 8";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Get all categories
function getAllCategories($pdo) {
    $sql = "SELECT * FROM categories ORDER BY name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Get featured products for homepage
$featuredProducts = getFeaturedProducts($pdo);

// Get all categories for category section
$categories = getAllCategories($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GreenCart - Your Online Plant Nursery</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include_once '../includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Bring Nature Into Your Home</h1>
                <p>Discover our wide selection of beautiful plants to transform your space.</p>
                <a href="shop.php" class="btn btn-primary">Shop Now</a>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="featured-products">
        <div class="container">
            <h2 class="section-title">Featured Plants</h2>
            <div class="slider-controls">
                <button class="slider-prev">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>
                <button class="slider-next">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </div>
            <div class="slider-container">
                <?php foreach ($featuredProducts as $product): ?>
                <div class="product-card" data-product-id="<?php echo $product['product_id']; ?>">
                    <div class="product-image">
                        <img src="../img/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                    </div>
                    <div class="product-info">
                        <h3><?php echo $product['name']; ?></h3>
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
                            <span class="reviews">(<?php echo $product['review_count']; ?>)</span>
                        </div>
                        <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                        <form method="POST" action="cart.php">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-add-cart">Add to Cart</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="view-all">
                <a href="shop.php" class="btn btn-outline">View All Products</a>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section class="categories">
        <div class="container">
            <h2 class="section-title">Shop by Category</h2>
            <div class="categories-grid">
                <?php foreach ($categories as $category): ?>
                <a href="shop.php?category=<?php echo urlencode($category['name']); ?>" class="category-card">
                    <div class="category-image">
                        <img src="../img/<?php echo $category['image']; ?>" alt="<?php echo $category['name']; ?>">
                    </div>
                    <h3><?php echo $category['name']; ?></h3>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Benefits -->
    <section class="benefits">
        <div class="container">
            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    </div>
                    <h3>Quality Guarantee</h3>
                    <p>We ensure that all our plants are healthy and vibrant when they arrive at your doorstep.</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                    <h3>Fast Delivery</h3>
                    <p>We offer quick and reliable shipping to ensure your plants arrive fresh and on time.</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    </div>
                    <h3>Expert Advice</h3>
                    <p>Our team of plant experts is always ready to help you with care tips and recommendations.</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                    </div>
                    <h3>Satisfaction Guaranteed</h3>
                    <p>If you're not completely satisfied with your purchase, we offer hassle-free returns.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials">
        <div class="container">
            <h2 class="section-title">What Our Customers Say</h2>
            <div class="testimonial-slider">
                <div class="testimonial-card active">
                    <div class="testimonial-content">
                        <p>"I'm absolutely in love with the plants I received from GreenCart. They arrived in perfect condition and have been thriving in my home. The customer service was exceptional too!"</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-image">
                            <img src="../img/testimonial-1.jpg" alt="Sarah Johnson">
                        </div>
                        <div class="author-info">
                            <h4>Sarah Johnson</h4>
                            <p>Plant Enthusiast</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"As a first-time plant parent, I was nervous about buying plants online. GreenCart made it so easy with their detailed care instructions and responsive support team. My snake plant is doing great!"</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-image">
                            <img src="../img/testimonial-2.jpg" alt="Michael Chen">
                        </div>
                        <div class="author-info">
                            <h4>Michael Chen</h4>
                            <p>New Plant Owner</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"I've ordered from many plant shops online, but GreenCart stands out for their quality and packaging. Every plant I've received has been healthy and beautiful. I'll definitely be a repeat customer!"</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-image">
                            <img src="../img/testimonial-3.jpg" alt="Emily Rodriguez">
                        </div>
                        <div class="author-info">
                            <h4>Emily Rodriguez</h4>
                            <p>Interior Designer</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="testimonial-dots">
                <span class="dot active"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>
        </div>
    </section>

    <?php include_once '../includes/footer.php'; ?>
    <script src="script.js"></script>
</body>
</html>