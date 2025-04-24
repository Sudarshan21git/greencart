<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include '../database/database.php';

// Check if this is an AJAX request
if (isset($_POST['ajax_update']) && $_POST['ajax_update'] == '1') {
    $response = array('success' => false);
    
    // Get the item ID and quantity
    $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
    
    if ($item_id > 0 && $quantity > 0) {
        // Get the product ID from cart_items
        $item_query = "SELECT ci.product_id FROM cart_items ci WHERE ci.cart_item_id = ?";
        $item_stmt = mysqli_prepare($conn, $item_query);
        mysqli_stmt_bind_param($item_stmt, "i", $item_id);
        mysqli_stmt_execute($item_stmt);
        $item_result = mysqli_stmt_get_result($item_stmt);
        
        if ($item_data = mysqli_fetch_assoc($item_result)) {
            $product_id = $item_data['product_id'];
            
            // Check stock quantity
            $stock_query = "SELECT stock_quantity FROM products WHERE product_id = ?";
            $stock_stmt = mysqli_prepare($conn, $stock_query);
            mysqli_stmt_bind_param($stock_stmt, "i", $product_id);
            mysqli_stmt_execute($stock_stmt);
            $stock_result = mysqli_stmt_get_result($stock_stmt);
            
            if ($stock_data = mysqli_fetch_assoc($stock_result)) {
                $stock_quantity = $stock_data['stock_quantity'];
                
                // Check if requested quantity exceeds stock
                if ($quantity > $stock_quantity) {
                    $response['success'] = false;
                    $response['message'] = "Cannot update quantity. Only {$stock_quantity} items in stock.";
                    $response['stock_error'] = true;
                    $response['max_stock'] = $stock_quantity;
                } else {
                    // Update the quantity in the database
                    $update_query = "UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?";
                    $update_stmt = mysqli_prepare($conn, $update_query);
                    mysqli_stmt_bind_param($update_stmt, "ii", $quantity, $item_id);
                    
                    if (mysqli_stmt_execute($update_stmt)) {
                        $response['success'] = true;
                        $response['message'] = "Quantity updated successfully.";
                    } else {
                        $response['message'] = "Failed to update quantity in database.";
                    }
                }
            } else {
                $response['message'] = "Product not found.";
            }
        } else {
            $response['message'] = "Cart item not found.";
        }
    } else {
        $response['message'] = "Invalid item ID or quantity.";
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// If not an AJAX request, redirect back to cart
header("Location: ../user/cart.php");
exit;
?>
