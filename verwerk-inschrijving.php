<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/data.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_ok()) { redirect('toernooien.php'); }
$slug = $_POST['toernooi'] ?? '';
if (!isset(TOERNOOIEN[$slug])) { redirect('toernooien.php'); }
$t = toernooi_ophalen($db, $slug);
if (!$t) { redirect('toernooien.php'); }

$terug = 'inschrijven.php?toernooi=' . urlencode($slug);
$heeftKeuze = $slug !== 'voetvolley';
$fouten = [];

// ----- gegevens ophalen -----
$str = fn($k) => isset($_POST[$k]) && is_string($_POST[$k]) ? trim($_POST[$k]) : '';
$teamnaam = $str('teamnaam');
$captain  = $str('captain');
$email    = $str('email');
$telefoon = $str('telefoon');
$keuze    = $heeftKeuze ? $str('keuze') : null;

// ----- team valideren -----
if (mb_strlen($teamnaam) < 2 || mb_strlen($teamnaam) > 60) { $fouten[] = 'Vul een teamnaam in (2 t/m 60 tekens).'; }
if (mb_strlen($captain) < 2 || mb_strlen($captain) > 80) { $fouten[] = 'Vul de naam van de teamcaptain in.'; }
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 120) { $fouten[] = 'Vul een geldig e-mailadres in.'; }
if (!preg_match('/^[0-9 +()\-]{8,20}$/', $telefoon)) { $fouten[] = 'Vul een geldig telefoonnummer in.'; }
if ($heeftKeuze && !in_array($keuze, keuze_namen($slug), true)) { $fouten[] = 'Kies een geldige ' . ($slug === 'wk' ? 'land' : 'club') . '.'; }
if (($_POST['akkoord'] ?? '') !== '1') { $fouten[] = 'Je moet bevestigen dat de gegevens kloppen en de privacyverklaring is gelezen.'; }

// ----- spelers valideren -----
$namen = $_POST['speler_naam'] ?? [];
$leeftijden = $_POST['speler_leeftijd'] ?? [];
$spelers = [];
if (!is_array($namen) || !is_array($leeftijden) || count($namen) !== count($leeftijden)) {
    $fouten[] = 'De spelersgegevens zijn onvolledig.';
} else {
    $aantal = count($namen);
    if ($aantal < MIN_SPELERS || $aantal > MAX_SPELERS) {
        $fouten[] = 'Een team heeft ' . MIN_SPELERS . ' t/m ' . MAX_SPELERS . ' spelers.';
    }
    $namen = array_values($namen);
    $leeftijden = array_values($leeftijden);
    foreach ($namen as $i => $n) {
        $n = is_scalar($n) ? trim((string)$n) : '';
        $l = is_scalar($leeftijden[$i]) ? trim((string)$leeftijden[$i]) : '';
        $spelers[] = [$n, $l];
        $nr = $i + 1;
        if (mb_strlen($n) < 2 || mb_strlen($n) > 60) { $fouten[] = "Speler $nr: vul een naam in."; }
        if (!ctype_digit($l) || (int)$l < MIN_LEEFTIJD || (int)$l > MAX_LEEFTIJD) {
            $fouten[] = "Speler $nr: de leeftijd moet tussen " . MIN_LEEFTIJD . ' en ' . MAX_LEEFTIJD . ' jaar zijn.';
        }
    }
}

// Bij fouten: terug naar het formulier met de ingevulde gegevens
function terug_met_fouten($fouten, $terug, $slug, $teamnaam, $captain, $email, $telefoon, $keuze, $spelers) {
    $_SESSION['fouten'] = array_slice($fouten, 0, 8);
    $_SESSION['oud'] = ['toernooi' => $slug, 'teamnaam' => $teamnaam, 'captain' => $captain, 'email' => $email,
                        'telefoon' => $telefoon, 'keuze' => $keuze, 'spelers' => array_slice($spelers, 0, MAX_SPELERS)];
    redirect($terug);
}
if ($fouten) { terug_met_fouten($fouten, $terug, $slug, $teamnaam, $captain, $email, $telefoon, $keuze, $spelers); }

// ----- opslaan (in een transactie zodat de 25 plekken kloppen) -----
$token = maak_token();
try {
    $db->beginTransaction();
    // Toernooi vergrendelen: twee teams kunnen niet tegelijk de laatste plek pakken
    $db->prepare('SELECT id FROM toernooien WHERE id = ? FOR UPDATE')->execute([$t['id']]);

    $bezet = plekken_bezet($db, $t['id']);
    $status = $bezet >= (int)$t['max_teams'] ? 'wachtlijst' : 'in behandeling';

    if ($heeftKeuze && $status !== 'wachtlijst' && in_array($keuze, bezette_keuzes($db, $t['id']), true)) {
        throw new RuntimeException('keuze_bezet');
    }

    $ins = $db->prepare('INSERT INTO teams (token, toernooi_id, teamnaam, captain, email, telefoon, keuze, status) VALUES (?,?,?,?,?,?,?,?)');
    $ins->execute([$token, $t['id'], $teamnaam, $captain, $email, $telefoon, $keuze, $status]);
    $teamId = (int)$db->lastInsertId();
    $db->prepare('UPDATE teams SET registratienummer = ? WHERE id = ?')->execute([sprintf('VDH-2027-%04d', $teamId), $teamId]);

    $sp = $db->prepare('INSERT INTO spelers (team_id, nummer, naam, leeftijd) VALUES (?,?,?,?)');
    foreach ($spelers as $i => $s) { $sp->execute([$teamId, $i + 1, $s[0], (int)$s[1]]); }

    $db->commit();
} catch (RuntimeException $ex) {
    if ($db->inTransaction()) { $db->rollBack(); }
    terug_met_fouten(['Deze ' . ($slug === 'wk' ? 'land' : 'club') . ' is net door een ander team gekozen. Kies een andere.'], $terug, $slug, $teamnaam, $captain, $email, $telefoon, null, $spelers);
} catch (PDOException $ex) {
    if ($db->inTransaction()) { $db->rollBack(); }
    $dubbel = ($ex->errorInfo[1] ?? 0) === 1062;
    terug_met_fouten([$dubbel ? 'Er bestaat al een team met deze naam in dit toernooi. Kies een andere teamnaam.' : 'Opslaan is niet gelukt. Probeer het zo opnieuw.'],
        $terug, $slug, $teamnaam, $captain, $email, $telefoon, $keuze, $spelers);
}

unset($_SESSION['oud'], $_SESSION['fouten']);
redirect('bevestiging.php?t=' . $token);