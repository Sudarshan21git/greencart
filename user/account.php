<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
else if ($_SESSION['is_admin'] == 1) {
    header("Location: 404.html");
}

// Include database connection
include '../database/database.php';

// Initialize variables
$success_message = '';
$error_message = '';

// Get user information
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
} else {
    $error_message = "Database error: " . mysqli_error($conn);
}

// Handle form submission for profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // Get form data
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = isset($_POST['phone']) ? mysqli_real_escape_string($conn, $_POST['phone']) : '';
    $address = isset($_POST['address']) ? mysqli_real_escape_string($conn, $_POST['address']) : '';

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format";
    } else if (!preg_match('/^(98|97)\d{8}$/', $phone)) {
        $error_message = "Enter a valid phone number!! ";
        
    } else {
        // Check if email already exists for another user
        $check_email_sql = "SELECT user_id FROM users WHERE email = ? AND user_id != ?";
        $check_stmt = mysqli_prepare($conn, $check_email_sql);

        if ($check_stmt) {
            mysqli_stmt_bind_param($check_stmt, "si", $email, $user_id);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);

            if (mysqli_stmt_num_rows($check_stmt) > 0) {
                $error_message = "Email already in use by another account";
            } else {
                // Update user profile
                $update_sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ? WHERE user_id = ?";
                $update_stmt = mysqli_prepare($conn, $update_sql);

                if ($update_stmt) {
                    mysqli_stmt_bind_param($update_stmt, "sssssi", $fname, $lname, $email, $phone, $address, $user_id);

                    if (mysqli_stmt_execute($update_stmt)) {
                        // Update session variables
                        $_SESSION['fname'] = $fname;
                        $_SESSION['lname'] = $lname;
                        $_SESSION['email'] = $email;

                        $success_message = "Profile updated successfully";

                        // Refresh user data
                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param($stmt, "i", $user_id);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        $user = mysqli_fetch_assoc($result);
                        mysqli_stmt_close($stmt);
                    } else {
                        $error_message = "Error updating profile: " . mysqli_error($conn);
                    }

                    mysqli_stmt_close($update_stmt);
                } else {
                    $error_message = "Database error: " . mysqli_error($conn);
                }
            }

            mysqli_stmt_close($check_stmt);
        } else {
            $error_message = "Database error: " . mysqli_error($conn);
        }
    }
}

// Handle form submission for password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current-password'];
    $new_password = $_POST['new-password'];
    $confirm_password = $_POST['confirm-password'];

    // Validate password
    if ($new_password !== $confirm_password) {
        $error_message = "New passwords do not match";
    } else {
        // Get current password from database
        $password_sql = "SELECT password FROM users WHERE user_id = ?";
        $password_stmt = mysqli_prepare($conn, $password_sql);

        if ($password_stmt) {
            mysqli_stmt_bind_param($password_stmt, "i", $user_id);
            mysqli_stmt_execute($password_stmt);
            mysqli_stmt_bind_result($password_stmt, $stored_password);
            mysqli_stmt_fetch($password_stmt);
            mysqli_stmt_close($password_stmt);

            // Direct string comparison (no hashing)
            if ($current_password === $stored_password) {
                // Update password directly (no hashing)
                $update_password_sql = "UPDATE users SET password = ? WHERE user_id = ?";
                $update_password_stmt = mysqli_prepare($conn, $update_password_sql);

                if ($update_password_stmt) {
                    mysqli_stmt_bind_param($update_password_stmt, "si", $new_password, $user_id);

                    if (mysqli_stmt_execute($update_password_stmt)) {
                        $success_message = "Password changed successfully";
                    } else {
                        $error_message = "Error changing password: " . mysqli_error($conn);
                    }

                    mysqli_stmt_close($update_password_stmt);
                } else {
                    $error_message = "Database error: " . mysqli_error($conn);
                }
            } else {
                $error_message = "Current password is incorrect";
            }
        } else {
            $error_message = "Database error: " . mysqli_error($conn);
        }
    }
}

