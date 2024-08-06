<?php
require_once(__DIR__ . "/../../partials/nav.php");

// Ensure the user is logged in and has admin privileges
is_logged_in(true);
if (!has_role("Admin")) {
    flash("You do not have permission to access this page", "danger");
    die(header("Location: " . get_url("home.php")));
}

// Handle filtering, sorting, and pagination parameters
$limit = se($_GET, "limit", 10, false);
$limit = min(max(1, $limit), 100); // Limit between 1 and 100
$page = se($_GET, "page", 1, false);
$page = max(1, $page); // Ensure page number is at least 1
$offset = ($page - 1) * $limit;
$sort = se($_GET, "sort", "last_name", false); // Sorting by last name by default
$order = se($_GET, "order", "ASC", false); // Default order
$filter = se($_GET, "filter", "", false);

// Fetch associations with filtering, sorting, and pagination
$db = getDB();
$query = "SELECT p.*, u.username, u.id AS user_id, COUNT(uf.user_id) AS total_users 
          FROM player_stats p 
          JOIN user_favorites uf ON p.player_id = uf.player_id 
          JOIN Users u ON uf.user_id = u.id 
          WHERE u.username LIKE :filter 
          GROUP BY p.player_id, u.username 
          ORDER BY $sort $order 
          LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($query);
$filterParam = "%" . $filter . "%";
$stmt->bindParam(":filter", $filterParam, PDO::PARAM_STR);
$stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
$stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
$stmt->execute();
$associations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count total records for pagination
$totalQuery = "SELECT COUNT(DISTINCT p.player_id, u.username) 
               FROM player_stats p 
               JOIN user_favorites uf ON p.player_id = uf.player_id 
               JOIN Users u ON uf.user_id = u.id 
               WHERE u.username LIKE :filter";
$totalStmt = $db->prepare($totalQuery);
$totalStmt->execute([":filter" => $filterParam]);
$totalRecords = $totalStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All User Associations</title>
    <link rel="stylesheet" href="<?php echo get_url('styles.css'); ?>">
</head>
<body>
    <div class="container">
        <h1>All User Associations (<?php echo $totalRecords; ?>)</h1>
        <?php require_once(__DIR__ . "/../../partials/flash.php"); ?>
        <form method="GET">
            <label for="filter">Filter by Username:</label>
            <input id="filter" name="filter" value="<?php se($filter); ?>" />
            <label for="limit">Limit:</label>
            <input type="number" id="limit" name="limit" value="<?php se($limit); ?>" min="1" max="100" />
            <input type="submit" value="Apply" />
        </form>
        <?php if (empty($associations)) : ?>
            <p>No associations found.</p>
        <?php else : ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th><a href="?sort=first_name&order=<?php echo $order === 'ASC' ? 'DESC' : 'ASC'; ?>">First Name</a></th>
                            <th><a href="?sort=last_name&order=<?php echo $order === 'ASC' ? 'DESC' : 'ASC'; ?>">Last Name</a></th>
                            <th><a href="?sort=username&order=<?php echo $order === 'ASC' ? 'DESC' : 'ASC'; ?>">Username</a></th>
                            <th>Total Users</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($associations as $assoc) : ?>
                            <tr>
                                <td><?php se($assoc, "first_name"); ?></td>
                                <td><?php se($assoc, "last_name"); ?></td>
                                <td><a href="profile.php?id=<?php se($assoc, "user_id"); ?>"><?php se($assoc, "username"); ?></a></td>
                                <td><?php se($assoc, "total_users"); ?></td>
                                <td>
                                    <a href="view_player.php?id=<?php se($assoc, "player_id"); ?>">View</a>
                                    <form method="POST" action="remove_from_favorites.php" style="display:inline;">
                                        <input type="hidden" name="player_id" value="<?php se($assoc, "player_id"); ?>">
                                        <input type="hidden" name="user_id" value="<?php se($assoc, "user_id"); ?>">
                                        <input type="submit" value="Delete Association">
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
                <input type="hidden" name="filter" value="<?php se($filter); ?>">
                <input type="submit" value="Clear All Associations for Filtered Users">
            </form>
        <?php endif; ?>
    </div>
    <?php include(__DIR__ . "/../../partials/footer.php"); ?>
</body>
</html>
