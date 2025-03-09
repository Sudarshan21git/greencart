
    
    <?php 
include '../include/db.php';   
    ?>
<?php
        if (isset($_POST['login'])) {
          include '../include/db.php';
            $username = $_POST['username'];
            $password = $_POST['password'];

            $sql = "SELECT id, username, role FROM customeruser WHERE username='{$username}' AND password='{$password}'";
            $result = mysqli_query($conn, $sql) or die("Query Failed");

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    session_start();
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['id'] = $row['id'];
                    $_SESSION['user_role'] = $row['role'];
                    if($row ['role'] == 1) {
                    header("location:../adminside/admin.php");
                }else{
                  header("location:./product.php");
                }
                }

            } else {
                echo  "<script>alert('Username and password are not Match')</script>";
            }
        }
        ?>

<!-- Frontend  Fontend  Fontend Fontend  Fontend  Fontend  Fontend Fontend  Fontend  Fontend ----------->
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Login Page</title>
	<link rel="stylesheet" type="text/css" href="../css/login.css">
  <link rel="stylesheet" type="text/css" href="../include/nav.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
  <?php include "../include/nav.php"; ?>

	<div class="background-image">
	<div class="login-box Login">
			<h1>Login</h1>
			<div class="social-media-icons">
				<a href="#"><i class="fab fa-instagram"></i></a>
				<a href="#"><i class="fab fa-facebook"></i></a>
				<a href="https://www.youtube.com/@sudarshansharma867"><i class="fab fa-youtube"></i></a>
				<a href="#"><i class="fab fa-twitter"></i></a>
			</div>
			<div class="or-text">
				<div class="line"></div>
				<div class="or">or</div>
				<div class="line"></div>
			</div>
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <input type="text" name="username" placeholder="Username">
    <input type="password" name="password" placeholder="Password">
    <button type="submit" name="login">Login</button>
</form>
<div class="forgot-create">
				
				<a href="./signup.php">Create Account</a>
			</div>
	
		</div>
	</div>
</body>
</html>