<?php
if (isset($_POST['submit'])) {
    include '../database/database.php';

    $fname = trim($_POST["first-name"]);
    $lname =  trim($_POST["last-name"]);
    $umail =  trim($_POST["email"]);
    $upw =  trim($_POST["password"]);
    $upwc =  trim($_POST["confirm-password"]);
    $accCheck = "SELECT * FROM users WHERE email = '$umail'";
    $result = $conn->query($accCheck);

    // Check if any field is empty
    if (empty($fname) || empty($lname) || empty($umail) || empty($upw) || empty($upwc)) {
        echo "<script>alert('Please fill in all fields!');</script>";
    }
     else {
        // Validate name format (first & last name)
        if (!preg_match("/^[a-zA-Z][a-zA-Z\s']{2,19}$/", $fname) || !preg_match("/^[a-zA-Z][a-zA-Z\s']{2,19}$/", $lname)) {
            echo "<script>alert('Invalid name format or length (3 to 20 characters, starting with an alphabet)!');</script>";
        }
        // Validate email format (only Gmail addresses)
        elseif (!filter_var($umail, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $umail)) {
            echo "<script>alert('Invalid email format or not a Gmail address!');</script>";
        }
        // Validate password (min 6 chars, 1 uppercase, 1 lowercase, 1 digit)
        elseif (strlen($upw) < 6 || !preg_match('/[A-Z]/', $upw) || !preg_match('/[a-z]/', $upw) || !preg_match('/[0-9]/', $upw)) {
            echo "<script>alert('Password must be at least 6 characters long, contain at least one uppercase letter, and one number!');</script>";
        }
        // Check if passwords match
        elseif ($upw !== $upwc) {
            echo "<script>alert('The passwords do not match!');</script>";
        } elseif ($result->num_rows > 0) {
            echo "<script>alert('An account with this email or phone number already exists!');</script>";
        }else {
            $sql = "INSERT INTO users (first_name,last_name, email, password) VALUES ('$fname', '$lname', '$umail', '$upw')";

          if ($conn->query($sql) === true) {
              echo "<script>alert('Success');</script>";
          } else {
              echo "<script>alert('Failure');</script>" . $conn->error;
          }
      }
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - GreenCart</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="icon" type="image/png" href="../img/logo.png">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Header -->
    <?php include_once '../includes/header.php'; ?>

    <!-- Signup Section -->
    <section class="auth-section">
        <div class="container">
            <div class="auth-container">
                <div class="auth-header">
                    <h2>Create an Account</h2>
                    <p>Join our community of plant lovers</p>
                </div>
                <form id="signup-form" class="auth-form" action="" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first-name">First Name</label>
                            <input type="text" id="first-name" name="first-name" placeholder="Enter your first name" >
                        </div>
                        <div class="form-group">
                            <label for="last-name">Last Name</label>
                            <input type="text" id="last-name" name="last-name" placeholder="Enter your last name" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" <?php if (isset($_GET['email'])) echo "value='{$_GET['email']}'"; ?>>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-input">
                            <input type="password" id="password" name="password" placeholder="Create a password" >
                            <button type="button" class="toggle-password">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                        <div class="password-strength">
                            <div class="strength-meter">
                                <div class="strength-segment"></div>
                                <div class="strength-segment"></div>
                                <div class="strength-segment"></div>
                                <div class="strength-segment"></div>
                            </div>
                            <span class="strength-text">Password strength</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirm-password">Confirm Password</label>
                        <div class="password-input">
                            <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password" >
                            <button type="button" class="toggle-password">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block" name="submit">Create Account</button>
                    <div class="auth-footer">
                        <p>Already have an account? <a href="login.php">Login</a></p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include_once '../includes/footer.php'; ?>
    <script src="../js/script.js"></script>
</body>

</html>