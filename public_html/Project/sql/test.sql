-- UCID: vs53, Date: July 30th, 2024
DROP TABLE IF EXISTS player_stats;

CREATE TABLE player_stats (
    player_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    position VARCHAR(50),
    height FLOAT,
    weight FLOAT,
    country VARCHAR(100),
    college VARCHAR(100),
    birth_date DATE,
    nba_start_year INT,
    years_pro INT
);
