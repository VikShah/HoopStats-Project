<?php
require_once(__DIR__ . "/../../partials/nav.php");

if (!is_logged_in()) {
    flash("You must be logged in to view this page", "warning");
    die(header("Location: " . get_url("login.php")));
}

$user_id = get_user_id();
$limit = 10; // Fixed limit for pagination
$page = se($_GET, "page", 1, false);
$page = max(1, $page); // Ensure page number is at least 1
$offset = ($page - 1) * $limit;
$sort = se($_GET, "sort", "last_name", false); // Sorting by last name by default
$order = se($_GET, "order", "ASC", false); // Default order
$filter = se($_GET, "filter", "", false);

$db = getDB();
$query = "SELECT p.* FROM player_stats p 
          JOIN user_favorites uf ON p.player_id = uf.player_id 
          WHERE uf.user_id = :user_id AND (p.first_name LIKE :filter OR p.last_name LIKE :filter) 
          ORDER BY $sort $order LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($query);
$filterParam = "%" . $filter . "%";
$stmt->bindParam(":filter", $filterParam, PDO::PARAM_STR);
$stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
$stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
$stmt->execute();
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count total records for pagination
$totalQuery = "SELECT COUNT(*) FROM player_stats p 
               JOIN user_favorites uf ON p.player_id = uf.player_id 
               WHERE uf.user_id = :user_id AND (p.first_name LIKE :filter OR p.last_name LIKE :filter)";
$totalStmt = $db->prepare($totalQuery);
$totalStmt->execute([":user_id" => $user_id, ":filter" => $filterParam]);
$totalRecords = $totalStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Favorites</title>
    <link rel="stylesheet" href="<?php echo get_url('styles.css'); ?>">
</head>
<body>

    <div class="container">
        <h1>My Favorites (<?php echo count($favorites); ?>)</h1>
        <?php require_once(__DIR__ . "/../../partials/flash.php"); ?>
        <form method="GET">
            <label for="filter">Filter by Player Name:</label>
            <input id="filter" name="filter" value="<?php se($filter); ?>" />
            <input type="submit" value="Apply" />
        </form>
        <?php if (empty($favorites)) : ?>
            <p>No favorite players found.</p>
        <?php else : ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th><a href="?sort=first_name&order=<?php echo $order === 'ASC' ? 'DESC' : 'ASC'; ?>">First Name</a></th>
                            <th><a href="?sort=last_name&order=<?php echo $order === 'ASC' ? 'DESC' : 'ASC'; ?>">Last Name</a></th>
                            <th>Position</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($favorites as $player) : ?>
                            <tr>
                                <td><?php se($player, "first_name"); ?></td>
                                <td><?php se($player, "last_name"); ?></td>
                                <td><?php se($player, "position"); ?></td>
                                <td>
                                    <a href="view_player.php?id=<?php se($player, "player_id"); ?>">View</a>
                                    <form method="POST" action="remove_from_favorites.php" style="display:inline;">
                                        <input type="hidden" name="player_id" value="<?php se($player, "player_id"); ?>">
                                        <input type="submit" value="Remove from Favorites">
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="pagination">
                <?php if ($page > 1) : ?>
                    <a href="?page=<?php echo $page - 1; ?>&filter=<?php echo urlencode($filter); ?>&sort=<?php echo urlencode($sort); ?>&order=<?php echo urlencode($order); ?>">Previous</a>
                <?php endif; ?>
                <?php if ($page < $totalPages) : ?>
                    <a href="?page=<?php echo $page + 1; ?>&filter=<?php echo urlencode($filter); ?>&sort=<?php echo urlencode($sort); ?>&order=<?php echo urlencode($order); ?>">Next</a>
                <?php endif; ?>
            </div>
            <form method="POST" action="clear_favorites.php">
                <input type="submit" value="Clear All Favorites">
            </form>
        <?php endif; ?>
    </div>
    <?php include(__DIR__ . "/../../partials/footer.php"); ?>
</body>
</html>
