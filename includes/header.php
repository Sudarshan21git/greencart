<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
function isLoggedIn(): bool
{
    if (isset($_SESSION['user_id'])) {
        return true;
    } else {
        return false;
    }
}

// Get cart count
$cartCount = 0;

if (isset($_SESSION['user_id'])) {
    // Include the database connection file
    include '../database/database.php';
    $userId = $_SESSION['user_id'];

    // Prepare the MySQLi statement
    $stmt = mysqli_prepare($conn, "
        SELECT SUM(ci.quantity) as cart_count 
        FROM cart_items ci 
        JOIN cart c ON ci.cart_id = c.cart_id 
        WHERE c.user_id = ?
    ");

    if ($stmt) {
        // Bind the parameter
        mysqli_stmt_bind_param($stmt, "i", $userId);
        // Execute the statement
        mysqli_stmt_execute($stmt);
        // Get the result
        $result = mysqli_stmt_get_result($stmt);
        // Fetch the data
        if ($row = mysqli_fetch_assoc($result)) {
            $cartCount = $row['cart_count'] ?? 0;
        }
        // Close the statement
        mysqli_stmt_close($stmt);
    }
}

?>

<header class="header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <a href="index.html">
                    <span class="green">Green</span> <span class="white">Cart</span>
                </a>
            </div>
            <nav class="nav-menu">
                <ul class="nav-list">
                    <li><a href="http://localhost/greencart/user/index.php">Home</a></li>
                    <li><a href="http://localhost/greencart/user/shop.php">Shop</a></li>
                    <li><a href="http://localhost/greencart/user/blog.php">Blog</a></li>
                    <li><a href="http://localhost/greencart/user/contact.php">Contact</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="account.php" <?php echo basename($_SERVER['PHP_SELF']) == 'account.php' ? 'class="active"' : ''; ?>>My Account</a></li>
                    <?php else: ?>
                        <li><a href="http://localhost/greencart/auth/login.php" <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'class="active"' : ''; ?>>Login</a></li>
                        <li><a href="http://localhost/greencart/auth/signup.php" <?php echo basename($_SERVER['PHP_SELF']) == 'register.php' ? 'class="active"' : ''; ?>>Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <div class="header-icons">
                <a href="http://localhost/greencart/user/cart.php" class="icon-cart">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    <span class="cart-count"><?php echo $cartCount ?></span>
                </a>
                <button class="mobile-menu-toggle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <div class="mobile-menu">
        <ul class="mobile-nav-list">
            <li><a href="http://localhost/greencart/user/index.php">Home</a></li>
            <li><a href="http://localhost/greencart/user/shop.php">Shop</a></li>
            <li><a href="http://localhost/greencart/user/blog.php">Blog</a></li>
            <li><a href="http://localhost/greencart/user/contact.php">Contact</a></li>
            <?php if (isLoggedIn()): ?>
                <li><a href="account.php" <?php echo basename($_SERVER['PHP_SELF']) == 'account.php' ? 'class="active"' : ''; ?>>My Account</a></li>
            <?php else: ?>
                <li><a href="http://localhost/greencart/auth/login.php" <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'class="active"' : ''; ?>>Login</a></li>
                <li><a href="http://localhost/greencart/auth/signup.php" <?php echo basename($_SERVER['PHP_SELF']) == 'register.php' ? 'class="active"' : ''; ?>>Sign Up</a></li>
            <?php endif; ?>
        </ul>
    </div>
</header>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile Menu Toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');

    mobileMenuToggle.addEventListener('click', function() {
        mobileMenu.classList.toggle('active');
    });
  });
</script>