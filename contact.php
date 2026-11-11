<?php
require __DIR__ . '/includes/db.php';
$root = '';
$paginaTitel = 'Contact';
$fouten = [];
$naam = $email = $bericht = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_ok()) {
    $naam = trim((string)($_POST['naam'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $bericht = trim((string)($_POST['bericht'] ?? ''));
    if (mb_strlen($naam) < 2 || mb_strlen($naam) > 80) { $fouten[] = 'Vul je naam in.'; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $fouten[] = 'Vul een geldig e-mailadres in.'; }
    if (mb_strlen($bericht) < 5 || mb_strlen($bericht) > 2000) { $fouten[] = 'Je bericht moet tussen 5 en 2000 tekens zijn.'; }
    if (!$fouten) {
        try {
            $db->prepare('INSERT INTO berichten (naam, email, bericht) VALUES (?,?,?)')->execute([$naam, $email, $bericht]);
            redirect('contact.php?verzonden=1');
        } catch (PDOException $ex) { $fouten[] = 'Versturen is niet gelukt. Probeer het zo opnieuw.'; }
    }
}
include __DIR__ . '/includes/header.php';
?>
<section class="kop">
    <div class="container"><span class="eyebrow">CONTACT</span><h1>Neem contact op</h1><p>Heb je een vraag over de Voetbaldagen Horst 2027? Stuur ons een bericht.</p></div>
</section>
<section class="sectie">
    <div class="container grid2" style="align-items:start">
        <div class="paneel">
            <?php if (isset($_GET['verzonden'])): ?><div class="melding ok">Bedankt! Je bericht is verzonden.</div><?php endif; ?>
            <?php if ($fouten): ?><div class="melding fout"><ul><?php foreach ($fouten as $f): ?><li><?= e($f) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
            <form method="post" novalidate>
                <?= csrf_veld() ?>
                <div class="veld"><label for="naam">Naam</label><input id="naam" name="naam" type="text" maxlength="80" value="<?= e($naam) ?>" required></div>
                <div class="veld"><label for="email">E-mailadres</label><input id="email" name="email" type="email" maxlength="120" value="<?= e($email) ?>" required></div>
                <div class="veld"><label for="bericht">Bericht</label><textarea id="bericht" name="bericht" maxlength="2000" required><?= e($bericht) ?></textarea></div>
                <button class="btn btn-oranje" type="submit">Verstuur bericht</button>
            </form>
        </div>
        <div class="paneel">
            <h3>Gegevens</h3>
            <div class="rij"><span>E-mail</span><strong>info@voetbaldagenhorst.nl</strong></div>
            <div class="rij"><span>Telefoon</span><strong>000-0000000</strong></div>
            <div class="rij"><span>Locatie</span><strong>Horst</strong></div>
            <div class="rij"><span>Datum</span><strong>30 aug – 3 sep 2027</strong></div>
            <p style="margin-top:14px"><small>Dit zijn voorbeeldgegevens. Pas ze aan zodra de organisatie de echte gegevens heeft.</small></p>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>