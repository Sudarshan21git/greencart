 
<!-- backend backend backend backend backend backend backend backend backend backend backend backend backend backend backend backend  -->
<?php
if(isset($_POST['submit'])){
  include '../include/db.php';

    $uname = $_POST["username"];
    $umail = $_POST["email"];
    $uphone = $_POST["phone"];
    $uaddress = $_POST["address"];
    $upw = $_POST["password"];
    $upwc = $_POST["confirm_password"];
    $accCheck = "SELECT * FROM customeruser WHERE email = '$umail' OR phone='$uphone'";
    $result = $conn->query($accCheck);
    if (empty($uname) || empty($umail) || empty($uphone) || empty($uaddress) || empty($upw) || empty($upwc)) {
      echo "Please fill in all fields!";
  } else {
      // Perform other validations
      if (!preg_match("/^[a-zA-Z][a-zA-Z\s']*$/", $uname) || strlen($uname) < 3 || strlen($uname) > 20) {
          echo "Invalid name format or length (3 to 20 characters, starting with an alphabet)!";
      } elseif (!filter_var($umail, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $umail)) {
        echo "Invalid email format or not a Gmail address!";
      } elseif (!preg_match('/^(98|97)\d{8}/', $uphone)) {
          echo "Enter a valid phone number!!";
      } elseif (!preg_match("/^[a-zA-Z0-9\s,'-]*$/", $uaddress)) {
          echo "Address should contain only alphabets, numbers, spaces, commas, apostrophes, and hyphens.";
      } elseif (strlen($upw) < 6 || !preg_match('/[A-Z]/', $upw) || !preg_match('/[0-9]/', $upw)) {
          echo "Password must be at least 6 characters long, contain at least one capital letter, and at least one number!!";
      } elseif ($upw !== $upwc) {
          echo "The passwords do not match";
      }  elseif ($result->num_rows > 0) {
        echo "An account with this email or phone number already exists!";
    }else {
          $sql = "INSERT INTO customeruser (username, Email, Password, phone, address) VALUES ('$uname', '$umail', '$upw', '$uphone', '$uaddress')";

          if ($conn->query($sql) === true) {
              echo "Success";
          } else {
              echo "Failure: " . $conn->error;
          }
      }
  }

  $conn->close();
}?>
<!-- front end front end front end  front end front end front end front end front end front end front end  -->
<!DOCTYPE html>
<html>
<head>
	<title>Sign up</title>
	<link rel="stylesheet" href="../css/signup.css">
  <link rel="stylesheet" href="../include/nav.css">
</head>
<body>
    <!-- nav bar -->
    <?php include "../include/nav.php"; ?>
     <!-- nav bar -->
	<section id="signup-us">
		<div class="background-image"></div>
		<div class="signup-box">
			<h1>Sign up</h1>
			
      <form method="post"  >
    <label for="username">Username:</label>
    <input type="text" id="username" name="username">

    <label for="email">Email:</label>
    <input type="email" id="email" name="email">

    <label for="phone">Phone Number:</label>
    <input type="text" id="phone" name="phone">

    <label for="text">Address:</label>
    <input type="text" id="address" name="address">

    <label for="password">Password:</label>
    <input type="password" id="password" name="password">

    <label for="confirm_password">Confirm Password:</label>
    <input type="password" id="confirm_password" name="confirm_password">

    <button type="submit" name="submit">Sign Up</button>
</form>
		</div>
	</section>

</body>
</html>