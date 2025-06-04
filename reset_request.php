<?php
require_once 'inc/config.php';
require_once 'inc/flash.php';
$page_title = 'Mot de passe oublié';
$page_description = "Recevez un lien pour réinitialiser votre mot de passe";
require_once 'inc/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $userId = $stmt->fetchColumn();
    if ($userId) {
        $token = bin2hex(random_bytes(16));
        $expires = date('Y-m-d H:i:s', time() + 3600);
        $pdo->prepare('UPDATE users SET reset_token = ?, reset_expires_at = ? WHERE id = ?')
            ->execute([$token, $expires, $userId]);
        require_once 'inc/mailer.php';
        $link = 'https://' . $_SERVER['HTTP_HOST'] . '/reset_password.php?token=' . $token;
        send_email($email, 'Réinitialisation du mot de passe',
            'Cliquez sur ce lien pour réinitialiser votre mot de passe : <a href="' . $link . '">' . $link . '</a>');
    }
    set_flash('Si cet email existe, un lien a été envoyé.', 'info');
    header('Location: /login.php');
    exit;
}
?>
<div class="row justify-content-center py-5">
  <div class="col-md-6">
    <h1 class="mb-4 text-center">Mot de passe oublié</h1>
    <div class="card p-4 shadow-sm">
      <form method="post">
        <div class="mb-3">
          <label for="resetEmail">Email</label>
          <input type="email" id="resetEmail" name="email" class="form-control" required autofocus value="<?= htmlspecialchars($email ?? '') ?>">
        </div>
        <div class="mb-3"><label>Email</label>
          <input type="email" name="email" class="form-control" required></div>
        <button class="btn btn-primary w-100">Envoyer</button>
      </form>
    </div>
  </div>
</div>
<?php require_once 'inc/footer.php'; ?>
