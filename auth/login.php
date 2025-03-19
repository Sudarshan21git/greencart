<?php
session_start();
// $error_message = isset($_SESSION['error']) ? $_SESSION['error'] : "";
$stored_email = isset($_SESSION['login_email']) ? $_SESSION['login_email'] : "";
$stored_password = isset($_SESSION['login_password']) ? $_SESSION['login_password'] : ""; 
unset($_SESSION['error']); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GreenCart</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="icon" type="image/png" href="../img/logo.png">
</head>
<body>
    <?php include_once "../includes/header.php"; ?>

    <section class="auth-section">
        <div class="container">
            <div class="auth-container">
                <div class="auth-header">
                    <h2>Login to Your Account</h2>
                    <p>Welcome back! Please enter your details</p>
                </div>

                

                <form id="login-form" class="auth-form" action="login-process.php" method="POST">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" id="email" name="email" placeholder="Enter your email" 
                            value="<?php echo htmlspecialchars($stored_email); ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-input">
                            <input type="password" id="password" name="password" placeholder="Enter your password" value="<?php echo htmlspecialchars($stored_password); ?>">
                            <button type="button" class="toggle-password">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                    <div class="auth-footer">
                        <p>Don't have an account? <a href="signup.php">Sign up</a></p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <?php include_once "../includes/footer.php"; ?>
<script src="../js/script.js"></script>
    <script>
        // Retain password securely using JavaScript
        document.addEventListener("DOMContentLoaded", function () {
            let storedPassword = "<?php echo addslashes($stored_password); ?>";
            if (storedPassword) {
                document.getElementById("password").value = storedPassword;
            }
        });
    </script>
</body>
</html>
