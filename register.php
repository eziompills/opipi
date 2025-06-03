<?php
require_once 'inc/header.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $name=$_POST['name']??''; $email=$_POST['email']??''; $pass=$_POST['password']??'';
  $exists=$pdo->prepare('SELECT id FROM users WHERE email=?'); $exists->execute([$email]);
  if($exists->fetchColumn()){ $error='Email déjà utilisé.'; }
  else {
    $token=bin2hex(random_bytes(16));
    $pdo->prepare('INSERT INTO users (name,email,password_hash,verify_token) VALUES (?,?,?,?)')
        ->execute([$name,$email,password_hash($pass,PASSWORD_DEFAULT),$token]);
    require_once 'inc/mailer.php';
    $link='https://'.$_SERVER['HTTP_HOST'].'/verify.php?token='.$token;
    send_email($email,'Vérification de compte','Cliquez pour vérifier : <a href="'.$link.'">'.$link.'</a>');
    echo '<div class="alert alert-success">Compte créé ! Vérifiez vos e‑mails.</div>';
  }
}
?>
<h1>Inscription</h1>
<?php if(isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
<form method="post">
  <div class="mb-3"><label>Nom</label><input name="name" class="form-control" required></div>
  <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
  <div class="mb-3"><label>Mot de passe</label><input type="password" name="password" class="form-control" required></div>
  <button class="btn btn-success">Créer mon compte</button>
</form>
<?php require_once 'inc/footer.php'; ?>
