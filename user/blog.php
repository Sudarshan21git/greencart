<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
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
    <title>Blog - GreenCart</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="icon" type="image/png" href="../img/logo.png">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <?php include_once '../includes/header.php'; ?>


    <!-- Blog Banner -->
    <section>
        <div class="background"></div>
    </section>
    <section class="title">
        <div class="title-text">
            <h1>Top Indoor Plants for Creating a Relaxing Atmosphere</h1><br><br>
            <p>There are many different indoor plants that can help create a relaxing atmosphere in your home. Some good options include peace lilies, snake plants, and English ivy. These plants are known for their ability to purify the air, reduce stress, and improve focus. Additionally, they are easy to care for and require minimal maintenance, making them perfect for people with busy lifestyles.
                Other popular indoor plants that are great for creating a relaxing atmosphere include spider plants, aloe vera, and bamboo. These plants are also known for their air-purifying properties, and they can help reduce stress and improve focus. They are also easy to care for, which makes them a good choice for people who don't have a lot of time to spend on plant maintenance.
                In addition to their air-purifying and stress-reducing properties, indoor plants can also add a touch of beauty and greenery to your home. They can be placed in a variety of locations, such as on a windowsill, on a bookshelf, or on a coffee table. Indoor plants are available in a wide range of sizes, shapes, and colors, so you can choose the ones that best fit your personal style and the decor of your home. </p>
        </div>
    </section>

    <section>
        <div class="buttombg"></div>
    </section>
    <section class="secondtitle">
        <div class="secondtitle-text">
            <h1>Reasons Why You Must Keep a Plant Near Working Area</h1><br><br>
        </div>
        <div class="thirdtitle-text">
            <p>People have an inherent urge to be connected to nature, referred to as "biophilia" by scientists. Unfortunately, the areas where we spend the majority of our days — our workplaces – have lost much of their connection to nature.
                According to studies, merely adding some greenery in the form of indoor plants can have significant positive effects on people and their workplaces. Including some green buddies about your workplace is a terrific way to boost productivity and give some happiness to your workplace. Office plants, like office snacks, are a low-cost method to brighten your team's day and perhaps increase productivity. </p>
            <p>Certain plants can help you be more creative.<br><br>Creative barriers are no joking matter. Whether you're running out of ideas or have been stuck on the same one for far too long, workplace plants can help. Bright colors and scents are essential for ensuring that your green companion has a favorable impact on your creativity.
                It's long been known that activating our senses may help us think more creatively, and taking the time to literally smell the roses can help you get out of a rut.<br><br>
                Plants are mood lifters<br><br>With all of the advantages listed above, it isn't a stretch to conclude that plants can improve your mood. Houseplants, on the other hand, have been scientifically shown to improve your mental wellness. People who spend more time outside in nature have a substantially more optimistic attitude on life than people who spend a lot of time indoors, according to studies.<br><br>
                Plants bring life to the workplace by adding color and texture<br><br>Plants help to integrate color, texture, and softness into the ordinary office from a strictly aesthetic standpoint. Similarly, if you want to maximize productivity during the usual working day, you must make your workspace as comfortable as feasible.</p>
        </div>

    </section>

    <!-- Footer -->
    <?php include_once '../includes/footer.php'; ?>

    <script src="../js/script.js"></script>
</body>
</html>

