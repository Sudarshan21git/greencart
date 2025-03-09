<?php
include "../include/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {

    $id = $_POST['id'];
    $query = "DELETE FROM customeruser WHERE id = '$id'";
    $result = mysqli_query($conn, $query);
    if ($conn->query($query) === false) {
        echo "User deletion failed";
    } else {
        header("Refresh:0; url=users.php");
    }
}
mysqli_close($conn);