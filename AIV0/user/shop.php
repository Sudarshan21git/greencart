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

// Function to get all products
function getAllProducts($pdo, $limit = null, $category = null, $sort = null, $search = null, $maxPrice = null) {
    $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.category_id 
            WHERE 1=1";
    $params = [];
    
    // Add category filter
    if ($category && $category != 'all') {
        $sql .= " AND c.name = ?";
        $params[] = $category;
    }
    
    // Add search filter
    if ($search) {
        $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    // Add price filter
    if ($maxPrice) {
        $sql .= " AND p.price <= ?";
        $params[] = $maxPrice;
    }
    
    // Add sorting
    if ($sort) {
        switch ($sort) {
            case 'price-low':
                $sql .= " ORDER BY p.price ASC";
                break;
            case 'price-high':
                $sql .= " ORDER BY p.price DESC";
                break;
            case 'name-asc':
                $sql .= " ORDER BY p.name ASC";
                break;
            case 'name-desc':
                $sql .= " ORDER BY p.name DESC";
                break;
            default:
                $sql .= " ORDER BY p.is_featured DESC, p.created_at DESC";
        }
    } else {
        $sql .= " ORDER BY p.is_featured DESC, p.created_at DESC";
    }
    
    // Add limit
    if ($limit) {
        $sql .= " LIMIT ?";
        $params[] = $limit;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// Function to get all categories
function getAllCategories($pdo) {
    $sql = "SELECT * FROM categories ORDER BY name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Get filter parameters
$category = isset($_GET['category']) ? sanitize($_GET['category']) : 'all';
$sort = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'featured';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null;

// Get all categories
$categories = getAllCategories($pdo);

// Get filtered products
$products = getAllProducts($pdo, null, $category, $sort, $search, $maxPrice);

// Get highest product price for price slider
$highestPrice = 0;
foreach ($products as $product) {
    if ($product['price'] > $highestPrice) {
        $highestPrice = $product['price'];
    }
}
$roundedMaxPrice = ceil($highestPrice / 10) * 10;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - GreenCart</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include_once 'includes/header.php'; ?>

    <!-- Shop Filters -->
    <section class="shop-filters">
        <div class="container">
            <form id="filter-form" method="GET" action="shop.php" class="filters-container">
                <div class="filter-group">
                    <label for="category-filter">Category</label>
                    <select id="category-filter" name="category" class="filter-select" onchange="this.form.submit()">
                        <option value="all" <?php echo $category == 'all' ? 'selected' : ''; ?>>All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['name']; ?>" <?php echo $category == $cat['name'] ? 'selected' : ''; ?>>
                            <?php echo $cat['name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="sort-filter">Sort By</label>
                    <select id="sort-filter" name="sort" class="filter-select" onchange="this.form.submit()">
                        <option value="featured" <?php echo $sort == 'featured' ? 'selected' : ''; ?>>Featured</option>
                        <option value="price-low" <?php echo $sort == 'price-low' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price-high" <?php echo $sort == 'price-high' ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="name-asc" <?php echo $sort == 'name-asc' ? 'selected' : ''; ?>>Name: A to Z</option>
                        <option value="name-desc" <?php echo $sort == 'name-desc' ? 'selected' : ''; ?>>Name: Z to A</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="price-range">Price Range</label>
                    <div class="price-range-container">
                        <input type="range" id="price-range" name="max_price" min="0" max="<?php echo $roundedMaxPrice; ?>" value="<?php echo $maxPrice ?? $roundedMaxPrice; ?>" class="price-slider">
                        <div class="price-values">
                            <span id="min-price">$0</span>
                            <span id="max-price">$<?php echo $maxPrice ?? $roundedMaxPrice; ?></span>
                        </div>
                    </div>
                </div>
                <div class="filter-group search-group">
                    <input type="text" id="search-filter" name="search" placeholder="Search products..." value="<?php echo $search; ?>">
                    <button type="submit" class="search-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Shop Products -->
    <section class="shop-products">
        <div class="container">
            <div class="products-grid">
                <?php if (empty($products)): ?>
                <div class="no-products">
                    <p>No products found. Try adjusting your filters.</p>
                </div>
                <?php else: ?>
                <?php foreach ($products as $product): ?>
                <div class="product-card" data-product-id="<?php echo $product['product_id']; ?>">
                    <div class="product-image">
                        <img src="img/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
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
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if (count($products) > 0): ?>
            <div class="pagination">
                <button class="pagination-btn active">1</button>
                <button class="pagination-btn">2</button>
                <button class="pagination-btn">3</button>
                <button class="pagination-btn pagination-next">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include_once 'includes/footer.php'; ?>
    <script src="script.js"></script>
</body>
</html>