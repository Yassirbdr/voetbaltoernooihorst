<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/data.php';
vereis_admin();
$root = '../';
$adminPagina = true;
$paginaTitel = 'Dashboard';

$aantalTeams = (int)$db->query('SELECT COUNT(*) FROM teams')->fetchColumn();
$aantalSpelers = (int)$db->query('SELECT COUNT(*) FROM spelers')->fetchColumn();
$aantalVrijw = (int)$db->query('SELECT COUNT(*) FROM vrijwilligers')->fetchColumn();
$aantalBerichten = (int)$db->query('SELECT COUNT(*) FROM berichten')->fetchColumn();

$perToernooi = $db->query("SELECT tn.naam, tn.max_teams,
        COALESCE(SUM(t.status IN ('in behandeling','ingeschreven')), 0) AS bezet,
        COALESCE(SUM(t.status = 'wachtlijst'), 0) AS wacht
    FROM toernooien tn LEFT JOIN teams t ON t.toernooi_id = tn.id GROUP BY tn.id ORDER BY tn.id")->fetchAll();

$laatste = $db->query('SELECT t.registratienummer, t.teamnaam, t.status, t.aangemaakt, tn.naam AS toernooi
    FROM teams t JOIN toernooien tn ON tn.id = t.toernooi_id ORDER BY t.id DESC LIMIT 8')->fetchAll();
$berichten = $db->query('SELECT * FROM berichten ORDER BY id DESC LIMIT 5')->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="container">
    <h1 style="font-size:3rem;margin-bottom:24px">Dashboard</h1>
    <div class="grid4">
        <div class="stat"><b><?= $aantalTeams ?></b>Teams</div>
        <div class="stat"><b><?= $aantalSpelers ?></b>Spelers</div>
        <div class="stat"><b><?= $aantalVrijw ?></b>Vrijwilligers</div>
        <div class="stat"><b><?= $aantalBerichten ?></b>Berichten</div>
    </div>

    <h2 style="font-size:2rem;margin:36px 0 14px">Plekken per toernooi</h2>
    <div class="grid3">
        <?php foreach ($perToernooi as $p):
            $bezet = (int)$p['bezet']; $max = (int)$p['max_teams']; ?>
            <div class="paneel">
                <h3><?= e($p['naam']) ?></h3>
                <p><strong><?= $bezet ?></strong> van <?= $max ?> plekken bezet</p>
                <div class="balk" style="background:#e5e1d6"><i style="width:<?= $max ? min(100, round($bezet / $max * 100)) : 0 ?>%;background:var(--groen)"></i></div>
                <p style="margin-top:10px">Wachtlijst: <strong><?= (int)$p['wacht'] ?></strong></p>
                <?php if ($bezet >= $max): ?><div class="melding info" style="margin:10px 0 0">Toernooi is vol.</div>
                <?php elseif ($bezet >= $max - 3): ?><div class="melding info" style="margin:10px 0 0">Bijna vol!</div><?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <h2 style="font-size:2rem;margin:36px 0 14px">Laatste inschrijvingen</h2>
    <?php if (!$laatste): ?>
        <div class="melding info">Er zijn nog geen inschrijvingen.</div>
    <?php else: ?>
    <div class="tabel-wrap"><table class="tabel">
        <tr><th>Nummer</th><th>Team</th><th>Toernooi</th><th>Datum</th><th>Status</th></tr>
        <?php foreach ($laatste as $l): ?>
            <tr><td><?= e($l['registratienummer']) ?></td><td><?= e($l['teamnaam']) ?></td><td><?= e($l['toernooi']) ?></td><td><?= e(datum_nl($l['aangemaakt'])) ?></td>
                <td><span class="badge b-<?= e(str_replace(' ', '-', $l['status'])) ?>"><?= e($l['status']) ?></span></td></tr>
        <?php endforeach; ?>
    </table></div>
    <?php endif; ?>

    <h2 style="font-size:2rem;margin:36px 0 14px">Laatste berichten</h2>
    <?php if (!$berichten): ?>
        <div class="melding info">Nog geen berichten via het contactformulier.</div>
    <?php else: foreach ($berichten as $b): ?>
        <div class="paneel" style="margin-bottom:12px"><strong><?= e($b['naam']) ?></strong> (<?= e($b['email']) ?>) – <small><?= e(datum_nl($b['aangemaakt'])) ?></small><p style="margin-top:6px"><?= nl2br(e($b['bericht'])) ?></p></div>
    <?php endforeach; endif; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>