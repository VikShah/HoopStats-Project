<?php
require(__DIR__ . "/../../lib/functions.php");
is_logged_in(true);
if (!has_role("Admin")) {
    flash("You do not have permission to access this page.", "danger");
    die(header("Location: home.php"));
}

if (isset($_POST["create"])) {
    $first_name = se($_POST, "first_name", "", false);
    $last_name = se($_POST, "last_name", "", false);
    $height = se($_POST, "height", "", false);
    $weight = se($_POST, "weight", "", false);
    $years_pro = se($_POST, "years_pro", "", false);
    $college = se($_POST, "college", "", false);
    $country = se($_POST, "country", "", false);
    $birth_date = se($_POST, "birth_date", "", false);
    $nba_start_year = se($_POST, "nba_start_year", "", false);
    $position = se($_POST, "position", "", false);

    if (empty($first_name) || empty($last_name)) {
        flash("First name and last name are required.", "danger");
    } else {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO player_stats (first_name, last_name, height, weight, years_pro, college, country, birth_date, nba_start_year, position) VALUES (:first_name, :last_name, :height, :weight, :years_pro, :college, :country, :birth_date, :nba_start_year, :position)");
        try {
            $stmt->execute([
                ":first_name" => $first_name,
                ":last_name" => $last_name,
                ":height" => $height,
                ":weight" => $weight,
                ":years_pro" => $years_pro,
                ":college" => $college,
                ":country" => $country,
                ":birth_date" => $birth_date,
                ":nba_start_year" => $nba_start_year,
                ":position" => $position,
            ]);
            flash("Player created successfully.", "success");
            die(header("Location: list_players.php"));
        } catch (Exception $e) {
            flash("An error occurred while adding the player. Please try again.", "danger");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HoopStats - Create Player</title>
    <link rel="stylesheet" href="<?php echo get_url('styles.css'); ?>">
    <style>
        body {
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            flex-grow: 1;
        }
        .mb-3 {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"],
        input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #ff6600;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #e65c00;
        }
        .alert {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
        }
        .alert-danger {
            background-color: #dc3545;
            color: #fff;
        }
        .alert-success {
            background-color: #28a745;
            color: #fff;
        }
        .alert-warning {
            background-color: #ffc107;
            color: #000;
        }
        .alert-info {
            background-color: #ff6600;
            color: #fff;
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
    <?php include(__DIR__ . "/../../partials/nav.php"); ?>

    <div class="container">
        <h1>Create Player</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="first_name">First Name</label>
                <input type="text" name="first_name" id="first_name" required />
            </div>
            <div class="mb-3">
                <label for="last_name">Last Name</label>
                <input type="text" name="last_name" id="last_name" required />
            </div>
            <div class="mb-3">
                <label for="height">Height (meters)</label>
                <input type="number" step="0.01" name="height" id="height" />
            </div>
            <div class="mb-3">
                <label for="weight">Weight (kg)</label>
                <input type="number" name="weight" id="weight" />
            </div>
            <div class="mb-3">
                <label for="years_pro">Years Pro</label>
                <input type="number" name="years_pro" id="years_pro" />
            </div>
            <div class="mb-3">
                <label for="college">College</label>
                <input type="text" name="college" id="college" />
            </div>
            <div class="mb-3">
                <label for="country">Country</label>
                <input type="text" name="country" id="country" />
            </div>
            <div class="mb-3">
                <label for="birth_date">Date of Birth</label>
                <input type="date" name="birth_date" id="birth_date" />
            </div>
            <div class="mb-3">
                <label for="nba_start_year">NBA Start Year</label>
                <input type="number" name="nba_start_year" id="nba_start_year" />
            </div>
            <div class="mb-3">
                <label for="position">Position</label>
                <input type="text" name="position" id="position" />
            </div>
            <input type="submit" value="Create Player" name="create" />
        </form>
    </div>

    <footer>
        <?php include(__DIR__ . "/../../partials/footer.php"); ?>
    </footer>
</body>
</html>
