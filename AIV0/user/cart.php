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

// Function to get user cart
function getUserCart($pdo, $userId) {
    // Check if user has a cart
    $sql = "SELECT * FROM cart WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    $cart = $stmt->fetch();
    
    // If no cart exists, create one
    if (!$cart) {
        $sql = "INSERT INTO cart (user_id) VALUES (?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        $cartId = $pdo->lastInsertId();
    } else {
        $cartId = $cart['cart_id'];
    }
    
    // Get cart items
    $sql = "SELECT ci.*, p.name, p.price, p.image FROM cart_items ci 
            JOIN products p ON ci.product_id = p.product_id 
            WHERE ci.cart_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$cartId]);
    $cartItems = $stmt->fetchAll();
    
    return [
        'cart_id' => $cartId,
        'items' => $cartItems
    ];
}

// Function to add item to cart
function addToCart($pdo, $userId, $productId, $quantity = 1) {
    // Get user cart
    $cart = getUserCart($pdo, $userId);
    $cartId = $cart['cart_id'];
    
    // Check if product already in cart
    $sql = "SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$cartId, $productId]);
    $cartItem = $stmt->fetch();
    
    if ($cartItem) {
        // Update quantity
        $sql = "UPDATE cart_items SET quantity = quantity + ? WHERE cart_item_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$quantity, $cartItem['cart_item_id']]);
    } else {
        // Add new item
        $sql = "INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$cartId, $productId, $quantity]);
    }
    
    return true;
}

// Function to update cart item quantity
function updateCartItemQuantity($pdo, $cartItemId, $quantity) {
    $sql = "UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$quantity, $cartItemId]);
    return true;
}

// Function to remove item from cart
function removeFromCart($pdo, $cartItemId) {
    $sql = "DELETE FROM cart_items WHERE cart_item_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$cartItemId]);
    return true;
}

// Function to calculate cart total
function calculateCartTotal($cartItems) {
    $total = 0;
    foreach ($cartItems as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
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

// Handle cart actions
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isLoggedIn()) {
        // Store intended action in session
        $_SESSION['cart_action'] = $_POST;
        redirect('login.php?redirect=cart.php');
    }
    
    $action = isset($_POST['action']) ? sanitize($_POST['action']) : '';
    
    switch ($action) {
        case 'add':
            $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            
            if ($productId > 0 && $quantity > 0) {
                addToCart($pdo, $_SESSION['user_id'], $productId, $quantity);
                $message = displaySuccess('Product added to cart successfully!');
            }
            break;
            
        case 'update':
            $cartItemId = isset($_POST['cart_item_id']) ? (int)$_POST['cart_item_id'] : 0;
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            
            if ($cartItemId > 0 && $quantity > 0) {
                updateCartItemQuantity($pdo, $cartItemId, $quantity);
                $message = displaySuccess('Cart updated successfully!');
            }
            break;
            
        case 'remove':
            $cartItemId = isset($_POST['cart_item_id']) ? (int)$_POST['cart_item_id'] : 0;
            
            if ($cartItemId > 0) {
                removeFromCart($pdo, $cartItemId);
                $message = displaySuccess('Item removed from cart successfully!');
            }
            break;
    }
}

