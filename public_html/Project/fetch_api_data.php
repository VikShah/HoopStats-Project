<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require(__DIR__ . "/../../lib/functions.php");

function fetchAndStoreApiData() {
    $apiKey = "349fe690638d4800a67c83dc5ef44fbb"; // Your custom replay API key

    // Initialize database connection
    $db = getDB();

    // Clear out the player_stats table
    $db->exec("TRUNCATE TABLE player_stats");

    // Fetch all teams data
    $teamApiUrl = "https://replay.sportsdata.io/api/v3/nba/stats/json/allteams?key=$apiKey";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $teamApiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);
    $teamResponse = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
        exit;
    }
    curl_close($ch);

    // Print the raw response for debugging
    echo "Raw API Response: " . $teamResponse . "\n";

    // Attempt to decode the JSON response
    $teamsData = json_decode($teamResponse, true);

    // Check for a valid API response
    if (!is_array($teamsData)) {
        echo "Unexpected teams response structure.\n";
        var_dump($teamsData);
        return;
    }

    // Store teams in an associative array for easy lookup
    $teams = [];
    foreach ($teamsData as $team) {
        $teams[$team['TeamID']] = $team['Name'];
    }

    // Fetch player data
    $playerApiUrl = "https://replay.sportsdata.io/api/v3/nba/stats/json/playergamestatsbydate/2023-12-01?key=$apiKey"; // Adjust endpoint if needed
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $playerApiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);
    $playerResponse = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
        exit;
    }
    curl_close($ch);
    $playersData = json_decode($playerResponse, true);

    // Check if the response structure matches expectations
    if (is_array($playersData)) {
        // Store data in the database
        foreach ($playersData as $player) {
            $player_id = $player['PlayerID'];
            $player_name = $player['Name'];
            $team_id = $player['TeamID'] ?? null;
            $team_name = $team_id ? ($teams[$team_id] ?? 'Unknown') : 'Unknown';

            // Insert or update player data
            $stmt = $db->prepare("
                INSERT INTO player_stats 
                (player_id, player_name, team_name) 
                VALUES 
                (:player_id, :player_name, :team_name)
                ON DUPLICATE KEY UPDATE 
                team_name = VALUES(team_name)
            ");
            $stmt->execute([
                ":player_id" => $player_id,
                ":player_name" => $player_name,
                ":team_name" => $team_name
            ]);
        }

        echo "Data import completed.\n";
    } else {
        echo "Unexpected players response structure.\n";
        var_dump($playersData);
    }
}

// Call the function to fetch and store the API data
fetchAndStoreApiData();
?>
