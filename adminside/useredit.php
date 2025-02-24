<?php
include "header.php";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])) {
        include "../include/db.php";

    $id = $_POST['id'];
    $username = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $password = $_POST['password'];
  

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="editUser.css">
    <link rel="stylesheet" href="admin.css">

    <title>Edit User</title>
</head>

<body>
    <div class="container">
        <h1>Edit User</h1>
        <form action="confirmUserEdit.php" method="post">
            <label for="id">ID:</label>s
            <input readonly type="number" id="id" name="change_id" value="<?php echo $id ?>"><br>

            <label for="username">Username:</label>
            <input type="text" id="username" name="change_username" value="<?php echo $username ?>"><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="change_email" value="<?php echo $email ?>"><br>

            <label for="phone">Phone:</label>
            <input type="number" id="phone" name="change_phone" value="<?php echo $phone ?>"><br>
            
            <label for="address">Address:</label>
            <input type="text" id="address" name="change_address" value="<?php echo $address ?>"><br>

            <label for="password">Password:</label>
            <input type="text" id="password" name="change_password" value="<?php echo $password ?>"><br>

            <button type="submit" name="confirm">Confirm</button>
            <button type="submit" name="cancel">Cancel</button>
        </form>
    </div>
</body>

</html>
