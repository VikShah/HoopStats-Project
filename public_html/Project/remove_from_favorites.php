<?php
require(__DIR__ . "/../../partials/nav.php");

if (!is_logged_in()) {
    flash("You must be logged in to perform this action", "warning");
    die(header("Location: " . get_url("login.php")));
}

$user_id = get_user_id();
$player_id = se($_POST, "player_id", null, false);

if (!$player_id || !filter_var($player_id, FILTER_VALIDATE_INT)) {
    flash("Invalid player ID", "danger");
    die(header("Location: " . get_url("favorites.php")));
}

$db = getDB();
$stmt = $db->prepare("DELETE FROM user_favorites WHERE user_id = :user_id AND player_id = :player_id");
try {
    $stmt->execute([":user_id" => $user_id, ":player_id" => $player_id]);
    flash("Player removed from favorites", "success");
} catch (PDOException $e) {
    flash("Error removing from favorites: " . $e->getMessage(), "danger");
}

header("Location: " . get_url("favorites.php"));
