<?php
require_once '../inc/config.php';
require_role(['owner']);
require_once '../inc/header.php';
include 'sidebar.php';
echo '<div class="content-wrap">';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $name=$_POST['name'];$city=$_POST['city'];$address=$_POST['address'];$desc=$_POST['description'];$cat=$_POST['category'];$lat=$_POST['lat'];$lng=$_POST['lng'];
  $slug=strtolower(preg_replace('/[^a-z0-9]+/','-',$name));
  $pdo->prepare("INSERT INTO salons (owner_id,name,city,address,description,slug,category,lat,lng) VALUES (?,?,?,?,?,?,?,?,?)")
      ->execute([user()['id'],$name,$city,$address,$desc,$slug,$cat,$lat,$lng]);
  header('Location: dashboard.php');exit;
}
?>
<h1>Ajouter un salon</h1>
<form method="post" class="row g-3">
  <div class="col-md-6"><label class="form-label">Nom</label><input name="name" class="form-control" required></div>
  <div class="col-md-6"><label class="form-label">Ville</label><input name="city" class="form-control" required></div>
  <div class="col-12"><label class="form-label">Adresse</label><input name="address" class="form-control"></div>
  <div class="col-md-6"><label class="form-label">Catégorie</label>
    <select name="category" class="form-select">
      <option value="barbershop">Barbier / Coiffeur</option>
      <option value="bio">Bio / Naturel</option>
      <option value="kids">Kids</option>
      <option value="mixte">Mixte</option>
      <option value="spa">Spa / Bien‑être</option>
    </select>
  </div>
  <div class="col-md-3"><label class="form-label">Latitude</label><input name="lat" class="form-control"></div>
  <div class="col-md-3"><label class="form-label">Longitude</label><input name="lng" class="form-control"></div>
  <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control"></textarea></div>
  <div class="col-12"><button class="btn btn-success">Créer</button></div>
</form>
</div><?php require_once '../inc/footer.php'; ?>
