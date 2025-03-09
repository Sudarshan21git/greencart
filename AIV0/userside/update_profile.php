<!-- php backend -->
<?php
    session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])) {
    include '../include/db.php';
    $userID = $_POST['id'];
    $username = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
 
  

}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="../css/profile.css">
    <link rel="stylesheet" href="edituser.css">
    <link rel="stylesheet" href="../include/nav.css">


  
</head>
<body>
<!-- nav bar -->
<?php include "../include/nav.php"; ?>
     <!-- nav bar -->
<div class="container">
<img src="../img/editprofile.png" alt="profile" height="160px">
        <h1>Edit Profile</h1>

        <form action="confirmUserEdit.php" method="post">
            
        <label for="username">ID:</label>
            <input  readonly type="text" id="userid" name="id" value="<?php echo $userID ?>"><br>
     

            <label for="username">Username:</label>
            <input type="text" id="username" name="change_username" value="<?php echo $username ?>"><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="change_email" value="<?php echo $email ?>"><br>

            <label for="phone">Phone:</label>
            <input type="number" id="phone" name="change_phone" value="<?php echo $phone ?>"><br>
            
            <label for="address">Address:</label>
            <input type="text" id="address" name="change_address" value="<?php echo $address ?>"><br>

       

            <button type="submit" name="confirm">Confirm</button>
            <button type="submit" name="cancel" class="cancel-btn">Cancel</button>
            <a href="change_password.php">change Password</a>
        </form>
    </div>

</body>
</html>