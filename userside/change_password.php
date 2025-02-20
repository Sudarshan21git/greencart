<?php
session_start();
include '../include/db.php';

$userId = $_SESSION['id'];
$qry = "SELECT * FROM customeruser WHERE id=$userId ";
$result = $conn->query($qry);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change'])) {
        $cpw = $_POST['cpw'];
        $npw = $_POST['npw'];
        $cnpw = $_POST['cnpw'];
        $password = $row['Password'];

        if (empty($cpw) || empty($npw) || empty($cnpw)) {
            echo "<script>alert('Please fill in all the fields.');</script>";
        } elseif ($cpw !== $password) {
            echo "<script>alert('Current password is incorrect.');</script>";
        } elseif ($npw !== $cnpw) {
            echo "<script>alert('New passwords do not match.');</script>";
        } elseif (strlen($npw) < 6 || !preg_match('/[A-Z]/', $npw) || !preg_match('/[0-9]/', $npw)) {
            echo "<script>alert('Password must be at least 6 characters long, contain at least one uppercase letter, and at least one number.');</script>";
        } else {
            $query = "UPDATE customeruser SET password='$npw' WHERE id=$userId";
            $result2 = $conn->query($query);
            if ($result2) {
                echo "<script>alert('Password changed successfully!'); window.location.href='profile.php';</script>";
                exit;
            }
        }
    }

    if (isset($_POST['cancel'])) {
        header("Location:profile.php");
        exit;
    }
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="../css/Cp.css">
    <link rel="stylesheet" href="../include/nav.css">
    <script>
        function validateForm() {
            let cpw = document.getElementById("cpw").value;
            let npw = document.getElementById("npw").value;
            let cnpw = document.getElementById("cnpw").value;
            let error = "";

            if (!cpw || !npw || !cnpw) {
                error = "Please fill in all fields.";
            } else if (npw.length < 6 || !/[A-Z]/.test(npw) || !/[0-9]/.test(npw)) {
                error = "Password must be at least 6 characters long, contain at least one uppercase letter, and one number.";
            } else if (npw !== cnpw) {
                error = "New passwords do not match.";
            }

            if (error) {
                document.getElementById("error-msg").innerText = error;
                return false;
            }
            return true;
        }
    </script>
</head>

<body>
    
<!-- Navbar -->
<?php include "../include/nav.php"; ?>

<div class='container'>
    <form action='change_password.php' method='post' onsubmit="return validateForm()">
        <h2>Change Password</h2>
        <p id="error-msg" style="color: red; font-weight: bold;"></p>

        <div class='form-group'>
            <label for='cpw'>Current Password:</label>
            <input type='password' id='cpw' name='cpw'>
        </div>
        <div class='form-group'>
            <label for='npw'>New Password:</label>
            <input type='password' id='npw' name='npw'>
        </div>
        <div class='form-group'>
            <label for='cnpw'>Confirm New Password:</label>
            <input type='password' id='cnpw' name='cnpw'>
        </div>

        <div class='form-group'>
            <button type='submit' name='change' class='green-button'>Change Password</button>
            <button type='cancel' name='cancel' class='red-button'>Cancel</button>
        </div>
    </form>
</div>

</body>

</html>
