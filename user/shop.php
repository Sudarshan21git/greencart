<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - GreenCart</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="icon" type="image/png" href="../img/logo.png">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
     <?php include_once '../includes/header.php'; ?>

    <!-- Shop Filters -->
    <section class="shop-filters">
        <div class="container">
            <div class="filters-container">
                <div class="filter-group">
                    <label for="category-filter">Category</label>
                    <select id="category-filter" class="filter-select">
                        <option value="all">All Categories</option>
                        <option value="indoor">Indoor Plants</option>
                        <option value="outdoor">Outdoor Plants</option>
                        <option value="succulents">Succulents</option>
                        <option value="tools">Gardening Tools</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="sort-filter">Sort By</label>
                    <select id="sort-filter" class="filter-select">
                        <option value="featured">Featured</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                        <option value="name-asc">Name: A to Z</option>
                        <option value="name-desc">Name: Z to A</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="price-range">Price Range</label>
                    <div class="price-range-container">
                        <input type="range" id="price-range" min="0" max="100" value="100" class="price-slider">
                        <div class="price-values">
                            <span id="min-price">$0</span>
                            <span id="max-price">$100</span>
                        </div>
                    </div>
                </div>
                <div class="filter-group search-group">
                    <input type="text" id="search-filter" placeholder="Search products...">
                    <button class="search-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Shop Products -->
    <section class="shop-products">
        <div class="container">
            <div class="products-grid">
                <div class="product-card">
                    <div class="product-image">
                        <img src="img/flower.jpg" alt="Monstera Deliciosa">
                    </div>
                    <div class="product-info">
                        <h3>Monstera Deliciosa</h3>
                        <div class="product-rating">
                            <span class="stars">★★★★★</span>
                            <span class="reviews">(124)</span>
                        </div>
                        <div class="product-price">$39.99</div>
                        <button class="btn btn-add-cart">Add to Cart</button>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-image">
                        <img src="img/Black rose.jpg" alt="Snake Plant">
                        </div>
                    <div class="product-info">
                        <h3>Snake Plant</h3>
                        <div class="product-rating">
                            <span class="stars">★★★★☆</span>
                            <span class="reviews">(86)</span>
                        </div>
                        <div class="product-price">$24.99</div>
                        <button class="btn btn-add-cart">Add to Cart</button>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://placehold.co/300x300/e2f5e2/1a4d1a?text=Peace+Lily" alt="Peace Lily">
                    </div>
                    <div class="product-info">
                        <h3>Peace Lily</h3>
                        <div class="product-rating">
                            <span class="stars">★★★★★</span>
                            <span class="reviews">(102)</span>
                        </div>
                        <div class="product-price">$29.99</div>
                        <button class="btn btn-add-cart">Add to Cart</button>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://placehold.co/300x300/e2f5e2/1a4d1a?text=Fiddle+Leaf" alt="Fiddle Leaf Fig">
                    </div>
                    <div class="product-info">
                        <h3>Fiddle Leaf Fig</h3>
                        <div class="product-rating">
                            <span class="stars">★★★★☆</span>
                            <span class="reviews">(78)</span>
                        </div>
                        <div class="product-price">$49.99</div>
                        <button class="btn btn-add-cart">Add to Cart</button>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://placehold.co/300x300/e2f5e2/1a4d1a?text=Pothos" alt="Pothos">
                    </div>
                    <div class="product-info">
                        <h3>Pothos</h3>
                        <div class="product-rating">
                            <span class="stars">★★★★★</span>
                            <span class="reviews">(95)</span>
                        </div>
                        <div class="product-price">$19.99</div>
                        <button class="btn btn-add-cart">Add to Cart</button>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://placehold.co/300x300/e2f5e2/1a4d1a?text=ZZ+Plant" alt="ZZ Plant">
                    </div>
                    <div class="product-info">
                        <h3>ZZ Plant</h3>
                        <div class="product-rating">
                            <span class="stars">★★★★☆</span>
                            <span class="reviews">(67)</span>
                        </div>
                        <div class="product-price">$34.99</div>
                        <button class="btn btn-add-cart">Add to Cart</button>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://placehold.co/300x300/e2f5e2/1a4d1a?text=Aloe+Vera" alt="Aloe Vera">
                    </div>
                    <div class="product-info">
                        <h3>Aloe Vera</h3>
                        <div class="product-rating">
                            <span class="stars">★★★★★</span>
                            <span class="reviews">(112)</span>
                        </div>
                        <div class="product-price">$22.99</div>
                        <button class="btn btn-add-cart">Add to Cart</button>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://placehold.co/300x300/e2f5e2/1a4d1a?text=Rubber+Plant" alt="Rubber Plant">
                    </div>
                    <div class="product-info">
                        <h3>Rubber Plant</h3>
                        <div class="product-rating">
                            <span class="stars">★★★★☆</span>
                            <span class="reviews">(54)</span>
                        </div>
                        <div class="product-price">$32.99</div>
                        <button class="btn btn-add-cart">Add to Cart</button>
                        </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <button class="pagination-btn active">1</button>
                <button class="pagination-btn">2</button>
                <button class="pagination-btn">3</button>
                <button class="pagination-btn pagination-next">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </div>
        </div>
    </section>

    <!-- Footer -->
<?php include_once '../includes/footer.php'; ?>
    <!-- Scripts -->
    <script src="../js/script.js"></script>
</body>
</html>

