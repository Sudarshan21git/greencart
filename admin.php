<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>admin panel</title>
    <!-- CSS Link -->
    <link rel="stylesheet" href="admin.css">
    <!-- Font Awesome Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* Styling for the success message popup */
        .success-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
            z-index: 999;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        /* Styling for the close button */
        .close-btn {
            background-color: #ddd;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 3px;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .close-btn:hover {
            background-color: #ccc;
        }
    </style>
</head>
<body>
    <!-- Include header -->
    <?php include('header.php')?>
    <?php include('db.php')?>
  

    <!-- Form section -->
    <div class="container">
        <section>
            <h3 class="heading">Add Product</h3>
            <?php
if (isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_stock = $_POST['product_stock'];
    $product_desc = $_POST['product_desc'];
    $product_image = $_FILES['product_image']['name'];
    $product_image_temp_name = $_FILES['product_image']['tmp_name'];
    $product_image_folder = 'img/' . $product_image;

    // Validation for empty fields
    if (empty($product_name) || empty($product_price) || empty($product_stock) || empty($product_desc) || empty($product_image)) {
        echo "Please fill in all the fields!";
    } else {
        include('db.php'); // Include the database connection file here

        // Validation for product name format
        if (!preg_match("/^[a-zA-Z][a-zA-Z\s']*$/", $product_name)) {
            echo "Product name must start with an alphabet and contain only letters, spaces, and apostrophes.";
        } else if (!is_numeric($product_price) || $product_price <= 0) {
            echo "Product price must be a positive number greater than 0";
        } else if (!is_numeric($product_stock) || $product_stock <= 0) {
            echo "Product stock must be a positive number greater than 0";
        } else if (strlen($product_desc) < 1 || strlen($product_desc) > 200 || !preg_match("/^[a-zA-Z]/", $product_desc)) {
            echo "Product description must start with an alphabet and be between 1 and 200 characters long.";
        } else {
            $query = "SELECT * FROM products WHERE name='$product_name'";
            $validProduct = mysqli_query($conn, $query);

            if (mysqli_num_rows($validProduct) == 0) {
                // Use backticks for the `desc` column
                $insert_query = mysqli_query($conn, "INSERT INTO products (name, price, stock, `desc`, image) VALUES ('$product_name', '$product_price', '$product_stock', '$product_desc', '$product_image')") or die("insert query failed");

                if ($insert_query) {
                    move_uploaded_file($product_image_temp_name, $product_image_folder);
                    echo '<div class="success-popup">Product inserted successfully. <button class="close-btn" onclick="closeMessage()">Close</button></div>';
                    echo '<script>setTimeout(function(){ document.querySelector(".success-popup").style.display = "none"; }, 3000);</script>';
                } else {
                    echo "Product not inserted successfully";
                }
            } else {
                echo "Product already exists!";
            }
        }
    }
}
?>
            <form action="" class="add_product" method="post" enctype="multipart/form-data">
                <input type="text" name="product_name" placeholder="Enter the product name" class="input_fields" >
                <input type="text" name="product_price" placeholder="Enter the product price" class="input_fields" >
                <input type="number" name="product_stock"placeholder="Enter the product stock" class="input_fields" >
                <input type="text" name="product_desc"  placeholder="Enter the product description" class="input_fields" >
                <input type="file" name="product_image" class="input_fields"  accept="image/png,image/jpg,image/jpeg">
                <input type="submit" name="add_product" class="submit_btn" value="Add product" >
            </form>
        </section>
    </div>

    <!-- JS file -->
    <script src="admin.js"></script>
    <script>
        function closeMessage() {
            document.querySelector(".success-popup").style.display = "none";
        }
    </script>
</body>
</html>