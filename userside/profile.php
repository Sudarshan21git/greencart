<?php
include '../include/db.php';
session_start();
$id=$_SESSION['id'];
$accCheck = "SELECT * FROM customeruser WHERE id=$id";
$result = $conn->query($accCheck);
$row = mysqli_fetch_assoc($result);
$uname = $row["username"];
$umail = $row["Email"];
$uphone = $row["phone"];
$uaddress = $row["address"];


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="../css/profile.css">


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
        <li><a href="./product.php">Product</a></li>
       
        <li><a href="./blog.php">Blog</a></li>
        <li><a href="./cart.php">Cart</a></li>
        <div class= "dropdown">
        <li><a href="#" class="dropbtn">My profile</a></li>
        <div class="dropdown-content">
        <a href="profile.php">Manage My Account</a>
        <a href="Myorders.php">My orders</a>
        <a href="logout.php">Logout</a>
    </div>
    </div>


     
    </ul>
    <div class="hamburger-menu">
        <div class="line"></div>
        <div class="line"></div>
        <div class="line"></div>
    </div>
</nav>

<div class="container">
    <img src="../img/user.png" alt="profile" height="150px">
        <h1>Personal Profile</h1>
        <form action="update_profile.php" method="POST">
            <label for="id">ID:</label><br> 
            <input readonly type="text" value= "<?php echo $id?>" name="id">

            <label for="username">Username:</label><br>
            <input readonly type="text" value= "<?php echo $uname?>" name="name">
            
            <label for="email">Email:</label><br>
            <input  readonly type="email" value= "<?php echo $umail?>" name="email">

            <label for="phone">Phone:</label><br>
            <input readonly type="number" value= "<?php echo $uphone?>" name="phone">
            
            <label for="address">Address:</label><br>
            <input readonly type="text" value= "<?php echo $uaddress?>" name="address">

            <button type='submit' name='edit' formaction="update_profile.php">Edit</button>

        </form>
    </div>
    

</body>
</html>