// Get user cart
$cart = isLoggedIn() ? getUserCart($pdo, $_SESSION['user_id']) : ['items' => []];
$cartItems = $cart['items'];
$subtotal = calculateCartTotal($cartItems);
$shipping = 5.99; // Fixed shipping cost
$tax = $subtotal * 0.07; // 7% tax rate
$total = $subtotal + $shipping + $tax;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - GreenCart</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="cart-styles.css">
    <link rel="icon" type="image/png" href="img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include_once 'includes/header.php'; ?>

    <div class="page-container">
        <!-- Cart Section -->
        <section class="cart-section">
            <div class="container">
                <h1 class="page-title">Your Shopping Cart</h1>
                
                <?php echo $message; ?>
                
                <?php if (empty($cartItems)): ?>
                <div id="empty-cart">
                    <div class="empty-cart-message">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                        <h2>Your cart is empty</h2>
                        <p>Looks like you haven't added any plants to your cart yet.</p>
                        <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
                    </div>
                </div>
                <?php else: ?>
                <div id="cart-content">
                    <div class="cart-container" id="cart-container">
                        <div class="cart-header">
                            <div class="product-col">Product</div>
                            <div class="price-col">Price</div>
                            <div class="quantity-col">Quantity</div>
                            <div class="total-col">Total</div>
                            <div class="remove-col"></div>
                        </div>
                        <div class="cart-items" id="cart-items">
                            <?php foreach ($cartItems as $item): ?>
                            <div class="cart-item">
                                <div class="cart-product-info">
                                    <div class="cart-product-image">
                                        <img src="img/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                                    </div>
                                    <div class="cart-product-details">
                                        <h3><?php echo $item['name']; ?></h3>
                                    </div>
                                </div>
                                <div class="price-col" data-label="Price">$<?php echo number_format($item['price'], 2); ?></div>
                                <div class="quantity-col" data-label="Quantity">
                                    <form method="POST" action="cart.php" class="quantity-form">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                                        <div class="quantity-selector">
                                            <button type="button" class="quantity-btn decrease" data-id="<?php echo $item['cart_item_id']; ?>">-</button>
                                            <input type="number" name="quantity" class="quantity-input" value="<?php echo $item['quantity']; ?>" min="1" data-id="<?php echo $item['cart_item_id']; ?>">
                                            <button type="button" class="quantity-btn increase" data-id="<?php echo $item['cart_item_id']; ?>">+</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="total-col" data-label="Total">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                                <form method="POST" action="cart.php" class="remove-form">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                                    <button type="submit" class="remove-btn" data-id="<?php echo $item['cart_item_id']; ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                    </button>
                                </form>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="cart-actions">
                            <div class="coupon">
                                <input type="text" id="coupon-code" placeholder="Coupon code">
                                <button id="apply-coupon" class="btn btn-outline">Apply Coupon</button>
                            </div>
                            <button id="update-cart" class="btn btn-outline">Update Cart</button>
                        </div>
                    </div>
                    
                    <div class="cart-summary">
                        <h3>Cart Totals</h3>
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span id="cart-subtotal">$<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping</span>
                            <span id="cart-shipping">$<?php echo number_format($shipping, 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Tax (7%)</span>
                            <span id="cart-tax">$<?php echo number_format($tax, 2); ?></span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span>
                            <span id="cart-total">$<?php echo number_format($total, 2); ?></span>
                        </div>
                        <button id="checkout-btn" class="btn btn-primary checkout-btn">Proceed to Checkout</button>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Checkout Section (Hidden by default) -->
        <section id="checkout-section" class="checkout-section" style="display: none;">
            <div class="container">
                <h1 class="page-title">Checkout</h1>
                
                <div class="checkout-container">
                    <div class="checkout-form-container">
                        <h2>Billing Details</h2>
                        <form id="checkout-form" method="POST" action="order-confirmation.php">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="first_name">First Name *</label>
                                    <input type="text" id="first_name" name="first_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="last_name">Last Name *</label>
                                    <input type="text" id="last_name" name="last_name" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Phone *</label>
                                <input type="tel" id="phone" name="phone" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="address">Address *</label>
                                <input type="text" id="address" name="address" required>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="city">City *</label>
                                    <input type="text" id="city" name="city" required>
                                </div>
                                <div class="form-group">
                                    <label for="state">State *</label>
                                    <input type="text" id="state" name="state" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="notes">Order Notes (optional)</label>
                                <textarea id="notes" name="notes" rows="4"></textarea>
                            </div>
                            
                            <h2>Payment Method</h2>
                            <div class="payment-methods">
                                <div class="payment-method">
                                    <input type="radio" id="payment-esewa" name="payment-method" value="esewa" checked>
                                    <label for="payment-esewa">eSewa</label>
                                </div>
                                <div class="payment-method">
                                    <input type="radio" id="payment-cod" name="payment-method" value="cod">
                                    <label for="payment-cod">Cash on Delivery</label>
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="button" id="back-to-cart" class="btn btn-outline">Back to Cart</button>
                                <button type="submit" class="btn btn-primary">Place Order</button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="order-summary">
                        <h2>Order Summary</h2>
                        <div id="checkout-items" class="checkout-items">
                            <?php foreach ($cartItems as $item): ?>
                            <div class="checkout-item">
                                <div class="checkout-item-name">
                                    <?php echo $item['name']; ?>
                                    <span class="checkout-item-quantity">x<?php echo $item['quantity']; ?></span>
                                </div>
                                <div class="checkout-item-price">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="checkout-totals">
                            <div class="checkout-total-row">
                                <span>Subtotal</span>
                                <span id="checkout-subtotal">$<?php echo number_format($subtotal, 2); ?></span>
                            </div>
                            <div class="checkout-total-row">
                                <span>Shipping</span>
                                <span id="checkout-shipping">$<?php echo number_format($shipping, 2); ?></span>
                            </div>
                            <div class="checkout-total-row">
                                <span>Tax (7%)</span>
                                <span id="checkout-tax">$<?php echo number_format($tax, 2); ?></span>
                            </div>
                            <div class="checkout-total-row total">
                                <span>Total</span>
                                <span id="checkout-total">$<?php echo number_format($total, 2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Order Confirmation Section (Hidden by default) -->
        <section id="confirmation-section" class="confirmation-section" style="display: none;">
            <div class="container">
                <div class="confirmation-container">
                    <div class="confirmation-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    </div>
                    <h1>Thank You For Your Order!</h1>
                    <p>Your order has been placed successfully. We've sent a confirmation email to <span id="confirmation-email">your email</span>.</p>
                    
                    <div class="order-details">
                        <h2>Order Details</h2>
                        <div class="order-info">
                            <div class="order-info-row">
                                <span>Order Number:</span>
                                <span id="order-number">ORD-123456</span>
                            </div>
                            <div class="order-info-row">
                                <span>Date:</span>
                                <span id="order-date">March 9, 2025</span>
                            </div>
                            <div class="order-info-row">
                                <span>Total:</span>
                                <span id="order-total">$<?php echo number_format($total, 2); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="confirmation-actions">
                        <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php include_once 'includes/footer.php'; ?>
    <script src="script.js"></script>
</body>
</html>