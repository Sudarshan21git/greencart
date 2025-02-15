<?php
// Include the database connection file
include 'db.php';

// Initialize $fetch_data
$fetch_data = null;

// Update logic
if(isset($_POST['update_product'])) {
    $update_product_id = $_POST['update_product_id'];
    $update_product_name = $_POST['update_product_name'];
    $update_product_price = $_POST['update_product_price'];
    $update_product_stock = $_POST['update_product_stock'];
    $update_product_desc = $_POST['update_product_desc'];
    $update_product_image = $_FILES['update_product_image']['name'];
    $update_product_img_tmp_name = $_FILES['update_product_image']['tmp_name'];
    $update_product_image_folder = 'img/' . $update_product_image;
    $getPname = "SELECT name FROM products where id=$update_product_id";
    $getPres = mysqli_query($conn,$getPname);
    $rowP = mysqli_fetch_assoc($getPres);

    if($update_product_name != $rowP['name']){
        $nameCheck = "SELECT * FROM products WHERE name ='$update_product_name'";
        $nameCheckResult = mysqli_query($conn,$nameCheck);
        if($nameCheckResult->num_rows>0){
            echo "Product with the same name already exist";
        }
        else{
            if (!preg_match("/^[a-zA-Z][a-zA-Z\s']*$/", $update_product_name)) {
                echo "Product name must start with an alphabet and contain only letters, spaces, and apostrophes.";
            } else if (!is_numeric($update_product_price) || $update_product_price <= 0) {
                echo "Product price must be a positive number greater than 0";
            } else if (!is_numeric($update_product_stock) || $update_product_stock <= 0) {
                echo "Product stock must be a positive number greater than 0";
            } else if (strlen($update_product_desc) < 1 || strlen($update_product_desc) > 200 || !preg_match("/^[a-zA-Z]/", $update_product_desc)) {
                echo "Product description must start with an alphabet and be between 1 and 200 characters long.";
            }else{
            $update_query = "UPDATE products SET name='$update_product_name', price='$update_product_price',stock=$update_product_stock, `desc`='$update_product_desc', image='$update_product_image' WHERE id=$update_product_id";
            $update_result = mysqli_query($conn, $update_query);
            if($update_result) {
        
                if(move_uploaded_file($update_product_img_tmp_name, $update_product_image_folder)) {
                    echo "Product updated successfully.";
                } else {
                    echo "Error uploading file.";
                }
            } else {
                echo "Error updating product: " . mysqli_error($conn);
            }
            }
        }
    }
    // Update query
    else{       
         if (!preg_match("/^[a-zA-Z][a-zA-Z\s']*$/", $update_product_name)) {
        echo "Product name must start with an alphabet and contain only letters, spaces, and apostrophes.";
    } else if (!is_numeric($update_product_price) || $update_product_price <= 0) {
        echo "Product price must be a positive number greater than 0";
    } else if (!is_numeric($update_product_stock) || $update_product_stock <= 0) {
        echo "Product stock must be a positive number greater than 0";
    } else if (strlen($update_product_desc) < 1 || strlen($update_product_desc) > 200 || !preg_match("/^[a-zA-Z]/", $update_product_desc)) {
        echo "Product description must start with an alphabet and be between 1 and 200 characters long.";
    }else{
    $update_query = "UPDATE products SET  price='$update_product_price', stock=$update_product_stock, `desc`='$update_product_desc', image='$update_product_image' WHERE id=$update_product_id";
    $update_result = mysqli_query($conn, $update_query);
    if($update_result) {
        
        if(move_uploaded_file($update_product_img_tmp_name, $update_product_image_folder)) {
            echo "Product updated successfully.";
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "Error updating product: " . mysqli_error($conn);
    }
    }

    }

}

if (isset($_GET['id'])) {
    $edit_id = (int) $_GET['id'];
    $edit_query = mysqli_query($conn, "SELECT * FROM products WHERE id = '$edit_id'");

   
    if ($edit_query) {
        $fetch_data = mysqli_fetch_assoc($edit_query);
    } else {
        
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="admin.css">
    <style>
        /* Additional CSS for centering the box */
        .edit_container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column; /* To stack elements vertically */
        }
        .previous_image {
            margin-bottom: 20px; /* Add space below the previous image */
        }
    </style>
</head>
<body>
    <?php include 'header.php' ?>
    <section class="edit_container">
        <pre>
            <?php
        // echo $fetch_data;
        ?>
        </pre>
        <!-- Current image -->
        <!-- form -->
        <form action="" method="POST" enctype="multipart/form-data" class="update_product product_container_box">
            <img src="img/<?php echo isset($fetch_data['image']) ? $fetch_data['image'] : ''; ?>" alt="Current Image" class="previous_image">
            <h2 style="color: green;">Update Product</h2>
            <br><br> <!-- Add more line breaks here -->

            <input type="hidden" value="<?php echo isset($fetch_data['id']) ? $fetch_data['id'] : ''; ?>" name="update_product_id">
            
            <input type="text" class="input_fields fields" placeholder="Product Name" required value="<?php echo isset($fetch_data['name']) ? $fetch_data['name'] : ''; ?>" name="update_product_name"><br>
            <input type="text" class="input_fields fields" placeholder="Product Price" required value="<?php echo isset($fetch_data['price']) ? $fetch_data['price'] : ''; ?>" name="update_product_price"><br>
            <input type="text" class="input_fields fields" placeholder="Product Stock" required value="<?php echo isset($fetch_data['stock']) ? $fetch_data['stock'] : ''; ?>" name="update_product_stock"><br>
            <input type="text" class="input_fields fields" placeholder="Product Description" required value="<?php echo isset($fetch_data['desc']) ? $fetch_data['desc'] : ''; ?>" name="update_product_desc"><br>
            <input type="file" class="input_fields fields" required accept="image/png,image/jpg,image/jpeg" name="update_product_image"> 
            <div class="btns">
                <input type="submit" class="edit_btn" value="Update" name="update_product">
                <input type="reset" id="close-edit" class="cancel_btn" value="Cancel">   
            </div>
        </form>
    </section>
</body>
</html>
