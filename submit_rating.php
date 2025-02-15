<?php
session_start();
include 'db.php';

if (!isset($_SESSION['id'])) {
    die("Unauthorized access.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['id'];
    $product_id = $_POST['product_id'];
    $order_id = $_POST['order_id'];

    // Check if rating is set and valid
    if (!isset($_POST['rating']) || $_POST['rating'] < 1 || $_POST['rating'] > 5) {
        // Redirect back to rating page with error message
        header("Location: rating.php?order_id=$order_id&error=Please%20select%20a%20rating%20between%201%20and%205.");
        exit();
    }

    $rating = $_POST['rating'];

    // Insert or update rating in the database
    $query = "INSERT INTO ratings (user_id, product_id, order_id, rating) 
              VALUES (?, ?, ?, ?) 
              ON DUPLICATE KEY UPDATE rating = VALUES(rating)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiii", $user_id, $product_id, $order_id, $rating);

    if ($stmt->execute()) {
        echo "<script>
                alert('Rating submitted successfully!');
                window.location.href = 'Product page/product.php'; 
              </script>";
    } else {
        echo "<script>
                alert('Error submitting rating. Try again. " . $stmt->error . "');
                window.location.href = 'rating.php?order_id=$order_id'; 
              </script>";
    }
    $stmt->close();
    exit();
}
?>
