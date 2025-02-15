<?php
session_start();
$userId = $_SESSION['id'];
@include '../db.php';

if (isset($_POST['update_update_btn'])) {
   $update_value = $_POST['update_quantity'];
   $update_id = $_POST['update_quantity_id'];
   $update_quantity_query = mysqli_query($conn, "UPDATE `cart` SET quantity = '$update_value' WHERE id = '$update_id'");
   if ($update_quantity_query) {
      header('location:cart.php');
   };
};

if (isset($_GET['remove'])) {
   $remove_id = $_GET['remove'];
   mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$remove_id'");
   header('location:cart.php');
};

if (isset($_GET['delete_all'])) {
   mysqli_query($conn, "DELETE FROM `cart` where userid=$userId");
   header('location:cart.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Cart</title>
   <link rel="stylesheet" href="cart.css">
   <link rel="stylesheet" href="reset.css">
   <link rel="stylesheet" href="../carts.css">


</head>

<body>
   <nav>
      <div class="logo">
         <img src="../logo.png" alt="Nature's Nursery">
         <div class="logo-text">
            <span class="green">Nature's</span> <span class="white">Nursery</span>
         </div>
      </div>
      <ul class="menu">
         <li><a href="../home.php">Home</a></li>
         <li><a href="../Product page/product.php">Product</a></li>
         <li><a href="../Blog/blog.php">Blog</a></li>
         <?php
         ?>
         <li> <a href="../cart/cart.php" class="cart"> Cart  </a></li>

         <?php
         if (isset($_SESSION['id'])) {
            echo '<div class="dropdown">';
            echo '<a href="" class="dropbtn">My Profile</a>';
            echo '<div class="dropdown-content">';
            echo '<a href="../profile.php">Manage My Account</a>';
            echo '<a href="../Myorders.php">My orders</a>';
            echo '<a href="../logout.php">Logout</a>';
            echo '</div>';
            echo '</div>';
         } else {
            echo '<li><a href="../loginpage/login.php">Login</a></li>';
         }
         ?>
      </ul>
      <div class="hamburger-menu">
         <div class="line"></div>
         <div class="line"></div>
         <div class="line"></div>
      </div>
   </nav>
   <div class="container">

      <section class="shopping-cart">

         <h1 class="heading" style="color: green;">shopping cart</h1>

         <table>

            <thead>
               <th>image</th>
               <th>name</th>
               <th>price</th>
               <th>quantity</th>
               <th>Stock</th>
               <th>total price</th>
               <th>action</th>
            </thead>

            <tbody>
               <?php
               include '../db.php';

               // Getting cart details
               $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE userId=$userId");
               $grand_total = 0;

               if (mysqli_num_rows($select_cart) > 0) {
                  // Loop through each cart item
                  while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
                     $product_id = $fetch_cart['productId'];

                     // Get product details for the current cart item
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
                              <form action="" method="post">
                                 <input type="hidden" name="update_quantity_id" value="<?php echo $fetch_cart['id']; ?>">
                                 <input type="number" name="update_quantity" min="1" max="<?php echo $fetch_product['stock']; ?>" value="<?php echo $fetch_cart['quantity']; ?>">
                                 <input type="submit" value="update" name="update_update_btn">
                              </form>
                           </td>
                           <td><?php echo $fetch_product['stock']; ?></td>
                           <td>Rs.<?php echo $sub_total = floatval($fetch_product['price'] * $fetch_cart['quantity']); ?>/-</td>
                           <td><a href="cart.php?remove=<?php echo $fetch_cart['id']; ?>" onclick="return confirm('Remove item from cart?')" class="delete-btn"> <i class="fas fa-trash"></i> remove</a></td>
                        </tr>
               <?php
                        $grand_total += $sub_total;
                     }
                  }
               
               ?>
               <tr class="table-bottom">
                  <td><a href="../Product page/product.php" class="option-btn" style="margin-top: 0;">continue Shopping</a></td>
                  <td colspan="4">grand total</td>
                  <td>Rs.<?php echo (floatval($grand_total)); ?>/-</td>
                  <td><a href="cart.php?delete_all" onclick="return confirm('are you sure you want to delete all?');" class="delete-btn"> delete all </a></td>
               </tr>

            </tbody>

         </table>


         <div class="checkout-btn">
            <a href="../checkout.php" class="btn <?= ($grand_total > 1) ? '' : 'disabled'; ?>">procced to checkout</a>
         </div>
      </section>

   </div>

   <!-- custom js file link  -->
   <script src="cart.js"></script>


   </div>
</body>

</html>