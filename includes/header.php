<?php
// Zet vooraf $paginaTitel en $root ('' of '../'). Voor admin: $adminPagina = true;
$root = $root ?? '';
$adminPagina = $adminPagina ?? false;
$huidig = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($paginaTitel ?? 'Home') ?> | Voetbaldagen Horst 2027</title>
<meta name="description" content="Voetbaldagen Horst 2027: drie toernooien, vijf dagen voetbalplezier. Schrijf jouw team in!">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= $root ?>assets/css/style.css">
</head>
<body class="<?= $adminPagina ? 'admin' : '' ?>">
<header class="site-header" id="header" <?= $adminPagina ? 'data-solide="1"' : '' ?>>
    <div class="container header-in">
        <a class="logo" href="<?= $root ?><?= $adminPagina ? 'admin/dashboard.php' : 'index.php' ?>">⚽ Voetbaldagen <b>Horst</b></a>
        <button class="menu-knop" id="menuKnop" type="button" aria-label="Menu" aria-expanded="false"><span></span><span></span><span></span></button>
        <nav class="nav" id="nav">
            <?php if ($adminPagina): ?>
                <a href="dashboard.php" class="<?= $huidig === 'dashboard.php' ? 'actief' : '' ?>">Dashboard</a>
                <a href="teams.php" class="<?= in_array($huidig, ['teams.php', 'teams-bewerken.php']) ? 'actief' : '' ?>">Teams</a>
                <a href="vrijwilligers.php" class="<?= $huidig === 'vrijwilligers.php' ? 'actief' : '' ?>">Vrijwilligers</a>
                <a href="../index.php">Naar website</a>
                <a class="btn btn-oranje btn-klein" href="logout.php">Uitloggen</a>
            <?php else: ?>
                <a href="index.php" class="<?= $huidig === 'index.php' ? 'actief' : '' ?>">Home</a>
                <a href="toernooien.php" class="<?= $huidig === 'toernooien.php' ? 'actief' : '' ?>">Toernooien</a>
                <a href="vrijwilligers.php" class="<?= $huidig === 'vrijwilligers.php' ? 'actief' : '' ?>">Vrijwilligers</a>
                <a href="contact.php" class="<?= $huidig === 'contact.php' ? 'actief' : '' ?>">Contact</a>
                <a class="btn btn-oranje btn-klein" href="toernooien.php">Schrijf je in</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main>