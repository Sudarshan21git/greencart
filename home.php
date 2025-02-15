
<!DOCTYPE html>
<html>
<head>
    <title>Nursery Website</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <link rel="stylesheet" type="text/css" href="home.css">
    <link rel="stylesheet" type="text/css" href="reset.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

  <body>
	<nav>
    <div class="logo">
      <img src="logo.png" alt="Nature's Nursery">
      <div class="logo-text">
        <span class="green">Nature's</span> <span class="white">Nursery</span>
      </div>
    </div>
    <ul class="menu">
      <li><a href="#">Home</a></li>
      <li><a href="Product page\product.php">Product</a></li>
      <li><a href="Blog\blog.php">Blog</a></li>
      <!-- <li><a href="login page\login.php" class="login-btn">Login</a></li> -->
      <?php
        session_start();
        if (isset($_SESSION['username'])) {
          echo "<li><a href='cart\cart.php'>Cart</a></li>";
            echo '<div class="dropdown">';
            echo '<a href="#" class="dropbtn">My Profile</a>';
            echo '<div class="dropdown-content">';
            echo '<a href="profile.php">Manage My Account</a>';
            echo '<a href="Myorders.php">My orders</a>';
            echo '<a href="logout.php">Logout</a>';
            echo '</div>';
            echo '</div>';
        } else {
            echo '<li><a href="loginpage/login.php">Login</a></li>';
        }
        ?>

    </ul>
    <div class="hamburger-menu">
      <div class="line"></div>
      <div class="line"></div>
      <div class="line"></div>
    </div>
  </nav>

  <section class="hero">
    <div class="hero-content">
      <h1>Grow your Health,<br> Grow a Garden</h1>
      <a href="Product page\product.php" class="button">Explore More</a>
    </div>
  </section>


<section class="header">
  <h1>Welcome to the green team<h1>
</section>

<div class="image-grid">
  <div class="image-box">
    <img src="img\Black rose.jpg" alt="Image 1">
    <div class="image-text">Black Rose</div>
  </div>
  <div class="image-box">
    <img src="img\sakura.jpg" alt="Image 2">
    <div class="image-text"> sakura</div>
  </div>
  <div class="image-box">
    <img src="img\blue rose.jpg" alt="Image 3">
    <div class="image-text">Blue Rose </div>
  </div>
  <div class="image-box">
    <img src="img\jacaranda.jpg" alt="Image 4">
    <div class="image-text">Jacaranda</div>
  </div>
</div>




<!--************************footer**********-->

  <footer>
    <div class="container">
      <div class="left">
        <h3>About Us</h3>
        <ul>
          <li><a href="#">My Account</a></li>
          <li><a href="#">Our Blog</a></li>
        </ul>
      </div>
      <div class="middle">
        <h3>Contact Us</h3>
        <p>Nature's Nursery<br>Lazimpat<br>Kathmandu<br>PH no. +977 9882882488<br>email: naturenursery@gmail.com</p>
      </div>
      <div class="right">
        <h3>Connect</h3>
        <ul class="social-icons">
          <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
          <li><a href="#"><i class="fab fa-twitter"></i></a></li>
          <li><a href="#"><i class="fab fa-instagram"></i></a></li>
      </div>
    </div>
  </footer>
    
  </body>
  </html>