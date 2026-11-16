<?php
// De 25 clubs, 25 landen en de teksten per toernooi
const PLACEHOLDER = 'Wordt binnenkort bekendgemaakt door de organisatie.';

const TOERNOOIEN = [
    'voetvolley' => [
        'naam' => 'Voetvolley', 'icoon' => '🏐', 'accent' => '#FF7A1A',
        'kop' => 'DOE MEE AAN VOETVOLLEY',
        'intro' => 'Laat jouw techniek zien en strijd samen met je team tijdens de Voetbaldagen Horst 2027.',
        'kaarttekst' => 'Test je techniek, controle en samenwerking tijdens een uitdagend potje voetvolley.',
        'beschrijving' => 'Techniek, controle en samenwerking staan centraal. Neem het met jouw team op tegen andere deelnemers en laat zien wat je kunt.',
        'weetje' => 'Voetvolley is begin jaren zestig ontstaan op de stranden van Rio de Janeiro in Brazilië.',
    ],
    'champions-league' => [
        'naam' => 'Champions League', 'icoon' => '🏆', 'accent' => '#1F4FD8',
        'kop' => 'KIES JOUW CLUB. STRIJD OM DE BEKER.',
        'intro' => 'Vorm jouw team, kies jouw favoriete Europese topclub en strijd samen om de overwinning.',
        'kaarttekst' => 'Kies jouw favoriete Europese topclub en strijd samen met je team om de overwinning.',
        'beschrijving' => 'Vertegenwoordiger van jouw favoriete Europese topclub worden? Vorm jouw team, kies jouw club en strijd om de overwinning.',
        'weetje' => 'Real Madrid is recordhouder met de meeste Europese kampioenschappen voor clubteams.',
    ],
    'wk' => [
        'naam' => 'Wereldkampioenschap', 'icoon' => '🌍', 'accent' => '#19864A',
        'kop' => 'KIES JOUW LAND. GA VOOR DE WERELDTITEL.',
        'intro' => 'Vertegenwoordig jouw favoriete land en ga samen met je team voor de wereldtitel.',
        'kaarttekst' => 'Vertegenwoordig jouw favoriete land en ga samen met je team voor de wereldtitel.',
        'beschrijving' => 'Vertegenwoordig een land tijdens het wereldkampioenschap van Voetbaldagen Horst. Werk samen, speel met trots en ga voor de wereldtitel.',
        'weetje' => 'Het allereerste WK werd in 1930 gehouden in Uruguay, en Uruguay won het ook meteen.',
    ],
];

// naam, afkorting (voor als er geen logo is), kleur, bestandsnaam van het logo
const CLUBS = [
    ['Real Madrid', 'RMA', '#1F3A93', 'real-madrid'],
    ['FC Barcelona', 'FCB', '#A50044', 'fc-barcelona'],
    ['Manchester City', 'MCI', '#6CABDD', 'manchester-city'],
    ['Liverpool', 'LIV', '#C8102E', 'liverpool'],
    ['Arsenal', 'ARS', '#EF0107', 'arsenal'],
    ['Manchester United', 'MUN', '#DA291C', 'manchester-united'],
    ['Chelsea', 'CHE', '#034694', 'chelsea'],
    ['Bayern München', 'BAY', '#DC052D', 'bayern-munchen'],
    ['Borussia Dortmund', 'BVB', '#C9A400', 'borussia-dortmund'],
    ['Paris Saint-Germain', 'PSG', '#004170', 'paris-saint-germain'],
    ['Juventus', 'JUV', '#111111', 'juventus'],
    ['AC Milan', 'ACM', '#FB090B', 'ac-milan'],
    ['Inter Milan', 'INT', '#0068A8', 'inter-milan'],
    ['Atlético Madrid', 'ATM', '#CB3524', 'atletico-madrid'],
    ['Benfica', 'BEN', '#E30613', 'benfica'],
    ['Ajax', 'AJA', '#D2122E', 'ajax'],
    ['PSV', 'PSV', '#E1000F', 'psv'],
    ['Feyenoord', 'FEY', '#C8102E', 'feyenoord'],
    ['AS Roma', 'ROM', '#8E1F2F', 'as-roma'],
    ['Napoli', 'NAP', '#12A0D7', 'napoli'],
    ['FC Porto', 'POR', '#003DA5', 'fc-porto'],
    ['Sporting CP', 'SCP', '#00703C', 'sporting-cp'],
    ['Bayer Leverkusen', 'B04', '#E32221', 'bayer-leverkusen'],
    ['Tottenham Hotspur', 'TOT', '#132257', 'tottenham-hotspur'],
    ['Aston Villa', 'AVL', '#670E36', 'aston-villa'],
];

