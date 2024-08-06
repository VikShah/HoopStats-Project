<?php
require_once(__DIR__ . "/../../partials/nav.php");

if (!is_logged_in()) {
    flash("You must be logged in to view this page", "warning");
    die(header("Location: " . get_url("login.php")));
}

$user_id = get_user_id();
$db = getDB();
$query = "SELECT p.* FROM player_stats p 
          JOIN user_favorites uf ON p.player_id = uf.player_id 
          WHERE uf.user_id = :user_id";
$stmt = $db->prepare($query);
$stmt->execute([":user_id" => $user_id]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Favorites</title>
    <link rel="stylesheet" href="<?php echo get_url('styles.css'); ?>">
</head>
<body>
    <?php include(__DIR__ . "/../../partials/nav.php"); ?>
    <div class="container">
        <h1>My Favorites (<?php echo count($favorites); ?>)</h1>
        <?php require_once(__DIR__ . "/../../partials/flash.php"); ?>
        <?php if (empty($favorites)) : ?>
            <p>No favorite players found.</p>
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
            <form method="POST" action="clear_favorites.php">
                <input type="submit" value="Clear All Favorites">
            </form>
        <?php endif; ?>
    </div>
    <?php include(__DIR__ . "/../../partials/footer.php"); ?>
</body>
</html>
