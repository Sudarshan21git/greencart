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
    <?php
    include_once "../includes/header.php";
    ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Bring Nature Home</h1>
            <p>Discover our handpicked collection of beautiful plants to transform your space</p>
            <div class="hero-buttons">
                <a href="#" class="btn btn-primary">Shop Now</a>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories">
        <div class="container">
            <h2 class="section-title">Shop by Category</h2>
            <div class="categories-grid">
                <div class="category-card">
                    <img src="https://placehold.co/300x300/e2f5e2/1a4d1a?text=Indoor+Plants" alt="Indoor Plants">
                    <div class="category-content">
                        <h3>Indoor Plants</h3>
                        <a href="#" class="category-link">View Collection</a>
                    </div>
                </div>
                <div class="category-card">
                    <img src="https://placehold.co/300x300/e2f5e2/1a4d1a?text=Outdoor+Plants" alt="Outdoor Plants">
                    <div class="category-content">
                        <h3>Outdoor Plants</h3>
                        <a href="#" class="category-link">View Collection</a>
                    </div>
                </div>
                <div class="category-card">
                    <img src="https://placehold.co/300x300/e2f5e2/1a4d1a?text=Succulents" alt="Succulents">
                    <div class="category-content">
                        <h3>Succulents</h3>
                        <a href="#" class="category-link">View Collection</a>
                    </div>
                </div>
                <div class="category-card">
                    <img src="https://placehold.co/300x300/e2f5e2/1a4d1a?text=Gardening+Tools" alt="Gardening Tools">
                    <div class="category-content">
                        <h3>Gardening Tools</h3>
                        <a href="#" class="category-link">View Collection</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="products">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Best Sellers</h2>
                <a href="#" class="view-all">View All</a>
            </div>
            <div class="products-slider">
                <div class="slider-container">
                    <div class="product-card">
                        <div class="product-image">
                            <img src="https://placehold.co/300x300/e2f5e2/1a4d1a?text=Monstera" alt="Monstera Deliciosa">
                            <button class="wishlist-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                </svg>
                            </button>
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
                            <img src="https://placehold.co/300x300/e2f5e2/1a4d1a?text=Snake+Plant" alt="Snake Plant">
                            <button class="wishlist-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                </svg>
                            </button>
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
                            <button class="wishlist-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                </svg>
                            </button>
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
                            <button class="wishlist-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                </svg>
                            </button>
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
                </div>
                <div class="slider-controls">
                    <button class="slider-prev">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </button>
                    <button class="slider-next">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </button>
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
            <div class="testimonial-dots">
                <button class="dot active" data-index="0"></button>
                <button class="dot" data-index="1"></button>
                <button class="dot" data-index="2"></button>
            </div>
        </div>
    </section>



    <?php
    include_once "../includes/footer.php";
    ?>
    <script src="../js/script.js"></script>
    </body>

</html>