<?php
require_once '../inc/config.php';
require_role(['owner']);
require_once '../inc/header.php';
include 'sidebar.php';
echo '<div class="content-wrap">';
$salon_id=(int)($_GET['salon_id']??0);
$salon=$pdo->prepare("SELECT * FROM salons WHERE id=? AND owner_id=?");
$salon->execute([$salon_id,user()['id']]);
$salon=$salon->fetch();
if(!$salon) die('Accès refusé');

if($_SERVER['REQUEST_METHOD']==='POST'){
  $name=$_POST['name']; $city=$_POST['city']; $address=$_POST['address']; $desc=$_POST['description']; $cat=$_POST['category'];
  $pdo->prepare("UPDATE salons SET name=?, city=?, address=?, description=?, category=? WHERE id=?")
      ->execute([$name,$city,$address,$desc,$cat,$salon_id]);
  header("Location: profile.php?salon_id=$salon_id");
  exit;
}
?>
<h1>Profil salon</h1>
<form method="post" class="row g-3">
  <div class="col-md-6">
    <label class="form-label" for="profileName">Nom</label>
    <input id="profileName" name="name" value="<?= htmlspecialchars($salon['name']) ?>" class="form-control" required>
  </div>
  <div class="col-md-6">
    <label class="form-label" for="profileCity">Ville</label>
    <input id="profileCity" name="city" value="<?= htmlspecialchars($salon['city']) ?>" class="form-control" required>
  </div>
  <div class="col-12">
    <label class="form-label" for="profileAddress">Adresse</label>
    <input id="profileAddress" name="address" value="<?= htmlspecialchars($salon['address']) ?>" class="form-control">
  </div>
  <div class="col-md-6">
    <label class="form-label" for="profileCategory">Catégorie</label>
    <select name="category" class="form-select">
      <?php foreach(['barbershop'=>'Barbier','bio'=>'Bio','kids'=>'Kids','mixte'=>'Mixte','spa'=>'Spa'] as $k=>$v): ?>
        <option value="<?= $k ?>" <?= ($salon['category']==$k)?'selected':'' ?>><?= $v ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-12">
    <label class="form-label" for="profileDescription">Description</label>
    <textarea id="profileDescription" name="description" class="form-control"><?= htmlspecialchars($salon['description']) ?></textarea>
  </div>
  <div class="col-12"><button class="btn btn-success">Enregistrer</button></div>
</form>
</div><?php require_once '../inc/footer.php'; ?>
