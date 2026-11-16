<?php
// Maakt de databaseverbinding en laadt de gedeelde functies
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functies.php';

try {
    $db = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAAM . ';charset=utf8mb4',
        DB_USER,
        DB_WACHTWOORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $ex) {
    http_response_code(500);
    exit('Geen databaseverbinding. Controleer includes/config.php en of de database is geimporteerd.');
}