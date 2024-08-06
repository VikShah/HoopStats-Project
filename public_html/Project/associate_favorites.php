<?php
require(__DIR__ . "/../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You do not have permission to access this page", "danger");
    die(header("Location: " . get_url("home.php")));
}

$filterPlayer = se($_POST, "filter_player", "", false);
$filterUser = se($_POST, "filter_user", "", false);

$db = getDB();

// Fetch players based on filter
$playerQuery = "SELECT * FROM player_stats WHERE first_name LIKE :filter OR last_name LIKE :filter LIMIT 25";
$stmt = $db->prepare($playerQuery);
$playerFilterParam = "%" . $filterPlayer . "%";
$stmt->execute([":filter" => $playerFilterParam]);
$players = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch users based on filter
$userQuery = "SELECT * FROM users WHERE username LIKE :filter LIMIT 25";
$stmt = $db->prepare($userQuery);
$userFilterParam = "%" . $filterUser . "%";
$stmt->execute([":filter" => $userFilterParam]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["associate"])) {
    $selectedPlayers = $_POST["players"] ?? [];
    $selectedUsers = $_POST["users"] ?? [];

    $stmt = $db->prepare("INSERT INTO user_favorites (user_id, player_id) VALUES (:user_id, :player_id) ON DUPLICATE KEY UPDATE user_id=user_id");
    foreach ($selectedUsers as $user_id) {
        foreach ($selectedPlayers as $player_id) {
            $stmt->execute([":user_id" => $user_id, ":player_id" => $player_id]);
        }
    }
    flash("Associations updated successfully", "success");
    die(header("Location: " . get_url("associate_favorites.php")));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Associate Favorites</title>
    <link rel="stylesheet" href="<?php echo get_url('styles.css'); ?>">
</head>
<body>
    <?php include(__DIR__ . "/../../partials/nav.php"); ?>
    <div class="container">
        <h1>Associate Favorites</h1>
        <?php require_once(__DIR__ . "/../../partials/flash.php"); ?>
        <form method="POST">
            <div class="filter-container">
                <label for="filter_player">Filter Players by Name:</label>
                <input id="filter_player" name="filter_player" value="<?php se($filterPlayer); ?>" />
                <label for="filter_user">Filter Users by Username:</label>
                <input id="filter_user" name="filter_user" value="<?php se($filterUser); ?>" />
                <input type="submit" value="Filter">
            </div>
            <div class="selection-container">
                <div class="players-list">
                    <h3>Select Players</h3>
                    <?php foreach ($players as $player) : ?>
                        <div>
                            <input type="checkbox" name="players[]" value="<?php se($player, "player_id"); ?>">
                            <label><?php se($player, "first_name"); ?> <?php se($player, "last_name"); ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="users-list">
                    <h3>Select Users</h3>
                    <?php foreach ($users as $user) : ?>
                        <div>
                            <input type="checkbox" name="users[]" value="<?php se($user, "id"); ?>">
                            <label><?php se($user, "username"); ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <input type="submit" name="associate" value="Associate">
        </form>
    </div>
    <?php include(__DIR__ . "/../../partials/footer.php"); ?>
</body>
</html>
