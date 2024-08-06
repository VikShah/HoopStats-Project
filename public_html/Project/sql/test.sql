ALTER TABLE player_stats ADD COLUMN data_source ENUM('API', 'CUSTOM') DEFAULT 'API';
