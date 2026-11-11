<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/data.php';
$paginaTitel = 'Home';
$root = '';
$toernooien = $db->query('SELECT * FROM toernooien ORDER BY id')->fetchAll();
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