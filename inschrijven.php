<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/data.php';
$root = '';
$slug = $_GET['toernooi'] ?? '';
if (!isset(TOERNOOIEN[$slug])) { redirect('toernooien.php'); }
$t = toernooi_ophalen($db, $slug);
$meta = TOERNOOIEN[$slug];
$max = (int)$t['max_teams'];
$bezet = plekken_bezet($db, $t['id']);
$vol = $bezet >= $max;
$heeftKeuze = $slug !== 'voetvolley';
$keuzes = keuze_lijst($slug);
$bezetteKeuzes = (!$vol && $heeftKeuze) ? bezette_keuzes($db, $t['id']) : [];
$keuzeLabel = $slug === 'wk' ? 'Land' : 'Club';

// Fouten en eerder ingevulde gegevens (na een serverfout)
$fouten = $_SESSION['fouten'] ?? [];
$oud = $_SESSION['oud'] ?? [];
unset($_SESSION['fouten'], $_SESSION['oud']);
if (($oud['toernooi'] ?? '') !== $slug) { $oud = []; }

$namen = ['Teamgegevens'];
if ($heeftKeuze) { $namen[] = $keuzeLabel . ' kiezen'; }
$namen[] = 'Spelers toevoegen';
$namen[] = 'Controleren';

$paginaTitel = 'Inschrijven ' . $meta['naam'];
include __DIR__ . '/includes/header.php';
?>
<section class="kop" style="--acc:<?= e($meta['accent']) ?>">
    <div class="container">
        <a class="terug" href="toernooien.php?t=<?= e($slug) ?>">← Terug naar toernooi</a><br>
        <span class="eyebrow">30 AUGUSTUS – 3 SEPTEMBER 2027</span>
        <h1><?= e($meta['kop']) ?></h1>
        <p><?= e($meta['intro']) ?></p>
    </div>
</section>

