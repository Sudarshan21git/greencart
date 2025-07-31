<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Initialize error variable and checkout visibility
$error_message = '';
$show_checkout = false;

// Check for order errors from process-order.php
if (isset($_SESSION['order_error'])) {
    $error_message = $_SESSION['order_error'];
    unset($_SESSION['order_error']);
    $show_checkout = true;
}

// Check for validation errors
if (isset($_SESSION['validation_error'])) {
    $error_message = $_SESSION['validation_error'];
    unset($_SESSION['validation_error']);
    $show_checkout = true;
}

// Check if checkout section should be shown
if (isset($_SESSION['show_checkout']) && $_SESSION['show_checkout']) {
    $show_checkout = true;
    unset($_SESSION['show_checkout']);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
else if ($_SESSION['is_admin'] == 1) {
    header("Location: 404.html");
}
include '../database/database.php';
$cart_items = [];
$cart_total = 0;

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Fetch the cart for the user
    $qry = "SELECT * FROM cart WHERE user_id=$userId";
    $result = mysqli_query($conn, $qry);

    if ($result && mysqli_num_rows($result) > 0) {
        $cartData = mysqli_fetch_assoc($result);
        $cart_id = $cartData['cart_id'];

        // Fetch cart items with stock information
        $qry2 = "SELECT ci.*, p.name, p.price, p.image, p.stock_quantity 
                 FROM cart_items ci
                 JOIN products p ON ci.product_id = p.product_id
                 WHERE ci.cart_id=$cart_id";
        $result2 = mysqli_query($conn, $qry2);
        
        if ($result2) {
            while ($item = mysqli_fetch_assoc($result2)) {
                $cart_items[] = $item;
                $cart_total += ($item['price'] * $item['quantity']);
            }
        }
    }
}

// Handle item removal
if (isset($_POST['remove-btn'])) {
   $remove_id = mysqli_real_escape_string($conn, $_POST['cart_item_id-remove']);
   $removeResult = mysqli_query($conn, "DELETE FROM `cart_items` WHERE cart_item_id = '$remove_id'");
   if(!$removeResult) {
       echo "Error removing item from cart: " . mysqli_error($conn);
   }
   header('location:cart.php');
   exit();
}

// Get saved checkout data if available
$checkout_data = isset($_SESSION['checkout_data']) ? $_SESSION['checkout_data'] : [
    'first_name' => isset($_SESSION['fname']) ? $_SESSION['fname'] : '',
    'last_name' => isset($_SESSION['lname']) ? $_SESSION['lname'] : '',
    'email' => isset($_SESSION['email']) ? $_SESSION['email'] : '',
    'phone' => isset($_SESSION['phone']) ? $_SESSION['phone'] : '',
    'address' => isset($_SESSION['address']) ? $_SESSION['address'] : '',
    'payment_method' => 'esewa'
];

