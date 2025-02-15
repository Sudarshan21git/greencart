<?php
// Include your database connection file
include 'db.php';
session_start();
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
}
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $product_quantity = 1;

    $select_cart = mysqli_query($conn, "SELECT * FROM cart WHERE productId = $product_id AND userId = $userId");

    if (mysqli_num_rows($select_cart) > 0) {
        echo '<div class="alert alert-success">Product already added to cart.</div>';
    } else {
        // Insert the product into the cart if it's not already there
        $insert_product = mysqli_query($conn, "INSERT INTO `cart`(userId,productId,quantity) VALUES('$userId','$product_id','$product_quantity')");
        if ($insert_product) {
            echo '<div class="alert alert-success">Product added to cart successfully.</div>';
        }
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="search.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="../home.php">
            <img src="./logo.png" alt="Nature's Nursery" width="50" height="50" class="me-2">
            <span class="fw-bold text-light">Nature's Nursery</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link text-white" href="home.php">Home</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="./Product page/product.php">Product</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="./Blog/blog.php">Blog</a></li>
                <?php if (isset($_SESSION['id'])) { ?>
                    <li class="nav-item"><a class="nav-link text-white" href="./cart/cart.php">Cart</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">My Profile</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php">Manage My Account</a></li>
                            <li><a class="dropdown-item" href="Myorders.php">My Orders</a></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php } else { ?>
                    <li class="nav-item"><a class="nav-link text-white" href="../loginpage/login.php">Login</a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Product Display -->
<div class="container mt-5">
    <div class="row">
        <?php
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql = "SELECT products.id, products.name, products.price, products.stock, products.desc, products.image FROM products WHERE products.name LIKE '%$search%' ORDER BY products.id";
            $result = mysqli_query($conn, $sql) or die("Query Failed");

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
        ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <img src="./img/<?php echo $row['image'];?>" class="card-img-top" alt="Product Image">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['name'];?></h5>
                        <p class="card-text">Stock: <?php echo $row['stock'];?></p>
                        <p class="card-text">Rs.<?php echo $row['price'];?></p>
                        <p class="card-text"><?php echo $row['desc'];?></p>
                        <form action="" method="post">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <?php
                            if (isset($_SESSION['id'])) {
                                if ($row['stock'] > 0) {
                                    echo "<button type='submit' class='btn btn-success w-100' name='add_to_cart'>Add to Cart</button>";
                                } else {
                                    echo "<span class='text-danger'>Out of stock</span>";
                                }
                            } else {
                                if ($row['stock'] > 0) {
                                    echo "<button type='submit' class='btn btn-success w-100' formaction='./loginpage/login.php'>Add to Cart</button>";
                                } else {
                                    echo "<span class='text-danger'>Out of stock</span>";
                                }
                            }
                            ?>
                        </form>
                    </div>
                </div>
            </div>
        <?php
                }
            } else {
                echo '<div class="col-12 text-center">Product is not available.</div>';
            }
        }
        ?>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
