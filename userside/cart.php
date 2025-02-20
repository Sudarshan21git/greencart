<?php
session_start();
$userId = $_SESSION['id'];
include '../include/db.php';

if (isset($_POST['update_update_btn'])) {
   $update_value = $_POST['update_quantity'];
   $update_id = $_POST['update_quantity_id'];
   mysqli_query($conn, "UPDATE `cart` SET quantity = '$update_value' WHERE id = '$update_id'");
   header('location:cart.php');
}

if (isset($_GET['remove'])) {
   $remove_id = $_GET['remove'];
   mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$remove_id'");
   header('location:cart.php');
}

if (isset($_GET['delete_all'])) {
   mysqli_query($conn, "DELETE FROM `cart` WHERE userid=$userId");
   header('location:cart.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Cart</title>
   <!-- Bootstrap CSS -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" type="text/css" href="../include/nav.css">
   <link rel="stylesheet" href="../css/carts.css">
</head>

<body>
   <?php include "../include/nav.php"; ?>
   
   <div class="container my-5">
      <section class="shopping-cart">
         <h1 class="text-center text-success">Shopping Cart</h1>

         <div class="table-responsive">
            <table class="table table-bordered text-center">
               <thead class="table-success">
                  <tr>
                     <th>Image</th>
                     <th>Name</th>
                     <th>Price</th>
                     <th>Quantity</th>
                     <th>Stock</th>
                     <th>Total Price</th>
                     <th>Action</th>
                  </tr>
               </thead>
               <tbody>
                  <?php
                  $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE userId=$userId");
                  $grand_total = 0;

                  if (mysqli_num_rows($select_cart) > 0) {
                     while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
                        $product_id = $fetch_cart['productId'];
                        $get_product_data = mysqli_query($conn, "SELECT * FROM `products` WHERE id=$product_id");
                        $fetch_product = mysqli_fetch_assoc($get_product_data);
                        $pname = $fetch_product['name'];
                        $stock = $fetch_product['stock'];
                  ?>
                        <tr>
                           <td><img src="../img/<?php echo $fetch_product['image']; ?>" height="100" alt=""></td>
                           <td><?php echo $pname; ?></td>
                           <td>Rs.<?php echo floatval($fetch_product['price']); ?>/-</td>
                           <td>
                              <form action="" method="post" class="d-flex align-items-center justify-content-center">
                                 <input type="hidden" name="update_quantity_id" value="<?php echo $fetch_cart['id']; ?>">
                                 <input type="number" name="update_quantity" class="form-control w-50 me-2" min="1" max="<?php echo $stock; ?>" value="<?php echo $fetch_cart['quantity']; ?>">
                                 <input type="submit" value="Update" name="update_update_btn" class="btn btn-sm btn-success">
                              </form>
                           </td>
                           <td><?php echo $stock; ?></td>
                           <td>Rs.<?php echo floatval($fetch_product['price'] * $fetch_cart['quantity']); ?>/-</td>
                           <td>
                              <a href="cart.php?remove=<?php echo $fetch_cart['id']; ?>" onclick="return confirm('Remove item from cart?')" class="btn btn-sm btn-danger">
                                 <i class="fas fa-trash"></i> Remove
                              </a>
                           </td>
                        </tr>
                  <?php
                        $grand_total += floatval($fetch_product['price'] * $fetch_cart['quantity']);
                     }
                  }
                  ?>
                  <tr class="table-secondary">
                     <td><a href="./product.php" class="btn btn-warning">Continue Shopping</a></td>
                     <td colspan="4"><strong>Grand Total</strong></td>
                     <td><strong>Rs.<?php echo $grand_total; ?>/-</strong></td>
                     <td><a href="cart.php?delete_all" onclick="return confirm('Are you sure you want to delete all?');" class="btn btn-danger">Delete All</a></td>
                  </tr>
               </tbody>
            </table>
         </div>

         <div class="text-center mt-4">
            <a href="../checkout.php" class="btn btn-primary <?= ($grand_total > 1) ? '' : 'disabled'; ?>">Proceed to Checkout</a>
         </div>
      </section>
   </div>

   <!-- Bootstrap JS -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
