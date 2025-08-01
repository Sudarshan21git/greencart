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

// Include database connection
include_once '../database/database.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
    $user_id = $_SESSION['user_id'];

    // Validate data
    if ($product_id <= 0 || $rating < 1 || $rating > 5 || empty($comment)) {
        $_SESSION['error_message'] = "Invalid review data. Please try again.";
        header("Location: ../product-details.php?id=$product_id");
        exit();
    }

    // Check if user has purchased this product
    $purchase_query = "SELECT COUNT(*) as purchase_count 
                      FROM orders o 
                      JOIN order_items oi ON o.order_id = oi.order_id 
                      WHERE o.user_id = ? AND oi.product_id = ? AND o.status = 'delivered'";
    $purchase_stmt = mysqli_prepare($conn, $purchase_query);
    mysqli_stmt_bind_param($purchase_stmt, "ii", $user_id, $product_id);
    mysqli_stmt_execute($purchase_stmt);
    $purchase_result = mysqli_stmt_get_result($purchase_stmt);
    $purchase_data = mysqli_fetch_assoc($purchase_result);

    if ($purchase_data['purchase_count'] <= 0) {
        $_SESSION['error_message'] = "You can only review products you have purchased.";
        header("Location: ../product-details.php?id=$product_id");
        exit();
    }

    // Check if user has already reviewed this product
    $check_review_query = "SELECT review_id FROM reviews WHERE user_id = ? AND product_id = ?";
    $check_review_stmt = mysqli_prepare($conn, $check_review_query);
    mysqli_stmt_bind_param($check_review_stmt, "ii", $user_id, $product_id);
    mysqli_stmt_execute($check_review_stmt);
    mysqli_stmt_store_result($check_review_stmt);

    if (mysqli_stmt_num_rows($check_review_stmt) > 0) {
        $_SESSION['error_message'] = "You have already reviewed this product.";
        header("Location: ../user/product-details.php?id=$product_id");
        exit();
    }

    // Insert review into database
    $insert_query = "INSERT INTO reviews (user_id, product_id, rating, message) VALUES (?, ?, ?, ?)";
    $insert_stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($insert_stmt, "iiis", $user_id, $product_id, $rating, $comment);

    if (mysqli_stmt_execute($insert_stmt)) {
        $_SESSION['success_message'] = "Your review has been submitted successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to submit your review. Please try again.";
    }

    // Redirect back to product page
    header("Location: ../user/product-details.php?id=$product_id");
    exit();
} else {
    // If not a POST request, redirect to home
    header("Location: ../index.php");
    exit();
}
