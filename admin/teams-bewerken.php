<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/data.php';
vereis_admin();

$id = (int)($_GET['id'] ?? 0);
$s = $db->prepare('SELECT t.*, tn.naam AS toernooi, tn.slug FROM teams t JOIN toernooien tn ON tn.id = t.toernooi_id WHERE t.id = ?');
$s->execute([$id]);
$team = $s->fetch();
if (!$team) { redirect('teams.php'); }

$melding = ''; $fouten = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_ok()) {
    $str = fn($k) => isset($_POST[$k]) && is_string($_POST[$k]) ? trim($_POST[$k]) : '';
    $teamnaam = $str('teamnaam'); $captain = $str('captain'); $email = $str('email'); $telefoon = $str('telefoon');
    $status = $str('status');
    $keuze = $team['slug'] !== 'voetvolley' ? $str('keuze') : null;

    if (mb_strlen($teamnaam) < 2 || mb_strlen($teamnaam) > 60) { $fouten[] = 'Ongeldige teamnaam.'; }
    if (mb_strlen($captain) < 2) { $fouten[] = 'Ongeldige captain.'; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $fouten[] = 'Ongeldig e-mailadres.'; }
    if (!preg_match('/^[0-9 +()\-]{8,20}$/', $telefoon)) { $fouten[] = 'Ongeldig telefoonnummer.'; }
    if (!in_array($status, STATUSSEN, true)) { $fouten[] = 'Ongeldige status.'; }
    if ($keuze !== null && !in_array($keuze, keuze_namen($team['slug']), true)) { $fouten[] = 'Ongeldige club/land.'; }

    $ids = $_POST['speler_id'] ?? []; $nm = $_POST['speler_naam'] ?? []; $lf = $_POST['speler_leeftijd'] ?? [];
    $spelerUpdates = [];
    if (is_array($ids) && is_array($nm) && is_array($lf)) {
        foreach ($ids as $i => $sid) {
            $n = is_string($nm[$i] ?? null) ? trim($nm[$i]) : '';
            $l = is_string($lf[$i] ?? null) ? trim($lf[$i]) : '';
            if (mb_strlen($n) < 2 || !ctype_digit($l) || (int)$l < MIN_LEEFTIJD || (int)$l > MAX_LEEFTIJD) {
                $fouten[] = 'Speler ' . ($i + 1) . ': naam of leeftijd (' . MIN_LEEFTIJD . '-' . MAX_LEEFTIJD . ') klopt niet.';
            }
            $spelerUpdates[] = [(int)$sid, $n, (int)$l];
        }
    }

    if (!$fouten) {
        try {
            $db->beginTransaction();
            $db->prepare('UPDATE teams SET teamnaam=?, captain=?, email=?, telefoon=?, keuze=?, status=? WHERE id=?')
               ->execute([$teamnaam, $captain, $email, $telefoon, $keuze, $status, $id]);
            $up = $db->prepare('UPDATE spelers SET naam=?, leeftijd=? WHERE id=? AND team_id=?');
            foreach ($spelerUpdates as $u) { $up->execute([$u[1], $u[2], $u[0], $id]); }
            $db->commit();
            $melding = 'Wijzigingen opgeslagen.';
        } catch (PDOException $ex) {
            if ($db->inTransaction()) { $db->rollBack(); }
            $fouten[] = ($ex->errorInfo[1] ?? 0) === 1062 ? 'Deze teamnaam bestaat al in dit toernooi.' : 'Opslaan is niet gelukt.';
        }
    }
    $s->execute([$id]);
    $team = $s->fetch();
}

$sp = $db->prepare('SELECT * FROM spelers WHERE team_id = ? ORDER BY nummer');
$sp->execute([$id]);
$spelers = $sp->fetchAll();

$root = '../';
$adminPagina = true;
$paginaTitel = 'Team ' . $team['teamnaam'];
include __DIR__ . '/../includes/header.php';
?>
<div class="container" style="max-width:900px">
    <a class="terug" style="color:var(--groen)" href="teams.php">← Terug naar teams</a>
    <h1 style="font-size:2.6rem;margin-bottom:6px"><?= e($team['teamnaam']) ?></h1>
    <p style="margin-bottom:18px"><?= e($team['registratienummer']) ?> • <?= e($team['toernooi']) ?> • aangemeld op <?= e(datum_nl($team['aangemaakt'])) ?></p>
    <?php if ($melding): ?><div class="melding ok"><?= e($melding) ?></div><?php endif; ?>
    <?php if ($fouten): ?><div class="melding fout"><ul><?php foreach ($fouten as $f): ?><li><?= e($f) ?></li><?php endforeach; ?></ul></div><?php endif; ?>

    <form method="post" class="paneel">
        <?= csrf_veld() ?>
        <div class="rij2">
            <div class="veld"><label>Teamnaam</label><input name="teamnaam" maxlength="60" value="<?= e($team['teamnaam']) ?>" required></div>
            <div class="veld"><label>Captain</label><input name="captain" maxlength="80" value="<?= e($team['captain']) ?>" required></div>
            <div class="veld"><label>E-mail</label><input name="email" type="email" value="<?= e($team['email']) ?>" required></div>
            <div class="veld"><label>Telefoon</label><input name="telefoon" value="<?= e($team['telefoon']) ?>" required></div>
        </div>
        <div class="rij2">
            <?php if ($team['slug'] !== 'voetvolley'): ?>
            <div class="veld"><label><?= $team['slug'] === 'wk' ? 'Land' : 'Club' ?></label>
                <select name="keuze"><?php foreach (keuze_namen($team['slug']) as $k): ?><option <?= $team['keuze'] === $k ? 'selected' : '' ?>><?= e($k) ?></option><?php endforeach; ?></select></div>
            <?php endif; ?>
            <div class="veld"><label>Status</label>
                <select name="status"><?php foreach (STATUSSEN as $st): ?><option <?= $team['status'] === $st ? 'selected' : '' ?>><?= e($st) ?></option><?php endforeach; ?></select></div>
        </div>

        <h3 style="margin:10px 0 12px">Spelers (<?= count($spelers) ?>)</h3>
        <?php foreach ($spelers as $i => $p): ?>
            <div class="speler-kaart">
                <div class="shirt" style="margin-top:0"><?= sprintf('%02d', $i + 1) ?></div>
                <div class="speler-velden">
                    <input type="hidden" name="speler_id[]" value="<?= (int)$p['id'] ?>">
                    <div class="veld"><input name="speler_naam[]" maxlength="60" value="<?= e($p['naam']) ?>" required></div>
                    <div class="veld"><input name="speler_leeftijd[]" type="number" min="<?= MIN_LEEFTIJD ?>" max="<?= MAX_LEEFTIJD ?>" value="<?= (int)$p['leeftijd'] ?>" required></div>
                </div>
            </div>
        <?php endforeach; ?>
        <button class="btn btn-oranje" type="submit">Opslaan</button>
    </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>