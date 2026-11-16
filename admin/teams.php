<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/data.php';
vereis_admin();

// ----- acties: status wijzigen of verwijderen -----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_ok()) {
    $id = (int)($_POST['id'] ?? 0);
    $actie = $_POST['actie'] ?? '';
    if ($actie === 'status' && in_array($_POST['status'] ?? '', STATUSSEN, true)) {
        $db->prepare('UPDATE teams SET status = ? WHERE id = ?')->execute([$_POST['status'], $id]);
    } elseif ($actie === 'verwijder') {
        $db->prepare('DELETE FROM teams WHERE id = ?')->execute([$id]); // spelers verdwijnen automatisch mee
    }
    redirect(strtok($_SERVER['REQUEST_URI'], '#'));
}

// ----- zoeken en filteren -----
$q = trim((string)($_GET['q'] ?? ''));
$fT = (string)($_GET['toernooi'] ?? '');
$fS = (string)($_GET['status'] ?? '');
$where = []; $par = [];
if ($q !== '') {
    $where[] = '(t.teamnaam LIKE ? OR t.registratienummer LIKE ? OR t.captain LIKE ? OR t.email LIKE ?)';
    array_push($par, "%$q%", "%$q%", "%$q%", "%$q%");
}
if ($fT !== '') { $where[] = 'tn.slug = ?'; $par[] = $fT; }
if (in_array($fS, STATUSSEN, true)) { $where[] = 't.status = ?'; $par[] = $fS; }
$sql = 'SELECT t.*, tn.naam AS toernooi, (SELECT COUNT(*) FROM spelers s WHERE s.team_id = t.id) AS aantal
        FROM teams t JOIN toernooien tn ON tn.id = t.toernooi_id'
     . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY t.id DESC';
$s = $db->prepare($sql);
$s->execute($par);
$teams = $s->fetchAll();

// ----- export naar CSV (Excel) -----
if (isset($_GET['export'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="teams-voetbaldagen-horst.csv"');
    echo "\xEF\xBB\xBF";
    $u = fopen('php://output', 'w');
    fputcsv($u, ['Nummer', 'Team', 'Toernooi', 'Club/land', 'Spelers', 'Captain', 'E-mail', 'Telefoon', 'Datum', 'Status'], ';');
    foreach ($teams as $r) {
        fputcsv($u, [$r['registratienummer'], $r['teamnaam'], $r['toernooi'], $r['keuze'], $r['aantal'], $r['captain'], $r['email'], $r['telefoon'], $r['aangemaakt'], $r['status']], ';');
    }
    exit;
}

$root = '../';
$adminPagina = true;
$paginaTitel = 'Teams';
$toernooien = $db->query('SELECT slug, naam FROM toernooien ORDER BY id')->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="container">
    <h1 style="font-size:3rem">Teams</h1>
    <form class="filters" method="get">
        <input type="search" name="q" placeholder="Zoek team, nummer, captain..." value="<?= e($q) ?>">
        <select name="toernooi"><option value="">Alle toernooien</option>
            <?php foreach ($toernooien as $t): ?><option value="<?= e($t['slug']) ?>" <?= $fT === $t['slug'] ? 'selected' : '' ?>><?= e($t['naam']) ?></option><?php endforeach; ?></select>
        <select name="status"><option value="">Alle statussen</option>
            <?php foreach (STATUSSEN as $st): ?><option value="<?= e($st) ?>" <?= $fS === $st ? 'selected' : '' ?>><?= e($st) ?></option><?php endforeach; ?></select>
        <button class="btn btn-groen btn-klein" type="submit">Filter</button>
        <a class="btn btn-grijs btn-klein" href="teams.php">Wis</a>
        <a class="btn btn-oranje btn-klein" href="?<?= e(http_build_query(array_merge($_GET, ['export' => 1]))) ?>">Export CSV</a>
    </form>

    <?php if (!$teams): ?>
        <div class="melding info">Geen teams gevonden.</div>
    <?php else: ?>
    <div class="tabel-wrap"><table class="tabel">
        <tr><th>Nummer</th><th>Team</th><th>Toernooi</th><th>Club/land</th><th>Spelers</th><th>Captain</th><th>Datum</th><th>Status</th><th></th></tr>
        <?php foreach ($teams as $r): ?>
        <tr>
            <td><?= e($r['registratienummer']) ?></td>
            <td><strong><?= e($r['teamnaam']) ?></strong></td>
            <td><?= e($r['toernooi']) ?></td>
            <td><?= e($r['keuze'] ?? '–') ?></td>
            <td><?= (int)$r['aantal'] ?></td>
            <td><?= e($r['captain']) ?></td>
            <td><?= e(datum_nl($r['aangemaakt'])) ?></td>
            <td>
                <form method="post"><?= csrf_veld() ?><input type="hidden" name="actie" value="status"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                    <select name="status" onchange="this.form.submit()">
                        <?php foreach (STATUSSEN as $st): ?><option value="<?= e($st) ?>" <?= $r['status'] === $st ? 'selected' : '' ?>><?= e($st) ?></option><?php endforeach; ?>
                    </select></form>
            </td>
            <td style="white-space:nowrap">
                <a class="btn btn-groen btn-klein" href="teams-bewerken.php?id=<?= (int)$r['id'] ?>">Bekijk</a>
                <form method="post" style="display:inline" data-bevestig="Team '<?= e($r['teamnaam']) ?>' en alle spelers definitief verwijderen?">
                    <?= csrf_veld() ?><input type="hidden" name="actie" value="verwijder"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                    <button class="btn btn-rood btn-klein" type="submit">Verwijder</button></form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table></div>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>