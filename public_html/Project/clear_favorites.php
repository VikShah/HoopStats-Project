<?php
require_once(__DIR__ . "/../../partials/nav.php");

if (!is_logged_in()) {
    flash("You must be logged in to perform this action", "warning");
    die(header("Location: " . get_url("login.php")));
}

$user_id = get_user_id();
$db = getDB();
$stmt = $db->prepare("DELETE FROM user_favorites WHERE user_id = :user_id");
try {
    $stmt->execute([":user_id" => $user_id]);
    flash("All favorites cleared", "success");
} catch (PDOException $e) {
    flash("Error clearing favorites: " . $e->getMessage(), "danger");
}

header("Location: " . get_url("favorites.php"));
?>
