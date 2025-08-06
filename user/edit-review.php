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

// Check if review ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: reviews.php");
    exit();
}

$review_id = (int)$_GET['id'];

// Get review details
$review_query = "SELECT r.*, p.name as product_name, p.image as product_image, p.product_id 
                FROM reviews r 
                JOIN products p ON r.product_id = p.product_id 
                WHERE r.review_id = ? AND r.user_id = ?";
$review_stmt = mysqli_prepare($conn, $review_query);
mysqli_stmt_bind_param($review_stmt, "ii", $review_id, $user_id);
mysqli_stmt_execute($review_stmt);
$review_result = mysqli_stmt_get_result($review_stmt);

if (mysqli_num_rows($review_result) === 0) {
    // Review not found or doesn't belong to user
    header("Location: reviews.php");
    exit();
}

$review = mysqli_fetch_assoc($review_result);

// Handle form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_review'])) {
    $rating = (int)$_POST['rating'];
    $review_text = mysqli_real_escape_string($conn, $_POST['review_text']);
    
    // Validate input
    if ($rating < 1 || $rating > 5) {
        $error_message = "Rating must be between 1 and 5.";
    } else if (empty($review_text)) {
        $error_message = "Review text cannot be empty.";
    } else {
        // Update review
        $update_query = "UPDATE reviews SET rating = ?, message = ? WHERE review_id = ? AND user_id = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "isii", $rating, $review_text, $review_id, $user_id);
        
        if (mysqli_stmt_execute($update_stmt)) {
            $success_message = "Review updated successfully.";
            
            // Refresh review data
            mysqli_stmt_execute($review_stmt);
            $review_result = mysqli_stmt_get_result($review_stmt);
            $review = mysqli_fetch_assoc($review_result);
        } else {
            $error_message = "Failed to update review. Please try again.";
        }
        
        mysqli_stmt_close($update_stmt);
    }
}

// Close database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Review - GreenCart</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/account-styles.css">
    <link rel="stylesheet" href="../css/reviews-styles.css">
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Header -->
    <?php include_once "../includes/header.php"; ?>

    <!-- Edit Review Section -->
    <section class="account-section">
        <div class="container">
            <div class="account-header">
                <h1 class="section-title">Edit Review</h1>
                <p>Update your review for <?php echo $review['product_name']; ?></p>
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

                <!-- Edit Review Content -->
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

                    <div class="review-edit-container">
                        <div class="product-preview">
                            <div class="product-image">
                                <img src="../img/<?php echo $review['product_image']; ?>" alt="<?php echo $review['product_name']; ?>">
                            </div>
                            <div class="product-info">
                                <h3><?php echo $review['product_name']; ?></h3>
                                <p>Originally reviewed on <?php echo date('F d, Y', strtotime($review['created_at'])); ?></p>
                            </div>
                        </div>

                        <form method="POST" action="edit-review.php?id=<?php echo $review_id; ?>" class="review-form">
                            <div class="form-group">
                                <label for="rating">Rating</label>
                                <div class="rating-input">
                                    <input type="radio" name="rating" id="star5" value="5" <?php echo $review['rating'] == 5 ? 'checked' : ''; ?>>
                                    <label for="star5">★</label>
                                    <input type="radio" name="rating" id="star4" value="4" <?php echo $review['rating'] == 4 ? 'checked' : ''; ?>>
                                    <label for="star4">★</label>
                                    <input type="radio" name="rating" id="star3" value="3" <?php echo $review['rating'] == 3 ? 'checked' : ''; ?>>
                                    <label for="star3">★</label>
                                    <input type="radio" name="rating" id="star2" value="2" <?php echo $review['rating'] == 2 ? 'checked' : ''; ?>>
                                    <label for="star2">★</label>
                                    <input type="radio" name="rating" id="star1" value="1" <?php echo $review['rating'] == 1 ? 'checked' : ''; ?>>
                                    <label for="star1">★</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="review_text">Review</label>
                                <textarea id="review_text" name="review_text" rows="6" required><?php echo htmlspecialchars($review['message']); ?></textarea>
                            </div>

                            <div class="form-actions">
                                <a href="reviews.php" class="btn btn-outline">Cancel</a>
                                <button type="submit" name="update_review" class="btn btn-primary">Update Review</button>
                            </div>
                        </form>
                    </div>
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
            
            // Simple star rating functionality
            const ratingInputs = document.querySelectorAll('.rating-input input');
            const ratingLabels = document.querySelectorAll('.rating-input label');
            
            // Handle input changes
            ratingInputs.forEach((input, index) => {
                input.addEventListener('change', function() {
                    // The CSS will handle the visual changes automatically
                });
            });
        });
    </script>
</body>
</html>
