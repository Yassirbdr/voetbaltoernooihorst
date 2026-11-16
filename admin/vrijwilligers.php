<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/data.php';
vereis_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_ok()) {
    $id = (int)($_POST['id'] ?? 0);
    $actie = $_POST['actie'] ?? '';
    if ($actie === 'status' && in_array($_POST['status'] ?? '', VRIJW_STATUSSEN, true)) {
        $db->prepare('UPDATE vrijwilligers SET status = ? WHERE id = ?')->execute([$_POST['status'], $id]);
    } elseif ($actie === 'verwijder') {
        $db->prepare('DELETE FROM vrijwilligers WHERE id = ?')->execute([$id]);
    }
    redirect('vrijwilligers.php');
}

$lijst = $db->query('SELECT * FROM vrijwilligers ORDER BY id DESC')->fetchAll();
$root = '../';
$adminPagina = true;
$paginaTitel = 'Vrijwilligers';
include __DIR__ . '/../includes/header.php';
?>
<div class="container">
    <h1 style="font-size:3rem;margin-bottom:18px">Vrijwilligers (<?= count($lijst) ?>)</h1>
    <?php if (!$lijst): ?>
        <div class="melding info">Er zijn nog geen aanmeldingen.</div>
    <?php else: ?>
    <div class="tabel-wrap"><table class="tabel" style="min-width:980px">
        <tr><th>Nummer</th><th>Naam</th><th>Contact</th><th>Beschikbaar</th><th>Taken</th><th>Opmerking</th><th>Status</th><th></th></tr>
        <?php foreach ($lijst as $v): ?>
        <tr>
            <td><?= e($v['registratienummer']) ?><br><small><?= e(datum_nl($v['aangemaakt'])) ?></small></td>
            <td><strong><?= e($v['naam']) ?></strong></td>
            <td><?= e($v['email']) ?><br><?= e($v['telefoon']) ?></td>
            <td><?= e($v['dagen']) ?></td>
            <td><?= e($v['taken'] ?? '–') ?></td>
            <td><?= e($v['opmerkingen'] ?? '–') ?></td>
            <td><form method="post"><?= csrf_veld() ?><input type="hidden" name="actie" value="status"><input type="hidden" name="id" value="<?= (int)$v['id'] ?>">
                <select name="status" onchange="this.form.submit()"><?php foreach (VRIJW_STATUSSEN as $st): ?><option <?= $v['status'] === $st ? 'selected' : '' ?>><?= e($st) ?></option><?php endforeach; ?></select></form></td>
            <td><form method="post" data-bevestig="Aanmelding van <?= e($v['naam']) ?> verwijderen?"><?= csrf_veld() ?><input type="hidden" name="actie" value="verwijder"><input type="hidden" name="id" value="<?= (int)$v['id'] ?>">
                <button class="btn btn-rood btn-klein" type="submit">Verwijder</button></form></td>
        </tr>
        <?php endforeach; ?>
    </table></div>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>