<?php
require(__DIR__ . "/../../lib/functions.php");

// Define the number of players per page
$playersPerPage = 10;

// Get the current page number from the query string, default to page 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $playersPerPage;

// Fetch total number of players
$db = getDB();
$stmt = $db->query("SELECT COUNT(*) FROM player_stats");
$totalPlayers = $stmt->fetchColumn();
$totalPages = ceil($totalPlayers / $playersPerPage);

// Fetch player data for the current page
$stmt = $db->prepare("SELECT * FROM player_stats ORDER BY player_name ASC LIMIT :offset, :limit");
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':limit', $playersPerPage, PDO::PARAM_INT);
$stmt->execute();
$players = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Player List</title>
    <link rel="stylesheet" href="<?php echo get_url('styles.css'); ?>">
</head>
<body>
    <?php require(__DIR__ . "/../../partials/nav.php"); ?>
    <h1>Player List</h1>
    <table>
        <thead>
            <tr>
                <th>Player Name</th>
                <th>Team Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($players as $player): ?>
                <tr>
                    <td><?php echo htmlspecialchars($player['player_name']); ?></td>
                    <td><?php echo htmlspecialchars($player['team_name']); ?></td>
                    <td><a href="view_player.php?id=<?php echo $player['player_id']; ?>">View Details</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>">&laquo; Previous</a>
        <?php endif; ?>
        <?php if ($page < $totalPages): ?>
            <a href="?page=<?php echo $page + 1; ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>
</body>
</html>
