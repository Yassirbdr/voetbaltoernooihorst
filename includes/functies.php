<?php
// Gedeelde functies en REGELS van de website
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

// ===== REGELS (hier aanpassen als de organisatie iets wijzigt) =====
const MIN_SPELERS  = 14;
const MAX_SPELERS  = 23;
const MIN_LEEFTIJD = 6;
const MAX_LEEFTIJD = 16;

const STATUSSEN       = ['in behandeling', 'ingeschreven', 'wachtlijst', 'afgewezen', 'geannuleerd'];
const VRIJW_STATUSSEN = ['nieuw', 'goedgekeurd', 'afgewezen'];
const DAGEN = ['Maandag 30 augustus', 'Dinsdag 31 augustus', 'Woensdag 1 september', 'Donderdag 2 september', 'Vrijdag 3 september'];
const TAKEN = [
    'Scheidsrechter' => '🟨',
    'Kantine' => '🥤',
    'Opbouw en afbouw' => '🛠️',
    'Wedstrijdbegeleiding' => '📋',
    'Algemene ondersteuning' => '🤝',
];

// Veilig tekst tonen (tegen XSS)
function e($tekst) { return htmlspecialchars((string)$tekst, ENT_QUOTES, 'UTF-8'); }

function redirect($url) { header('Location: ' . $url); exit; }

// CSRF-bescherming voor formulieren
function csrf_token() {
    if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(16)); }
    return $_SESSION['csrf'];
}
function csrf_veld() { return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">'; }
function csrf_ok() {
    return isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string)$_POST['csrf']);
}

function maak_token() { return bin2hex(random_bytes(16)); }
function datum_nl($d) { return date('d-m-Y H:i', strtotime($d)); }

// Haalt een lijst met tekst uit $_POST (negeert alles wat geen tekst is)
function lijst_post($naam) {
    $x = $_POST[$naam] ?? [];
    return is_array($x) ? array_values(array_filter($x, 'is_string')) : [];
}

// Admin
function is_admin() { return !empty($_SESSION['admin_id']); }
function vereis_admin() { if (!is_admin()) { redirect('login.php'); } }

// Toernooi en plekken
function toernooi_ophalen(PDO $db, $slug) {
    $s = $db->prepare('SELECT * FROM toernooien WHERE slug = ?');
    $s->execute([$slug]);
    return $s->fetch();
}
// Aantal bezette plekken (wachtlijst, afgewezen en geannuleerd tellen niet mee)
function plekken_bezet(PDO $db, $toernooiId) {
    $s = $db->prepare("SELECT COUNT(*) FROM teams WHERE toernooi_id = ? AND status IN ('in behandeling','ingeschreven')");
    $s->execute([$toernooiId]);
    return (int)$s->fetchColumn();
}
// Clubs/landen die al gekozen zijn door een ander team
function bezette_keuzes(PDO $db, $toernooiId) {
    $s = $db->prepare("SELECT keuze FROM teams WHERE toernooi_id = ? AND keuze IS NOT NULL AND status IN ('in behandeling','ingeschreven')");
    $s->execute([$toernooiId]);
    return $s->fetchAll(PDO::FETCH_COLUMN);
}