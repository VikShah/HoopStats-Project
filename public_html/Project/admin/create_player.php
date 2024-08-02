<?php
// Note: Correct path to nav.php
require(__DIR__ . "/../../../partials/nav.php");

// UCID: Vs53 Date: July 30th 2024

// Ensure the user is logged in and has admin privileges
is_logged_in(true);
if (!has_role("Admin")) {
    flash("You do not have permission to access this page", "danger");
    die(header("Location: " . get_url("home.php")));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Start of PHP validations
    $first_name = se($_POST, "first_name", "", false);
    $last_name = se($_POST, "last_name", "", false);
    $position = se($_POST, "position", "", false);
    $height = se($_POST, "height", 0, false);
    $weight = se($_POST, "weight", 0, false);
    $country = se($_POST, "country", "", false);
    $college = se($_POST, "college", "", false);
    $birth_date = se($_POST, "birth_date", null, false);
    $nba_start_year = se($_POST, "nba_start_year", 0, false);
    $years_pro = se($_POST, "years_pro", 0, false);

    $hasError = false;

    // PHP Server-side validation
    if (empty($first_name)) {
        flash("First name is required", "danger");
        $hasError = true;
    }
    if (empty($last_name)) {
        flash("Last name is required", "danger");
        $hasError = true;
    }
    if (empty($position)) {
        flash("Position is required", "danger");
        $hasError = true;
    }
    if ($height <= 0) {
        flash("Height must be a positive number", "danger");
        $hasError = true;
    }
    if ($weight <= 0) {
        flash("Weight must be a positive number", "danger");
        $hasError = true;
    }
    if (empty($country)) {
        flash("Country is required", "danger");
        $hasError = true;
    }
    if (!empty($nba_start_year) && !filter_var($nba_start_year, FILTER_VALIDATE_INT)) {
        flash("NBA start year must be a valid integer", "danger");
        $hasError = true;
    }
    if (!empty($years_pro) && !filter_var($years_pro, FILTER_VALIDATE_INT)) {
        flash("Years pro must be a valid integer", "danger");
        $hasError = true;
    }

    if (!$hasError) {
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
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HoopStats - Add Player</title>
    <link rel="stylesheet" href="<?php echo get_url('styles.css'); ?>">
    <script>
        // Start of JavaScript validation
        function validateForm() {
            let valid = true;
            let elements = document.querySelectorAll('input[required]');
            elements.forEach(element => {
                if (!element.value) {
                    alert(element.name + " is required.");
                    valid = false;
                }
            });

            let height = parseFloat(document.getElementById('height').value);
            let weight = parseFloat(document.getElementById('weight').value);
            if (isNaN(height) || height <= 0) {
                alert("Height must be a positive number.");
                valid = false;
            }
            if (isNaN(weight) || weight <= 0) {
                alert("Weight must be a positive number.");
                valid = false;
            }

            return valid;
        }
    </script>
</head>
<body>
    <?php require_once(__DIR__ . "/../../../partials/nav.php"); ?>
    <div class="container">
        <h1>Add Player</h1>
        <form method="POST" onsubmit="return validateForm();">
            <!-- Start of HTML validation -->
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
                <input type="number" id="height" name="height" step="0.01" min="0" required>
            </div>
            <div class="mb-3">
                <label for="weight">Weight (kg)</label>
                <input type="number" id="weight" name="weight" step="0.1" min="0" required>
            </div>
            <div class="mb-3">
                <label for="country">Country</label>
                <input type="text" id="country" name="country" required>
            </div>
            <div class="mb-3">
                <label for="college">College</label>
                <input type="text" id="college" name="college">
            </div>
            <div class="mb-3">
                <label for="birth_date">Date of Birth</label>
                <input type="date" id="birth_date" name="birth_date" required>
            </div>
            <div class="mb-3">
                <label for="nba_start_year">NBA Start Year</label>
                <input type="number" id="nba_start_year" name="nba_start_year" required>
            </div>
            <div class="mb-3">
                <label for="years_pro">Years Pro</label>
                <input type="number" id="years_pro" name="years_pro" required>
            </div>
            <input type="submit" value="Add Player">
        </form>
    </div>
    <?php require_once(__DIR__ . "/../../../partials/footer.php"); ?>
    <?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>
</body>
</html>
