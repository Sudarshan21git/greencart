<?php
session_start();
include "../include/db.php";

// Get the user ID from session
$uid = $_SESSION['id'];

// Fetch orders for the user
$query = "SELECT * FROM orders WHERE u_id = $uid";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link rel="stylesheet" href="../css/orders.css">
    <link rel="stylesheet" href="../include/nav.css">
</head>

<body>
    
<!-- Navbar -->
<?php include "../include/nav.php"; ?>
<!---Navbar--------->

    <h1>My Orders</h1>

    <div class="container">
        <section class="display_product">
            <table>
                <thead>
                    <tr>
                        <th>S.N.</th> 
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Product(s)</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Rating</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Initialize the counter for the sequential order number
                    $counter = 1;

                    // Check if any orders are fetched
                    if (mysqli_num_rows($result) > 0) {
                        // Loop through each row of data
                        while ($row = mysqli_fetch_assoc($result)) {
                            $order_id = $row['id']; // The order's unique ID
                            $user_name = $row['name'];
                            $user_email = $row['email'];
                            $user_phone = $row['phno'];
                            $location = $row['location'];
                            $total_price = $row['total_price'];
                            $status = $row['status'];

                            // Fetch products for the current order
                            $order_items_query = "SELECT oi.product_id, oi.quantity, oi.price, p.name AS product_name, p.image AS product_image
                        FROM order_items oi
                        JOIN products p ON oi.product_id = p.id
                        WHERE oi.order_id = $order_id";
                            $order_items_result = $conn->query($order_items_query);

                            // Display the order details with the sequential counter
                            echo "<tr>
                            <td>" . $counter . "</td>
                            <td>" . $user_name . "</td>
                            <td>" . $user_email . "</td>
                            <td>" . $user_phone . "</td>
                            <td>" . $location . "</td>
                            <td>";

                            // Loop through each product in the order
                            while ($item = mysqli_fetch_assoc($order_items_result)) {
                                $product_name = $item['product_name'];
                                $product_image = $item['product_image']; // Fetch the image filename
                                $quantity = $item['quantity'];
                                $price = $item['price'];

                                // Display product information
                                echo "<div class='order-item'>
                            <img src='../img/" . $product_image . "' alt='" . $product_name . "'>
                            <div class='product-details'>
                                <p><strong>" . $product_name . "</strong></p>
                                <p>Qty: " . $quantity . "</p>
                                <p>Price: Rs." . $price . "</p>
                            </div>
                          </div>";
                            }

                            // Close the product details
                            echo "</td>
                            <td>Rs." . $total_price . "</td>
                            <td>";

                            // Handle order status
                            if ($status == 1) {
                                echo "Pending";
                            } elseif ($status == 2) {
                                echo "Approved";
                            } else {
                                echo "Declined";
                            }
                            echo "</td>";

                            // Display rating button only if status is Approved (2)
                            echo "<td>";
                            if ($status == 2) {
                                // Redirect to rating page, passing the order_id as a query parameter
                                echo "<a href='rating.php?order_id=" . urlencode($order_id) . "' class='rating-button'>Rate Product</a>";
                            } else {
                                echo "N/A";
                            }
                            echo "</td></tr>";

                            // Increment the counter for the next order
                            $counter++;
                        }
                    } else {
                        // No orders found in the database
                        echo "<tr><td colspan='9'>No orders found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </div>

</body>

</html>