
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation Bar</title>
    <link rel="stylesheet" type="text/css" href="nav.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<nav>
    <div class="logo">
        <img src="../img/logo.png" alt="Nature's Nursery">
        <div class="logo-text">
            <span class="green">Nature's</span> <span class="white">Nursery</span>
        </div>
    </div>
    <ul class="menu">
        <li><a href="home.php">Home</a></li>
        <li><a href="product.php">Product</a></li>
        <li><a href="blog.php">Blog</a></li>

        <?php
        if (isset($_SESSION['id'])) {
            echo '<li><a href="cart.php">Cart</a></li>';
            echo '<div class="dropdown">';
            echo '<a href="#" class="dropbtn">My Profile</a>';
            echo '<div class="dropdown-content">';
            echo '<a href="profile.php">Manage My Account</a>';
            echo '<a href="Myorders.php">My Orders</a>';
            echo '<a href="logout.php">Logout</a>';
            echo '</div>';
            echo '</div>';
        } else {
            echo '<li><a href="login.php">Login</a></li>';
        }
        ?>
    </ul>
    <div class="hamburger-menu">
        <div class="line"></div>
        <div class="line"></div>
        <div class="line"></div>
    </div>
</nav>

</body>
</html>
