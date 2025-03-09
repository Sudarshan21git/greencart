<?php
     include('../include/db.php');

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $product_id = $_GET['delete'];

    // Delete the product from the database
    $delete_query = mysqli_query($conn, "DELETE FROM products WHERE id = $product_id");

    if ($delete_query) {
        // Redirect back to the viewproducts.php page after successful deletion
        header("Location: viewproducts.php");
        exit();
    } else {
       
        echo "Failed to delete product.";
    }
} else {
    
    echo "Invalid product ID.";
}
?>
