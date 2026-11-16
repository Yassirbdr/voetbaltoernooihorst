<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/data.php';
$root = '';
$paginaTitel = 'Nieuws';

try {
    $nieuws = $db->query('SELECT * FROM nieuws ORDER BY id DESC')->fetchAll();
} catch (Exception $e) {
    $nieuws = [];
}

include __DIR__ . '/includes/header.php';
?>
<section class="kop">
    <div class="container">
        <span class="eyebrow">ACTUEEL</span>
        <h1>Laatste Nieuws</h1>
        <p>Blijf op de hoogte van al het nieuws rondom de Voetbaldagen Horst 2027.</p>
    </div>
</section>

<section class="sectie">
    <div class="container">
        <?php if (!$nieuws): ?>
            <div class="paneel" style="text-align:center; padding: 40px;">
                <h3>Nog geen nieuwsberichten</h3>
                <p style="margin-top: 10px;">Zodra er updates zijn, verschijnen ze hier!</p>
            </div>
        <?php else: ?>
            <div class="grid3">
                <?php foreach ($nieuws as $n): ?>
                    <div class="paneel reveal">
                        <?php if (!empty($n['afbeelding'])): ?>
                            <img src="<?= e($n['afbeelding']) ?>" alt="" style="border-radius: 10px; margin-bottom: 15px; width: 100%; height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <small style="color: var(--oranje); font-weight: 700;"><?= e(datum_nl($n['aangemaakt'])) ?></small>
                        <h3 style="margin: 8px 0; font-size: 1.3rem;"><?= e($n['titel']) ?></h3>
                        <p><?= nl2br(e($n['inhoud'])) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>