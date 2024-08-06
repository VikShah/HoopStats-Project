<?php
require(__DIR__ . "/../../lib/functions.php");
is_logged_in(true);

$user_id = get_user_id();
$db = getDB();
$stmt = $db->prepare("SELECT ps.* FROM player_stats ps JOIN user_favorites uf ON ps.player_id = uf.player_id WHERE uf.user_id = :user_id");
$stmt->execute([":user_id" => $user_id]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Favorite Players</title>
    <link rel="stylesheet" href="<?php echo get_url('styles.css'); ?>">
</head>
<body>
    <?php include(__DIR__ . "/../../partials/nav.php"); ?>
    <main>
        <div class="container">
            <h1>My Favorite Players</h1>
            <?php require(__DIR__ . "/../../partials/flash.php"); ?>
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
                                        <form method="POST" action="<?php echo get_url('manage_favorites.php'); ?>" style="display:inline;">
                                            <input type="hidden" name="player_id" value="<?php se($player, "player_id"); ?>">
                                            <input type="hidden" name="action" value="remove">
                                            <input type="submit" value="Remove from Favorites">
                                        </form>
                                        <a href="<?php echo get_url('view_player.php?id=' . se($player, "player_id", "", false)); ?>">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <?php include(__DIR__ . "/../../partials/footer.php"); ?>
</body>
</html>
