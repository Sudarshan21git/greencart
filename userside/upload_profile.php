<?php
include '../include/db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_pic"])) {
    $id = $_POST['id'];
    $targetDir = "../img/"; // Folder to store images
    $fileName = basename($_FILES["profile_pic"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    // Allowed file types
    $allowTypes = array('jpg', 'png', 'jpeg', 'gif');

    if (in_array($fileType, $allowTypes)) {
        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $targetFilePath)) {
            // Update profile image in the database
            $updateQuery = "UPDATE customeruser SET profile_image='$fileName' WHERE id=$id";
            if ($conn->query($updateQuery) === TRUE) {
                $_SESSION['success'] = "Profile picture updated successfully.";
            } else {
                $_SESSION['error'] = "Database update failed.";
            }
        } else {
            $_SESSION['error'] = "File upload failed.";
        }
    } else {
        $_SESSION['error'] = "Only JPG, JPEG, PNG & GIF files are allowed.";
    }
}

// Redirect back to profile page
header("Location: profile.php");
exit();
?>
