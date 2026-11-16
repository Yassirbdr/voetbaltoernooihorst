# Voetbaldagen Horst 2027

Website voor het voetbaltoernooi (30 augustus – 3 september 2027) met drie toernooien:
Voetvolley, Champions League en Wereldkampioenschap. PHP + MySQL.

## Regels
- 25 teams per toernooi (daarna wachtlijst)
- 14 t/m 23 spelers per team
- Spelers van 6 t/m 16 jaar
- Aanpassen: `includes/functies.php` (spelers/leeftijd) en tabel `toernooien` (max_teams)

## Installeren (lokaal met XAMPP)
1. Zet de map in `htdocs` en start Apache + MySQL.
2. Open phpMyAdmin en importeer `database/database.sql`.
3. Kopieer `config.example.php` naar `includes/config.php` en vul je database-gegevens in.
4. Ga naar http://localhost/voetbaltoernooihorst/

## Admin
- http://localhost/voetbaltoernooihorst/admin/login.php
- Standaard: `admin` / `Horst2027!` (DIRECT WIJZIGEN)
- Nieuw wachtwoord: maak een hash met `php -r "echo password_hash('nieuw-wachtwoord', PASSWORD_DEFAULT);"`
  en voer uit: `UPDATE admins SET wachtwoord_hash='HASH' WHERE gebruikersnaam='admin';`

## Afbeeldingen
- Hero-foto: `assets/img/hero.jpg` (zonder foto zie je een grasveld-patroon)
- Clublogo's: PNG in `assets/img/clubs/` met de naam uit `includes/data.php` (bijv. `ajax.png`).
  Zonder logo zie je een afkorting. Vlaggen komen van flagcdn.com.

## GitHub
`includes/config.php` staat in `.gitignore` en komt dus niet online.