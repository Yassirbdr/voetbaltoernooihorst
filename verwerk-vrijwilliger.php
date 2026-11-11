<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/data.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_ok()) { redirect('vrijwilligers.php'); }

$str = fn($k) => isset($_POST[$k]) && is_string($_POST[$k]) ? trim($_POST[$k]) : '';
$naam = $str('naam');
$email = $str('email');
$telefoon = $str('telefoon');
$opm = $str('opmerkingen');
// Alleen toegestane dagen en taken
$dagen = array_values(array_intersect(DAGEN, lijst_post('dagen')));
$taken = array_values(array_intersect(array_keys(TAKEN), lijst_post('taken')));

$fouten = [];
if (mb_strlen($naam) < 2 || mb_strlen($naam) > 80) { $fouten[] = 'Vul je voor- en achternaam in.'; }
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 120) { $fouten[] = 'Vul een geldig e-mailadres in.'; }
if (!preg_match('/^[0-9 +()\-]{8,20}$/', $telefoon)) { $fouten[] = 'Vul een geldig telefoonnummer in.'; }
if (!$dagen) { $fouten[] = 'Kies minimaal één dag waarop je kunt helpen.'; }
if (mb_strlen($opm) > 1000) { $fouten[] = 'Je opmerking is te lang (maximaal 1000 tekens).'; }
if (($_POST['akkoord'] ?? '') !== '1') { $fouten[] = 'Je moet akkoord gaan met de privacyverklaring.'; }

if ($fouten) {
    $_SESSION['fouten_v'] = $fouten;
    $_SESSION['oud_v'] = ['naam' => $naam, 'email' => $email, 'telefoon' => $telefoon, 'dagen' => $dagen, 'taken' => $taken, 'opmerkingen' => $opm];
    redirect('vrijwilligers.php#aanmelden');
}

$token = maak_token();
try {
    $db->beginTransaction();
    $db->prepare('INSERT INTO vrijwilligers (token, naam, email, telefoon, dagen, taken, opmerkingen) VALUES (?,?,?,?,?,?,?)')
       ->execute([$token, $naam, $email, $telefoon, implode(', ', $dagen), $taken ? implode(', ', $taken) : null, $opm !== '' ? $opm : null]);
    $id = (int)$db->lastInsertId();
    $db->prepare('UPDATE vrijwilligers SET registratienummer = ? WHERE id = ?')->execute([sprintf('VRW-2027-%04d', $id), $id]);
    $db->commit();
} catch (PDOException $ex) {
    if ($db->inTransaction()) { $db->rollBack(); }
    $_SESSION['fouten_v'] = ['Opslaan is niet gelukt. Probeer het zo opnieuw.'];
    $_SESSION['oud_v'] = ['naam' => $naam, 'email' => $email, 'telefoon' => $telefoon, 'dagen' => $dagen, 'taken' => $taken, 'opmerkingen' => $opm];
    redirect('vrijwilligers.php#aanmelden');
}

redirect('bevestiging.php?type=vrijwilliger&t=' . $token);