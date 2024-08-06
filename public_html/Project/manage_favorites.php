<?php
require_once(__DIR__ . "/../../partials/nav.php");

// Check if the user is logged in and has admin privileges
if (!is_logged_in() || !has_role("Admin")) {
    flash("You do not have permission to access this page", "danger");
    die(header("Location: " . get_url("login.php")));
}

$limit = se($_GET, "limit", 10, false);
$page = se($_GET, "page", 1, false);
$page = max(1, $page); // Ensure page number is at least 1
$offset = ($page - 1) * $limit;
$filter = se($_GET, "filter", "", false);
$sort = se($_GET, "sort", "username", false); // Sorting by username by default
$order = se($_GET, "order", "ASC", false); // Default order

$db = getDB();
$query = "SELECT u.username, p.first_name, p.last_name, uf.user_id, uf.player_id FROM user_favorites uf 
          JOIN Users u ON uf.user_id = u.id 
          JOIN player_stats p ON uf.player_id = p.player_id 
          WHERE u.username LIKE :filter OR p.first_name LIKE :filter OR p.last_name LIKE :filter 
          ORDER BY $sort $order LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($query);
$filterParam = "%" . $filter . "%";
$stmt->bindParam(":filter", $filterParam, PDO::PARAM_STR);
$stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
$stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count total records for pagination
$totalQuery = "SELECT COUNT(*) FROM user_favorites uf 
               JOIN Users u ON uf.user_id = u.id 
               JOIN player_stats p ON uf.player_id = p.player_id 
               WHERE u.username LIKE :filter OR p.first_name LIKE :filter OR p.last_name LIKE :filter";
$totalStmt = $db->prepare($totalQuery);
$totalStmt->execute([":filter" => $filterParam]);
$totalRecords = $totalStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Favorites</title>
    <link rel="stylesheet" href="<?php echo get_url('styles.css'); ?>">
</head>
<body>
    <div class="container">
        <h1>Manage Favorites</h1>
        <?php require_once(__DIR__ . "/../../partials/flash.php"); ?>
        <form method="GET">
            <label for="filter">Filter by Username or Player Name:</label>
            <input id="filter" name="filter" value="<?php se($filter); ?>" />
            <input type="submit" value="Apply" />
        </form>
        <?php if (empty($results)) : ?>
            <p>No favorites found.</p>
        <?php else : ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th><a href="?sort=username&order=<?php echo $order === 'ASC' ? 'DESC' : 'ASC'; ?>">Username</a></th>
                            <th><a href="?sort=first_name&order=<?php echo $order === 'ASC' ? 'DESC' : 'ASC'; ?>">First Name</a></th>
                            <th><a href="?sort=last_name&order=<?php echo $order === 'ASC' ? 'DESC' : 'ASC'; ?>">Last Name</a></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $row) : ?>
                            <tr>
                                <td><?php se($row, "username"); ?></td>
                                <td><?php se($row, "first_name"); ?></td>
                                <td><?php se($row, "last_name"); ?></td>
                                <td>
                                    <form method="POST" action="remove_favorite.php" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?php se($row, "user_id"); ?>">
                                        <input type="hidden" name="player_id" value="<?php se($row, "player_id"); ?>">
                                        <input type="submit" value="Remove Favorite">
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
