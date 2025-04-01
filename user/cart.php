<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../database/database.php';
$cart_items = [];

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Fetch the cart for the user
    $qry = "SELECT * FROM cart WHERE user_id=$userId";
    $result = mysqli_query($conn, $qry);

    if ($result && mysqli_num_rows($result) > 0) {
        $cartData = mysqli_fetch_assoc($result);
        $cart_id = $cartData['cart_id'];

        // Fetch cart items
        $qry2 = "SELECT ci.*, p.name, p.price, p.image 
                 FROM cart_items ci
                 JOIN products p ON ci.product_id = p.product_id
                 WHERE ci.cart_id=$cart_id";
        $result2 = mysqli_query($conn, $qry2);
        
        while ($item = mysqli_fetch_assoc($result2)) {
            $cart_items[] = $item;
        }
    }
}

if (isset($_POST['update_update_btn'])) {
   $update_value = $_POST['update_quantity'];
   $update_id = $_POST['update_quantity_id'];
   mysqli_query($conn, "UPDATE `cart` SET quantity = '$update_value' WHERE id = '$update_id'");
   header('location:cart.php');
}

if (isset($_GET['remove'])) {
   $remove_id = $_GET['remove'];
   mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$remove_id'");
   header('location:cart.php');
}

if (isset($_GET['delete_all'])) {
   mysqli_query($conn, "DELETE FROM `cart` WHERE userid=$userId");
   header('location:cart.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - GreenCart</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="icon" type="image/png" href="img/logo.png">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <?php include_once '../includes/header.php'; ?>


    <!-- Cart Section -->
    <section class="cart-section">
        <div class="container">
            <h1 class="section-title">Your Shopping Cart</h1>
            
            <div class="cart-container" id="cart-container">
                
                <?php if(empty($cart_items)): ?>
                <!-- Empty cart message (shown when cart is empty) -->
                <div class="empty-cart" id="empty-cart">
                    <div class="empty-cart-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                    </div>
                    <h2>Your cart is empty</h2>
                    <p>Looks like you haven't added any plants to your cart yet.</p>
                    <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
                </div>
                <?php else: ?>
                <!-- Cart items (hidden when cart is empty) -->
                <div class="cart-content" id="cart-content">
                    <div class="cart-header">
                        <div class="cart-header-item product-col">Product</div>
                        <div class="cart-header-item price-col">Price</div>
                        <div class="cart-header-item quantity-col">Quantity</div>
                        <div class="cart-header-item total-col">Total</div>
                        <div class="cart-header-item remove-col"></div>
                    </div>

                    <div class="cart-items" id="cart-items">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="cart-item">
                                <div class="cart-product-info">
                                    <div class="cart-product-image">
                                        <img src="img/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                                    </div>
                                    <div class="cart-product-details">
                                        <h3><?php echo $item['name']; ?></h3>
                                    </div>
                                </div>
                                <div class="price-col" data-label="Price">Rs.<?php echo number_format($item['price'], 2); ?></div>
                                <div class="quantity-col" data-label="Quantity">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                                        <div class="quantity-selector">
                                            <button type="button" class="quantity-btn decrease" data-id="<?php echo $item['cart_item_id']; ?>">-</button>
                                            <input type="number" name="quantity" class="quantity-input" value="<?php echo $item['quantity']; ?>" min="1" data-id="<?php echo $item['cart_item_id']; ?>">
                                            <button type="button" class="quantity-btn increase" data-id="<?php echo $item['cart_item_id']; ?>">+</button>
                                        </div>
                                    
                                </div>
                                <div class="total-col" data-label="Total">Rs.<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                                <form method="POST" action="cart.php" class="remove-form">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                                    <button type="submit" class="remove-btn" data-id="<?php echo $item['cart_item_id']; ?> ">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                    </button>
                                </form>
                            </div>
                            <?php endforeach; ?>
                    </div>

                    <div class="cart-actions">
                        <div class="coupon-container">
                            <!-- to make update button align right -->
                        </div>
                        <button class="btn btn-secondary" id="update-cart">Update Cart</button>
                    </div>

                    <div class="cart-summary">
                        <h3>Cart Totals</h3>
                        <div class="summary-row total">
                            <span>Total</span>
                            <span id="cart-total">Rs.0.00</span>
                        </div>
                        <button class="btn btn-primary btn-checkout" id="checkout-btn">Proceed to Checkout</button>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Checkout Section (initially hidden) -->
    <section class="checkout-section" id="checkout-section" style="display: none;">
        <div class="container">
            <h1 class="section-title">Checkout</h1>
            
            <div class="checkout-container">
                <div class="checkout-form-container">
                    <h2>Billing Details</h2>
                    <form id="checkout-form" class="checkout-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first-name">First Name</label>
                                <input type="text" id="first-name" name="first-name" required>
                            </div>
                            <div class="form-group">
                                <label for="last-name">Last Name</label>
                                <input type="text" id="last-name" name="last-name" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Street Address</label>
                            <input type="text" id="address" name="address" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" id="city" name="city" required>
                            </div>
                            <div class="form-group">
                                <label for="state">State/Province</label>
                                <input type="text" id="state" name="state" required>
                            </div>
                        </div>
                        <!-- <div class="form-row">
                            <div class="form-group">
                                <label for="zip">Postal/Zip Code</label>
                                <input type="text" id="zip" name="zip" required>
                            </div>
                            <div class="form-group">
                                <label for="country">Country</label>
                                <select id="country" name="country" required>
                                    <option value="">Select Country</option>
                                    <option value="US">United States</option>
                                    <option value="CA">Canada</option>
                                    <option value="UK">United Kingdom</option>
                                    <option value="AU">Australia</option>
                                    <option value="NP">Nepal</option>
                                    <option value="IN">India</option>
                                </select>
                            </div>
                        </div> -->
                        
                        <h2>Payment Information</h2>
                        <div class="payment-methods">
                            <div class="payment-method">
                                <input type="radio" id="payment-credit" name="payment-method" value="credit" checked>
                                <label for="payment-credit">Esewa</label>
                            </div>
                            <div class="payment-method">
                                <input type="radio" id="payment-paypal" name="payment-method" value="paypal">
                                <label for="payment-paypal">Cash on Delivery</label>
                            </div>
                        </div>
                        
                        <!-- <div id="credit-card-fields">
                            <div class="form-group">
                                <label for="card-number">Phone Number</label>
                                <input type="text" id="card-number" name="card-number" placeholder="XXXX XXXX XXXX XXXX">
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="expiry-date">Expiry Date</label>
                                    <input type="text" id="expiry-date" name="expiry-date" placeholder="MM/YY">
                                </div>
                                <div class="form-group">
                                    <label for="cvv">CVV</label>
                                    <input type="text" id="cvv" name="cvv" placeholder="XXX">
                                </div>
                            </div>
                        </div> -->
                        
                        <div class="checkout-actions">
                            <button type="button" class="btn btn-secondary" id="back-to-cart">Back to Cart</button>
                            <button type="submit" class="btn btn-primary">Place Order</button>
                        </div>
                    </form>
                </div>
                
                <div class="order-summary">
                    <h3>Order Summary</h3>
                    <div id="checkout-items">
                        <!-- Order items will be dynamically added here -->
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span id="checkout-total">$0.00</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Order Confirmation (initially hidden) -->
    <section class="confirmation-section" id="confirmation-section" style="display: none;">
        <div class="container">
            <div class="confirmation-container">
                <div class="confirmation-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
                <h2>Thank You for Your Order!</h2>
                <p>Your order has been placed successfully. We've sent a confirmation email to <span id="confirmation-email"></span>.</p>
                <div class="order-details">
                    <div class="order-detail">
                        <span>Order Number:</span>
                        <span id="order-number"></span>
                    </div>
                    <div class="order-detail">
                        <span>Order Date:</span>
                        <span id="order-date"></span>
                    </div>
                    <div class="order-detail">
                        <span>Order Total:</span>
                        <span id="order-total"></span>
                    </div>
                </div>
                <p>We'll send you another email when your order ships.</p>
                <a href="shop.html" class="btn btn-primary">Continue Shopping</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
<?php include_once '../includes/footer.php'; ?>


</body>    <script src="../js/script.js"></script>
</html>

