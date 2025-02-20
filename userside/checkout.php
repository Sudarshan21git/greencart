<?php
    session_start();
    include '../include/db.php';

    if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
    }

    $userId=$_SESSION['id'];

    // Fetch user details
    $user_query="SELECT username, phone, Email, address FROM customeruser WHERE id = $userId" ;
    $user_result=mysqli_query($conn, $user_query);
    if ($user_result && mysqli_num_rows($user_result)> 0) {
    $user = mysqli_fetch_assoc($user_result);
    $uName = $user['username'];
    $uPhone = $user['phone'];
    $uMail = $user['Email'];
    $uAddress = $user['address'];
    } else {
    die("User not found.");
    }

    if (isset($_POST['order_btn'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $number = mysqli_real_escape_string($conn, $_POST['number']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $method = mysqli_real_escape_string($conn, $_POST['method']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);

    // Validate phone number
    if (!preg_match('/^(98|97)\d{8}$/', $number)) {
    echo "<script>
        alert('Invalid phone number. Please enter a valid 10-digit phone number.');
    </script>";
    } else {
    // Fetch cart details
    $cart_query = mysqli_query($conn, "SELECT * FROM cart WHERE userid = $userId");
    if ($cart_query && mysqli_num_rows($cart_query) > 0) {
    $price_total = 0;
    $total_products = '';
    $product_quantities = [];

    // Insert order details first to get the order ID
    $order_query = "INSERT INTO orders (u_id, name, phno, location, email, method, total_products, total_price)
    VALUES ('$userId', '$name', '$number', '$location', '$email', '$method', '', '$price_total')";
    if (mysqli_query($conn, $order_query)) {
    $order_id = mysqli_insert_id($conn); // Get the last inserted order ID

    // Process cart items and insert them into order_items
    while ($fetch_cart = mysqli_fetch_assoc($cart_query)) {
    $product_id = $fetch_cart['productId'];
    $product_quantity = $fetch_cart['quantity'];

    // Fetch product details
    $get_product_data = mysqli_query($conn, "SELECT * FROM products WHERE id = $product_id");
    $product_data = mysqli_fetch_assoc($get_product_data);

    $product_name = $product_data['name'];
    $product_price = $product_data['price'];
    $item_total = $product_price * $product_quantity;

    // Insert the product into order_items table
    $order_item_query = "INSERT INTO order_items (order_id, product_id, quantity, price)
    VALUES ('$order_id', '$product_id', '$product_quantity', '$product_price')";
    mysqli_query($conn, $order_item_query);

    // Update the total price for the order
    $price_total += $item_total;
    $total_products .= $product_name . " (" . $product_quantity . "), ";

    // Update stock for the product
    $product_update_query = "UPDATE products SET stock = stock - $product_quantity WHERE id = $product_id";
    mysqli_query($conn, $product_update_query);
    }

    // Update the order with the total price and products
    $total_products = rtrim($total_products, ", ");
    $update_order_query = "UPDATE orders SET total_products = '$total_products', total_price = '$price_total' WHERE id = $order_id";
    mysqli_query($conn, $update_order_query);

    // Clear the user's cart
    mysqli_query($conn, "DELETE FROM cart WHERE userid = $userId");

    // Display success message
    echo "
    <div class='order-message-container'>
        <div class='message-container'>
            <h3>Thank you for shopping!</h3>
            <div class='order-detail'>
                <span>$total_products</span>
                <span class='total'>Total: Rs.$price_total/-</span>
            </div>
            <div class='customer-details'>
                <p>Your name: <span>$name</span></p>
                <p>Your number: <span>$number</span></p>
                <p>Your email: <span>$email</span></p>
                <p>Your location: <span>$location</span></p>
                <p>Your payment method: <span>$method</span></p>
                <p>(*Pay when the product arrives*)</p>
            </div>
            <a href='./product.php' class='btn'>Continue shopping</a>
        </div>
    </div>";
    } else {
    echo "<script>
        alert('Failed to place order. Please try again.');
    </script>";
    }
    } else {
    echo "<script>
        alert('Your cart is empty. Please add items to your cart.');
    </script>";
    }
    }
    }
    ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
  
    <link rel="stylesheet" href="../css/checkout.css">
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
            <li><a href="./product.php">Products</a></li>
            <li><a href="./blog.php">Blog</a></li>
            <li><a href="cart.php" class="cart">Cart <span>
                        <?php
                        // Count items in the cart
                        $cart_count_query = mysqli_query($conn, "SELECT COUNT(*) AS count FROM cart WHERE userid = $userId");
                        $cart_count = mysqli_fetch_assoc($cart_count_query);
                        echo $cart_count['count'];
                        ?>
                    </span></a></li>
            <?php if (isset($_SESSION['id'])): ?>
                <div class="dropdown">
                    <a href="#" class="dropbtn">My Profile</a>
                    <div class="dropdown-content">
                        <a href="profile.php">Manage My Account</a>
                        <a href="#">My Orders</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <li><a href="./login.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="container">
        <section class="checkout-form">
            <h1 class="heading">Complete Your Order</h1>
            <form action="" method="post">
                <div class="display-order">
                    <?php
                    // Fetching cart items
                    $select_cart = mysqli_query($conn, "SELECT * FROM cart WHERE userid = $userId");
                    $grand_total = 0;
                    $total_products = '';
                    if (mysqli_num_rows($select_cart) > 0) {
                        while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
                            // Get product details
                            $product_id = $fetch_cart['productId'];
                            $get_product_data = mysqli_query($conn, "SELECT * FROM products WHERE id=$product_id");
                            $fetch_product_data = mysqli_fetch_assoc($get_product_data);

                            // Calculate item total
                            $item_total = $fetch_product_data['price'] * $fetch_cart['quantity'];
                            $grand_total += $item_total;
                            $total_products .= $fetch_product_data['name'] . " (" . $fetch_cart['quantity'] . "), ";
                            echo "<span>{$fetch_product_data['name']} ({$fetch_cart['quantity']})</span>";
                        }
                    } else {
                        echo "<span>Your cart is empty!</span>";
                    }
                    ?>
                    <span class="grand-total">Grand Total: Rs.<?= $grand_total; ?>/-</span>
                </div>
                <div class="flex">
                    <div class="inputBox">
                        <span>Your Name</span>
                        <input type="text" name="name" value="<?= $uName; ?>" readonly required>
                    </div>
                    <div class="inputBox">
                        <span>Phone No:</span>
                        <input type="text" name="number" value="<?= $uPhone; ?>" required>
                    </div>
                    <div class="inputBox">
                        <span>Your Email</span>
                        <input type="email" name="email" value="<?= $uMail; ?>" readonly required>
                    </div>
                    <div class="inputBox">
                        <span>Payment Method</span>
                        <select name="method" required>
                            <option value="cash on delivery" selected>Cash on Delivery</option>
                        </select>
                    </div>
                    <div class="inputBox">
                        <span>Location</span>
                        <select name="location" required>
                            <option value="Kathmandu">Kathmandu</option>
                            <option value="Lalitpur">Lalitpur</option>
                            <option value="Bhaktapur">Bhaktapur</option>
                        </select>
                    </div>
                </div>
                <input type="submit" value="Order Now" name="order_btn" class="btn">
            </form>
        </section>
    </div>

</body>

</html>
