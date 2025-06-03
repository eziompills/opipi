<?php
require_once '../inc/header.php';
include 'sidebar.php';
echo '<div class="content-wrap">';
require_role(['owner']);
if($_SERVER['REQUEST_METHOD']==='POST'){
  $subject=$_POST['subject']; $message=$_POST['message'];
  $salon_ids=$_POST['salon_id'] ?? [];
  foreach($salon_ids as $sid){
    $users=$pdo->prepare("SELECT DISTINCT u.email FROM bookings b JOIN users u ON u.id=b.customer_id WHERE b.salon_id=?");
    $users->execute([$sid]);
    foreach($users as $u){
      send_email($u['email'],$subject,$message); // inc/mailer.php
    }
  }
  echo '<div class="alert alert-success">Campagne envoyée</div>';
}
$my_salons=$pdo->prepare("SELECT id,name FROM salons WHERE owner_id=?");
$my_salons->execute([user()['id']]);
?>
<h1>Campagne marketing</h1>
<form method="post">
  <div class="mb-3"><label>Sujet</label><input name="subject" class="form-control" required></div>
  <div class="mb-3"><label>Message (HTML autorisé)</label><textarea name="message" class="form-control" rows="6"></textarea></div>
  <div class="mb-3">
    <label>Salons cibles</label><br>
    <?php foreach($my_salons as $s): ?>
      <label class="me-3"><input type="checkbox" name="salon_id[]" value="<?= $s['id'] ?>"> <?= htmlspecialchars($s['name']) ?></label>
    <?php endforeach; ?>
  </div>
  <button class="btn btn-primary">Envoyer</button>
</form>
</div><?php require_once '../inc/footer.php'; ?>