<section class="sectie" style="padding-top:50px">
    <div class="container">
        <ol class="stepper">
            <?php foreach ($namen as $i => $n): ?><li><b><?= sprintf('%02d', $i + 1) ?></b><span><?= e($n) ?></span></li><?php endforeach; ?>
        </ol>

        <?php if ($vol): ?>
            <div class="melding info">Dit toernooi is vol (<?= $max ?> teams). Je kunt je nog aanmelden voor de wachtlijst.</div>
        <?php endif; ?>
        <?php if ($fouten): ?>
            <div class="melding fout"><strong>Er ging iets mis:</strong><ul><?php foreach ($fouten as $f): ?><li><?= e($f) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <div class="inschrijf">
            <form id="inschrijfForm" method="post" action="verwerk-inschrijving.php" novalidate
                  data-min="<?= MIN_SPELERS ?>" data-max="<?= MAX_SPELERS ?>" data-lmin="<?= MIN_LEEFTIJD ?>" data-lmax="<?= MAX_LEEFTIJD ?>" data-keuzelabel="<?= e($keuzeLabel) ?>">
                <?= csrf_veld() ?>
                <input type="hidden" name="toernooi" value="<?= e($slug) ?>">

                <!-- Stap: team -->
                <div class="stap paneel" data-type="team">
                    <h2>Vertel ons over jouw team</h2>
                    <p>Vul de gegevens van jouw team in om jullie deelname compleet te maken.</p>
                    <div class="veld"><label for="teamnaam">Teamnaam</label><input id="teamnaam" name="teamnaam" type="text" maxlength="60" placeholder="Bijv. FC De Kampioenen" value="<?= e($oud['teamnaam'] ?? '') ?>" required></div>
                    <div class="veld"><label for="captain">Naam van de teamcaptain</label><input id="captain" name="captain" type="text" maxlength="80" placeholder="Voor- en achternaam" value="<?= e($oud['captain'] ?? '') ?>" required></div>
                    <div class="rij2">
                        <div class="veld"><label for="email">E-mailadres</label><input id="email" name="email" type="email" maxlength="120" placeholder="naam@voorbeeld.nl" value="<?= e($oud['email'] ?? '') ?>" required></div>
                        <div class="veld"><label for="telefoon">Telefoonnummer</label><input id="telefoon" name="telefoon" type="tel" maxlength="20" pattern="[0-9 +\(\)\-]{8,20}" placeholder="06 12345678" value="<?= e($oud['telefoon'] ?? '') ?>" required></div>
                    </div>
                    <div class="stap-knoppen"><span></span><button type="button" class="btn btn-oranje" data-volgende>Volgende →</button></div>
                </div>

                <?php if ($heeftKeuze): ?>
                <!-- Stap: club of land -->
                <div class="stap paneel" data-type="keuze" hidden>
                    <h2><?= $slug === 'wk' ? 'Kies jullie land' : 'Kies jullie club' ?></h2>
                    <p><?= $slug === 'wk' ? 'Welk land gaan jullie vertegenwoordigen?' : 'Welke Europese topclub gaan jullie vertegenwoordigen?' ?> Je kunt er maar één kiezen.</p>
                    <div class="veld zoekbalk"><input type="search" id="zoek" placeholder="Zoek <?= $slug === 'wk' ? 'een land' : 'een club' ?>..." aria-label="Zoeken"></div>
                    <span class="gekozen-chip" id="gekozenChip" hidden></span>
                    <div class="keuze-grid <?= $slug === 'wk' ? 'landen' : '' ?>">
                        <?php foreach ($keuzes as $k):
                            $isBezet = in_array($k['naam'], $bezetteKeuzes, true); ?>
                            <label class="keuze-kaart<?= $isBezet ? ' bezet' : '' ?>" data-naam="<?= e(mb_strtolower($k['naam'])) ?>">
                                <input type="radio" name="keuze" value="<?= e($k['naam']) ?>" <?= $isBezet ? 'disabled' : '' ?> <?= ($oud['keuze'] ?? '') === $k['naam'] ? 'checked' : '' ?>>
                                <span class="kaart-in">
                                    <span class="vink">✓</span>
                                    <span class="wapen" style="--kl:<?= e($k['kleur']) ?>"><em><?= e($k['afk']) ?></em><img src="<?= e($k['img']) ?>" alt="" loading="lazy" onload="this.parentNode.classList.add('geladen')" onerror="this.remove()"></span>
                                    <strong><?= e($k['naam']) ?></strong>
                                    <?php if ($isBezet): ?><small>Al bezet</small><?php endif; ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <span class="fout-melding" id="keuzeFout" hidden>Kies een <?= strtolower($keuzeLabel) ?> om verder te gaan.</span>
                    <div class="stap-knoppen"><button type="button" class="btn btn-grijs" data-vorige>← Vorige</button><button type="button" class="btn btn-oranje" data-volgende>Volgende →</button></div>
                </div>
                <?php endif; ?>

                <!-- Stap: spelers -->
                <div class="stap paneel" data-type="spelers" hidden>
                    <h2>Wie zitten er in jouw team?</h2>
                    <p>Voeg alle spelers toe die tijdens het toernooi voor jullie team uitkomen (<?= MIN_SPELERS ?> t/m <?= MAX_SPELERS ?> spelers, leeftijd <?= MIN_LEEFTIJD ?> t/m <?= MAX_LEEFTIJD ?> jaar).</p>
                    <div class="speler-balk">
                        <span class="teller" id="spelerTeller">0 spelers toegevoegd</span>
                        <label>Aantal spelers:
                            <select id="aantalSel"><?php for ($i = MIN_SPELERS; $i <= MAX_SPELERS; $i++): ?><option value="<?= $i ?>"><?= $i ?></option><?php endfor; ?></select>
                        </label>
                    </div>
                    <div id="spelerLijst"></div>
                    <button type="button" class="speler-plus" id="spelerPlus">+ SPELER TOEVOEGEN</button>
                    <span class="fout-melding" id="spelersFout" hidden>Een team heeft <?= MIN_SPELERS ?> t/m <?= MAX_SPELERS ?> spelers.</span>
                    <div class="stap-knoppen"><button type="button" class="btn btn-grijs" data-vorige>← Vorige</button><button type="button" class="btn btn-oranje" data-volgende>Volgende →</button></div>
                </div>

                <!-- Stap: controleren -->
                <div class="stap paneel" data-type="review" hidden>
                    <h2>Controleer jullie inschrijving</h2>
                    <p>Klopt alles? Met “Wijzig” ga je terug naar de juiste stap.</p>
                    <div id="reviewInhoud"></div>
                    <label class="vinkje"><input type="checkbox" id="akkoord" name="akkoord" value="1"><span>Ik bevestig dat alle gegevens kloppen en dat ik de <a href="privacy.php" target="_blank" style="text-decoration:underline">privacyverklaring</a> heb gelezen.</span></label>
                    <span class="fout-melding" id="akkoordFout" hidden>Je moet dit bevestigen om te kunnen versturen.</span>
                    <div class="stap-knoppen"><button type="button" class="btn btn-grijs" data-vorige>← Vorige</button>
                        <button type="submit" class="btn btn-oranje"><?= $vol ? 'AANMELDEN VOOR DE WACHTLIJST' : 'INSCHRIJVING DEFINITIEF VERZENDEN' ?></button></div>
                </div>
            </form>

            <aside class="ticket">
                <span class="eyebrow">Jullie ticket</span>
                <h3><?= e($meta['naam']) ?></h3>
                <div class="ticket-streep"></div>
                <div class="rij"><span>Team</span><strong id="sumTeam">–</strong></div>
                <?php if ($heeftKeuze): ?><div class="rij"><span><?= $keuzeLabel ?></span><strong id="sumKeuze">–</strong></div><?php endif; ?>
                <div class="rij"><span>Spelers</span><strong id="sumSpelers">0</strong></div>
                <div class="rij"><span>Datum</span><strong>30 aug – 3 sep 2027</strong></div>
                <div class="ticket-streep"></div>
                <div class="plekken"><?= $vol ? 'Toernooi vol – wachtlijst' : $bezet . ' van ' . $max . ' plekken bezet' ?>
                    <div class="balk"><i style="width:<?= $max ? min(100, round($bezet / $max * 100)) : 0 ?>%"></i></div></div>
            </aside>
        </div>
    </div>
</section>
<script>window.OUD = <?= json_encode($oud, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;</script>
<?php include __DIR__ . '/includes/footer.php'; ?>