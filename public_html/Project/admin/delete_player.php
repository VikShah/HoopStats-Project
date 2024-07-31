<?php
require(__DIR__ . "/../../../partials/nav.php");

// Ensure the user is logged in and has admin privileges
is_logged_in(true);
if (!has_role("Admin")) {
    flash("You do not have permission to access this page", "danger");
    die(header("Location: " . get_url("list_players.php")));
}

$player_id = $_GET['id'] ?? null;

if (!$player_id) {
    flash("Invalid player ID", "danger");
    die(header("Location: " . get_url("list_players.php")));
}

$db = getDB();
$stmt = $db->prepare("SELECT * FROM player_stats WHERE player_id = :player_id");
$stmt->execute([":player_id" => $player_id]);
$player = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$player) {
    flash("Player not found", "danger");
    die(header("Location: " . get_url("list_players.php")));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Soft delete the player (set a deleted flag or remove the record)
    $stmt = $db->prepare("DELETE FROM player_stats WHERE player_id = :player_id");
    try {
        $stmt->execute([":player_id" => $player_id]);
        flash("Player deleted successfully", "success");
    } catch (PDOException $e) {
        flash("Error deleting player: " . $e->getMessage(), "danger");
    }
    // Redirect back to the players list page with any active filters/sorts
    die(header("Location: " . get_url("list_players.php")));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Player</title>
    <link rel="stylesheet" href="<?php echo get_url('styles.css'); ?>">
</head>
<body>
    <div class="container">
        <h1>Delete Player</h1>
        <p>Are you sure you want to delete the player <strong><?php se($player, 'first_name'); ?> <?php se($player, 'last_name'); ?></strong>?</p>
        <form method="POST">
            <input type="submit" value="Confirm Deletion">
        </form>
        <a href="<?php echo get_url('list_players.php'); ?>">Cancel</a>
    </div>
    <?php require_once(__DIR__ . "/../../../partials/footer.php"); ?>
    <?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>
</body>
</html>
