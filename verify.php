<?php
require_once 'inc/config.php';
require_once 'inc/flash.php';

$token = $_GET['token'] ?? '';
if (!$token) {
    set_flash('Token manquant.', 'danger');
    header('Location: /login.php');
    exit;
}

$stmt = $pdo->prepare(
    "UPDATE users
       SET verify_token = NULL,
           verified     = 1
     WHERE verify_token = ?"
);
$stmt->execute([$token]);

if ($stmt->rowCount()) {
    set_flash('Votre compte est désormais vérifié !', 'success');
} else {
    set_flash('Lien invalide ou déjà utilisé.', 'warning');
}
header('Location: /login.php');
exit;
