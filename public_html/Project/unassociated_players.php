<?php
require(__DIR__ . "/../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You do not have permission to access this page", "danger");
    die(header("Location: " . get_url("home.php")));
}

// Handle filtering, sorting, and pagination parameters
$limit = 10; // Fixed limit for pagination
$page = se($_GET, "page", 1, false);
$page = max(1, $page); // Ensure page number is at least 1
$offset = ($page - 1) * $limit;
$filter = se($_GET, "filter", "", false);

// Fetch players not associated with any user
$db = getDB();
$query = "SELECT p.* FROM player_stats p 
          LEFT JOIN user_favorites uf ON p.player_id = uf.player_id 
          WHERE uf.player_id IS NULL AND (p.first_name LIKE :filter OR p.last_name LIKE :filter) 
          LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($query);
$filterParam = "%" . $filter . "%";
$stmt->bindParam(":filter", $filterParam, PDO::PARAM_STR);
$stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
$stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
$stmt->execute();
$players = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count total unassociated records for pagination
$totalQuery = "SELECT COUNT(*) FROM player_stats p 
               LEFT JOIN user_favorites uf ON p.player_id = uf.player_id 
               WHERE uf.player_id IS NULL AND (p.first_name LIKE :filter OR p.last_name LIKE :filter)";
$totalStmt = $db->prepare($totalQuery);
$totalStmt->execute([":filter" => $filterParam]);
$totalRecords = $totalStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Unassociated Players</title>
    <link rel="stylesheet" href="<?php echo get_url('styles.css'); ?>">
</head>
<body>
    <div class="container">
        <h1>Unassociated Players</h1>
        <p>Total unassociated players: <?php echo $totalRecords; ?></p>
        <?php require_once(__DIR__ . "/../../partials/flash.php"); ?>
        <form method="GET">
            <label for="filter">Filter by Player Name:</label>
            <input id="filter" name="filter" value="<?php se($filter); ?>" />
            <input type="submit" value="Apply" />
        </form>

        <?php if (empty($players)) : ?>
            <p>No unassociated players found.</p>
        <?php else : ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Position</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($players as $player) : ?>
                            <tr>
                                <td><?php se($player, "first_name"); ?></td>
                                <td><?php se($player, "last_name"); ?></td>
                                <td><?php se($player, "position"); ?></td>
                                <td>
                                    <a href="view_player.php?id=<?php se($player, "player_id"); ?>">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="pagination">
                <?php if ($page > 1) : ?>
                    <a href="?page=<?php echo $page - 1; ?>&filter=<?php echo urlencode($filter); ?>">Previous</a>
                <?php endif; ?>
                <?php if ($page < $totalPages) : ?>
                    <a href="?page=<?php echo $page + 1; ?>&filter=<?php echo urlencode($filter); ?>">Next</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php include(__DIR__ . "/../../partials/footer.php"); ?>
</body>
</html>
