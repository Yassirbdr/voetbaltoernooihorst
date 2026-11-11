<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/data.php';
$root = '';
$paginaTitel = 'Vrijwilligers';
$fouten = $_SESSION['fouten_v'] ?? [];
$oud = $_SESSION['oud_v'] ?? [];
unset($_SESSION['fouten_v'], $_SESSION['oud_v']);
include __DIR__ . '/includes/header.php';
?>
<section class="hero" style="min-height:80vh">
    <div class="container">
        <span class="eyebrow">VRIJWILLIGERS • VOETBALDAGEN HORST 2027</span>
        <h1>Word onderdeel<br>van het <span class="oranje">team</span></h1>
        <p>Help mee om de Voetbaldagen Horst 2027 onvergetelijk te maken. Samen zorgen we voor vijf fantastische dagen vol voetbal en gezelligheid.</p>
        <div class="knoppen"><a class="btn btn-oranje" href="#aanmelden">MELD JE AAN</a></div>
    </div>
</section>

<section class="sectie sectie-zand">
    <div class="container">
        <div class="sectie-kop reveal"><h2>Dit krijg je <span class="oranje">terug</span></h2></div>
        <div class="grid4">
            <div class="voordeel reveal"><span>😄</span><h3>Een ontzettend gezellige dag</h3></div>
            <div class="voordeel reveal"><span>🥪</span><h3>Gratis lunch en koude drankjes</h3></div>
            <div class="voordeel reveal"><span>👕</span><h3>Een exclusief officieel toernooishirt</h3></div>
            <div class="voordeel reveal"><span>🔥</span><h3>Een geweldige barbecue als afsluiter</h3></div>
        </div>
    </div>
</section>

<section class="sectie" id="aanmelden">
    <div class="container" style="max-width:860px">
        <div class="sectie-kop"><h2>Doe mee als <span class="oranje">vrijwilliger</span></h2></div>
        <?php if ($fouten): ?>
            <div class="melding fout"><strong>Er ging iets mis:</strong><ul><?php foreach ($fouten as $f): ?><li><?= e($f) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>
        <form id="vrijwForm" class="paneel" method="post" action="verwerk-vrijwilliger.php" novalidate>
            <?= csrf_veld() ?>
            <div class="veld"><label for="naam">Voor- en achternaam</label><input id="naam" name="naam" type="text" maxlength="80" placeholder="Jouw volledige naam" value="<?= e($oud['naam'] ?? '') ?>" required></div>
            <div class="rij2">
                <div class="veld"><label for="email">E-mailadres</label><input id="email" name="email" type="email" maxlength="120" placeholder="naam@voorbeeld.nl" value="<?= e($oud['email'] ?? '') ?>" required></div>
                <div class="veld"><label for="telefoon">Telefoonnummer</label><input id="telefoon" name="telefoon" type="tel" maxlength="20" placeholder="06 12345678" value="<?= e($oud['telefoon'] ?? '') ?>" required></div>
            </div>

            <h3 style="margin:10px 0 12px">Wanneer kun je helpen?</h3>
            <div class="kies-grid">
                <?php foreach (DAGEN as $d): ?>
                    <label class="kies-kaart"><input type="checkbox" name="dagen[]" value="<?= e($d) ?>" <?= in_array($d, $oud['dagen'] ?? [], true) ? 'checked' : '' ?>>
                        <span class="kaart-in"><span class="vink">✓</span><span class="icoon">📅</span><strong><?= e($d) ?></strong></span></label>
                <?php endforeach; ?>
            </div>
            <span class="fout-melding" id="dagenFout" hidden>Kies minimaal één dag.</span>

            <h3 style="margin:24px 0 12px">Waar zou je graag mee willen helpen? <small style="font-family:Inter;text-transform:none;font-size:.9rem">(optioneel)</small></h3>
            <div class="kies-grid">
                <?php foreach (TAKEN as $taak => $icoon): ?>
                    <label class="kies-kaart"><input type="checkbox" name="taken[]" value="<?= e($taak) ?>" <?= in_array($taak, $oud['taken'] ?? [], true) ? 'checked' : '' ?>>
                        <span class="kaart-in"><span class="vink">✓</span><span class="icoon"><?= $icoon ?></span><strong><?= e($taak) ?></strong></span></label>
                <?php endforeach; ?>
            </div>

            <div class="veld"><label for="opmerkingen">Opmerkingen (optioneel)</label><textarea id="opmerkingen" name="opmerkingen" maxlength="1000" placeholder="Is er nog iets wat we moeten weten?"><?= e($oud['opmerkingen'] ?? '') ?></textarea></div>
            <label class="vinkje"><input type="checkbox" name="akkoord" value="1"><span>Ik ga akkoord met de <a href="privacy.php" target="_blank" style="text-decoration:underline">privacyverklaring</a>.</span></label>
            <div class="knoppen"><button type="submit" class="btn btn-oranje">MELD MIJ AAN ALS VRIJWILLIGER</button></div>
        </form>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>