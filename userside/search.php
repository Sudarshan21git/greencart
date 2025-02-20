<?php
// Include your database connection file
session_start();
include '../include/db.php';
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
    <link rel="stylesheet" type="text/css" href="../include/nav.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
</head>
<body>

<!-- Navbar -->
<?php include "../include/nav.php"; ?>
<!---Navbar--------->

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
                    <img src="../img/<?php echo $row['image'];?>" class="card-img-top" alt="Product Image">
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
                                    echo "<button type='submit' class='btn btn-success w-100' formaction='./login.php'>Add to Cart</button>";
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
