<?php 
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
else if ($_SESSION['is_admin'] == 1) {
    header("Location: 404.html");
}
include_once '../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Name - GreenCart</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="icon" type="image/png" href="img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <section class="product-details">
        <div class="container">
            <div class="product-details-product-container">
                <div class="product-details-product-images">
                    <div class="product-details-main-image">
                        <img src="../img/Black rose.jpg" alt="Product Name">
                    </div>
                </div>
                <div class="product-details-product-info">
        
                    <h1>Product Name</h1>
                    
                    <div class="product-details-product-rating">
                        <span class="stars">★★★★★</span>
                    </div>
                    
                    <div class="product-details-product-price">$99.99</div>
                    
                    <div class="product-details-product-description">
                        <p>Product description goes here.</p>
                    </div>
                    
                    <div class="product-details-stock-status">
                        <span class="in-stock">In Stock (10)</span>
                    </div>
                    
                    <form class="add-to-cart-form">
                        <div class="quantity-selector">
                            <button type="button" class="quantity-btn decrease">-</button>
                            <input type="number" class="quantity-input" value="1" min="1" max="10">
                            <button type="button" class="quantity-btn increase">+</button>
                        </div>
                        <button type="submit" class="btn btn-primary btn-add-cart">Add to Cart</button>
                    </form>
                    
                    <div class="product-meta">
                        <div class="meta-item">
                            <span class="meta-label">Category:</span>
                            <a href="shop.html?category=CategoryName">Category Name</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php include_once '../includes/footer.php'; ?>
    <script src="../js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {           
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
