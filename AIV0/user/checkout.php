<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    // Store current page in session for redirect after login
    $_SESSION['redirect_after_login'] = 'checkout.php';
    redirect('auth/login.php');
}

$userId = $_SESSION['user_id'];
$errors = [];
$success = false;

// Get user cart
$cart = getUserCart($pdo, $userId);
$cartItems = $cart['items'];
$cartTotal = calculateCartTotal($cartItems);

// Check if cart is empty
if (empty($cartItems)) {
    redirect('cart.php');
}

// Get user information
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Process checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $firstName = sanitize($_POST['first-name'] ?? '');
    $lastName = sanitize($_POST['last-name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $state = sanitize($_POST['state'] ?? '');
    $paymentMethod = sanitize($_POST['payment-method'] ?? '');
    
    // Validate form data
    if (empty($firstName)) {
        $errors[] = "First name is required";
    }
    
    if (empty($lastName)) {
        $errors[] = "Last name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($phone)) {
        $errors[] = "Phone number is required";
    }
    
    if (empty($address)) {
        $errors[] = "Address is required";
    }
    
    if (empty($city)) {
        $errors[] = "City is required";
    }
    
    if (empty($state)) {
        $errors[] = "State is required";
    }
    
    if (empty($paymentMethod)) {
        $errors[] = "Payment method is required";
    }
    
    // Process order if no errors
    if (empty($errors)) {
        try {
            $formData = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'city' => $city,
                'state' => $state,
                'payment_method' => $paymentMethod
            ];
            
            $orderId = createOrder($pdo, $userId, $cartItems, $formData);
            
            // Redirect to order confirmation page
            redirect("order-confirmation.php?order_id=$orderId");
        } catch (Exception $e) {
            $errors[] = "Error processing order: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - GreenCart</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include_once 'includes/header.php'; ?>

    <section class="checkout-section">
        <div class="container">
            <h1 class="section-title">Checkout</h1>
            
            <?php if (!empty($errors)): ?>
                <div class="error-container">
                    <?php foreach ($errors as $error): ?>
                        <p class="error-message"><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div class="checkout-container">
                <div class="checkout-form-container">
                    <h2>Billing Details</h2>
                    <form id="checkout-form" class="checkout-form" method="POST" action="checkout.php">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first-name">First Name</label>
                                <input type="text" id="first-name" name="first-name" value="<?php echo $user['first_name'] ?? ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="last-name">Last Name</label>
                                <input type="text" id="last-name" name="last-name" value="<?php echo $user['last_name'] ?? ''; ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" value="<?php echo $user['email'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo $user['phone'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Street Address</label>
                            <input type="text" id="address" name="address" value="<?php echo $user['address'] ?? ''; ?>" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" id="city" name="city" value="<?php echo $user['city'] ?? ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="state">State/Province</label>
                                <input type="text" id="state" name="state" value="<?php echo $user['state'] ?? ''; ?>" required>
                            </div>
                        </div>
                        
                        <h2>Payment Information</h2>
                        <div class="payment-methods">
                            <div class="payment-method">
                                <input type="radio" id="payment-esewa" name="payment-method" value="esewa" checked>
                                <label for="payment-esewa">Esewa</label>
                            </div>
                            <div class="payment-method">
                                <input type="radio" id="payment-cod" name="payment-method" value="cash_on_delivery">
                                <label for="payment-cod">Cash on Delivery</label>
                            </div>
                        </div>
                        
                        <div class="checkout-actions">
                            <a href="cart.php" class="btn btn-secondary">Back to Cart</a>
                            <button type="submit" class="btn btn-primary">Place Order</button>
                        </div>
                    </form>
                </div>
                
                <div class="order-summary">
                    <h3>Order Summary</h3>
                    <div id="checkout-items">
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
                    <div class="summary-row total">
                        <span>Total</span>
                        <span id="checkout-total">$<?php echo number_format($cartTotal, 2); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include_once 'includes/footer.php'; ?>
    <script src="script.js"></script>
</body>
</html>