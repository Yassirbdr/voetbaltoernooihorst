<?php
// Tijdelijk testbestand: laat zien welke database gebruikt wordt en waar de tabellen staan
require __DIR__ . '/includes/config.php';
echo 'Config wijst naar database: <b>' . DB_NAAM . '</b><br><br>';
try {
    $p = new PDO('mysql:host=' . DB_HOST . ';charset=utf8mb4', DB_USER, DB_WACHTWOORD);
    $systeem = ['information_schema', 'mysql', 'performance_schema', 'phpmyadmin', 'sys', 'test'];
    foreach ($p->query('SHOW DATABASES') as $d) {
        $naam = $d[0];
        if (in_array($naam, $systeem)) { continue; }
        $n = $p->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '" . $naam . "'")->fetchColumn();
        echo 'Database <b>' . $naam . '</b>: ' . $n . ' tabellen<br>';
    }
} catch (Exception $e) {
    echo 'Fout: ' . $e->getMessage();
}