<?php include 'db.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Products</title>
    <!-- CSS and Font Awesome Links -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <!-- Include header -->
    <?php include 'header.php'; ?>

    <div class="container">
        <section class="display_product">
            <table>
                <thead>
                    <tr>
                        <th>Sl No.</th>
                        <th>Product Image</th>
                        <th>Product Name</th>
                        <th>Product Price</th>
                        <th>Stock</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $display_product = mysqli_query($conn, "SELECT * FROM products");
                    $serial_number = 1; // Initialize serial number
                    if (mysqli_num_rows($display_product) > 0) {
                        // Fetch and display each product
                        while ($row = mysqli_fetch_assoc($display_product)) {
                            echo "<tr>";
                            echo "<td>{$serial_number}</td>";

                            // Check if image exists, otherwise use a placeholder
                            $image_src = !empty($row['image']) ? 'img/' . $row['image'] : 'img/default.png';
                            echo "<td><img src='{$image_src}' alt='{$row['name']}' style='width: 100px;'></td>";

                            echo "<td>{$row['name']}</td>";
                            echo "<td> Rs.{$row['price']}</td>";
                            echo "<td>{$row['stock']}</td>";
                            echo "<td>{$row['desc']}</td>";
                            echo "<td>";
                            // Safe DELETE action with SQL escaping
                            $product_id = $row['id'];
                            echo "<a href='delete.php?delete=" . urlencode($product_id) . "' class='delete_product_btn' onclick=\"return confirm('Are you sure you want to delete this product?')\"><i class='fas fa-trash'></i></a>";

                            // Safe UPDATE action with SQL escaping
                            echo "<a href='update.php?id=" . urlencode($product_id) . "' class='update_product_btn'><i class='fas fa-edit'></i></a>";

                            echo "</td>";
                            echo "</tr>";
                            $serial_number++; // Increment serial number
                        }
                    } else {
                        echo "<tr><td colspan='7'>No products found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </div>

</body>
</html>
