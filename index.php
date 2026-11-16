<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/data.php';
$paginaTitel = 'Home';
$root = '';
$toernooien = $db->query('SELECT * FROM toernooien ORDER BY id')->fetchAll();

// Laatste 1 media en 1 nieuwsbericht ophalen voor de preview op de homepage
try {
    $laatsteMedia = $db->query('SELECT * FROM media ORDER BY id DESC LIMIT 1')->fetch();
    $laatsteNieuws = $db->query('SELECT * FROM nieuws ORDER BY id DESC LIMIT 1')->fetch();
} catch (Exception $e) {
    $laatsteMedia = false;
    $laatsteNieuws = false;
}

include __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div class="container">
        <span class="eyebrow">30 AUGUSTUS – 3 SEPTEMBER 2027</span>
        <h1>Voetbal.<br><span class="oranje">Passie.</span><br>Samen strijden.</h1>
        <p>Vijf dagen vol voetbal, plezier en competitie. Vorm jouw team en beleef de Voetbaldagen Horst 2027!</p>
        <div class="knoppen">
            <a class="btn btn-oranje" href="toernooien.php">Schrijf jouw team in</a>
            <a class="btn btn-lijn" href="toernooien.php">Ontdek de toernooien</a>
        </div>
    </div>
    <div class="hero-info">
        <div class="container">
            <span><b>5</b>dagen</span><span><b>3</b>toernooien</span><span><b>25</b>teams per toernooi</span><span><b>Horst</b></span>
        </div>
    </div>
</section>

<div class="marquee"><div>
    <?php for ($i = 0; $i < 8; $i++): ?>VOETVOLLEY • CHAMPIONS LEAGUE • WERELDKAMPIOENSCHAP • 30 AUG – 3 SEP 2027 • <?php endfor; ?>
</div></div>

<section class="sectie">
    <div class="container grid2" style="align-items:center">
        <div class="reveal">
            <span class="eyebrow" style="color:var(--oranje)">Over het evenement</span>
            <h2>Meer dan alleen <span class="oranje">voetbal</span></h2>
            <p style="margin-top:16px;font-size:1.1rem">Voetbaldagen Horst brengt jongeren samen voor vijf dagen vol sportiviteit, plezier en onvergetelijke voetbalmomenten.</p>
        </div>
        <div class="weetje reveal"><small>Wist je dat?</small>Het allereerste WK werd in 1930 gehouden in Uruguay, en Uruguay won het ook meteen.</div>
    </div>
</section>

<section class="sectie sectie-zand">
    <div class="container">
        <div class="sectie-kop reveal">
            <span class="eyebrow" style="color:var(--oranje)">Kies jouw toernooi</span>
            <h2>Drie toernooien, één <span class="oranje">feest</span></h2>
        </div>
        <div class="grid3">
            <?php foreach ($toernooien as $t) { echo toernooi_kaart($t, plekken_bezet($db, $t['id'])); } ?>
        </div>
    </div>
</section>

<!-- SECTIE: 2 APARTE VAKKEN (MEDIA & NIEUWS) -->
<section class="sectie">
    <div class="container">
        <div class="sectie-kop reveal">
            <span class="eyebrow" style="color:var(--oranje)">Sfeer & Updates</span>
            <h2>Media & <span class="oranje">Nieuws</span></h2>
        </div>
        <div class="grid2">
            <!-- Vak 1: Media -->
            <div class="paneel reveal" style="display:flex; flex-direction:column; justify-content:space-between; border-top: 6px solid var(--groen);">
                <div>
                    <span class="eyebrow" style="color:var(--groen)">FANS & SPELERS</span>
                    <h3>📸 Media</h3>
                    
                    <?php if ($laatsteMedia && !empty($laatsteMedia['bestand'])): ?>
                        <div style="margin-top:14px; margin-bottom:14px; border-radius:10px; overflow:hidden; height:180px; background:#000;">
                            <?php if ($laatsteMedia['type'] === 'video'): ?>
                                <video src="<?= e($laatsteMedia['bestand']) ?>" style="width:100%; height:100%; object-fit:cover;" muted></video>
                            <?php else: ?>
                                <img src="<?= e($laatsteMedia['bestand']) ?>" alt="Laatste media" style="width:100%; height:100%; object-fit:cover;">
                            <?php endif; ?>
                        </div>
                        <small style="display:block; color:#666;">Geüpload door <strong><?= e($laatsteMedia['naam']) ?></strong></small>
                    <?php else: ?>
                        <p style="margin-top:10px; color:#666;">Bekijk de sfeerfoto's en video's of deel je eigen momenten.</p>
                    <?php endif; ?>
                </div>
                <div class="knoppen" style="margin-top:20px;">
                    <a class="btn btn-groen" href="media.php">Bekijk & upload media</a>
                </div>
            </div>

            <!-- Vak 2: Nieuws -->
            <div class="paneel reveal" style="display:flex; flex-direction:column; justify-content:space-between; border-top: 6px solid var(--oranje);">
                <div>
                    <span class="eyebrow" style="color:var(--oranje)">ORGANISATIE</span>
                    <h3>📰 Nieuws</h3>
                    <?php if ($laatsteNieuws): ?>
                        <div style="margin-top:14px;">
                            <?php if (!empty($laatsteNieuws['afbeelding'])): ?>
                                <div style="margin-bottom:12px; border-radius:10px; overflow:hidden; height:140px;">
                                    <img src="<?= e($laatsteNieuws['afbeelding']) ?>" alt="" style="width:100%; height:100%; object-fit:cover;">
                                </div>
                            <?php endif; ?>
                            <small style="color:var(--oranje); font-weight:700;"><?= e(datum_nl($laatsteNieuws['aangemaakt'])) ?></small>
                            <h4 style="margin:4px 0 8px; font-size:1.2rem;"><?= e($laatsteNieuws['titel']) ?></h4>
                            <p><?= e(mb_strimwidth($laatsteNieuws['inhoud'], 0, 100, '...')) ?></p>
                        </div>
                    <?php else: ?>
                        <p style="margin-top:10px;">Lees hier de officiële aankondigingen, wedstrijdschema's en het laatste nieuws vanuit de organisatie.</p>
                    <?php endif; ?>
                </div>
                <div class="knoppen" style="margin-top:20px;">
                    <a class="btn btn-oranje" href="nieuws.php">Lees al het nieuws</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="sectie sectie-navy schuin">
    <div class="container">
        <div class="sectie-kop reveal">
            <span class="eyebrow">Vrijwilligers</span>
            <h2>Maak de Voetbaldagen samen mogelijk</h2>
            <p>Help mee achter de schermen en beleef vijf fantastische dagen vol gezelligheid, sport en teamspirit.</p>
        </div>
        <div class="grid4">
            <div class="voordeel reveal"><span>🍔</span><h3>Gratis lunch en drankjes</h3></div>
            <div class="voordeel reveal"><span>👕</span><h3>Exclusief toernooishirt</h3></div>
            <div class="voordeel reveal"><span>😄</span><h3>Een ontzettend gezellige dag</h3></div>
            <div class="voordeel reveal"><span>🔥</span><h3>Een fantastische barbecue als afsluiter</h3></div>
        </div>
        <div class="knoppen"><a class="btn btn-oranje" href="vrijwilligers.php">Word vrijwilliger</a></div>
    </div>
</section>

<section class="sectie" style="text-align:center">
    <div class="container reveal">
        <h2>Klaar om te <span class="oranje">strijden?</span></h2>
        <div class="knoppen" style="justify-content:center"><a class="btn btn-oranje" href="toernooien.php">Schrijf jouw team in</a></div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>