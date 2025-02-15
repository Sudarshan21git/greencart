<?php 
include "db.php";

session_start();

session_unset();

session_destroy();

header("location:.\loginpage\login.php");
exit();

?>