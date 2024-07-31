<?php
// Corrected path to nav.php
require(__DIR__ . "/../../../partials/nav.php");

// Ensure the user is logged in and has admin privileges
is_logged_in(true);
if (!has_role("Admin")) {
    flash("You do not have permission to access this page", "danger");
    die(header("Location: " . get_url("home.php")));
}

$player_id = $_GET['id'] ?? null;

if ($player_id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM player_stats WHERE player_id = :player_id");
    $stmt->execute([":player_id" => $player_id]);
    $player = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$player) {
        flash("Player not found", "danger");
        die(header("Location: " . get_url("list_players.php")));
    }
} else {
    flash("Player ID not specified", "danger");
    die(header("Location: " . get_url("list_players.php")));
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

    $stmt = $db->prepare("UPDATE player_stats SET first_name = :first_name, last_name = :last_name, position = :position, height = :height, weight = :weight, country = :country, college = :college, birth_date = :birth_date, nba_start_year = :nba_start_year, years_pro = :years_pro WHERE player_id = :player_id");
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
            ":years_pro" => $years_pro,
            ":player_id" => $player_id
        ]);
        flash("Player updated successfully", "success");
        die(header("Location: " . get_url("view_player.php?id=" . $player_id)));
    } catch (PDOException $e) {
        flash("Error updating player: " . $e->getMessage(), "danger");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Player</title>
    <link rel="stylesheet" href="<?php echo get_url('styles.css'); ?>">
</head>
<body>
    <div class="container">
        <h1>Edit Player</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" value="<?php se($player, 'first_name'); ?>" required>
            </div>
            <div class="mb-3">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" value="<?php se($player, 'last_name'); ?>" required>
            </div>
            <div class="mb-3">
                <label for="position">Position</label>
                <input type="text" id="position" name="position" value="<?php se($player, 'position'); ?>" required>
            </div>
            <div class="mb-3">
                <label for="height">Height (meters)</label>
                <input type="text" id="height" name="height" value="<?php se($player, 'height'); ?>">
            </div>
            <div class="mb-3">
                <label for="weight">Weight (kg)</label>
                <input type="text" id="weight" name="weight" value="<?php se($player, 'weight'); ?>">
            </div>
            <div class="mb-3">
                <label for="country">Country</label>
                <input type="text" id="country" name="country" value="<?php se($player, 'country'); ?>">
            </div>
            <div class="mb-3">
                <label for="college">College</label>
                <input type="text" id="college" name="college" value="<?php se($player, 'college'); ?>">
            </div>
            <div class="mb-3">
                <label for="birth_date">Date of Birth</label>
                <input type="date" id="birth_date" name="birth_date" value="<?php se($player, 'birth_date'); ?>">
            </div>
            <div class="mb-3">
                <label for="nba_start_year">NBA Start Year</label>
                <input type="text" id="nba_start_year" name="nba_start_year" value="<?php se($player, 'nba_start_year'); ?>">
            </div>
            <div class="mb-3">
                <label for="years_pro">Years Pro</label>
                <input type="text" id="years_pro" name="years_pro" value="<?php se($player, 'years_pro'); ?>">
            </div>
            <input type="submit" value="Update Player">
        </form>
    </div>
    <?php require_once(__DIR__ . "/../../../partials/footer.php"); ?>
    <?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>
</body>
</html>
