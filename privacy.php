<?php
require __DIR__ . '/includes/db.php';
$root = '';
$paginaTitel = 'Privacy';
include __DIR__ . '/includes/header.php';
?>
<section class="kop"><div class="container"><span class="eyebrow">PRIVACY</span><h1>Privacyverklaring</h1></div></section>
<section class="sectie">
    <div class="container paneel" style="max-width:860px">
        <div class="melding info">Dit is een conceptversie. De organisatie moet deze tekst controleren voordat de website live gaat.</div>
        <h3>Welke gegevens verzamelen we?</h3>
        <p>Bij een teaminschrijving: teamnaam, naam, e-mailadres en telefoonnummer van de teamcaptain en de naam en leeftijd van elke speler. Bij een vrijwilligersaanmelding: naam, e-mailadres, telefoonnummer, beschikbaarheid, voorkeurstaken en eventuele opmerkingen. Via het contactformulier: naam, e-mailadres en je bericht.</p>
        <h3 style="margin-top:20px">Waarom?</h3>
        <p>Alleen om het toernooi te organiseren: teams indelen, contact opnemen en vrijwilligers inplannen. We verzamelen niet meer dan nodig is.</p>
        <h3 style="margin-top:20px">Minderjarigen</h3>
        <p>Spelers zijn tussen <?= MIN_LEEFTIJD ?> en <?= MAX_LEEFTIJD ?> jaar. De teamcaptain bevestigt bij het inschrijven dat de gegevens kloppen. Voor deelnemers onder de 16 jaar adviseren we dat een ouder of verzorger op de hoogte is van de inschrijving.</p>
        <h3 style="margin-top:20px">Wie ziet de gegevens?</h3>
        <p>Alleen de organisatie via de beveiligde beheeromgeving. We delen gegevens niet met derden.</p>
        <h3 style="margin-top:20px">Hoe lang bewaren we ze?</h3>
        <p>Tot kort na het evenement. Daarna worden de gegevens verwijderd.</p>
        <h3 style="margin-top:20px">Jouw rechten</h3>
        <p>Je kunt vragen om inzage, correctie of verwijdering van je gegevens via <a href="contact.php" style="text-decoration:underline">het contactformulier</a>.</p>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>