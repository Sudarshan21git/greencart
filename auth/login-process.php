<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

</body>

<script>
    function promptMessage(msg, showSignupButton = false) {
        // Create a modal overlay
        const overlay = document.createElement("div");
        overlay.style.position = "fixed";
        overlay.style.top = "0";
        overlay.style.left = "0";
        overlay.style.width = "100%";
        overlay.style.height = "100%";
        overlay.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
        overlay.style.display = "flex";
        overlay.style.justifyContent = "center";
        overlay.style.alignItems = "center";
        overlay.style.zIndex = "1000";

        // Create the modal content
        const modal = document.createElement("div");
        modal.style.backgroundColor = "white";
        modal.style.padding = "20px";
        modal.style.borderRadius = "8px";
        modal.style.boxShadow = "0 2px 10px rgba(0, 0, 0, 0.2)";
        modal.style.width = "300px";
        modal.style.maxWidth = "90%";
        modal.style.textAlign = "center"; // Center text

        // Create the title
        const title = document.createElement("h3");
        title.textContent = msg;
        title.style.marginTop = "0";
        title.style.marginBottom = "15px";

        // Create the buttons container
        const buttonContainer = document.createElement("div");
        buttonContainer.style.display = "flex";
        buttonContainer.style.justifyContent = "space-between";
        buttonContainer.style.width = "100%";
        buttonContainer.style.gap = "10px"; // Adds some space between the buttons

        // Create the 'Okay' button
        const button = document.createElement("button");
        button.textContent = "Okay";
        button.style.backgroundColor = "#128C7E";
        button.style.color = "white";
        button.style.border = "none";
        button.style.padding = "6px 12px"; // Smaller padding
        button.style.borderRadius = "4px";
        button.style.cursor = "pointer";
        button.style.flex = "1"; // Take equal space with the other button

        button.onclick = function() {
            window.location.href = "login.php";
        };

        buttonContainer.appendChild(button); // Add to button container

        // Create the 'Go Signup' button, only show if specified
        if (showSignupButton) {
            const redirectSignup = document.createElement("button");
            redirectSignup.textContent = "Go Signup";
            redirectSignup.style.backgroundColor = "#128C7E";
            redirectSignup.style.color = "white";
            redirectSignup.style.border = "none";
            redirectSignup.style.padding = "6px 12px"; // Smaller padding
            redirectSignup.style.borderRadius = "4px";
            redirectSignup.style.cursor = "pointer";
            redirectSignup.style.flex = "1"; // Take equal space with the other button

            redirectSignup.onclick = function() {
                window.location.href = "signup.php?email=" + encodeURIComponent("<?php echo $_SESSION['login_email']; ?>");
            };

            buttonContainer.appendChild(redirectSignup); // Add to button container
        }

        // Add the title and buttons container to the modal
        modal.appendChild(title);
        modal.appendChild(buttonContainer);
        overlay.appendChild(modal);

        // Add the modal to the body
        document.body.appendChild(overlay);
    }
</script>

</html>

<?php

// Include database connection
include '../database/database.php';

// Function to sanitize input
function sanitize($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $user_email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $user_pw = trim($_POST['password']);
    $_SESSION['login_email'] = $user_email;
    $_SESSION['login_password'] = $user_pw;

    // Validate input
    if (empty($user_email) || empty($user_pw)) {
        echo "<script>promptMessage('Please fill all the Fields')</script>";
        exit();
    }

    if (!filter_var($user_email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $user_email)) {
        echo "<script>promptMessage('Invalid Email format')</script>";
        exit();
    }

    // Prepare the statement to prevent SQL injection
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $user_email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            // Verify the password
            if ($user_pw == $row['password']) {
                // Store session variables
                $_SESSION['email'] = $row['email'];
                $_SESSION['fname'] = $row['first_name'];
                $_SESSION['lname'] = $row['last_name'];
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['is_admin'] = $row['is_admin'];
                $_SESSION['phone'] = $row['phone'];

                // Redirect based on role
                if ($row['is_admin'] == 1) {
                    echo "<script>window.location.href='../NiceAdmin/index.php';</script>";
                } else {
                    echo "<script>window.location.href='../user/shop.php';</script>";

                }
            } else {    
                echo "<script>
                promptMessage('Invalid password');
            </script>";
            }
        } else {
            // When no user is found, we want to display the "Go Signup" button
            echo "<script>promptMessage('No user found with this Email', true);</script>";
        }

        // Close statement
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>promptMessage('Database Error')</script>";
    }

    // Close database connection
    mysqli_close($conn);
}
?>