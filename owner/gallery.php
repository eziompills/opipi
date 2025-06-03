<?php
require_once '../inc/header.php';
include 'sidebar.php';
echo '<div class="content-wrap">';
require_role(['owner']);
$salon_id=(int)($_GET['salon_id']??0);
$check=$pdo->prepare("SELECT * FROM salons WHERE id=? AND owner_id=?");
$check->execute([$salon_id,user()['id']]);
$salon=$check->fetch();
if(!$salon) die('Accès refusé');

$gallery_dir='uploads/s'.$salon_id;
if(!is_dir($gallery_dir)) mkdir($gallery_dir,0777,true);

if($_SERVER['REQUEST_METHOD']==='POST' && isset($_FILES['img'])){
  $tmp=$_FILES['img']['tmp_name'];
  $name='s'.$salon_id.'_'.time().'.jpg';
  move_uploaded_file($tmp, $gallery_dir.'/'.$name);
  header("Location: gallery.php?salon_id=$salon_id");
  exit;
}
$pics=array_filter(glob($gallery_dir.'/*'), 'is_file');
?>
<h1>Galerie – <?= htmlspecialchars($salon['name']) ?></h1>
<form method="post" enctype="multipart/form-data" class="mb-3">
  <input type="file" name="img" accept="image/*" required>
  <button class="btn btn-primary">Ajouter</button>
</form>
<div class="row g-3">
<?php foreach($pics as $img): ?>
  <div class="col-md-3"><img src="/<?= $img ?>" class="img-fluid rounded"></div>
<?php endforeach; ?>
</div>
</div><?php require_once '../inc/footer.php'; ?>
