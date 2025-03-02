<?php
include '../include/db.php';
session_start();
$id = $_SESSION['id'];

// Fetch user data
$accCheck = "SELECT * FROM customeruser WHERE id=$id";
$result = $conn->query($accCheck);
$row = mysqli_fetch_assoc($result);

$uname = $row["username"];
$umail = $row["Email"];
$uphone = $row["phone"];
$uaddress = $row["address"];
$profileImage = $row["profile_image"] ?? "user.png"; // Show default image if no profile image
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="../css/profile.css">
    <link rel="stylesheet" type="text/css" href="../include/nav.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

</head>
<body>
    <?php include "../include/nav.php"; ?>

    <div class="container">
        <div class="profile-img">
            <!-- Display profile image -->
            <img src="../img/<?php echo $profileImage; ?>" alt="profile" height="150px" id="profilePic">
            
            <!-- Upload profile picture form -->
            <form action="upload_profile.php" method="POST" enctype="multipart/form-data">
            <label for="fileUpload">
    <i class="fa-solid fa-pen" style="font-size: 20px; cursor: pointer;"></i>
</label>

         <input type="file" name="profile_pic" id="fileUpload" style="display:none;" onchange="this.form.submit();">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
            </form>
        </div>

        <h1>Personal Profile</h1>
        <form action="update_profile.php" method="POST">
            <label for="id">ID:</label><br> 
            <input readonly type="text" value="<?php echo $id ?>" name="id">

            <label for="username">Username:</label><br>
            <input readonly type="text" value="<?php echo $uname ?>" name="name">
            
            <label for="email">Email:</label><br>
            <input readonly type="email" value="<?php echo $umail ?>" name="email">

            <label for="phone">Phone:</label><br>
            <input readonly type="number" value="<?php echo $uphone ?>" name="phone">
            
            <label for="address">Address:</label><br>
            <input readonly type="text" value="<?php echo $uaddress ?>" name="address">

            <button type='submit' name='edit' formaction="update_profile.php">Edit</button>
        </form>
    </div>

</body>
</html>
