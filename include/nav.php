
<?php 
 $servername = "localhost";
 $username = "root";
 $password = "";
   $dbname = "enursery";

   $conn = mysqli_connect($servername,$username,$password,$dbname);
   
   if(!$conn){
    die("Could not connect: " . mysqli_connect_error());

   }

 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="../home.php">
            <img src="../logo.png" alt="Nature's Nursery" width="50" height="50" class="me-2">
            <span class="fw-bold text-light">Nature's Nursery</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link text-white" href="../home.php">Home</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="../Product page/product.php">Product</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="../Blog/blog.php">Blog</a></li>
                <?php if (isset($_SESSION['id'])) { ?>
                    <li class="nav-item"><a class="nav-link text-white" href="../cart/cart.php">Cart</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">My Profile</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../profile.php">Manage My Account</a></li>
                            <li><a class="dropdown-item" href="../Myorders.php">My Orders</a></li>
                            <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php } else { ?>
                    <li class="nav-item"><a class="nav-link text-white" href="../loginpage/login.php">Login</a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>

</body>
</html>