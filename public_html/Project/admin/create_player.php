<?php
// Note: Correct path to nav.php
require(__DIR__ . "/../../../partials/nav.php");

// Ensure the user is logged in and has admin privileges
is_logged_in(true);
if (!has_role("Admin")) {
    flash("You do not have permission to access this page", "danger");
    die(header("Location: " . get_url("home.php")));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Process the form submission
    $first_name = se($_POST, "first_name", "", false);
    $last_name = se($_POST, "last_name", "", false);
    $position = se($_POST, "position", "", false);
    $height = se($_POST, "height", "", false);
    $weight = se($_POST, "weight", "", false);
    $country = se($_POST, "country", "", false);
    $college = se($_POST, "college", "", false);
    $birth_date = se($_POST, "birth_date", "", false);
    $nba_start_year = se($_POST, "nba_start_year", "", false);
    $years_pro = se($_POST, "years_pro", "", false);

    $db = getDB();
    $stmt = $db->prepare("INSERT INTO player_stats (first_name, last_name, position, height, weight, country, college, birth_date, nba_start_year, years_pro) VALUES (:first_name, :last_name, :position, :height, :weight, :country, :college, :birth_date, :nba_start_year, :years_pro)");
    try {
        $stmt->execute([
            ":first_name" => $first_name,
            ":last_name" => $last_name,
            ":position" => $position,
            ":height" => $height,
            ":weight" => $weight,
            ":country" => $country,
            ":college" => $college,
            ":birth_date" => $birth_date,
            ":nba_start_year" => $nba_start_year,
            ":years_pro" => $years_pro
        ]);
        flash("Player added successfully", "success");
    } catch (PDOException $e) {
        flash("Error adding player: " . $e->getMessage(), "danger");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HoopStats - Add Player</title>
    <link rel="stylesheet" href="<?php echo get_url('styles.css'); ?>">
</head>
<body>
    <?php require_once(__DIR__ . "/../../../partials/nav.php"); ?>
    <div class="container">
        <h1>Add Player</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>
            <div class="mb-3">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>
            <div class="mb-3">
                <label for="position">Position</label>
                <input type="text" id="position" name="position" required>
            </div>
            <div class="mb-3">
                <label for="height">Height (meters)</label>
                <input type="text" id="height" name="height">
            </div>
            <div class="mb-3">
                <label for="weight">Weight (kg)</label>
                <input type="text" id="weight" name="weight">
            </div>
            <div class="mb-3">
                <label for="country">Country</label>
                <input type="text" id="country" name="country">
            </div>
            <div class="mb-3">
                <label for="college">College</label>
                <input type="text" id="college" name="college">
            </div>
            <div class="mb-3">
                <label for="birth_date">Date of Birth</label>
                <input type="date" id="birth_date" name="birth_date">
            </div>
            <div class="mb-3">
                <label for="nba_start_year">NBA Start Year</label>
                <input type="text" id="nba_start_year" name="nba_start_year">
            </div>
            <div class="mb-3">
                <label for="years_pro">Years Pro</label>
                <input type="text" id="years_pro" name="years_pro">
            </div>
            <input type="submit" value="Add Player">
        </form>
    </div>
    <?php require_once(__DIR__ . "/../../../partials/footer.php"); ?>
    <?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>
</body>
</html>
