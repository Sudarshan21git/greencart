<?php
session_start();
include "db.php";

$userId = $_SESSION['id'];
$qry = "SELECT * FROM customeruser WHERE id=$userId ";
$result = $conn->query($qry);
// -----------------------user-----------------------------
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change'])) {
        $cpw = $_POST['cpw'];
        $npw = $_POST['npw'];
        $cnpw = $_POST['cnpw'];
        $password = $row['Password'];
        if (empty($cpw) || empty($npw) || empty($cnpw)) {
            echo "You can't leave this empty., Please fill in all the fields.";
        }

        if ($cpw == $password) {
            if ($npw === $cnpw) {
                if (strlen($npw) >= 6 && preg_match('/[A-Z]/', $npw) && preg_match('/[0-9]/', $npw)) {
                    // $hnpw = password_hash($npw, PASSWORD_DEFAULT);
                    $query = "UPDATE customeruser SET password='$npw' WHERE id=$userId";
                    $result2 = $conn->query($query);
                    if ($result2) {
                        echo "Password changed Successfully";
                        header("refresh:1;url=profile.php");
                        exit;
                    }
                } else {
                    echo "Password must be at least 6 characters long, contain at least one capital letter, and at least one number!!";
                }
            } else {
                echo "The passwords do not match";
            }
        } else {
            echo "Sorry, the current password you entered is incorrect. Please double-check and try again.";
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
    <link rel="stylesheet" href="Cp.css">
</head>

<body>
    <div class='container'>
        <form action='change_password.php' method='post'>
            <h2>Change Password</h2>
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
    </div>

</body>

</html>