<?php
/**
 * Gestion rapide des services du salon
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
    $price = (float)($_POST['price'] ?? 0)*100; // convert to cents
    if(isset($_POST['service_id']) && $_POST['service_id']){
        // update
        $stmt=$pdo->prepare('UPDATE services SET name=?, price_cents=? WHERE id=? AND salon_id=?');
        $stmt->execute([$name,$price,$_POST['service_id'],$salon_id]);
        set_flash('Service mis à jour ✅','success');
    }else{
        // insert
        $stmt=$pdo->prepare('INSERT INTO services (salon_id,name,price_cents) VALUES (?,?,?)');
        $stmt->execute([$salon_id,$name,$price]);
        set_flash('Service ajouté ✅','success');
    }
    header('Location: services.php');exit;
}

// Fetch services
$services=$pdo->prepare('SELECT * FROM services WHERE salon_id=? ORDER BY id DESC');
$services->execute([$salon_id]);

$editing = null;
if(isset($_GET['edit'])){
    $e=$pdo->prepare('SELECT * FROM services WHERE id=? AND salon_id=?');
    $e->execute([$_GET['edit'],$salon_id]);
    $editing=$e->fetch(PDO::FETCH_ASSOC);
}
?>
<h1 class="mb-4">Mes services</h1>

<!-- Form -->
<form method="post" class="card shadow-sm border-0 p-4 mb-4" style="max-width:600px">
  <?php if($editing): ?>
    <input type="hidden" name="service_id" value="<?= $editing['id'] ?>">
  <?php endif; ?>
  <div class="mb-3">
    <label class="form-label">Nom du service</label>
    <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($editing['name']??'') ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Prix (€)</label>
    <input type="number" name="price" step="0.01" min="0" class="form-control" required value="<?= isset($editing['price_cents'])?number_format($editing['price_cents']/100,2,'.',''):'' ?>">
  </div>
  <button class="btn btn-primary"><?= $editing?'Mettre à jour':'Ajouter' ?></button>
  <?php if($editing): ?><a href="services.php" class="btn btn-link">Annuler</a><?php endif; ?>
</form>

<!-- List -->
<table class="table table-bordered">
  <thead><tr><th>Nom</th><th>Prix</th><th></th></tr></thead>
  <tbody>
    <?php foreach($services as $s): ?>
      <tr>
        <td><?= htmlspecialchars($s['name']) ?></td>
        <td><?= number_format($s['price_cents']/100,2,',',' ') ?> €</td>
        <td><a href="?edit=<?= $s['id'] ?>" class="btn btn-sm btn-outline-secondary">Éditer</a></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once '../inc/footer.php'; ?>
