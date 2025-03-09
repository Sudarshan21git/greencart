<?php 
 $servername = "localhost";
 $username = "root";
 $password = "";
   $dbname = "enursery";

   $conn = mysqli_connect($servername,$username,$password,$dbname);
   
   if(!$conn){
    die("Could not connect: " . mysqli_connect_error());

   }

 ?>