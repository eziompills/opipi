<?php
require_once '../inc/header.php';
require_role(['owner']);
$salon_id=(int)($_GET['salon_id']??0);
$staff_id=(int)($_GET['staff_id']??0);
$staff=$pdo->prepare("SELECT * FROM staff s JOIN users u ON u.id=s.user_id WHERE s.salon_id=? AND s.user_id=?");
$staff->execute([$salon_id,$staff_id]);
$staff=$staff->fetch();
if(!$staff){ die('Invalide'); }

if($_SERVER['REQUEST_METHOD']==='POST'){
  $pdo->prepare("DELETE FROM staff_availability WHERE staff_id=?")->execute([$staff_id]);
  if(isset($_POST['slots'])){
    foreach($_POST['slots'] as $slot){
      list($dow,$start,$end)=explode('|',$slot);
      $pdo->prepare("INSERT INTO staff_availability (staff_id,day_of_week,start_time,end_time) VALUES (?,?,?,?)")
          ->execute([$staff_id,$dow,$start,$end]);
    }
  }
  header("Location: staff.php?salon_id={$salon_id}");
  exit;
}

$avail=$pdo->prepare("SELECT * FROM staff_availability WHERE staff_id=?");
$avail->execute([$staff_id]);
$avail=$avail->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_UNIQUE);
?>
<h1>Disponibilités de <?= htmlspecialchars($staff['name']) ?></h1>
<form method="post">
<table class="table">
<thead><tr><th>Jour</th><th>Plage horaire</th></tr></thead>
<tbody>
<?php
$days=['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'];
for($d=0;$d<7;$d++): ?>
<tr>
<td><?= $days[$d] ?></td>
<td>
  <input type="time" name="start[<?= $d ?>]" value="">
  <input type="time" name="end[<?= $d ?>]" value="">
</td>
</tr>
<?php endfor; ?>
</tbody>
</table>
<button class="btn btn-success">Enregistrer</button>
</form>
<?php require_once '../inc/footer.php'; ?>
