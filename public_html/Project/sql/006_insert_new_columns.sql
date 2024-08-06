# UCID: vs53, Date: Jul 22nd 2024
CREATE TABLE player_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    player_id INT NOT NULL,
    player_name VARCHAR(100) NOT NULL,
    team_name VARCHAR(50),
    points_per_game DECIMAL(4, 2),
    rebounds_per_game DECIMAL(4, 2),
    assists_per_game DECIMAL(4, 2),
    steals_per_game DECIMAL(4, 2),
    blocks_per_game DECIMAL(4, 2),
    field_goal_percentage DECIMAL(5, 2),
    free_throw_percentage DECIMAL(5, 2),
    three_point_percentage DECIMAL(5, 2)
);
