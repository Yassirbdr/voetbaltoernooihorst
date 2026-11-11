<?php
require __DIR__ . '/../includes/functies.php';
$_SESSION = [];
session_destroy();
header('Location: login.php');
exit;