// naam, landcode (voor de vlag van flagcdn.com)
const LANDEN = [
    ['Nederland', 'nl'], ['Argentinië', 'ar'], ['Brazilië', 'br'], ['Frankrijk', 'fr'], ['Spanje', 'es'],
    ['Engeland', 'gb-eng'], ['Duitsland', 'de'], ['Portugal', 'pt'], ['Marokko', 'ma'], ['België', 'be'],
    ['Italië', 'it'], ['Japan', 'jp'], ['Kroatië', 'hr'], ['Uruguay', 'uy'], ['Senegal', 'sn'],
    ['Mexico', 'mx'], ['Verenigde Staten', 'us'], ['Denemarken', 'dk'], ['Zwitserland', 'ch'], ['Oostenrijk', 'at'],
    ['Zuid-Korea', 'kr'], ['Colombia', 'co'], ['Turkije', 'tr'], ['Noorwegen', 'no'], ['Polen', 'pl'],
];

// Geeft de keuzelijst voor een toernooi (leeg bij voetvolley)
function keuze_lijst($slug) {
    $uit = [];
    if ($slug === 'champions-league') {
        foreach (CLUBS as $c) {
            // Logo's: zet PNG-bestanden in assets/img/clubs/ (bijv. ajax.png). Zonder bestand zie je de afkorting.
            $uit[] = ['naam' => $c[0], 'afk' => $c[1], 'kleur' => $c[2], 'img' => 'assets/img/clubs/' . $c[3] . '.png'];
        }
    } elseif ($slug === 'wk') {
        foreach (LANDEN as $l) {
            $uit[] = ['naam' => $l[0], 'afk' => strtoupper(substr($l[1], 0, 3)), 'kleur' => '#07516A', 'img' => 'https://flagcdn.com/w160/' . $l[1] . '.png'];
        }
    }
    return $uit;
}
function keuze_namen($slug) { return array_column(keuze_lijst($slug), 'naam'); }

// Toernooikaart (homepage en overzicht)
function toernooi_kaart($t, $bezet, $knoppen = false) {
    $m = TOERNOOIEN[$t['slug']];
    $max = (int)$t['max_teams'];
    $pct = $max > 0 ? min(100, round($bezet / $max * 100)) : 0;
    $vol = $bezet >= $max;
    ob_start(); ?>
    <article class="t-kaart reveal" style="--acc:<?= e($m['accent']) ?>">
        <span class="groot-icoon"><?= $m['icoon'] ?></span>
        <h3><?= e($m['naam']) ?></h3>
        <p><?= e($m['kaarttekst']) ?></p>
        <div class="plekken">
            <span><?= $vol ? 'VOL – wachtlijst open' : $bezet . ' van ' . $max . ' plekken bezet' ?></span>
            <div class="balk"><i style="width:<?= $pct ?>%"></i></div>
        </div>
        <div class="knoppen">
            <a class="btn btn-oranje" href="toernooien.php?t=<?= e($t['slug']) ?>">Bekijk toernooi</a>
            <?php if ($knoppen): ?>
                <a class="btn btn-lijn" href="inschrijven.php?toernooi=<?= e($t['slug']) ?>"><?= $vol ? 'Wachtlijst' : 'Schrijf in' ?></a>
            <?php endif; ?>
        </div>
    </article>
    <?php return ob_get_clean();
}