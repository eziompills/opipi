<?php
require_once 'inc/header.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $email=$_POST['email']??''; $pass=$_POST['password']??'';
  $stmt=$pdo->prepare('SELECT * FROM users WHERE email=?'); $stmt->execute([$email]);
  $user=$stmt->fetch();
  if($user && password_verify($pass,$user['password_hash'])){
    if(!$user['verified']) $error='Compte non vérifié – consultez vos e‑mails.';
    else {
      $_SESSION['user']=$user; header('Location:/'); exit;
    }
  } else $error='Identifiants incorrects';
}
?>
<h1>Connexion</h1>
<?php if(isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
<form method="post">
  <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
  <div class="mb-3"><label>Mot de passe</label><input type="password" name="password" class="form-control" required></div>
  <button class="btn btn-primary">Se connecter</button>
  <a href="reset_request.php" class="btn btn-link">Mot de passe oublié</a>
</form>
<?php require_once 'inc/footer.php'; ?>
