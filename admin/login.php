<?php
require __DIR__ . '/../includes/db.php';
if (is_admin()) { redirect('dashboard.php'); }
$root = '../';
$adminPagina = true;
$paginaTitel = 'Admin login';
$fout = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_ok()) {
    if (($_SESSION['login_blok'] ?? 0) > time()) {
        $fout = 'Te veel pogingen. Probeer het over enkele minuten opnieuw.';
    } else {
        $s = $db->prepare('SELECT * FROM admins WHERE gebruikersnaam = ?');
        $s->execute([trim((string)($_POST['gebruikersnaam'] ?? ''))]);
        $a = $s->fetch();
        if ($a && password_verify((string)($_POST['wachtwoord'] ?? ''), $a['wachtwoord_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $a['id'];
            unset($_SESSION['pogingen'], $_SESSION['login_blok']);
            redirect('dashboard.php');
        }
        usleep(400000);
        $_SESSION['pogingen'] = ($_SESSION['pogingen'] ?? 0) + 1;
        if ($_SESSION['pogingen'] >= 5) { $_SESSION['login_blok'] = time() + 300; $_SESSION['pogingen'] = 0; }
        $fout = 'Onjuiste gebruikersnaam of wachtwoord.';
    }
}
include __DIR__ . '/../includes/header.php';
?>
<div class="container login-doos">
    <div class="paneel">
        <span class="eyebrow" style="color:var(--oranje)">Beheer</span>
        <h2 style="font-size:2.4rem;margin-bottom:18px">Inloggen</h2>
        <?php if ($fout): ?><div class="melding fout"><?= e($fout) ?></div><?php endif; ?>
        <form method="post">
            <?= csrf_veld() ?>
            <div class="veld"><label for="gebruikersnaam">Gebruikersnaam</label><input id="gebruikersnaam" name="gebruikersnaam" type="text" autocomplete="username" required></div>
            <div class="veld"><label for="wachtwoord">Wachtwoord</label><input id="wachtwoord" name="wachtwoord" type="password" autocomplete="current-password" required></div>
            <button class="btn btn-oranje" type="submit" style="width:100%">Inloggen</button>
        </form>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>