CREATE DATABASE IF NOT EXISTS gamevault CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gamevault;
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS user_games;
DROP TABLE IF EXISTS games;
DROP TABLE IF EXISTS platforms;
DROP TABLE IF EXISTS genres;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE users (
 user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(50) NOT NULL UNIQUE,
 email VARCHAR(120) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE genres (
 genre_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(60) NOT NULL UNIQUE,
 description VARCHAR(255)
) ENGINE=InnoDB;

CREATE TABLE platforms (
 platform_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(60) NOT NULL UNIQUE,
 description VARCHAR(255)
) ENGINE=InnoDB;

CREATE TABLE games (
 game_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 title VARCHAR(150) NOT NULL,
 description TEXT,
 release_date DATE,
 genre_id INT UNSIGNED,
 cover_url VARCHAR(500),
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (genre_id) REFERENCES genres(genre_id) ON UPDATE CASCADE ON DELETE SET NULL,
 INDEX idx_games_title(title)
) ENGINE=InnoDB;

CREATE TABLE user_games (
 user_game_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id INT UNSIGNED NOT NULL,
 game_id INT UNSIGNED NOT NULL,
 platform_id INT UNSIGNED NOT NULL,
 status ENUM('Playing','Completed','Want to play') NOT NULL DEFAULT 'Want to play',
 rating TINYINT UNSIGNED NULL,
 date_added DATE NOT NULL,
 CHECK (rating IS NULL OR rating BETWEEN 1 AND 5),
 UNIQUE KEY uq_user_game_platform(user_id,game_id,platform_id),
 FOREIGN KEY (user_id) REFERENCES users(user_id) ON UPDATE CASCADE ON DELETE CASCADE,
 FOREIGN KEY (game_id) REFERENCES games(game_id) ON UPDATE CASCADE ON DELETE CASCADE,
 FOREIGN KEY (platform_id) REFERENCES platforms(platform_id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Demo-only accounts. Replace with real password_hash() output before production.
INSERT INTO users(username,email,password_hash) VALUES
('playerone','player1@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.'),
('player2','player2@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.');

INSERT INTO genres(name,description) VALUES
('2D Fighter','Two-dimensional fighting games'),
('RPG','Role-playing games'),
('Survival Horror','Horror games focused on survival'),
('Open World','Games featuring open-world exploration'),
('Puzzle','Games focused on solving puzzles'),
('FPS Shooter','First-person shooter games');

INSERT INTO platforms(name,description) VALUES
('PC','Personal computer'),('PlayStation 5','Sony console'),('Xbox Series X/S','Microsoft console'),('Nintendo Switch','Nintendo console');

INSERT INTO games(title,description,release_date,genre_id,cover_url) VALUES
('Dragon Ball FighterZ','2D fighting game based on Dragon Ball','2018-01-01',1,'dbfighterz'),
('Cyberpunk 2077','Open-world role-playing game','2020-01-01',2,'cyber'),
('Resident Evil 4','Survival horror game','2005-01-01',3,'re4'),
('The King Of Fighters XV','2D fighting game','2022-01-01',1,'kofxv'),
('Dying Light: The Beast','Open-world survival action game','2025-01-01',4,'dyinglight'),
('Portal 2','First-person puzzle game','2011-01-01',5,'portal'),
('Power Drill Massacre','Survival horror game','2028-01-01',3,'powerdrill'),
('Team Fortress 2','Team-based first-person shooter','2007-01-01',6,'teamfortress2');

INSERT INTO user_games(user_id,game_id,platform_id,status,rating,date_added) VALUES
(1,1,1,'Playing',NULL,'2026-10-31'),
(1,2,1,'Playing',4,'2026-10-31'),
(1,3,1,'Completed',NULL,'2026-10-31'),
(1,4,1,'Playing',NULL,'2026-10-31'),
(1,5,1,'Want to play',NULL,'2026-10-31'),
(1,6,1,'Completed',5,'2026-10-31'),
(1,7,1,'Want to play',NULL,'2026-10-31'),
(1,8,1,'Playing',NULL,'2026-10-31');