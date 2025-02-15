<?php
session_start();
include 'db.php';

if (!isset($_GET['order_id'])) {
    echo "<p class='text-danger text-center'>Invalid Request.</p>";
    exit;
}

$order_id = $_GET['order_id'];

// Check for error in the URL
$error_message = isset($_GET['error']) ? $_GET['error'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate Your Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .star-rating input {
            display: none;
        }
        .star-rating label {
            font-size: 30px;
            color: #ccc;
            cursor: pointer;
        }
        .star-rating input:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #f39c12;
        }
        .navbar {
            background-color: #28a745 !important;
        }
        .navbar .nav-link, .navbar-brand {
            color: white !important;
        }
        .btn-warning {
            background-color: #f39c12;
            border: none;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="home.php">
            <img src="logo.png" alt="Nature's Nursery" height="50">
            <span class="text-white">Nature's Nursery</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="Product page/product.php">Products</a></li>
                <li class="nav-item"><a class="nav-link" href="Blog/blog.php">Blog</a></li>
                <li class="nav-item"><a class="nav-link" href="cart/cart.php">Cart</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">My Profile</a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="profile.php">Manage My Account</a></li>
                        <li><a class="dropdown-item" href="Myorders.php">My Orders</a></li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="text-center">Rate Your Product</h2>

    <!-- Display error message if it exists -->
    <?php if ($error_message): ?>
        <div class="alert alert-danger text-center">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <?php
        $query = "SELECT oi.product_id, p.name AS product_name, p.image AS product_image, p.price 
                  FROM order_items oi 
                  JOIN products p ON oi.product_id = p.id 
                  WHERE oi.order_id = $order_id";
        $result = $conn->query($query);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                ?>
                <div class="col-md-4">
                    <div class="card p-3 mb-3">
                        <img src="./img/<?php echo $row['product_image']; ?>" class="card-img-top" alt="<?php echo $row['product_name']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row['product_name']; ?></h5>
                            <p class="card-text">Rs. <?php echo $row['price']; ?></p>
                            <form action="submit_rating.php" method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                                <div class="star-rating text-center">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <input type="radio" id="star<?php echo $i; ?>-<?php echo $row['product_id']; ?>" name="rating" value="<?php echo $i; ?>">
                                        <label for="star<?php echo $i; ?>-<?php echo $row['product_id']; ?>"><i class="fas fa-star"></i></label>
                                    <?php endfor; ?>
                                </div>
                                <button type="submit" class="btn btn-warning w-100 mt-2">Submit Rating</button>
                            </form>
                            <form action="add_to_cart.php" method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-success w-100 mt-2">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p class='text-center text-danger'>No products found for this order.</p>";
        }
        ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
