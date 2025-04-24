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
else if ($_SESSION['is_admin'] == 1) {
    header("Location: 404.html");
}

// Include database connection
include '../database/database.php';

// Get user information
$user_id = $_SESSION['user_id'];

// Get all reviews
$reviews_query = "SELECT r.*, p.name as product_name, p.image as product_image, p.product_id 
                 FROM reviews r 
                 JOIN products p ON r.product_id = p.product_id 
                 WHERE r.user_id = ? 
                 ORDER BY r.created_at DESC";
$reviews_stmt = mysqli_prepare($conn, $reviews_query);
mysqli_stmt_bind_param($reviews_stmt, "i", $user_id);
mysqli_stmt_execute($reviews_stmt);
$reviews_result = mysqli_stmt_get_result($reviews_stmt);
$reviews = [];
while ($review = mysqli_fetch_assoc($reviews_result)) {
    $reviews[] = $review;
}
mysqli_stmt_close($reviews_stmt);

// Handle review deletion
$success_message = '';
$error_message = '';

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $review_id = (int)$_GET['delete'];
    
    // Check if review belongs to user
    $check_review_query = "SELECT * FROM reviews WHERE review_id = ? AND user_id = ?";
    $check_review_stmt = mysqli_prepare($conn, $check_review_query);
    mysqli_stmt_bind_param($check_review_stmt, "ii", $review_id, $user_id);
    mysqli_stmt_execute($check_review_stmt);
    mysqli_stmt_store_result($check_review_stmt);
    
    if (mysqli_stmt_num_rows($check_review_stmt) > 0) {
        // Delete review
        $delete_query = "DELETE FROM reviews WHERE review_id = ?";
        $delete_stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($delete_stmt, "i", $review_id);
        
        if (mysqli_stmt_execute($delete_stmt)) {
            $success_message = "Review deleted successfully.";
            
            // Refresh reviews list
            mysqli_stmt_execute($reviews_stmt);
            $reviews_result = mysqli_stmt_get_result($reviews_stmt);
            $reviews = [];
            while ($review = mysqli_fetch_assoc($reviews_result)) {
                $reviews[] = $review;
            }
        } else {
            $error_message = "Failed to delete review. Please try again.";
        }
        
        mysqli_stmt_close($delete_stmt);
    } else {
        $error_message = "Review not found or you don't have permission to delete it.";
    }
    
    mysqli_stmt_close($check_review_stmt);
}

// Close database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reviews - GreenCart</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/account-styles.css">
    <link rel="stylesheet" href="../css/reviews-styles.css">
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Header -->
    <?php include_once "../includes/header.php"; ?>

    <!-- Reviews Section -->
    <section class="account-section">
        <div class="container">
            <div class="account-header">
                <h1 class="section-title">My Reviews</h1>
                <p>Manage your product reviews</p>
            </div>

            <div class="account-container">
                <!-- Account Sidebar -->
                <div class="account-sidebar">
                    <div class="user-info">
                        <div class="user-avatar">
                            <span><?php echo strtoupper(substr($_SESSION['fname'], 0, 1) . substr($_SESSION['lname'], 0, 1)); ?></span>
                        </div>
                        <div class="user-details">
                            <h3><?php echo htmlspecialchars($_SESSION['fname'] . ' ' . $_SESSION['lname']); ?></h3>
                            <p><?php echo htmlspecialchars($_SESSION['email']); ?></p>
                        </div>
                    </div>

                    <nav class="account-nav">
                        <ul>
                            <li><a href="dashboard.php">Dashboard</a></li>
                            <li><a href="account.php">Account Settings</a></li>
                            <li><a href="orders.php">My Orders</a></li>
                            <li><a href="reviews.php" class="active">My Reviews</a></li>
                            <li><a href="../auth/logout.php">Logout</a></li>
                        </ul>
                    </nav>
                </div>

                <!-- Reviews Content -->
                <div class="account-content">
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success">
                            <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-error">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (count($reviews) > 0): ?>
                        <div class="reviews-list">
                            <?php foreach ($reviews as $review): ?>
                                <div class="review-card">
                                    <div class="review-product">
                                        <div class="product-image">
                                            <img src="../img/<?php echo $review['product_image']; ?>" alt="<?php echo $review['product_name']; ?>">
                                        </div>
                                        <div class="product-info">
                                            <h3><?php echo $review['product_name']; ?></h3>
                                            <div class="review-date"><?php echo date('F d, Y', strtotime($review['created_at'])); ?></div>
                                        </div>
                                    </div>
                                    
                                    <div class="review-content">
                                        <div class="review-rating">
                                            <?php 
                                            for ($i = 1; $i <= 5; $i++) {
                                                echo $i <= $review['rating'] ? '★' : '☆';
                                            }
                                            ?>
                                        </div>
                                        <div class="review-text">
                                            <?php echo htmlspecialchars($review['message']); ?>
                                        </div>
                                    </div>
                                    
                                    <div class="review-actions">
                                        <a href="../product.php?id=<?php echo $review['product_id']; ?>" class="btn btn-sm">View Product</a>
                                        <a href="edit-review.php?id=<?php echo $review['review_id']; ?>" class="btn btn-sm btn-outline">Edit Review</a>
                                        <a href="reviews.php?delete=<?php echo $review['review_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this review?')">Delete</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            </div>
                            <h3>No Reviews Yet</h3>
                            <p>You haven't written any reviews yet. Share your thoughts on products you've purchased!</p>
                            <a href="../shop.php" class="btn btn-primary">Shop Products</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include_once "../includes/footer.php"; ?>
    <script src="../js/script.js"></script>
    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            if (alerts.length > 0) {
                setTimeout(() => {
                    alerts.forEach(alert => {
                        alert.style.opacity = '0';
                        setTimeout(() => {
                            alert.style.display = 'none';
                        }, 500);
                    });
                }, 5000);
            }
        });
    </script>
</body>
</html>
