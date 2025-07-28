<?php
// Database configuration
$host = "localhost";
$dbname = "greencart";
$username = "root";
$password = "the123";

$conn = mysqli_connect($host,$username,$password,$dbname);
   
if(!$conn){
 die("Could not connect: " . mysqli_connect_error());

}

?>