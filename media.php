<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/data.php';
$root = '';
$paginaTitel = 'Media';
$fouten = [];
$succes = isset($_GET['ok']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_ok()) {
    $naam = trim((string)($_POST['naam'] ?? ''));
    $omschrijving = trim((string)($_POST['omschrijving'] ?? ''));

    if (mb_strlen($naam) < 2 || mb_strlen($naam) > 80) {
        $fouten[] = 'Vul je naam in.';
    }

    if (!isset($_FILES['bestand']) || $_FILES['bestand']['error'] !== UPLOAD_ERR_OK) {
        $fouten[] = 'Kies een foto of video om te uploaden.';
    } else {
        $ext = strtolower(pathinfo($_FILES['bestand']['name'], PATHINFO_EXTENSION));
        $fotoExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $videoExt = ['mp4', 'webm', 'mov'];

        if (in_array($ext, $fotoExt, true)) {
            $type = 'foto';
        } elseif (in_array($ext, $videoExt, true)) {
            $type = 'video';
        } else {
            $fouten[] = 'Alleen foto\'s (JPG, PNG, WEBP) of video\'s (MP4, MOV, WEBM) zijn toegestaan.';
        }

        if ($_FILES['bestand']['size'] > 50 * 1024 * 1024) {
            $fouten[] = 'Het bestand mag maximaal 50 MB zijn.';
        }
    }

    if (!$fouten) {
        $uploadDir = __DIR__ . '/assets/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $nieuweNaam = uniqid('media_', true) . '.' . $ext;
        if (move_uploaded_file($_FILES['bestand']['tmp_name'], $uploadDir . $nieuweNaam)) {
            $db->prepare('INSERT INTO media (naam, bestand, type, omschrijving) VALUES (?,?,?,?)')
               ->execute([$naam, 'assets/uploads/' . $nieuweNaam, $type, $omschrijving !== '' ? $omschrijving : null]);
            redirect('media.php?ok=1');
        } else {
            $fouten[] = 'Uploaden is mislukt. Controleer of de map assets/uploads bestaat.';
        }
    }
}

try {
    $items = $db->query('SELECT * FROM media ORDER BY id DESC')->fetchAll();
} catch (Exception $e) {
    $items = [];
}

include __DIR__ . '/includes/header.php';
?>
<section class="kop" style="--acc:var(--groen)">
    <div class="container">
        <span class="eyebrow">FOTO'S & VIDEO'S</span>
        <h1>Media Galerij</h1>
        <p>Deel jouw mooiste acties, doelpunten en teamfoto's van de Voetbaldagen Horst 2027!</p>
    </div>
</section>

<section class="sectie">
    <div class="container">
        <!-- Upload formulier -->
        <div class="paneel" style="max-width:700px; margin: 0 auto 50px auto;">
            <h3>Upload jouw foto of video</h3>
            <?php if ($succes): ?><div class="melding ok" style="margin-top:14px;">Top! Jouw bestand is toegevoegd aan de galerij.</div><?php endif; ?>
            <?php if ($fouten): ?><div class="melding fout" style="margin-top:14px;"><ul><?php foreach ($fouten as $f): ?><li><?= e($f) ?></li><?php endforeach; ?></ul></div><?php endif; ?>

            <form method="post" enctype="multipart/form-data" style="margin-top:16px;">
                <?= csrf_veld() ?>
                <div class="veld"><label for="naam">Jouw naam of teamnaam</label><input id="naam" name="naam" type="text" maxlength="80" required></div>
                <div class="veld"><label for="bestand">Kies een foto of video (max. 50 MB)</label><input id="bestand" name="bestand" type="file" accept="image/*,video/*" required></div>
                <div class="veld"><label for="omschrijving">Korte beschrijving (optioneel)</label><input id="omschrijving" name="omschrijving" type="text" maxlength="255" placeholder="Bijv. Winnende goal in de finale!"></div>
                <button class="btn btn-groen" type="submit">Plaatsen in galerij</button>
            </form>
        </div>

        <!-- Galerij -->
        <?php if (!$items): ?>
            <div class="paneel" style="text-align:center;"><h3>Nog geen media</h3><p>Wees de eerste die een foto of video uploadt!</p></div>
        <?php else: ?>
            <div class="grid3">
                <?php foreach ($items as $m): ?>
                    <div class="paneel reveal" style="padding:18px;">
                        <?php if ($m['type'] === 'video'): ?>
                            <video src="<?= e($m['bestand']) ?>" controls style="width:100%; height:220px; border-radius:12px; background:#000; object-fit:cover;"></video>
                        <?php else: ?>
                            <img src="<?= e($m['bestand']) ?>" alt="" style="width:100%; height:220px; border-radius:12px; object-fit:cover;">
                        <?php endif; ?>
                        <div style="margin-top:12px;">
                            <strong><?= e($m['naam']) ?></strong> <small style="opacity:.7;">• <?= e(datum_nl($m['aangemaakt'])) ?></small>
                            <?php if (!empty($m['omschrijving'])): ?><p style="margin-top:4px; font-size:.95rem;"><?= e($m['omschrijving']) ?></p><?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>