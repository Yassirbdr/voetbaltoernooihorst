<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/data.php';
vereis_admin();

$fouten = [];
$melding = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_ok()) {
    $actie = $_POST['actie'] ?? '';

    if ($actie === 'toevoegen') {
        $titel = trim((string)($_POST['titel'] ?? ''));
        $inhoud = trim((string)($_POST['inhoud'] ?? ''));
        $afbeeldingPad = null;

        if (mb_strlen($titel) < 3) { $fouten[] = 'Vul een titel in (minimaal 3 tekens).'; }
        if (mb_strlen($inhoud) < 5) { $fouten[] = 'Vul de tekst van het nieuwsbericht in.'; }

        if (isset($_FILES['afbeelding']) && $_FILES['afbeelding']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['afbeelding']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                $uploadDir = __DIR__ . '/../assets/uploads/';
                if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
                $bestandsnaam = uniqid('nieuws_', true) . '.' . $ext;
                if (move_uploaded_file($_FILES['afbeelding']['tmp_name'], $uploadDir . $bestandsnaam)) {
                    $afbeeldingPad = 'assets/uploads/' . $bestandsnaam;
                }
            } else {
                $fouten[] = 'Alleen JPG, PNG of WEBP afbeeldingen zijn toegestaan.';
            }
        }

        if (!$fouten) {
            $db->prepare('INSERT INTO nieuws (titel, inhoud, afbeelding) VALUES (?,?,?)')->execute([$titel, $inhoud, $afbeeldingPad]);
            $melding = 'Nieuwsbericht is geplaatst!';
        }
    } elseif ($actie === 'verwijder') {
        $id = (int)($_POST['id'] ?? 0);
        $db->prepare('DELETE FROM nieuws WHERE id = ?')->execute([$id]);
        $melding = 'Nieuwsbericht verwijderd.';
    }
}

$lijst = $db->query('SELECT * FROM nieuws ORDER BY id DESC')->fetchAll();
$root = '../';
$adminPagina = true;
$paginaTitel = 'Nieuws beheren';
include __DIR__ . '/../includes/header.php';
?>
<div class="container" style="max-width:900px;">
    <h1 style="font-size:3rem; margin-bottom:20px;">Nieuws beheren</h1>
    <?php if ($melding): ?><div class="melding ok"><?= e($melding) ?></div><?php endif; ?>
    <?php if ($fouten): ?><div class="melding fout"><ul><?php foreach ($fouten as $f): ?><li><?= e($f) ?></li><?php endforeach; ?></ul></div><?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="paneel" style="margin-bottom:36px;">
        <h3>Nieuw bericht plaatsen</h3>
        <?= csrf_veld() ?>
        <input type="hidden" name="actie" value="toevoegen">
        <div class="veld" style="margin-top:14px;"><label>Titel</label><input name="titel" maxlength="150" required></div>
        <div class="veld"><label>Afbeelding (optioneel)</label><input name="afbeelding" type="file" accept="image/*"></div>
        <div class="veld"><label>Bericht</label><textarea name="inhoud" required></textarea></div>
        <button class="btn btn-oranje" type="submit">Plaats nieuwsbericht</button>
    </form>

    <h2 style="font-size:2rem; margin-bottom:14px;">Geplaatste berichten (<?= count($lijst) ?>)</h2>
    <?php if (!$lijst): ?>
        <div class="melding info">Nog geen nieuwsberichten geplaatst.</div>
    <?php else: ?>
        <?php foreach ($lijst as $n): ?>
            <div class="paneel" style="margin-bottom:14px; display:flex; justify-content:space-between; align-items:flex-start; gap:16px;">
                <div>
                    <strong><?= e($n['titel']) ?></strong> <small>• <?= e(datum_nl($n['aangemaakt'])) ?></small>
                    <p style="margin-top:6px;"><?= nl2br(e($n['inhoud'])) ?></p>
                </div>
                <form method="post" onsubmit="return confirm('Dit nieuwsbericht verwijderen?');">
                    <?= csrf_veld() ?>
                    <input type="hidden" name="actie" value="verwijder">
                    <input type="hidden" name="id" value="<?= (int)$n['id'] ?>">
                    <button class="btn btn-rood btn-klein" type="submit">Verwijder</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>