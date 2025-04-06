<?php
session_start(); // Start or resume the session


// Destroy all session variables
session_unset();
session_destroy();
// Redirect to login page or home page
header("Location: login.php");
exit();
?>