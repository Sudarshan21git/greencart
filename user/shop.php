<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
else if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
    header("Location: 404.html");
}
// Include the database connection
include_once '../includes/header.php';
include '../database/database.php';

// Number of products per page
$products_per_page = 8;

// Get the current page from the URL, default to page 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = $page <= 0 ? 1 : $page;

// Calculate the OFFSET for the current page
$offset = ($page - 1) * $products_per_page;

// Get filter, sort, and search values from the URL
$category_filter = isset($_GET['category']) ? $_GET['category'] : 'all';
$sort_filter = isset($_GET['sort']) ? $_GET['sort'] : 'featured';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Build the SQL query to fetch product data and join with the categories table
$query = "SELECT p.*, c.name AS category_name FROM products p
          JOIN categories c ON p.category_id = c.category_id
          WHERE 1";

// Apply search filter
if ($search_query != '') {
    $search_query = $conn->real_escape_string($search_query);  // Prevent SQL injection
    $query .= " AND (p.name LIKE '%$search_query%' OR p.description LIKE '%$search_query%')";
}

// Apply category filter if it's not 'all'
if ($category_filter != 'all') {
    $query .= " AND p.category_id = '$category_filter'";
}

// Apply sorting
if ($sort_filter == 'price-low') {
    $query .= " ORDER BY p.price ASC";
} elseif ($sort_filter == 'price-high') {
    $query .= " ORDER BY p.price DESC";
} elseif ($sort_filter == 'name-asc') {
    $query .= " ORDER BY p.name ASC";
} elseif ($sort_filter == 'name-desc') {
    $query .= " ORDER BY p.name DESC";
} else {
    $query .= " ORDER BY p.created_at DESC";
}

// Add LIMIT for pagination
$query .= " LIMIT $products_per_page OFFSET $offset";
$result = $conn->query($query);

// Fetch all the products in an array
$products = [];
if ($result) {
    while ($product = $result->fetch_assoc()) {
        $products[] = $product;
    }
}

// Get the total number of products (for pagination)
$total_products_query = "SELECT COUNT(*) as total FROM products p
                          JOIN categories c ON p.category_id = c.category_id
                          WHERE 1";
if ($search_query != '') {
    $total_products_query .= " AND (p.name LIKE '%$search_query%' OR p.description LIKE '%$search_query%')";
}
if ($category_filter != 'all') {
    $total_products_query .= " AND p.category_id = '$category_filter'";
}
$total_result = $conn->query($total_products_query);
$row = $total_result->fetch_assoc();
$total_products = $row['total'];

// Calculate the total number of pages
$total_pages = ceil($total_products / $products_per_page);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - GreenCart</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <!-- Shop Filters -->
    <section class="shop-filters">
        <div class="container">
            <div class="filters-container">
                <div class="filter-group">
                    <label for="category-filter">Category</label>
                    <form method="GET" action="shop.php">
                        <select name="category" id="category-filter" class="filter-select" onchange="this.form.submit()">
                            <option value="all" <?= $category_filter == 'all' ? 'selected' : ''; ?>>All Categories</option>
                            <?php
                            // Fetch the list of categories
                            $category_query = "SELECT * FROM categories";
                            $category_result = $conn->query($category_query);
                            while ($category = $category_result->fetch_assoc()) {
                                echo '<option value="' . $category['category_id'] . '" ' . ($category_filter == $category['category_id'] ? 'selected' : '') . '>' . htmlspecialchars($category['name']) . '</option>';
                            }
                            ?>
                        </select>
                        <!-- Hidden inputs for sort and search to retain values -->
                        <input type="hidden" name="sort" value="<?= $sort_filter; ?>">
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search_query); ?>">
                    </form>
                </div>
                <div class="filter-group">
                    <label for="sort-filter">Sort By</label>
                    <form method="GET" action="shop.php">
                        <select name="sort" id="sort-filter" class="filter-select" onchange="this.form.submit()">
                            <option value="featured" <?= $sort_filter == 'featured' ? 'selected' : ''; ?>>Featured</option>
                            <option value="price-low" <?= $sort_filter == 'price-low' ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price-high" <?= $sort_filter == 'price-high' ? 'selected' : ''; ?>>Price: High to Low</option>
                            <option value="name-asc" <?= $sort_filter == 'name-asc' ? 'selected' : ''; ?>>Name: A to Z</option>
                            <option value="name-desc" <?= $sort_filter == 'name-desc' ? 'selected' : ''; ?>>Name: Z to A</option>
                        </select>
                        <!-- Hidden inputs for category and search to retain values -->
                        <input type="hidden" name="category" value="<?= $category_filter; ?>">
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search_query); ?>">
                    </form>
                </div>

                <div class="filter-group">
                    <label for="price-range">Price Range</label>
                    <div class="price-range-container">
                        <input type="range" id="price-range" min="0" max="100" value="100" class="price-slider">
                        <div class="price-values">
                            <span id="min-price">Rs.0</span>
                            <span id="max-price">Rs.100</span>
                        </div>
                    </div>
                </div>
                <div class="filter-group search-group">
                    <form method="GET" action="shop.php">
                        <input type="text" id="search-filter" name="search" value="<?= htmlspecialchars($search_query); ?>" placeholder="Search products...">
                        <input type="hidden" name="category" value="<?= $category_filter; ?>">
                        <input type="hidden" name="sort" value="<?= $sort_filter; ?>">
                        <button class="search-btn" type="submit">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </section>

    <!-- Shop Products -->
    <section class="shop-products">
        <div class="container">
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                    <a href="product-details.php?id=<?= $product['product_id']; ?>" class="product-link">
                        <div class="product-image">
                            <img src="../img/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>">
                        </div>
                        </a>
                        <div class="product-info">
                            <h3><?= htmlspecialchars($product['name']); ?></h3>
                            <div class="product-rating">
                                <span class="stars">★★★★★</span>
                                <span class="reviews">(<?= rand(50, 150); ?>)</span>
                            </div>
                            <div class="product-price">Rs.<?php echo number_format($product['price']); ?>
                            </div>
                            <button type="button" class="btn btn-add-cart" data-productid="<?php echo $product['product_id'] ?>">Add to Cart</button>
                            </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1; ?>&category=<?= $category_filter; ?>&sort=<?= $sort_filter; ?>&search=<?= urlencode($search_query); ?>" class="pagination-btn">Prev</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i; ?>&category=<?= $category_filter; ?>&sort=<?= $sort_filter; ?>&search=<?= urlencode($search_query); ?>" class="pagination-btn <?= ($i == $page) ? 'active' : ''; ?>"><?= $i; ?></a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1; ?>&category=<?= $category_filter; ?>&sort=<?= $sort_filter; ?>&search=<?= urlencode($search_query); ?>" class="pagination-btn">Next</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Notification container -->
    <div id="notification-container"></div>

    <?php include_once '../includes/footer.php'; ?>
    <script src="../js/script.js"></script>
</body>

</html>
