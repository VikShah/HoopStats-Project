<?php
require(__DIR__ . "/../../lib/functions.php");
is_logged_in(true);

$user_id = get_user_id();
$player_id = $_POST['player_id'] ?? null;
$action = $_POST['action'] ?? null;

if (!$player_id || !filter_var($player_id, FILTER_VALIDATE_INT)) {
    flash("Invalid player ID", "danger");
    die(header("Location: " . get_url("list_players.php")));
}

$db = getDB();

if ($action === 'add') {
    $stmt = $db->prepare("INSERT INTO user_favorites (user_id, player_id) VALUES (:user_id, :player_id)");
    try {
        $stmt->execute([":user_id" => $user_id, ":player_id" => $player_id]);
        flash("Player added to favorites", "success");
    } catch (PDOException $e) {
        flash("Error adding player to favorites: " . $e->getMessage(), "danger");
    }
} elseif ($action === 'remove') {
    $stmt = $db->prepare("DELETE FROM user_favorites WHERE user_id = :user_id AND player_id = :player_id");
    try {
        $stmt->execute([":user_id" => $user_id, ":player_id" => $player_id]);
        flash("Player removed from favorites", "success");
    } catch (PDOException $e) {
        flash("Error removing player from favorites: " . $e->getMessage(), "danger");
    }
}

header("Location: " . get_url("list_players.php"));
?>
