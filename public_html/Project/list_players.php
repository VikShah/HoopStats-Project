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
$limit = 10; // Fixed limit for pagination
$page = se($_GET, "page", 1, false);
$page = max(1, $page); // Ensure page number is at least 1
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

// Count total records for pagination
$totalQuery = "SELECT COUNT(*) FROM player_stats WHERE first_name LIKE :filter OR last_name LIKE :filter";
$totalStmt = $db->prepare($totalQuery);
$totalStmt->execute([":filter" => $filterParam]);
$totalRecords = $totalStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HoopStats - Player List</title>
    <link rel="stylesheet" href="<?php echo get_url('styles.css'); ?>">
    <style>
        body {
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            flex-grow: 1;
        }
        .table-container {
            margin: 20px 0;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #333;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .pagination {
            margin: 20px 0;
            text-align: center;
        }
        .pagination a {
            margin: 0 5px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }
        .pagination a:hover {
            color: #ff6f00;
        }
        footer {
            background-color: #333;
            color: white;
            padding: 10px;
            text-align: center;
            position: absolute;
            bottom: 0;
            width: 100%;
        }
        .footer-content p {
            margin: 5px 0;
        }
        .footer-content a {
            color: #ff6f00;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Player Information</h1>
        <?php require_once(__DIR__ . "/../../partials/flash.php"); ?>
        <form method="GET">
            <label for="filter">Filter by Player Name:</label>
            <input id="filter" name="filter" value="<?php se($filter); ?>" />
            <input type="submit" value="Apply" />
        </form>

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
                    <?php if (empty($players)) : ?>
                        <tr>
                            <td colspan="4">No results available</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($players as $player) : ?>
                            <tr>
                                <td><?php se($player, "first_name"); ?></td>
                                <td><?php se($player, "last_name"); ?></td>
                                <td><?php se($player, "position"); ?></td>
                                <td>
                                    <a href="view_player.php?id=<?php se($player, "player_id"); ?>">View</a>
                                    <?php if ($isAdmin) : ?>
                                        | <a href="<?php echo get_url('admin/edit_player.php?id=' . se($player, "player_id", "", false)); ?>">Edit</a>
                                        | <a href="<?php echo get_url('admin/delete_player.php?id=' . se($player, "player_id", "", false)); ?>">Delete</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
    </div>
    <footer>
        <?php include(__DIR__ . "/../../partials/footer.php"); ?>
    </footer>
    <?php require_once(__DIR__ . "/../../partials/flash.php"); ?>
</body>
</html>
