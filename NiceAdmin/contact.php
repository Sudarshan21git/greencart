<?php
ob_start();
session_start();
include('../database/database.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Contact Messages - GreenCart Admin</title>

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans|Nunito|Poppins" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>

<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
        <a href="index.php" class="logo d-flex align-items-center">
            <img src="assets/img/logo.png" alt="">
            <span class="d-none d-lg-block">GreenCartAdmin</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>
</header><!-- End Header -->

<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item"><a class="nav-link collapsed" href="index.php"><i class="bi bi-grid"></i><span>Dashboard</span></a></li>
        <li class="nav-item"><a class="nav-link collapsed" href="category.php"><i class="bi-tags"></i><span>Category</span></a></li>
        <li class="nav-item"><a class="nav-link collapsed" href="product.php"><i class="bi-box-seam"></i><span>Product</span></a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php"><i class="bi bi-phone"></i><span>contact</span></a></li>
        
    </ul>
</aside><!-- End Sidebar-->

<main id="main" class="main">
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Contact Messages</h5>

                        <!-- Session Messages -->
                        <?php if(isset($_SESSION['success'])): ?>
                            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                        <?php endif; ?>
                        <?php if(isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                        <?php endif; ?>

                        <p class="text-center text-muted">Here is the list of all received messages. You can delete them as needed.</p>
                        <table class="table table-striped table-bordered text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>Sl No.</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Message</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if (!$conn) {
                                    die("Database connection failed: " . mysqli_connect_error());
                                }
                                $contact_messages = mysqli_query($conn, "SELECT * FROM contact_messages");
                                $serial_number = 1;
                                if (mysqli_num_rows($contact_messages) > 0) {
                                    while ($row = mysqli_fetch_assoc($contact_messages)) {
                                        echo "<tr>";
                                        echo "<td>{$serial_number}</td>";
                                        echo "<td>{$row['name']}</td>";
                                        echo "<td>{$row['email']}</td>";
                                        echo "<td>{$row['subject']}</td>";
                                        echo "<td>{$row['message']}</td>";
                                        echo "<td>
                                                <a href='contact.php?delete=" . urlencode($row['message_id']) . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this message?')\"><i class='bi bi-trash'></i></a>
                                              </td>";
                                        echo "</tr>";
                                        $serial_number++;
                                    }
                                } else {
                                    echo "<tr><td colspan='6'>No messages found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>

                        <style>
                            .btn-sm { padding: 5px 10px; font-size: 14px; }
                        </style>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main><!-- End #main -->

<!-- ======= Footer ======= -->
<footer id="footer" class="footer">
    <div class="copyright">
        &copy; Copyright <strong><span>GreenCartAdmin</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
    </div>
</footer><!-- End Footer -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="assets/vendor/tinymce/tinymce.min.js"></script>
<script src="assets/js/main.js"></script>

</body>
</html>

<!-- Delete Contact Message Backend Code -->
<?php
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $message_id = $_GET['delete'];

    // Prevent double deletion
    if (isset($_SESSION['delete_message']) && $_SESSION['delete_message'] == $message_id) {
        $_SESSION['error'] = "Message has already been deleted.";
        header("Location: contact.php");
        exit();
    }

    // Delete message from database
    $delete_query = mysqli_query($conn, "DELETE FROM contact_messages WHERE message_id = $message_id");

    if ($delete_query) {
        $_SESSION['delete_message'] = $message_id;
        $_SESSION['success'] = "Message deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete message.";
    }

    header("Location: contact.php");
    exit();
}
?>
