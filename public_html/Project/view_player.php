<?php
require(__DIR__ . "/../../lib/functions.php");
$player_id = $_GET['id'] ?? null;

// Check if player_id is provided and is a valid integer
if (!$player_id || !filter_var($player_id, FILTER_VALIDATE_INT)) {
    flash("Invalid or missing player ID", "warning");
    header("Location: " . get_url("list_players.php"));
    exit;
}

$db = getDB();
$stmt = $db->prepare("SELECT * FROM player_stats WHERE player_id = :player_id");
$stmt->execute([":player_id" => $player_id]);
$player = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if player exists
if (!$player) {
    flash("Player not found", "danger");
    header("Location: " . get_url("list_players.php"));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($player['first_name'] . ' ' . $player['last_name']); ?> - Details</title>
    <link rel="stylesheet" href="<?php echo get_url('styles.css'); ?>">
    <script src="<?php echo get_url('helpers.js'); ?>"></script>
</head>
<body>
    <?php include(__DIR__ . "/../../partials/nav.php"); ?>

    <main>
        <div class="container">
            <?php require_once(__DIR__ . "/../../partials/flash.php"); ?>
            <div class="player-details">
                <h1><?php echo htmlspecialchars($player['first_name'] . ' ' . $player['last_name']); ?></h1>
                <div class="player-info">
                    <p><strong>Height:</strong> <?php echo htmlspecialchars(($player['height'] ?? 'N/A') . ' meters'); ?></p>
                    <p><strong>Weight:</strong> <?php echo htmlspecialchars(($player['weight'] ?? 'N/A') . ' kg'); ?></p>
                    <p><strong>Years Pro:</strong> <?php echo htmlspecialchars($player['years_pro'] ?? 'N/A'); ?></p>
                    <p><strong>College:</strong> <?php echo htmlspecialchars($player['college'] ?? 'N/A'); ?></p>
                    <p><strong>Country:</strong> <?php echo htmlspecialchars($player['country'] ?? 'N/A'); ?></p>
                    <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($player['birth_date'] ?? 'N/A'); ?></p>
                    <p><strong>NBA Start Year:</strong> <?php echo htmlspecialchars($player['nba_start_year'] ?? 'N/A'); ?></p>
                    <p><strong>Position:</strong> <?php echo htmlspecialchars($player['position'] ?? 'N/A'); ?></p>
                </div>
                <!-- Link to Edit and Delete Player -->
                <?php if (has_role("Admin")): ?>
                    <a href="<?php echo get_url('admin/edit_player.php?id=' . $player['player_id']); ?>">Edit Player</a>
                    | <a href="<?php echo get_url('admin/delete_player.php?id=' . $player['player_id']); ?>" onclick="return confirm('Are you sure you want to delete this player?');">Delete Player</a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include(__DIR__ . "/../../partials/footer.php"); ?>
</body>
</html>
