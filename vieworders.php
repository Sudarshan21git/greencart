<?php
include "db.php"; // Include your database connection file

if (isset($_POST['approve']) && isset($_POST['order_id'])) {
    $approve_id = $_POST['order_id'];
    // Update the status to Approved in the database
    $approve_query = "UPDATE orders SET status=2 WHERE id=$approve_id";
    mysqli_query($conn, $approve_query);
    
} elseif (isset($_POST['decline']) && isset($_POST['order_id'])) {
    $decline_id = $_POST['order_id'];
    // Update the status to Declined in the database
    $decline_query = "UPDATE orders SET status=3 WHERE id=$decline_id";
    mysqli_query($conn, $decline_query);

    // Fetch products from the declined order via the order_items table
    $products_query = "SELECT oi.product_id, oi.quantity
                       FROM order_items oi
                       WHERE oi.order_id = $decline_id";
    $products_result = mysqli_query($conn, $products_query);

    if ($products_result && mysqli_num_rows($products_result) > 0) {
        // Loop through each product in the declined order
        while ($product_row = mysqli_fetch_assoc($products_result)) {
            $product_id = $product_row['product_id'];
            $product_qty = $product_row['quantity'];

            // Update the product stock in the products table based on product_id
            $update_qty_query = "UPDATE products SET stock = (stock + $product_qty) WHERE id = $product_id";
            mysqli_query($conn, $update_qty_query);
        }
    }
}


$query = "SELECT * FROM orders"; // Query to fetch orders from the database
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="vieworder.css">
</head>

<body>
    <!-- Include header -->
    <?php include 'header.php'; ?>

    <div class="container">
        <section class="display_product">

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Product(s)</th>
                        <th>Total Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        // Loop through each order
                        while ($row = mysqli_fetch_assoc($result)) {
                            $oid = $row['id'];
                            echo "<tr>
                                    <td>$oid</td>
                                    <td>" . $row['name'] . "</td>
                                    <td>" . $row['email'] . "</td>
                                    <td>" . $row['phno'] . "</td>
                                    <td>" . $row['location'] . "</td>";

                            // Fetch order items for this order
                            $order_items_query = "SELECT oi.product_id, oi.quantity, p.name AS product_name
                                                 FROM order_items oi
                                                 JOIN products p ON oi.product_id = p.id
                                                 WHERE oi.order_id = $oid";
                            $order_items_result = mysqli_query($conn, $order_items_query);

                            // Loop through the order items
                            echo "<td>";
                            while ($item = mysqli_fetch_assoc($order_items_result)) {
                                echo $item['product_name'] . " (Qty: " . $item['quantity'] . ")<br>";
                            }
                            echo "</td>";

                            // Display the total price and action buttons based on order status
                            echo "<td>Rs." . $row['total_price'] . "</td><td>";

                            if ($row['status'] == 1) {
                                echo "
                                    <form action='vieworders.php' method='post'>
                                        <input type='hidden' name='order_id' value='$oid'>
                                        <button type='submit' name='approve' class='approve-btn'>Approve</button>
                                        <button type='submit' name='decline' class='decline-btn'>Decline</button>
                                    </form>";
                            } elseif ($row['status'] == 2) {
                                echo "Approved";
                            } else {
                                echo "Declined";
                            }
                            echo "</td></tr>";
                        }
                    } else {
                        // No orders found
                        echo "<tr><td colspan='9'>No orders found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </div>

</body>

</html>