?>  

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - GreenCart</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/cart-styles.css">
    <link rel="icon" type="image/png" href="../img/logo.png">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .stock-warning {
            color: #f44336;
            font-size: 0.85rem;
            margin-top: 5px;
            display: none;
        }
        
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 4px;
            color: white;
            font-weight: 500;
            z-index: 1000;
            opacity: 0;
            transform: translateY(-20px);
            transition: opacity 0.3s, transform 0.3s;
            max-width: 300px;
        }
        
        .notification.success {
            background-color: #4CAF50;
        }
        
        .notification.error {
            background-color: #f44336;
        }
        
        .notification.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        .error-message {
            color: red;
            font-size: 0.8em;
            margin-top: 5px;
            display: block;
        }
    </style>
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
                <!-- Cart items (hidden when cart is empty) cart items-->
                <div class="cart-content" id="cart-content">
                    <form method="POST" action="cart.php" id="cart-form">
                        <div class="cart-header">
                            <div class="cart-header-item product-col">Product</div>
                            <div class="cart-header-item price-col">Price</div>
                            <div class="cart-header-item quantity-col">Quantity</div>
                            <div class="cart-header-item total-col">Total</div>
                            <div class="cart-header-item remove-col"></div>
                        </div>

                        <div class="cart-items" id="cart-items">
                            <?php foreach ($cart_items as $item): ?>
                                <div class="cart-item" data-id="<?php echo $item['cart_item_id']; ?>">
                                    <div class="cart-product-info">
                                        <div class="cart-product-image">
                                            <img src="../img/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                                        </div>
                                        <div class="cart-product-details">
                                            <h3><?php echo $item['name']; ?></h3>
                                            <?php if ($item['stock_quantity'] <= 5): ?>
                                                <small style="color: #ff9800;">Only <?php echo $item['stock_quantity']; ?> left in stock</small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="price-col" data-label="Price">Rs.<?php echo number_format($item['price']); ?></div>
                                    <div class="quantity-col" data-label="Quantity">
                                        <div class="quantity-selector">
                                            <button type="button" class="quantity-btn decrease" data-id="<?php echo $item['cart_item_id']; ?>">-</button>
                                            <input type="number" name="quantities[<?php echo $item['cart_item_id']; ?>]" class="quantity-input" 
                                                value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock_quantity']; ?>" 
                                                data-id="<?php echo $item['cart_item_id']; ?>" 
                                                data-price="<?php echo $item['price']; ?>"
                                                data-max="<?php echo $item['stock_quantity']; ?>">
                                            <button type="button" class="quantity-btn increase" data-id="<?php echo $item['cart_item_id']; ?>">+</button>
                                        </div>
                                        <div class="stock-warning" id="stock-warning-<?php echo $item['cart_item_id']; ?>">
                                            Maximum available: <?php echo $item['stock_quantity']; ?>
                                        </div>
                                    </div>
                                    <div class="total-col" data-label="Total">Rs.<span class="item-total"><?php echo number_format($item['price'] * $item['quantity']); ?></span></div>
                                    <div class="remove-col">
                                        <form method="POST" action="cart.php" class="remove-form">
                                            <input type="hidden" name="cart_item_id-remove" value="<?php echo $item['cart_item_id']; ?>">
                                            <button type="submit" class="remove-btn" name="remove-btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </form>

                    <div class="cart-summary">
                        <h3>Cart Totals</h3>
                        <div class="summary-row shipping">
                            <span>Shipping</span>
                            <span>Free</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span>
                            <span id="cart-total">Rs.<?php echo number_format($cart_total); ?></span>
                        </div>
                        <button class="btn btn-primary btn-checkout" id="checkout-btn">Proceed to Checkout</button>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <!-- Checkout Section (initially hidden unless there are errors) -->
    <section class="checkout-section" id="checkout-section" style="display: <?php echo $show_checkout ? 'block' : 'none'; ?>;">
        <div class="container">
            <h1 class="section-title">Checkout</h1>
            
            <div class="checkout-container">
                <div class="checkout-form-container">
                    <h2>Billing Details</h2>
                    <form id="checkout-form" class="checkout-form" method="POST" action="../functions/process-order.php" onsubmit="return validateCheckoutForm()">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first-name">First Name</label>
                                <input type="text" id="first-name" name="first_name" value="<?php echo htmlspecialchars($checkout_data['first_name']); ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="last-name">Last Name</label>
                                <input type="text" id="last-name" name="last_name" value="<?php echo htmlspecialchars($checkout_data['last_name']); ?>" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($checkout_data['email']); ?>" readonly>
                            <small class="error-message" id="email-error"></small>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($checkout_data['phone']); ?>" >
                            <small class="error-message" id="phone-error"></small>
                        </div>
                        <div class="form-group">
                            <label for="address">Street Address</label>
                            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($checkout_data['address']); ?>" >
                        </div>
                        
                        <h2>Payment Information</h2>
                        <div class="payment-methods">
                            <div class="payment-method">
                                <input type="radio" id="payment-esewa" name="payment_method" value="esewa" <?php echo ($checkout_data['payment_method'] === 'esewa' || empty($checkout_data['payment_method'])) ? 'checked' : ''; ?>>
                                <label for="payment-esewa">Esewa</label>
                            </div>
                            <div class="payment-method">
                                <input type="radio" id="payment-cod" name="payment_method" value="cod" <?php echo ($checkout_data['payment_method'] === 'cod') ? 'checked' : ''; ?>>
                                <label for="payment-cod">Cash on Delivery</label>
                            </div>
                        </div>
                        
                        <div class="checkout-actions">
                            <button type="button" class="btn btn-secondary" id="back-to-cart">Back to Cart</button>
                            <button type="submit" class="btn btn-primary">Place Order</button>
                        </div>
                    </form>
                </div>
                
                <div class="order-summary">
                    <h3>Order Summary</h3>
                    <div id="checkout-items">
                        <?php foreach ($cart_items as $item): ?>
                        <div class="checkout-item">
                            <div class="checkout-item-info">
                                <span class="checkout-item-quantity"><?php echo htmlspecialchars($item['quantity']); ?> ×</span>
                                <span class="checkout-item-name"><?php echo htmlspecialchars($item['name']); ?></span>
                            </div>
                            <span class="checkout-item-price">Rs.<?php echo number_format($item['price'] * $item['quantity']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="summary-row subtotal">
                        <span>Subtotal</span>
                        <span>Rs.<?php echo number_format($cart_total); ?></span>
                    </div>
                    <div class="summary-row shipping">
                        <span>Shipping</span>
                        <span>Free</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span id="checkout-total">Rs.<?php echo number_format($cart_total); ?></span>
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
                <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include_once '../includes/footer.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Calculate cart total
        function calculateCartTotal() {
            let total = 0;
            document.querySelectorAll('.cart-item').forEach(item => {
                const quantity = parseInt(item.querySelector('.quantity-input').value);
                const price = parseFloat(item.querySelector('.quantity-input').getAttribute('data-price'));
                total += quantity * price;
            });
            return total;
        }
        
        // Update item total
        function updateItemTotal(itemId, quantity) {
            const item = document.querySelector(`.cart-item[data-id="${itemId}"]`);
            const price = parseFloat(item.querySelector('.quantity-input').getAttribute('data-price'));
            const itemTotal = price * quantity;
            item.querySelector('.item-total').textContent = itemTotal.toLocaleString();
            
            // Update cart total
            const cartTotal = calculateCartTotal()
            document.getElementById('cart-total').textContent = 'Rs.' + cartTotal.toLocaleString();
            
            // Update checkout total if visible
            if (document.getElementById('checkout-total')) {
                document.getElementById('checkout-total').textContent = 'Rs.' + cartTotal.toLocaleString();
            }
        }
        
        // Show notification function
        function showNotification(message, type) {
            // Remove any existing notifications
            const existingNotifications = document.querySelectorAll('.notification');
            existingNotifications.forEach(notification => {
                notification.remove();
            });
            
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            
            // Add to document
            document.body.appendChild(notification);
            
            // Show notification
            setTimeout(() => {
                notification.classList.add('show');
            }, 10);
            
            // Hide and remove after 5 seconds
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 5000);
        }
        
        // Handle quantity decrease button
        const decreaseButtons = document.querySelectorAll('.quantity-btn.decrease');
        decreaseButtons.forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.getAttribute('data-id');
                const input = document.querySelector(`.quantity-input[data-id="${itemId}"]`);
                let value = parseInt(input.value);
                if (value > 1) {
                    value--;
                    input.value = value;
                    updateItemTotal(itemId, value);
                    updateQuantityInDatabase(itemId, value);
                    
                    // Hide stock warning if it's visible
                    const stockWarning = document.getElementById(`stock-warning-${itemId}`);
                    if (stockWarning) {
                        stockWarning.style.display = 'none';
                    }
                }
            });
        });
        
        // Handle quantity increase button
        const increaseButtons = document.querySelectorAll('.quantity-btn.increase');
        increaseButtons.forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.getAttribute('data-id');
                const input = document.querySelector(`.quantity-input[data-id="${itemId}"]`);
                let value = parseInt(input.value);
                const maxStock = parseInt(input.getAttribute('data-max'));
                
                if (value < maxStock) {
                    value++;
                    input.value = value;
                    updateItemTotal(itemId, value);
                    updateQuantityInDatabase(itemId, value);
                    
                    // Show stock warning if reaching max
                    if (value >= maxStock) {
                        const stockWarning = document.getElementById(`stock-warning-${itemId}`);
                        if (stockWarning) {
                            stockWarning.style.display = 'block';
                        }
                    }
                } else {
                    // Show stock warning
                    const stockWarning = document.getElementById(`stock-warning-${itemId}`);
                    if (stockWarning) {
                        stockWarning.style.display = 'block';
                    }
                    showNotification(`Sorry, only ${maxStock} items available`, 'error');
                }
            });
        });
        
        // Handle quantity input change
        const quantityInputs = document.querySelectorAll('.quantity-input');
        quantityInputs.forEach(input => {
            input.addEventListener('change', function() {
                const itemId = this.getAttribute('data-id');
                let value = parseInt(this.value);
                const maxStock = parseInt(this.getAttribute('data-max'));
                
                if (isNaN(value) || value < 1) {
                    value = 1;
                    this.value = value;
                } else if (value > maxStock) {
                    value = maxStock;
                    this.value = value;
                    
                    // Show stock warning
                    const stockWarning = document.getElementById(`stock-warning-${itemId}`);
                    if (stockWarning) {
                        stockWarning.style.display = 'block';
                    }
                    showNotification(`Sorry, only ${maxStock} items available`, 'error');
                } else {
                    // Hide stock warning
                    const stockWarning = document.getElementById(`stock-warning-${itemId}`);
                    if (stockWarning) {
                        stockWarning.style.display = 'none';
                    }
                }
                
                updateItemTotal(itemId, value);
                updateQuantityInDatabase(itemId, value);
            });
        });
        
        // Update quantity in database via AJAX
        function updateQuantityInDatabase(itemId, quantity) {
            const formData = new FormData();
            formData.append('ajax_update', '1');
            formData.append('item_id', itemId);
            formData.append('quantity', quantity);
            
            fetch('../functions/updateCart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update was successful
                    console.log('Quantity updated successfully');
                } else {
                    console.error('Failed to update quantity:', data.message);
                    
                    // If there's a max_quantity in the response, update the input
                    if (data.max_quantity) {
                        const input = document.querySelector(`.quantity-input[data-id="${itemId}"]`);
                        input.value = data.max_quantity;
                        updateItemTotal(itemId, data.max_quantity);
                        showNotification(data.message, 'error');
                    }
                }
            })
            .catch(error => {
                console.error('Error updating quantity:', error);
            });
        }
        
        // Checkout button
        const checkoutBtn = document.getElementById('checkout-btn');
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', function() {
                document.getElementById('checkout-section').style.display = 'block';
                document.getElementById('checkout-section').scrollIntoView({ behavior: 'smooth' });
            });
        }
        
        // Back to cart button
        const backToCartBtn = document.getElementById('back-to-cart');
        if (backToCartBtn) {
            backToCartBtn.addEventListener('click', function() {
                document.getElementById('checkout-section').style.display = 'none';
                document.querySelector('.cart-section').scrollIntoView({ behavior: 'smooth' });
            });
        }
        
        // Function to show error messages
        function showNotification(message, type) {
            // Remove any existing notifications
            const existingNotifications = document.querySelectorAll('.notification');
            existingNotifications.forEach(notification => {
                notification.remove();
            });
            
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            
            // Add to document
            document.body.appendChild(notification);
            
            // Show notification
            setTimeout(() => {
                notification.classList.add('show');
            }, 10);
            
            // Hide and remove after 5 seconds
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 5000);
        }

        // Display error message if it exists
        <?php if (!empty($error_message)): ?>
        document.addEventListener('DOMContentLoaded', function() {
            showNotification("<?php echo addslashes($error_message); ?>", 'error');
    
            // Make sure checkout section is visible if there's an error
            document.getElementById('checkout-section').style.display = 'block';
            document.getElementById('checkout-section').scrollIntoView({ behavior: 'smooth' });
        });
        <?php endif; ?>
    });
    
    // Form validation function
    function validateCheckoutForm() {
        let isValid = true;
        const firstName = document.getElementById('first-name').value.trim();
        const lastName = document.getElementById('last-name').value.trim();
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const address = document.getElementById('address').value.trim();
        
        // Reset error messages
        document.getElementById('email-error').textContent = '';
        document.getElementById('phone-error').textContent = '';
        
        // Check for empty fields
        if (!firstName || !lastName || !email || !phone || !address) {
            promptMessage('Please fill all the fields', false);
            isValid = false;
            return false;
        }
        
        // Validate email (must be Gmail)
        const emailRegex = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;
        if (!emailRegex.test(email)) {
            document.getElementById('email-error').textContent = 'Invalid email format or not a Gmail address!';
            promptMessage('Invalid email format or not a Gmail address!', false);
            isValid = false;
            return false;
        }
        
        // Validate phone number (must start with 97 or 98 and be 10 digits)
        const phoneRegex = /^(98|97)\d{8}$/;
        if (!phoneRegex.test(phone)) {
            document.getElementById('phone-error').textContent = 'Enter a valid phone number!';
            promptMessage('Enter a valid phone number!', false);
            isValid = false;
            return false;
        }
        
        return isValid;
    }

    // Function to show alert message similar to contact.php
    function promptMessage(msg, success = false) {
        // Create a modal overlay
        const overlay = document.createElement("div");
        overlay.style.position = "fixed";
        overlay.style.top = "0";
        overlay.style.left = "0";
        overlay.style.width = "100%";
        overlay.style.height = "100%";
        overlay.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
        overlay.style.display = "flex";
        overlay.style.justifyContent = "center";
        overlay.style.alignItems = "center";
        overlay.style.zIndex = "1000";

        // Create the modal content
        const modal = document.createElement("div");
        modal.style.backgroundColor = "white";
        modal.style.padding = "20px";
        modal.style.borderRadius = "8px";
        modal.style.boxShadow = "0 2px 10px rgba(0, 0, 0, 0.2)";
        modal.style.width = "300px";
        modal.style.maxWidth = "90%";
        modal.style.textAlign = "center"; // Center text

        // Create the title
        const title = document.createElement("h3");
        title.textContent = msg;
        title.style.marginTop = "0";
        title.style.marginBottom = "15px";
        title.style.color = success ? "green" : "red";

        // Create the buttons container
        const buttonContainer = document.createElement("div");
        buttonContainer.style.display = "flex";
        buttonContainer.style.justifyContent = "space-between";
        buttonContainer.style.width = "100%";
        buttonContainer.style.gap = "10px"; // Adds some space between the buttons

        // Create the 'Okay' button
        const button = document.createElement("button");
        button.textContent = "Okay";
        button.style.backgroundColor = "#128C7E";
        button.style.color = "white";
        button.style.border = "none";
        button.style.padding = "6px 12px"; // Smaller padding
        button.style.borderRadius = "4px";
        button.style.cursor = "pointer";
        button.style.flex = "1"; // Take equal space with the other button

        button.onclick = function() {
            overlay.remove();
        };

        buttonContainer.appendChild(button); // Add to button container

        // Add the title and buttons container to the modal
        modal.appendChild(title);
        modal.appendChild(buttonContainer);
        overlay.appendChild(modal);

        // Add the modal to the body
        document.body.appendChild(overlay);
    }
    </script>
</body>
</html>
