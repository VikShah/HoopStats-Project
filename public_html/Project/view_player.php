<?php
require(__DIR__ . "/../../lib/functions.php");
require(__DIR__ . "/../../partials/nav.php");

// Fetch the player ID from the query string
$playerId = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$playerId) {
    // Redirect back to the list if no valid ID is provided
    header("Location: list_players.php?error=Invalid player ID");
    exit;
}

// Fetch player details from the database
$db = getDB();
$stmt = $db->prepare("SELECT * FROM player_stats WHERE player_id = :player_id");
$stmt->execute([':player_id' => $playerId]);
$player = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$player) {
    // Redirect if the player is not found
    header("Location: list_players.php?error=Player not found");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Player Details</title>
    <link rel="stylesheet" href="<?php echo get_url('styles.css'); ?>">
</head>
<body>
    <h1><?php echo htmlspecialchars($player['player_name']); ?> - Details</h1>
    <p><strong>Team:</strong> <?php echo htmlspecialchars($player['team_name']); ?></p>
    <p><strong>Points Per Game:</strong> <?php echo htmlspecialchars($player['points_per_game']); ?></p>
    <p><strong>Rebounds Per Game:</strong> <?php echo htmlspecialchars($player['rebounds_per_game']); ?></p>
    <p><strong>Assists Per Game:</strong> <?php echo htmlspecialchars($player['assists_per_game']); ?></p>
    <p><strong>Height:</strong> <?php echo htmlspecialchars($player['height']); ?></p>
    <p><strong>Weight:</strong> <?php echo htmlspecialchars($player['weight']); ?></p>
    <p><strong>Years Pro:</strong> <?php echo htmlspecialchars($player['years_pro']); ?></p>
    <p><strong>College:</strong> <?php echo htmlspecialchars($player['college']); ?></p>
    <p><strong>Country:</strong> <?php echo htmlspecialchars($player['country']); ?></p>
    <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($player['date_of_birth']); ?></p>
    <p><strong>NBA Start Year:</strong> <?php echo htmlspecialchars($player['nba_start']); ?></p>
    <p><strong>Jersey Number:</strong> <?php echo htmlspecialchars($player['jersey_number']); ?></p>
    <p><strong>Position:</strong> <?php echo htmlspecialchars($player['position']); ?></p>

    <!-- Links for edit and delete, only shown if user has the appropriate role -->
    <?php if (has_role("Admin")): ?>
        <a href="edit_player.php?id=<?php echo $playerId; ?>">Edit</a>
        <a href="delete_player.php?id=<?php echo $playerId; ?>">Delete</a>
    <?php endif; ?>

    <p><a href="list_players.php">Back to Player List</a></p>
</body>
</html>
