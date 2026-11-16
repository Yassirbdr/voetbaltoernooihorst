-- Importeer dit bestand in phpMyAdmin (tab "Importeren")
CREATE DATABASE IF NOT EXISTS voetbaldagen_horst CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE voetbaldagen_horst;

CREATE TABLE toernooien (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(40) NOT NULL UNIQUE,
  naam VARCHAR(80) NOT NULL,
  max_teams INT NOT NULL DEFAULT 25
);

INSERT INTO toernooien (slug, naam, max_teams) VALUES
('voetvolley', 'Voetvolley', 25),
('champions-league', 'Champions League', 25),
('wk', 'Wereldkampioenschap', 25);

CREATE TABLE teams (
  id INT AUTO_INCREMENT PRIMARY KEY,
  registratienummer VARCHAR(20) UNIQUE,
  token CHAR(32) NOT NULL UNIQUE,
  toernooi_id INT NOT NULL,
  teamnaam VARCHAR(60) NOT NULL,
  captain VARCHAR(80) NOT NULL,
  email VARCHAR(120) NOT NULL,
  telefoon VARCHAR(25) NOT NULL,
  keuze VARCHAR(60) NULL,
  status ENUM('in behandeling','ingeschreven','wachtlijst','afgewezen','geannuleerd') NOT NULL DEFAULT 'in behandeling',
  aangemaakt DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniek_teamnaam (toernooi_id, teamnaam),
  FOREIGN KEY (toernooi_id) REFERENCES toernooien(id)
);

CREATE TABLE spelers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  team_id INT NOT NULL,
  nummer INT NOT NULL,
  naam VARCHAR(60) NOT NULL,
  leeftijd TINYINT UNSIGNED NOT NULL,
  FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE
);

CREATE TABLE vrijwilligers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  registratienummer VARCHAR(20) UNIQUE,
  token CHAR(32) NOT NULL UNIQUE,
  naam VARCHAR(80) NOT NULL,
  email VARCHAR(120) NOT NULL,
  telefoon VARCHAR(25) NOT NULL,
  dagen VARCHAR(255) NOT NULL,
  taken VARCHAR(255) NULL,
  opmerkingen TEXT NULL,
  status ENUM('nieuw','goedgekeurd','afgewezen') NOT NULL DEFAULT 'nieuw',
  aangemaakt DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE berichten (
  id INT AUTO_INCREMENT PRIMARY KEY,
  naam VARCHAR(80) NOT NULL,
  email VARCHAR(120) NOT NULL,
  bericht TEXT NOT NULL,
  aangemaakt DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  gebruikersnaam VARCHAR(40) NOT NULL UNIQUE,
  wachtwoord_hash VARCHAR(255) NOT NULL
);

-- Eerste admin: gebruikersnaam admin, wachtwoord Horst2027!  (VERANDER DIT, zie README)
INSERT INTO admins (gebruikersnaam, wachtwoord_hash) VALUES
('admin', '$2y$10$Y8gbgRgCCKFM2I4wQiPtYecF21oamH842.m9R3TtW7er7JaEVfed.');