// Close database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - GreenCart</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/account-styles.css">
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Header -->
    <?php include_once "../includes/header.php"; ?>

    <!-- Account Section -->
    <section class="account-section">
        <div class="container">
            <div class="account-header">
                <h1 class="section-title">My Account</h1>
                <p>Manage your personal information and preferences</p>
            </div>

            <div class="account-container">
                <!-- Account Sidebar -->
                <div class="account-sidebar">
                    <div class="user-info">
                        <div class="user-avatar">
                            <span><?php echo strtoupper(substr($_SESSION['fname'], 0, 1) . substr($_SESSION['lname'], 0, 1)); ?></span>
                        </div>
                        <div class="user-details">
                            <h3><?php echo htmlspecialchars($_SESSION['fname'] . ' ' . $_SESSION['lname']); ?></h3>
                            <p><?php echo htmlspecialchars($_SESSION['email']); ?></p>
                        </div>
                    </div>

                    <nav class="account-nav">
                        <ul>
                            <li><a href="dashboard.php">Dashboard</a></li>
                            <li><a href="account.php" class="active">Account Settings</a></li>
                            <li><a href="orders.php">My Orders</a></li>
                            <li><a href="reviews.php">My Reviews</a></li>
                            <li><a href="../auth/logout.php">Logout</a></li>
                        </ul>
                    </nav>
                </div>

                <!-- Account Content -->
                <div class="account-content">
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success">
                            <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-error">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <div class="account-tabs">
                        <div class="tab-header">
                            <button class="tab-btn active" data-tab="profile">Profile Information</button>
                            <button class="tab-btn" data-tab="password">Change Password</button>
                        </div>

                        <div class="tab-content">
                            <!-- Profile Tab -->
                            <div class="tab-pane active" id="profile-tab">
                                <h2>Profile Information</h2>
                                <form action="account.php" method="POST" class="account-form">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="fname">First Name</label>
                                            <input type="text" id="fname" name="fname" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="lname">Last Name</label>
                                            <input type="text" id="lname" name="lname" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email Address</label>
                                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="phone">Phone Number</label>
                                        <input type="tel" id="phone" name="phone" value="<?php echo isset($user['phone']) ? htmlspecialchars($user['phone']) : ''; ?>">
                                    </div>

                                    <h3 class="section-subtitle">Shipping Address</h3>
                                    <div class="form-group">
                                        <label for="address">Street Address</label>
                                        <input type="text" id="address" name="address" value="<?php echo isset($user['address']) ? htmlspecialchars($user['address']) : ''; ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="created-at">Member Since</label>
                                        <input type="text" id="created-at" value="<?php echo date('F d, Y', strtotime($user['created_at'])); ?>" readonly>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Password Tab -->
                            <div class="tab-pane" id="password-tab">
                                <h2>Change Password</h2>
                                <form action="account.php" method="POST" class="account-form">
                                    <div class="form-group">
                                        <label for="current-password">Current Password</label>
                                        <input type="password" id="current-password" name="current-password" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="new-password">Password</label>
                                        <div class="password-input">
                                            <input type="password" id="new-password" name="new-password" placeholder="Create a password">
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
                                            <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password">
                                            <button type="button" class="toggle-password">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="form-actions">
                                            <button type="submit" name="change_password" class="btn btn-primary">Update Password</button>
                                        </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include_once "../includes/footer.php"; ?>
    <script src="../js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            const tabBtns = document.querySelectorAll('.tab-btn');
            const tabPanes = document.querySelectorAll('.tab-pane');

            tabBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Remove active class from all buttons and panes
                    tabBtns.forEach(b => b.classList.remove('active'));
                    tabPanes.forEach(p => p.classList.remove('active'));

                    // Add active class to clicked button
                    this.classList.add('active');

                    // Show corresponding tab pane
                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(tabId + '-tab').classList.add('active');
                });
            });

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            if (alerts.length > 0) {
                setTimeout(() => {
                    alerts.forEach(alert => {
                        alert.style.opacity = '0';
                        setTimeout(() => {
                            alert.style.display = 'none';
                        }, 500);
                    });
                }, 5000);
            }
        });
    </script>
</body>

</html>