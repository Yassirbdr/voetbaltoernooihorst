<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/data.php';
$root = '';
$token = $_GET['t'] ?? '';
$type = ($_GET['type'] ?? 'team') === 'vrijwilliger' ? 'vrijwilliger' : 'team';
if (!preg_match('/^[a-f0-9]{32}$/', $token)) { redirect('index.php'); }

if ($type === 'team') {
    $s = $db->prepare('SELECT t.*, tn.naam AS toernooi, (SELECT COUNT(*) FROM spelers WHERE team_id = t.id) AS aantal
                       FROM teams t JOIN toernooien tn ON tn.id = t.toernooi_id WHERE t.token = ?');
} else {
    $s = $db->prepare('SELECT * FROM vrijwilligers WHERE token = ?');
}
$s->execute([$token]);
$r = $s->fetch();
if (!$r) { redirect('index.php'); }

$paginaTitel = 'Bevestiging';
include __DIR__ . '/includes/header.php';
$wacht = $type === 'team' && $r['status'] === 'wachtlijst';
?>
<section class="sectie" data-confetti>
    <div class="container bevestiging">
        <div class="groot-vink"><?= $wacht ? '⏳' : '✓' ?></div>
        <h1 style="font-size:clamp(2.2rem,6vw,3.6rem)">
            <?php if ($type === 'vrijwilliger'): ?>Bedankt voor je aanmelding!
            <?php elseif ($wacht): ?>Jullie staan op de wachtlijst!
            <?php else: ?>Jullie inschrijving is ontvangen!<?php endif; ?>
        </h1>
        <p style="margin-top:12px">Bewaar je registratienummer goed.</p>

        <div class="ticket">
            <span class="eyebrow">Registratienummer</span>
            <div class="nr"><?= e($r['registratienummer']) ?></div>
            <div class="ticket-streep"></div>
            <?php if ($type === 'team'): ?>
                <div class="rij"><span>Toernooi</span><strong><?= e($r['toernooi']) ?></strong></div>
                <div class="rij"><span>Team</span><strong><?= e($r['teamnaam']) ?></strong></div>
                <?php if ($r['keuze']): ?><div class="rij"><span><?= $r['toernooi'] === 'Wereldkampioenschap' ? 'Land' : 'Club' ?></span><strong><?= e($r['keuze']) ?></strong></div><?php endif; ?>
                <div class="rij"><span>Spelers</span><strong><?= (int)$r['aantal'] ?></strong></div>
                <div class="rij"><span>Status</span><strong><?= e(ucfirst($r['status'])) ?></strong></div>
            <?php else: ?>
                <div class="rij"><span>Naam</span><strong><?= e($r['naam']) ?></strong></div>
                <div class="rij"><span>Dagen</span><strong><?= e($r['dagen']) ?></strong></div>
                <?php if ($r['taken']): ?><div class="rij"><span>Taken</span><strong><?= e($r['taken']) ?></strong></div><?php endif; ?>
            <?php endif; ?>
            <div class="rij"><span>Evenement</span><strong>30 aug – 3 sep 2027</strong></div>
        </div>
        <div class="knoppen" style="justify-content:center">
            <a class="btn btn-oranje" href="index.php">Terug naar home</a>
            <button class="btn btn-grijs" type="button" onclick="window.print()">Print bevestiging</button>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>