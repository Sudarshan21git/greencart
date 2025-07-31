<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include "../database/database.php";

// Initialize recommended products array
$recommended_products = [];

// Get recommendations if user is logged in
if (isset($_SESSION['user_id']) && (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1)) {
    $user_id = $_SESSION['user_id'];

    // Get recommendations from Python script
    $python_url = 'http://localhost:5000/recommendations';
    $data = array('user_id' => $user_id);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $python_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); // 3 seconds timeout for connection
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); // 5 seconds timeout for the request
    $response = curl_exec($ch);

    // Check if the request was successful
    if ($response !== false) {
        $result = json_decode($response, true);  // true returns associative array

        if ($result && !empty($result)) {
            $product_ids = array_keys($result);

            if (!empty($product_ids)) {
                $ids_str = implode(',', $product_ids);
                $sql = "SELECT product_id, name, image, price, rating, review_count, 
                       (SELECT COUNT(*) FROM reviews WHERE reviews.product_id = products.product_id) as reviews 
                       FROM products WHERE product_id IN ($ids_str)";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $recommended_products[] = $row;
                    }
                }
            }
        }
    }
    curl_close($ch);
}

// If we have fewer than 2 recommended products, get featured products instead
if (count($recommended_products) < 2) {
    $featured_sql = "SELECT product_id, name, image, price, rating, review_count,
                    (SELECT COUNT(*) FROM reviews WHERE reviews.product_id = products.product_id) as reviews 
                    FROM products WHERE is_featured = 1 LIMIT 4";
    $featured_result = $conn->query($featured_sql);

    if ($featured_result && $featured_result->num_rows > 0) {
        $recommended_products = []; // Clear any existing recommendations
        while ($row = $featured_result->fetch_assoc()) {
            $recommended_products[] = $row;
        }
    }
}

// Get categories for the categories section
$query = "SELECT * FROM categories";
$categories_result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GreenCart - Online Nursery Platform</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="icon" type="image/png" href="../img/logo.png">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Header -->
    <?php include_once "../includes/header.php"; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Bring Nature Home</h1>
            <p>Discover our handpicked collection of beautiful plants to transform your space</p>
            <div class="hero-buttons">
                <a href="shop.php" class="btn btn-primary">Shop Now</a>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories">
        <div class="container">
            <h2 class="section-title">Shop by Category</h2>
            <div class="categories-grid">
                <?php while ($row = mysqli_fetch_assoc($categories_result)) { ?>
                    <div class="category-card">
                        <img src="../img/<?php echo $row['image']; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        <div class="category-content">
                            <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                            <a href="category_products.php?category_id=<?php echo $row['category_id']; ?>" class="category-link">
                                View Collection
                            </a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <!-- Recommended Products Section -->
    <section class="products">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php echo (count($recommended_products) < 2) ? 'Featured Products' : 'Recommended for You'; ?></h2>
            </div>
            <div class="products-slider">
                <div class="slider-container">
                    <?php foreach ($recommended_products as $product): ?>
                        <div class="product-card">
                            <a href="product-details.php?id=<?= $product['product_id'] ?>">
                                <div class="product-image">
                                    <img src="../img/<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                                </div>
                            </a>
                            <div class="product-info">
                                <h3><?= htmlspecialchars($product['name']) ?></h3>
                                <div class="product-rating">
                                    <?php
                                    switch (true) {
                                        case ($product['rating'] >= 5):
                                            echo '<span class="stars">★★★★★</span>';
                                            break;
                                        case ($product['rating'] >= 4):
                                            echo '<span class="stars">★★★★☆</span>';
                                            break;
                                        case ($product['rating'] >= 3):
                                            echo '<span class="stars">★★★☆☆</span>';
                                            break;
                                        case ($product['rating'] >= 2):
                                            echo '<span class="stars">★★☆☆☆</span>';
                                            break;
                                        case ($product['rating'] >= 1):
                                            echo '<span class="stars">★☆☆☆☆</span>';
                                            break;
                                        default:
                                            echo 'No rating';
                                    }
                                    ?>
                                    <span class="reviews-count">(<?php echo $product['review_count']; ?> reviews)</span>
                                </div>
                                <div class="product-price">Rs.<?= number_format($product['price'], 2) ?></div>
                                <button class="btn btn-add-cart" data-productid="<?php echo $product['product_id'] ?>">Add to Cart</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="benefits">
        <div class="container">
            <h2 class="section-title">Why Choose Us</h2>
            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>
                        </svg>
                    </div>
                    <h3>Healthy Plants</h3>
                    <p>All our plants are grown with care and love to ensure they thrive in your home.</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="3" width="15" height="13"></rect>
                            <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                            <circle cx="5.5" cy="18.5" r="2.5"></circle>
                            <circle cx="18.5" cy="18.5" r="2.5"></circle>
                        </svg>
                    </div>
                    <h3>Fast Delivery</h3>
                    <p>We ensure your plants arrive quickly and safely with our specialized packaging.</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                    </div>
                    <h3>Plant Guarantee</h3>
                    <p>Our 30-day guarantee ensures your plants arrive healthy or we'll replace them.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials">
        <div class="container">
            <h2 class="section-title">What Our Customers Say</h2>
            <div class="testimonials-slider">
                <div class="testimonial-card active">
                    <div class="testimonial-rating">★★★★★</div>
                    <p class="testimonial-text">"The plants I ordered arrived in perfect condition. They've been thriving in my apartment and I've already placed another order!"</p>
                    <div class="testimonial-author">
                        <img src="https://placehold.co/60x60/e2f5e2/1a4d1a?text=SJ" alt="Sarah Johnson">
                        <div class="author-info">
                            <h4>Sarah Johnson</h4>
                            <p>New York, NY</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-rating">★★★★★</div>
                    <p class="testimonial-text">"As a first-time plant parent, I appreciated the detailed care instructions. My snake plant is doing great!"</p>
                    <div class="testimonial-author">
                        <img src="https://placehold.co/60x60/e2f5e2/1a4d1a?text=MC" alt="Michael Chen">
                        <div class="author-info">
                            <h4>Michael Chen</h4>
                            <p>San Francisco, CA</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-rating">★★★★★</div>
                    <p class="testimonial-text">"Fast shipping and excellent customer service. The team helped me choose the perfect plants for my low-light apartment."</p>
                    <div class="testimonial-author">
                        <img src="https://placehold.co/60x60/e2f5e2/1a4d1a?text=ER" alt="Emily Rodriguez">
                        <div class="author-info">
                            <h4>Emily Rodriguez</h4>
                            <p>Chicago, IL</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="testimonial-controls">
                <div class="testimonial-dots">
                    <button class="dot active" data-index="0"></button>
                    <button class="dot" data-index="1"></button>
                    <button class="dot" data-index="2"></button>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include_once "../includes/footer.php"; ?>

</body>
<script src="../js/script.js"></script>

</html>