<?php
require __DIR__ . '/includes/db.php';
http_response_code(404);
$root = '';
$paginaTitel = 'Pagina niet gevonden';
include __DIR__ . '/includes/header.php';
?>
<section class="hero" style="min-height:90vh;text-align:center">
    <div class="container">
        <span class="eyebrow">FOUT 404</span>
        <h1>Buitenspel!</h1>
        <p style="margin-left:auto;margin-right:auto">Deze pagina bestaat niet (meer). Ga terug naar het midden van het veld.</p>
        <div class="knoppen" style="justify-content:center"><a class="btn btn-oranje" href="index.php">Naar de homepage</a><a class="btn btn-lijn" href="toernooien.php">Bekijk toernooien</a></div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>