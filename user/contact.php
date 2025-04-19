<?php 
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
else if ($_SESSION['is_admin'] == 1) {
    header("Location: 404.html");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - GreenCart</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="icon" type="image/png" href="../img/logo.png">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Header -->
    <?php
    include_once "../includes/header.php";
    ?>

    <!-- Contact Content -->
    <section class="contact-content">
        <div class="container">
            <div class="contact-layout">
                <div class="contact-info">
                    <div class="info-card">
                        <div class="info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                        </div>
                        <div class="info-content">
                            <h3>Email Us</h3>
                            <p>naturenursery@gmail.com</p>
                            <p>support@greencart.com</p>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                        </div>
                        <div class="info-content">
                            <h3>Call Us</h3>
                            <p>977 9882882488</p>
                            <p>Mon-Fri: 9am-5pm EST</p>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                        </div>
                        <div class="info-content">
                            <h3>Visit Us</h3>
                            <p>Lazimpat,kathmandu</p>
                            <p>Nepal</p>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <div class="info-content">
                            <h3>Business Hours</h3>
                            <p>Monday-Friday: 9am-5pm</p>
                            <p>Saturday: 10am-4pm</p>
                            <p>Sunday: Closed</p>
                        </div>
                    </div>
                </div>

                <div class="contact-form-container">
                    <h2>Send Us a Message</h2>
                    <p>Have a question or feedback? Fill out the form below and we'll get back to you as soon as possible.</p>
                    <form id="contact-form" class="contact-form" action="" method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Your Name</label>
                                <input type="text" id="name" name="name" placeholder="Enter your name">
                            </div>
                            <div class="form-group">
                                <label for="email">Your Email</label>
                                <input type="email" id="email" name="email" placeholder="Enter your email">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" id="subject" name="subject" placeholder="What is this regarding?">
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" rows="6" placeholder="Type your message here..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" name="submit">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="map-section">
        <div class="container">
            <h2 class="section-title">Find Us</h2>
            <div class="map-container">
                <!-- Replace with actual map embed code in production -->
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7063.691632646773!2d85.31633734291898!3d27.72204640469615!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39eb1919f7dd0685%3A0xc59baa0caae9c83d!2sLazimpat%2C%20Kathmandu%2044600!5e0!3m2!1sen!2snp!4v1741452798745!5m2!1sen!2snp" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </section>


    <!-- Footer -->
    <?php
    include_once "../includes/footer.php";
    ?>

    <script src="../js/script.js"></script>
    <script>
        function promptMessage(msg, success = false) {
            // Create a modal overlay
            const overlay = document.createElement("div");
            overlay.style.position = "fixed";
            overlay.style.top = "0";
            overlay.style.left = "0";
            overlay.style.width = "100%";
            overlay.style.height = "100%";
            overlay.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
            overlay.style.display = "flex";
            overlay.style.justifyContent = "center";
            overlay.style.alignItems = "center";
            overlay.style.zIndex = "1000";

            // Create the modal content
            const modal = document.createElement("div");
            modal.style.backgroundColor = "white";
            modal.style.padding = "20px";
            modal.style.borderRadius = "8px";
            modal.style.boxShadow = "0 2px 10px rgba(0, 0, 0, 0.2)";
            modal.style.width = "300px";
            modal.style.maxWidth = "90%";
            modal.style.textAlign = "center"; // Center text

            // Create the title
            const title = document.createElement("h3");
            title.textContent = msg;
            title.style.marginTop = "0";
            title.style.marginBottom = "15px";
            title.style.color = "red";

            // Create the buttons container
            const buttonContainer = document.createElement("div");
            buttonContainer.style.display = "flex";
            buttonContainer.style.justifyContent = "space-between";
            buttonContainer.style.width = "100%";
            buttonContainer.style.gap = "10px"; // Adds some space between the buttons

            // Create the 'Okay' button
            const button = document.createElement("button");
            button.textContent = "Okay";
            button.style.backgroundColor = "#128C7E";
            button.style.color = "white";
            button.style.border = "none";
            button.style.padding = "6px 12px"; // Smaller padding
            button.style.borderRadius = "4px";
            button.style.cursor = "pointer";
            button.style.flex = "1"; // Take equal space with the other button

            button.onclick = function() {
                overlay.remove();
            };

            buttonContainer.appendChild(button); // Add to button container

            // Create the 'Go Signup' button, only show if specified
            if (success) {
                title.style.color = "green";
                // Create the 'Okay' button
                const button = document.createElement("button");
                button.textContent = "Okay";
                button.style.backgroundColor = "#128C7E";
                button.style.color = "white";
                button.style.border = "none";
                button.style.padding = "6px 12px"; // Smaller padding
                button.style.borderRadius = "4px";
                button.style.cursor = "pointer";
                button.style.flex = "1"; // Take equal space with the other button

                button.onclick = function() {
                    overlay.remove();
                };
            }

            // Add the title and buttons container to the modal
            modal.appendChild(title);
            modal.appendChild(buttonContainer);
            overlay.appendChild(modal);

            // Add the modal to the body
            document.body.appendChild(overlay);
        }
    </script>

</body>

</html>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    include '../database/database.php';

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $subject = trim($_POST["subject"]);
    $msg = trim($_POST["message"]);

    if (empty($name) || empty($email) || empty($subject) || empty($msg)) {
        echo "<script>promptMessage('Please fill all the Fields')</script>";
    } else {
        // Validate name format (first & last name)
        if (!preg_match("/^[a-zA-Z][a-zA-Z\s']{2,19}$/", $name)) {
            echo "<script>promptMessage('Invalid name format or length (3 to 20 characters, starting with an alphabet)!');</script>";
        }
        // Validate email format (only Gmail addresses)
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $email)) {
            echo "<script>promptMessage('Invalid email format or not a Gmail address!');</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $subject, $msg);

            if ($stmt->execute()) {
                echo "<script>promptMessage('Successfully Received your message.',true);</script>";
                header("Location: " . $_SERVER['PHP_SELF']);
            } else {
                echo "<script>alert('Failure: " . addslashes($conn->error) . "');</script>";
            }
        }
    }
    $conn->close();
}
?>