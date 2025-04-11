<?php
session_start();
$userId = $_SESSION['user_id'];

require_once '../database/database.php'; // Ensure this file contains your database connection

// Handle AJAX quantity updates
if (isset($_POST['ajax_update']) && isset($_POST['item_id']) && isset($_POST['quantity'])) {
    $item_id = mysqli_real_escape_string($conn, $_POST['item_id']);
    $quantity = (int)$_POST['quantity'];
    
    if ($quantity > 0) {
        $update_query = "UPDATE cart_items SET quantity = $quantity WHERE cart_item_id = $item_id";
        $update_result = mysqli_query($conn, $update_query);
        
        if ($update_result) {
            // Get updated price
            $price_query = "SELECT ci.quantity, p.price 
                           FROM cart_items ci 
                           JOIN products p ON ci.product_id = p.product_id 
                           WHERE ci.cart_item_id = $item_id";
            $price_result = mysqli_query($conn, $price_query);
            
            if ($price_result && mysqli_num_rows($price_result) > 0) {
                $item_data = mysqli_fetch_assoc($price_result);
                $item_total = $item_data['quantity'] * $item_data['price'];
                
                // Get cart total
                $total_query = "SELECT SUM(ci.quantity * p.price) as cart_total 
                               FROM cart_items ci 
                               JOIN products p ON ci.product_id = p.product_id 
                               JOIN cart c ON ci.cart_id = c.cart_id 
                               WHERE c.user_id = $userId";
                $total_result = mysqli_query($conn, $total_query);
                $total_data = mysqli_fetch_assoc($total_result);
                
                echo json_encode([
                    'success' => true, 
                    'item_total' => $item_total,
                    'cart_total' => $total_data['cart_total']
                ]);
                exit();
            }
        }
    }
    
    echo json_encode(['success' => false, 'message' => 'Failed to update cart']);
    exit();
}