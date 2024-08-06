<?php
require_once(__DIR__ . "/../../partials/nav.php");

// Ensure the user is logged in and has admin privileges
is_logged_in(true);
if (!has_role("Admin")) {
    flash("You do not have permission to access this page", "danger");
    die(header("Location: " . get_url("home.php")));
}

$player_search = se($_POST, "player_search", "", false);
$user_search = se($_POST, "user_search", "", false);
$players = [];
$users = [];

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    // Fetch players matching the search term
    $playerQuery = "SELECT * FROM player_stats WHERE first_name LIKE :search OR last_name LIKE :search LIMIT 25";
    $stmt = $db->prepare($playerQuery);
    $searchTerm = "%" . $player_search . "%";
    $stmt->execute([":search" => $searchTerm]);
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch users matching the search term
    $userQuery = "SELECT * FROM Users WHERE username LIKE :search LIMIT 25";
    $stmt = $db->prepare($userQuery);
    $searchTerm = "%" . $user_search . "%";
    $stmt->execute([":search" => $searchTerm]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['associate'])) {
    $selectedPlayers = $_POST['players'] ?? [];
    $selectedUsers = $_POST['users'] ?? [];

    foreach ($selectedPlayers as $player_id) {
        foreach ($selectedUsers as $user_id) {
            $stmt = $db->prepare("INSERT INTO user_favorites (user_id, player_id) VALUES (:user_id, :player_id) ON DUPLICATE KEY UPDATE user_id = user_id");
            $stmt->execute([":user_id" => $user_id, ":player_id" => $player_id]);
        }
    }

    flash("Associations updated successfully", "success");
    header("Location: " . get_url("associate_entities.php"));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Associate Entities</title>
    <link rel="stylesheet" href="<?php echo get_url('styles.css'); ?>">
</head>
<body>
    <div class="container">
        <h1>Associate Entities</h1>
        <?php require_once(__DIR__ . "/../../partials/flash.php"); ?>
        <form method="POST">
            <div>
                <label for="player_search">Search Players:</label>
                <input type="text" id="player_search" name="player_search" value="<?php se($player_search); ?>">
            </div>
            <div>
                <label for="user_search">Search Users:</label>
                <input type="text" id="user_search" name="user_search" value="<?php se($user_search); ?>">
            </div>
            <div>
                <input type="submit" name="search" value="Search">
            </div>
        </form>
        <form method="POST">
            <input type="hidden" name="player_search" value="<?php se($player_search); ?>">
            <input type="hidden" name="user_search" value="<?php se($user_search); ?>">
            <div>
                <h2>Players</h2>
                <?php if (empty($players)) : ?>
                    <p>No players found.</p>
                <?php else : ?>
                    <?php foreach ($players as $player) : ?>
                        <div>
                            <input type="checkbox" name="players[]" value="<?php se($player, 'player_id'); ?>">
                            <?php se($player, 'first_name'); ?> <?php se($player, 'last_name'); ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div>
                <h2>Users</h2>
                <?php if (empty($users)) : ?>
                    <p>No users found.</p>
                <?php else : ?>
                    <?php foreach ($users as $user) : ?>
                        <div>
                            <input type="checkbox" name="users[]" value="<?php se($user, 'id'); ?>">
                            <?php se($user, 'username'); ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div>
                <input type="submit" name="associate" value="Update Associations">
            </div>
        </form>
    </div>
    <?php include(__DIR__ . "/../../partials/footer.php"); ?>
</body>
</html>
