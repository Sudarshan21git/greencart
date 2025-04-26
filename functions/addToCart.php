<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Create a log function for debugging
function logError($message) {
    // Uncomment this line to log errors to a file
    // file_put_contents('../logs/cart_errors.log', date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
    return $message;
}

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please log in to add items to your cart'
    ]);
    exit();
}

// Include database connection
include_once '../database/database.php';

if (!$conn) {
    echo json_encode([
        'success' => false,
        'message' => logError('Database connection failed')
    ]);
    exit();
}

$userId = $_SESSION['user_id'];

// Get product_id and quantity
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

// Validate inputs
if ($product_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => logError('Invalid product ID: ' . $product_id),
        'debug' => $_POST
    ]);
    exit();
}

if ($quantity <= 0) {
    echo json_encode([
        'success' => false,
        'message' => logError('Invalid quantity: ' . $quantity)
    ]);
    exit();
}

// Check product stock
$stock_query = "SELECT stock_quantity, name FROM products WHERE product_id = ?";
$stock_stmt = mysqli_prepare($conn, $stock_query);

if (!$stock_stmt) {
    echo json_encode([
        'success' => false,
        'message' => logError('Failed to prepare stock query: ' . mysqli_error($conn))
    ]);
    exit();
}

mysqli_stmt_bind_param($stock_stmt, "i", $product_id);
mysqli_stmt_execute($stock_stmt);
$stock_result = mysqli_stmt_get_result($stock_stmt);

if (mysqli_num_rows($stock_result) === 0) {
    echo json_encode([
        'success' => false,
        'message' => logError('Product not found: ' . $product_id)
    ]);
    exit();
}

$product = mysqli_fetch_assoc($stock_result);
$available_stock = $product['stock_quantity'];
$product_name = $product['name'];

if ($quantity > $available_stock) {
    echo json_encode([
        'success' => false,
        'message' => "Sorry, only {$available_stock} items of {$product_name} available"
    ]);
    exit();
}

// Check if user already has a cart
$cart_query = "SELECT cart_id FROM cart WHERE user_id = ?";
$cart_stmt = mysqli_prepare($conn, $cart_query);

if (!$cart_stmt) {
    echo json_encode([
        'success' => false,
        'message' => logError('Failed to prepare cart query: ' . mysqli_error($conn))
    ]);
    exit();
}

mysqli_stmt_bind_param($cart_stmt, "i", $userId);
mysqli_stmt_execute($cart_stmt);
$cart_result = mysqli_stmt_get_result($cart_stmt);

if (mysqli_num_rows($cart_result) === 0) {
    // Create a new cart for the user
    $create_cart_query = "INSERT INTO cart (user_id) VALUES (?)";
    $create_cart_stmt = mysqli_prepare($conn, $create_cart_query);
    
    if (!$create_cart_stmt) {
        echo json_encode([
            'success' => false,
            'message' => logError('Failed to prepare create cart query: ' . mysqli_error($conn))
        ]);
        exit();
    }
    
    mysqli_stmt_bind_param($create_cart_stmt, "i", $userId);
    
    if (!mysqli_stmt_execute($create_cart_stmt)) {
        echo json_encode([
            'success' => false,
            'message' => logError('Failed to create cart: ' . mysqli_error($conn))
        ]);
        exit();
    }
    
    $cart_id = mysqli_insert_id($conn);
} else {
    $cart = mysqli_fetch_assoc($cart_result);
    $cart_id = $cart['cart_id'];
}

// Check if the product is already in the cart
$check_item_query = "SELECT cart_item_id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ?";
$check_item_stmt = mysqli_prepare($conn, $check_item_query);

if (!$check_item_stmt) {
    echo json_encode([
        'success' => false,
        'message' => logError('Failed to prepare check item query: ' . mysqli_error($conn))
    ]);
    exit();
}

mysqli_stmt_bind_param($check_item_stmt, "ii", $cart_id, $product_id);
mysqli_stmt_execute($check_item_stmt);
$check_item_result = mysqli_stmt_get_result($check_item_stmt);

if (mysqli_num_rows($check_item_result) > 0) {
    // Update existing cart item
    $cart_item = mysqli_fetch_assoc($check_item_result);
    $new_quantity = $cart_item['quantity'] + $quantity;
    
    // Check if the new total quantity exceeds available stock
    if ($new_quantity > $available_stock) {
        echo json_encode([
            'success' => false,
            'message' => "Cannot add {$quantity} more. You already have {$cart_item['quantity']} in your cart and only {$available_stock} are available."
        ]);
        exit();
    }
    
    $update_query = "UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?";
    $update_stmt = mysqli_prepare($conn, $update_query);
    
    if (!$update_stmt) {
        echo json_encode([
            'success' => false,
            'message' => logError('Failed to prepare update query: ' . mysqli_error($conn))
        ]);
        exit();
    }
    
    mysqli_stmt_bind_param($update_stmt, "ii", $new_quantity, $cart_item['cart_item_id']);
    
    if (mysqli_stmt_execute($update_stmt)) {
        echo json_encode([
            'success' => true,
            'message' => "Cart updated! Added {$quantity} more {$product_name} to your cart."
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => logError('Failed to update cart: ' . mysqli_error($conn))
        ]);
    }
} else {
    // Add new cart item
    $insert_query = "INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)";
    $insert_stmt = mysqli_prepare($conn, $insert_query);
    
    if (!$insert_stmt) {
        echo json_encode([
            'success' => false,
            'message' => logError('Failed to prepare insert query: ' . mysqli_error($conn))
        ]);
        exit();
    }
    
    mysqli_stmt_bind_param($insert_stmt, "iii", $cart_id, $product_id, $quantity);
    
    if (mysqli_stmt_execute($insert_stmt)) {
        echo json_encode([
            'success' => true,
            'message' => "Added {$quantity} {$product_name} to your cart!"
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => logError('Failed to add to cart: ' . mysqli_error($conn))
        ]);
    }
}
