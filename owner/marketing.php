<?php
require_once '../inc/config.php';
require_role(['owner']);
require_once '../inc/header.php';
include 'sidebar.php';
echo '<div class="content-wrap">';
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
  <div class="mb-3">
    <label for="mkSubject">Sujet</label>
    <input id="mkSubject" name="subject" class="form-control" required>
  </div>
  <div class="mb-3">
    <label for="mkMessage">Message (HTML autorisé)</label>
    <textarea id="mkMessage" name="message" class="form-control" rows="6"></textarea>
  </div>
  <div class="mb-3">
    <span class="d-block mb-1">Salons cibles</span>
    <?php foreach($my_salons as $s): ?>
      <label class="me-3"><input type="checkbox" name="salon_id[]" value="<?= $s['id'] ?>"> <?= htmlspecialchars($s['name']) ?></label>
    <?php endforeach; ?>
  </div>
  <button class="btn btn-primary">Envoyer</button>
</form>
</div><?php require_once '../inc/footer.php'; ?>
