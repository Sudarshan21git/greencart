<?php
session_start();
require_once '../database/database.php'; // Ensure this file contains your database connection

header('Content-Type: application/json'); // Set header to return JSON

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "You must be logged in to add items to the cart", "redirect" => "../auth/login.php"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if ($product_id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid product ID"]);
    exit();
}

// Check if user has an active cart
$cart_query = "SELECT cart_id FROM cart WHERE user_id = ? LIMIT 1";
$stmt = $conn->prepare($cart_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_result = $stmt->get_result();
$cart = $cart_result->fetch_assoc();

if (!$cart) {
    // Create a new cart for the user
    $insert_cart = "INSERT INTO cart (user_id, created_at, updated_at) VALUES (?, NOW(), NOW())";
    $stmt = $conn->prepare($insert_cart);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_id = $stmt->insert_id;
} else {
    $cart_id = $cart['cart_id'];
}

// Check if product already exists in the cart
$item_query = "SELECT cart_item_id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ?";
$stmt = $conn->prepare($item_query);
$stmt->bind_param("ii", $cart_id, $product_id);
$stmt->execute();
$item_result = $stmt->get_result();
$item = $item_result->fetch_assoc();

if ($item) {
    // Update quantity
    $new_quantity = $item['quantity'] + 1;
    $update_item = "UPDATE cart_items SET quantity = ?, updated_at = NOW() WHERE cart_item_id = ?";
    $stmt = $conn->prepare($update_item);
    $stmt->bind_param("ii", $new_quantity, $item['cart_item_id']);
    $stmt->execute();
} else {
    // Insert new item into cart
    $insert_item = "INSERT INTO cart_items (cart_id, product_id, quantity, created_at, updated_at) VALUES (?, ?, 1, NOW(), NOW())";
    $stmt = $conn->prepare($insert_item);
    $stmt->bind_param("ii", $cart_id, $product_id);
    $stmt->execute();
}

// Fetch the updated cart count
$count_query = "SELECT SUM(quantity) as total_items FROM cart_items WHERE cart_id = ?";
$stmt = $conn->prepare($count_query);
$stmt->bind_param("i", $cart_id);
$stmt->execute();
$count_result = $stmt->get_result();
$total_items = $count_result->fetch_assoc()['total_items'];

echo json_encode(["success" => true, "message" => "Product added to cart", "cartCount" => $total_items]);
exit();
?>
