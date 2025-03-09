<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm'])) {
    session_start();
    include '../include/db.php';
    $cid = $_POST['id'];
    $cusername = $_POST['change_username'];
    $cemail = $_POST['change_email'];
    $caddress = $_POST['change_address'];
    $cphone = $_POST['change_phone'];
    $qry = "SELECT * FROM customeruser WHERE id='$cid'";
    $result0 = $conn->query($qry);
    $row = mysqli_fetch_assoc($result0);

    if (empty($cusername) || empty($cemail) || empty($cphone) || empty($caddress)) {
        echo "One or more required fields are empty, Please fill in all the fields.";
    } else {
        // Validate email and phone only if they have changed
        if ($cemail != $row['Email'] || $cphone != $row['phone']) {
            if (!preg_match('/^[a-zA-Z][a-zA-Z0-9._%+-]*@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $cemail)) {
                echo "Invalid email format!  ";
            } else if (!preg_match('/^(98|97)\d{8}/', $cphone)) {
                echo "Enter a valid phone number!";
            } else {
                $accCheck = "SELECT * FROM customeruser WHERE (Email = '$cemail' OR phone='$cphone') AND id!=$cid";
                $result_1 = $conn->query($accCheck);
                if ($result_1->num_rows > 0) {
                    echo "<script>alert('User with given email or phone nubmer already exists.')</script>";
                } else {
                    // Update user details in the database
                    $query = "UPDATE customeruser SET username='$cusername', email='$cemail', phone='$cphone', address='$caddress' WHERE id=$cid";
                    $result = mysqli_query($conn, $query);
                    if ($result) {
                        $_SESSION['Email']=$cemail;
                        echo "<script>alert('Details updated successfully.'); window.location.href = 'profile.php';</script>";
                        exit;
                    } else {
                        echo "<script>alert('Error updating details.')</script>" . mysqli_error($conn);
                    }
                }
            }
        } else {
            // Update user details in the database if other fields have changed
            if (strlen($cusername) < 3 || strlen($cusername) > 20 || !preg_match('/^[a-zA-Z][a-zA-Z\s]*[a-zA-Z]$/', $cusername)) {
                echo "Enter a valid name!";
            } else if (strlen($caddress) < 5 || strlen($caddress) > 30) {
                echo "Enter a valid location!";
            } else {
                $query = "UPDATE customeruser SET username='$cusername', address='$caddress' WHERE id=$cid";
                $result0 = mysqli_query($conn, $query);
                if ($result0) {
                    echo "<script>alert('Details updated successfully.'); window.location.href = 'profile.php';</script>";
                    exit;
                } else {
                    echo "<script>alert('Error updating details.')</script>" . mysqli_error($conn);
                }
            }
        }
    }
    mysqli_close($conn);
} elseif (isset($_POST['cancel'])) {
    header("Location: profile.php");
    exit;
}
?>