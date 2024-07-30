<?php
// Enable error reporting for debugging purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require(__DIR__ . "/../../lib/functions.php");

function fetchAndStoreApiData() {
    $apiKey = "93898150famsh91be62240b4e20fp15f209jsna1205e9ebeaf"; // Your RapidAPI key

    // Initialize database connection
    $db = getDB();

    // Clear out the player_stats table
    $db->exec("TRUNCATE TABLE player_stats");

    // Fetch player data from the API
    $playerApiUrl = "https://api-nba-v1.p.rapidapi.com/players?team=1&season=2021";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $playerApiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "x-rapidapi-host: api-nba-v1.p.rapidapi.com",
        "x-rapidapi-key: $apiKey"
    ]);
    $playerResponse = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
        exit;
    }
    curl_close($ch);
    $playersData = json_decode($playerResponse, true);

    if (isset($playersData['response']) && is_array($playersData['response'])) {
        foreach ($playersData['response'] as $player) {
            $stmt = $db->prepare("
                INSERT INTO player_stats 
                (player_id, first_name, last_name, position, height, weight, country, college, birth_date, nba_start_year, years_pro) 
                VALUES 
                (:player_id, :first_name, :last_name, :position, :height, :weight, :country, :college, :birth_date, :nba_start_year, :years_pro)
                ON DUPLICATE KEY UPDATE 
                first_name = VALUES(first_name),
                last_name = VALUES(last_name),
                position = VALUES(position),
                height = VALUES(height),
                weight = VALUES(weight),
                country = VALUES(country),
                college = VALUES(college),
                birth_date = VALUES(birth_date),
                nba_start_year = VALUES(nba_start_year),
                years_pro = VALUES(years_pro)
            ");
            // Convert the birth date to null if not available
            $birthDate = !empty($player['birth']['date']) ? $player['birth']['date'] : null;
            $stmt->execute([
                ":player_id" => $player['id'],
                ":first_name" => $player['firstname'],
                ":last_name" => $player['lastname'],
                ":position" => $player['leagues']['standard']['pos'] ?? 'N/A',
                ":height" => $player['height']['meters'] ?? null,
                ":weight" => $player['weight']['kilograms'] ?? null,
                ":country" => $player['birth']['country'] ?? 'N/A',
                ":college" => $player['college'] ?? 'N/A',
                ":birth_date" => $birthDate,
                ":nba_start_year" => $player['nba']['start'] ?? null,
                ":years_pro" => $player['nba']['pro'] ?? null
            ]);
        }

        echo "Data import completed.\n";
    } else {
        echo "Unexpected API response structure.\n";
        var_dump($playersData);
    }
}

fetchAndStoreApiData();
?>
