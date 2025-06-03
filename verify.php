<?php
require_once 'inc/header.php';

$token = $_GET['token'] ?? '';
if (!$token) {
    echo '<div class="alert alert-danger">Token manquant.</div>';
    require_once 'inc/footer.php';
    exit;
}

$stmt = $pdo->prepare(
    "UPDATE users
       SET verify_token = NULL,
           verified_at  = NOW()
     WHERE verify_token = ?"
);
$stmt->execute([$token]);

if ($stmt->rowCount()) {
    echo '<div class="alert alert-success">Votre compte est désormais vérifié !</div>';
} else {
    echo '<div class="alert alert-warning">Lien invalide ou déjà utilisé.</div>';
}

require_once 'inc/footer.php';
?>