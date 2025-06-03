<?php
require_once '../inc/header.php';
require_role(['admin']);
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_FILES['csv'])){
  $f = fopen($_FILES['csv']['tmp_name'],'r');
  while(($row=fgetcsv($f,0,';'))!==false){
    list($owner_email,$name,$city,$address,$description)=$row;
    $owner=$pdo->prepare("SELECT id FROM users WHERE email=?");
    $owner->execute([$owner_email]);
    $owner_id=$owner->fetchColumn();
    if(!$owner_id) continue;
    $slug=strtolower(preg_replace('/[^a-z0-9]+/','-',$name));
    $stmt=$pdo->prepare("INSERT INTO salons (owner_id,name,city,address,description,slug) VALUES (?,?,?,?,?,?)");
    $stmt->execute([$owner_id,$name,$city,$address,$description,$slug]);
  }
  echo "<div class='alert alert-success'>Import terminé</div>";
}
?>
<h2>Import salons (CSV ; séparateur ; )</h2>
<form method="post" enctype="multipart/form-data">
  <input type="file" name="csv" accept=".csv" required>
  <button class="btn btn-primary">Importer</button>
</form>
<?php require_once '../inc/footer.php'; ?>
