<?php
require(__DIR__ . "/../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You do not have permission to perform this action", "danger");
    die(header("Location: " . get_url("home.php")));
}

$user_id = se($_POST, "user_id", null, false);
$player_id = se($_POST, "player_id", null, false);

if (!$user_id || !filter_var($user_id, FILTER_VALIDATE_INT) || !$player_id || !filter_var($player_id, FILTER_VALIDATE_INT)) {
    flash("Invalid user or player ID", "danger");
    die(header("Location: " . get_url("manage_favorites.php")));
}

$db = getDB();
$stmt = $db->prepare("DELETE FROM user_favorites WHERE user_id = :user_id AND player_id = :player_id");
try {
    $stmt->execute([":user_id" => $user_id, ":player_id" => $player_id]);
    flash("Favorite removed successfully", "success");
} catch (PDOException $e) {
    flash("Error removing favorite: " . $e->getMessage(), "danger");
}

header("Location: " . get_url("manage_favorites.php"));
?>
