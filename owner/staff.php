<?php
/**
 * Gestion simple du staff
 */
require_once '../inc/config.php';
require_role(['owner']);
require_once '../inc/header.php';

$salonIdStmt = $pdo->prepare('SELECT id FROM salons WHERE owner_id = ? LIMIT 1');
$salonIdStmt->execute([user()['id']]);
$salon_id = $salonIdStmt->fetchColumn();

if(!$salon_id){
    echo '<p class="alert alert-warning">Créez d\'abord votre salon.</p>';
    echo '<a class="btn btn-primary" href="salon_edit.php">Créer mon salon</a>';
    require_once '../inc/footer.php';
    exit;
}

// Handle POST add/update
if($_SERVER['REQUEST_METHOD']==='POST'){
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    if(isset($_POST['staff_id']) && $_POST['staff_id']){
        $stmt=$pdo->prepare('UPDATE staff SET name=?, email=? WHERE id=? AND salon_id=?');
        $stmt->execute([$name,$email,$_POST['staff_id'],$salon_id]);
        set_flash('Membre mis à jour ✅','success');
    }else{
        $stmt=$pdo->prepare('INSERT INTO staff (salon_id,name,email) VALUES (?,?,?)');
        $stmt->execute([$salon_id,$name,$email]);
        set_flash('Membre ajouté ✅','success');
    }
    header('Location: staff.php');exit;
}

$staff=$pdo->prepare('SELECT * FROM staff WHERE salon_id=? ORDER BY id DESC');
$staff->execute([$salon_id]);

$editing = null;
if(isset($_GET['edit'])){
    $e=$pdo->prepare('SELECT * FROM staff WHERE id=? AND salon_id=?');
    $e->execute([$_GET['edit'],$salon_id]);
    $editing=$e->fetch(PDO::FETCH_ASSOC);
}
?>
<h1 class="mb-4">Mon équipe</h1>

<form method="post" class="card shadow-sm border-0 p-4 mb-4" style="max-width:600px">
  <?php if($editing): ?>
    <input type="hidden" name="staff_id" value="<?= $editing['id'] ?>">
  <?php endif; ?>
  <div class="mb-3">
    <label class="form-label">Nom</label>
    <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($editing['name']??'') ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Email (optionnel)</label>
    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($editing['email']??'') ?>">
  </div>
  <button class="btn btn-primary"><?= $editing?'Mettre à jour':'Ajouter' ?></button>
  <?php if($editing): ?><a href="staff.php" class="btn btn-link">Annuler</a><?php endif; ?>
</form>

<table class="table table-bordered">
  <thead><tr><th>Nom</th><th>Email</th><th></th></tr></thead>
  <tbody>
    <?php foreach($staff as $m): ?>
      <tr>
        <td><?= htmlspecialchars($m['name']) ?></td>
        <td><?= htmlspecialchars($m['email']) ?></td>
        <td><a href="?edit=<?= $m['id'] ?>" class="btn btn-sm btn-outline-secondary">Éditer</a></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once '../inc/footer.php'; ?>
