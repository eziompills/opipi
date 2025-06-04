<?php
require_once 'inc/config.php';
require_once 'inc/flash.php';

$token = $_GET['token'] ?? '';
if (!$token) {
    set_flash('Token manquant.', 'danger');
    header('Location: /reset_request.php');
    exit;
}

$stmt = $pdo->prepare('SELECT id, reset_expires_at FROM users WHERE reset_token = ?');
$stmt->execute([$token]);
$user = $stmt->fetch();
if (!$user || strtotime($user['reset_expires_at']) < time()) {
    set_flash('Lien invalide ou expiré.', 'danger');
    header('Location: /reset_request.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass = $_POST['password'] ?? '';
    if ($pass) {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $pdo->prepare('UPDATE users SET password_hash = ?, reset_token = NULL, reset_expires_at = NULL WHERE id = ?')
            ->execute([$hash, $user['id']]);
        set_flash('Mot de passe mis à jour. Vous pouvez vous connecter.', 'success');
        header('Location: /login.php');
        exit;
    } else {
        $error = 'Mot de passe invalide';
    }
}

require_once 'inc/header.php';
?>
<h1>Nouveau mot de passe</h1>
<?php if(isset($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form method="post">
  <div class="mb-3"><label>Nouveau mot de passe</label>
    <input type="password" name="password" class="form-control" required></div>
  <button class="btn btn-success">Réinitialiser</button>
</form>
<?php require_once 'inc/footer.php'; ?>
