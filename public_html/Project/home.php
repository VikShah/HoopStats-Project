<?php
require(__DIR__ . "/../../partials/nav.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HoopStats - Home</title>
    <link rel="stylesheet" href="<?php echo get_url('styles.css'); ?>">
    <style>
        body {
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
            color: #333;
            text-align: center;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        nav ul {
            display: flex;
            justify-content: flex-start; /* Aligns navigation items to the top left */
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            flex-grow: 1; /* Ensures the container takes up the remaining space */
        }
        .logo {
            margin-top: 20px;
        }
        .header-text {
            font-size: 24px;
            margin: 10px 0;
        }
        .description {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .image-container {
            text-align: center;
            margin-top: 20px;
        }
        .image-container img {
            width: 100%;
            max-width: 300px;
            border-radius: 10px;
        }
        footer {
            background-color: #333;
            color: white;
            padding: 10px;
            text-align: center;
        }
        .footer-content p {
            margin: 5px 0;
        }
        .footer-content a {
            color: #ff6f00;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="<?php echo get_url('Logo.png'); ?>" alt="HoopStats Logo">
        </div>
        <div class="header-text">
            Welcome to HoopStats
        </div>
        <div class="description">
            Your go-to platform for all NBA player statistics and insights.
            Dive into detailed stats and stay updated with the latest player performances.
        </div>
    </div>
    <?php
    require(__DIR__ . "/../../partials/footer.php");
    ?>
</body>
</html>
<?php
require(__DIR__ . "/../../partials/flash.php");
?>
