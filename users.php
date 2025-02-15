<?php
include "db.php"; // Include your database connection file

$query = "SELECT * FROM customeruser"; // Query to fetch customeruser from the database
$result = mysqli_query($conn, $query);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Products</title>
    <!-- CSS and Font Awesome Links -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="viewuser.css">
</head>
<body>
    <!-- Include header -->
    <?php include 'header.php'; ?>

    <div class="container">
        <section class="display_product">
           <!-- Table for Users -->
    <table >
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                // Loop through each row of data
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                        <td>" . $row['id'] . "</td>
                        <td>" . $row['username'] . "</td>
                        <td>" . $row['Email'] . "</td>
                        <td>" . $row['phone'] . "</td>
                        <td>" . $row['address'] . "</td>
                        <td>" . $row['role'] . "</td>
                    <td>
                        <form action='Useredit.php' method='post'>
                        <input type='hidden' name='id' value='" . $row['id'] . "'>
                        <input type='hidden' name='name' value='" . $row['username'] . "'>
                        <input type='hidden' name='email' value='" . $row['Email'] . "'>
                        <input type='hidden' name='phone' value='" . $row['phone'] . "'>
                        <input type='hidden' name='address' value='" . $row['address'] . "'>
                        <input type='hidden' name='role' value='" . $row['role'] . "'>
                        ";
                        if($row['role']==1){
                            echo "Admin";
                        }
                        else{
                            echo "
                        </form>
                        <form action='userdelete.php' method='post'>
                        <input type='hidden' name='id' value='" . $row['id'] . "'>
                        <button type='submit' name='delete' class='deltetn'>Delete</button>
                        </form>
                    </td>
                    </tr>";
                        }   
                }
            } else {
                // No data found in the database
                echo "<tr><td colspan='4'>No users found</td></tr>";
            }

            // Close the database connection
            mysqli_close($conn);
            ?>
        </tbody>
    </table>
 
</body>

</html>