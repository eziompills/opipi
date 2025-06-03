<?php
// Laisser un avis sur une réservation terminée
require_once 'inc/config.php';
require_once 'inc/auth.php';
require_role(['customer']);

$booking_id = (int)($_GET['booking_id'] ?? 0);

// Vérifier la réservation
$stmt = $pdo->prepare("""
    SELECT b.*, s.name AS salon_name
    FROM bookings b
    JOIN salons s ON s.id = b.salon_id
    WHERE b.id = ? AND b.customer_id = ? AND b.status = 'done'
""");
$stmt->execute([$booking_id, user()['id']]);
$booking = $stmt->fetch();
if(!$booking){
    die('Réservation invalide ou non terminée');
}

// Si un avis existe déjà, rediriger
$check = $pdo->prepare('SELECT id FROM reviews WHERE booking_id = ?');
$check->execute([$booking_id]);
if($check->fetchColumn()){
    header('Location: /my_bookings.php?msg=Avis+déjà+déposé');
    exit;
}

// Soumission du formulaire
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $rating  = (int)($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');
    if($rating < 1 || $rating > 5){
        $error = 'La note doit être comprise entre 1 et 5';
    }else{
        $ins = $pdo->prepare('INSERT INTO reviews (booking_id,rating,comment) VALUES (?,?,?)');
        $ins->execute([$booking_id,$rating,$comment]);
        header('Location: /my_bookings.php?msg=Merci+pour+votre+avis');
        exit;
    }
}
?>
<?php require_once 'inc/header.php'; ?>
<div class="container py-5">
  <h1 class="mb-4">Laisser un avis pour <?= htmlspecialchars($booking['salon_name']) ?></h1>
  <?php if(isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
  <form method="post" class="w-100" style="max-width:600px">
    <div class="mb-3">
      <label class="form-label">Note (1 à 5)</label>
      <select name="rating" class="form-select" required>
        <?php for($i=5;$i>=1;$i--): ?>
          <option value="<?= $i ?>"><?= $i ?></option>
        <?php endfor; ?>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Commentaire</label>
      <textarea name="comment" rows="4" class="form-control"></textarea>
    </div>
    <button class="btn btn-primary">Envoyer</button>
  </form>
</div>
<?php require_once 'inc/footer.php'; ?>
