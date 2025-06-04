<?php
require_once 'inc/config.php';
require_once 'inc/flash.php';

if($_SERVER['REQUEST_METHOD']==='POST'){
  $email=$_POST['email']??'';
  $pass=$_POST['password']??'';
  $stmt=$pdo->prepare('SELECT * FROM users WHERE email=?'); $stmt->execute([$email]);
  $user=$stmt->fetch();
  if($user && password_verify($pass,$user['password_hash'])){
    if(!$user['verified']) $error='Compte non vérifié – consultez vos e‑mails.';
    else {
      $_SESSION['user']=$user; header('Location:/'); exit;
    }
  } else $error='Identifiants incorrects';
}

$page_title = 'Connexion';
$page_description = "Accédez à votre compte client et gérez vos réservations";
require_once 'inc/header.php';
?>
<div class="row justify-content-center py-5">
  <div class="col-md-6">
    <h1 class="mb-4 text-center">Connexion</h1>
    <?php if(isset($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <div class="card p-4 shadow-sm">
      <form method="post">
        <div class="mb-3">
          <label for="loginEmail">Email</label>
          <input type="email" id="loginEmail" name="email" value="<?= htmlspecialchars($email ?? '') ?>" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
          <label for="loginPassword">Mot de passe</label>
          <input type="password" id="loginPassword" name="password" class="form-control" required>
        </div>
        <button class="btn btn-primary w-100">Se connecter</button>
        <a href="reset_request.php" class="btn btn-link d-block mt-2">Mot de passe oublié</a>
      </form>
    </div>
  </div>
</div>
<?php require_once 'inc/footer.php'; ?>
