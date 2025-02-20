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
    <link rel="stylesheet" type="text/css" href="../include/nav.css">


</head>
<body>
    <?php include "../include/nav.php"; ?>
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
