<?php
require(__DIR__ . "/../../lib/functions.php");
$player_id = $_GET['id'] ?? null;

if ($player_id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM player_stats WHERE player_id = :player_id");
    $stmt->execute([":player_id" => $player_id]);
    $player = $stmt->fetch(PDO::FETCH_ASSOC);
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
                <!-- Link to Edit Player -->
                <?php if (has_role("Admin")): ?>
                    <a href="<?php echo get_url('admin/edit_player.php?id=' . $player['player_id']); ?>">Edit Player</a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include(__DIR__ . "/../../partials/footer.php"); ?>
</body>
</html>
