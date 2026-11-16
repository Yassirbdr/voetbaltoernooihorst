<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/data.php';
$root = '';
$slug = $_GET['t'] ?? '';
if ($slug !== '' && !isset(TOERNOOIEN[$slug])) { redirect('404.php'); }

if ($slug === '') {
    // ===== Overzicht =====
    $paginaTitel = 'Toernooien';
    $toernooien = $db->query('SELECT * FROM toernooien ORDER BY id')->fetchAll();
    include __DIR__ . '/includes/header.php';
?>
<section class="kop">
    <div class="container">
        <span class="eyebrow">30 AUGUSTUS – 3 SEPTEMBER 2027</span>
        <h1>De toernooien</h1>
        <p>Kies een toernooi, bekijk de regels en schrijf jouw team in.</p>
    </div>
</section>
<section class="sectie">
    <div class="container grid3">
        <?php foreach ($toernooien as $t) { echo toernooi_kaart($t, plekken_bezet($db, $t['id']), true); } ?>
    </div>
</section>
<?php
} else {
    // ===== Detailpagina =====
    $m = TOERNOOIEN[$slug];
    $t = toernooi_ophalen($db, $slug);
    $bezet = plekken_bezet($db, $t['id']);
    $max = (int)$t['max_teams'];
    $vol = $bezet >= $max;
    $paginaTitel = $m['naam'];
    include __DIR__ . '/includes/header.php';
?>
<section class="kop" style="--acc:<?= e($m['accent']) ?>">
    <div class="container">
        <span class="eyebrow">30 AUGUSTUS – 3 SEPTEMBER 2027</span>
        <h1><?= e($m['naam']) ?></h1>
        <p><?= e($m['beschrijving']) ?></p>
        <div class="plekken" style="max-width:360px"><?= $vol ? 'VOL – je kunt je aanmelden voor de wachtlijst' : $bezet . ' van ' . $max . ' plekken bezet' ?>
            <div class="balk"><i style="width:<?= $max ? min(100, round($bezet / $max * 100)) : 0 ?>%"></i></div></div>
        <div class="knoppen"><a class="btn btn-oranje" href="inschrijven.php?toernooi=<?= e($slug) ?>"><?= $vol ? 'Aanmelden voor wachtlijst' : 'Schrijf jouw team in' ?></a></div>
    </div>
</section>

<section class="sectie">
    <div class="container">
        <div class="grid2">
            <div class="paneel reveal"><h3>Omschrijving</h3><p><?= e($m['beschrijving']) ?></p></div>
            <div class="paneel reveal"><h3>Belangrijke informatie</h3>
                <div class="rij"><span>Datum</span><strong>30 aug – 3 sep 2027</strong></div>
                <div class="rij"><span>Locatie</span><strong>Horst</strong></div>
                <div class="rij"><span>Teams</span><strong>maximaal <?= $max ?></strong></div>
                <div class="rij"><span>Spelers per team</span><strong><?= MIN_SPELERS ?> t/m <?= MAX_SPELERS ?></strong></div>
                <div class="rij"><span>Leeftijd spelers</span><strong><?= MIN_LEEFTIJD ?> t/m <?= MAX_LEEFTIJD ?> jaar</strong></div>
            </div>
            <div class="paneel reveal"><h3>Speelregels</h3><p><?= PLACEHOLDER ?></p></div>
            <div class="paneel reveal"><h3>Wedstrijdduur</h3><p><?= PLACEHOLDER ?></p></div>
            <div class="paneel reveal"><h3>Rode kaart</h3><p><?= PLACEHOLDER ?></p></div>
            <div class="paneel reveal"><h3>Fair play</h3><p>Sportiviteit staat voorop. Groet elkaar, help elkaar overeind en accepteer de beslissingen van de scheidsrechter.</p></div>
        </div>

        <?php $keuzes = keuze_lijst($slug); if ($keuzes): ?>
        <div class="paneel reveal" style="margin-top:24px">
            <h3><?= $slug === 'wk' ? 'De 25 landen' : 'De 25 clubs' ?></h3>
            <div class="chips"><?php foreach ($keuzes as $k): ?><span><?= e($k['naam']) ?></span><?php endforeach; ?></div>
        </div>
        <?php endif; ?>

        <div class="weetje reveal" style="margin-top:24px"><small>Wist je dat?</small><?= e($m['weetje']) ?></div>
        <div class="respect reveal" style="margin-top:24px">Respecteer elkaar op en naast het veld.</div>
        <div class="knoppen"><a class="btn btn-oranje" href="inschrijven.php?toernooi=<?= e($slug) ?>"><?= $vol ? 'Aanmelden voor wachtlijst' : 'Schrijf jouw team in' ?></a>
            <a class="btn btn-grijs" href="toernooien.php">Alle toernooien</a></div>
    </div>
</section>
<?php }
include __DIR__ . '/includes/footer.php'; ?>