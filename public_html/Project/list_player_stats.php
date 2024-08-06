<?php
// Use __DIR__ to include the nav.php file relative to the current file's location
require(__DIR__ . "/../../partials/nav.php");

// Check if the user is logged in
if (!is_logged_in()) {
    flash("You must be logged in to view this page", "warning");
    die(header("Location: login.php"));
}

// Check if the user has admin role for admin-specific functionalities
$isAdmin = has_role("Admin");

// Handle filtering, sorting, and pagination parameters
$limit = se($_GET, "limit", 10, false);
$limit = max(1, min(100, $limit)); // Ensure limit is within 1-100
$page = se($_GET, "page", 1, false);
$offset = ($page - 1) * $limit;
$sort = se($_GET, "sort", "last_name", false); // Sorting by last name by default
$order = se($_GET, "order", "ASC", false); // Default order
$filter = se($_GET, "filter", "", false);

// Fetch player stats with filtering, sorting, and pagination
$db = getDB();
$query = "SELECT * FROM player_stats WHERE first_name LIKE :filter OR last_name LIKE :filter ORDER BY $sort $order LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($query);
$filterParam = "%" . $filter . "%";
$stmt->bindParam(":filter", $filterParam, PDO::PARAM_STR);
$stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
$stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
$stmt->execute();
$players = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<h1>Player Information</h1>
<form method="GET">
    <label for="filter">Filter by Player Name:</label>
    <input id="filter" name="filter" value="<?php se($filter); ?>" />
    <label for="limit">Records per page:</label>
    <input id="limit" name="limit" type="number" value="<?php se($limit); ?>" min="1" max="100" />
    <input type="submit" value="Apply" />
</form>

<table>
    <thead>
        <tr>
            <th><a href="?sort=first_name&order=<?php echo $order === 'ASC' ? 'DESC' : 'ASC'; ?>">First Name</a></th>
            <th><a href="?sort=last_name&order=<?php echo $order === 'ASC' ? 'DESC' : 'ASC'; ?>">Last Name</a></th>
            <th>Team Name</th>
            <th>Position</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($players)) : ?>
            <tr>
                <td colspan="5">No results available</td>
            </tr>
        <?php else : ?>
            <?php foreach ($players as $player) : ?>
                <tr>
                    <td><?php se($player, "first_name"); ?></td>
                    <td><?php se($player, "last_name"); ?></td>
                    <td><?php se($player, "team_name"); ?></td>
                    <td><?php se($player, "position"); ?></td>
                    <td>
                        <a href="view_player.php?id=<?php se($player, "player_id"); ?>">View</a>
                        <?php if ($isAdmin) : ?>
                            | <a href="edit_player.php?id=<?php se($player, "player_id"); ?>">Edit</a>
                            | <a href="delete_player.php?id=<?php se($player, "player_id"); ?>">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>
