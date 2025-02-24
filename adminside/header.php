  
  <?php
session_start(); // Start the session to access session variables

// Check if the user is logged in as an admin
if (!isset($_SESSION['username']) || $_SESSION['user_role'] != 1) {
    header("location:../userside/login.php"); // Redirect to login page if not logged in as admin
    exit();
}

// Get the logged-in user's name from the session
$admin_name = $_SESSION['username'];
?><!-- header -->
  <header class="header">
<div class="header_body">
<div class="logo">
      <img src="../img/logo.png" alt="Nature's Nursery">
      <div class="logo-text">
      <h1 style="font-size: 20px;">Welcome, <?php echo $admin_name; ?></h1>
        <span class="green">E-nursery</span> <span class="white">Admin</span>  <span class="white">Panel</span>
       
      </div>
    </div>
<nav class="navbar">

    <a href="admin.php">Add product</a>
    <a href="viewproducts.php">Viewproduct</a>
    <a href="users.php">users</a>
    <a href="vieworders.php">view order</a>
    <a href="../userside/logout.php">logout</a>
   
</nav>

</div>
</header>