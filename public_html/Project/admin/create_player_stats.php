<?php
require(__DIR__ . "/../../../partials/nav.php");

// Check if the user has the admin role
if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("home.php")));
}

// Handle form submission for creating or updating player stats
if (isset($_POST["player_name"])) {
    $player_name = se($_POST, "player_name", "", false);
    $team_name = se($_POST, "team_name", "", false);
    $points_per_game = se($_POST, "points_per_game", 0.0, false);
    $rebounds_per_game = se($_POST, "rebounds_per_game", 0.0, false);
    $assists_per_game = se($_POST, "assists_per_game", 0.0, false);
    $steals_per_game = se($_POST, "steals_per_game", 0.0, false);
    $blocks_per_game = se($_POST, "blocks_per_game", 0.0, false);
    $field_goal_percentage = se($_POST, "field_goal_percentage", 0.0, false);
    $free_throw_percentage = se($_POST, "free_throw_percentage", 0.0, false);
    $three_point_percentage = se($_POST, "three_point_percentage", 0.0, false);
    $player_id = se($_POST, "player_id", null, false);

    // Ensure the player name is not empty
    if (empty($player_name)) {
        flash("Player name is required", "warning");
    } else {
        $db = getDB();
        // Insert or update the player stats
        $stmt = $db->prepare("INSERT INTO player_stats (player_id, player_name, team_name, points_per_game, rebounds_per_game, assists_per_game, steals_per_game, blocks_per_game, field_goal_percentage, free_throw_percentage, three_point_percentage, api_record_id) VALUES(:player_id, :player_name, :team_name, :points_per_game, :rebounds_per_game, :assists_per_game, :steals_per_game, :blocks_per_game, :field_goal_percentage, :free_throw_percentage, :three_point_percentage, :api_record_id) ON DUPLICATE KEY UPDATE player_name = VALUES(player_name), team_name = VALUES(team_name), points_per_game = VALUES(points_per_game), rebounds_per_game = VALUES(rebounds_per_game), assists_per_game = VALUES(assists_per_game), steals_per_game = VALUES(steals_per_game), blocks_per_game = VALUES(blocks_per_game), field_goal_percentage = VALUES(field_goal_percentage), free_throw_percentage = VALUES(free_throw_percentage), three_point_percentage = VALUES(three_point_percentage)");
        try {
            $stmt->execute([
                ":player_id" => $player_id,
                ":player_name" => $player_name,
                ":team_name" => $team_name,
                ":points_per_game" => $points_per_game,
                ":rebounds_per_game" => $rebounds_per_game,
                ":assists_per_game" => $assists_per_game,
                ":steals_per_game" => $steals_per_game,
                ":blocks_per_game" => $blocks_per_game,
                ":field_goal_percentage" => $field_goal_percentage,
                ":free_throw_percentage" => $free_throw_percentage,
                ":three_point_percentage" => $three_point_percentage,
                ":api_record_id" => $player_id ? null : "manual" // Mark manually created records
            ]);
            flash("Successfully added/updated player stats!", "success");
        } catch (PDOException $e) {
            flash("Database error: " . var_export($e->errorInfo, true), "danger");
        }
    }
}
?>

<h1>Create Player Stats</h1>
<form method="POST">
    <label for="player_id">Player ID (Leave empty for new players)</label>
    <input id="player_id" name="player_id" type="number" />
    <label for="player_name">Player Name</label>
    <input id="player_name" name="player_name" required />
    <label for="team_name">Team Name</label>
    <input id="team_name" name="team_name" />
    <label for="points_per_game">Points Per Game</label>
    <input id="points_per_game" name="points_per_game" type="number" step="0.01" min="0" />
    <label for="rebounds_per_game">Rebounds Per Game</label>
    <input id="rebounds_per_game" name="rebounds_per_game" type="number" step="0.01" min="0" />
    <label for="assists_per_game">Assists Per Game</label>
    <input id="assists_per_game" name="assists_per_game" type="number" step="0.01" min="0" />
    <label for="steals_per_game">Steals Per Game</label>
    <input id="steals_per_game" name="steals_per_game" type="number" step="0.01" min="0" />
    <label for="blocks_per_game">Blocks Per Game</label>
    <input id="blocks_per_game" name="blocks_per_game" type="number" step="0.01" min="0" />
    <label for="field_goal_percentage">Field Goal Percentage</label>
    <input id="field_goal_percentage" name="field_goal_percentage" type="number" step="0.01" min="0" max="100" />
    <label for="free_throw_percentage">Free Throw Percentage</label>
    <input id="free_throw_percentage" name="free_throw_percentage" type="number" step="0.01" min="0" max="100" />
    <label for="three_point_percentage">Three Point Percentage</label>
    <input id="three_point_percentage" name="three_point_percentage" type="number" step="0.01" min="0" max="100" />
    <input type="submit" value="Create/Update Stats" />
</form>

<?php
require_once(__DIR__ . "/../../../partials/flash.php");
?>
