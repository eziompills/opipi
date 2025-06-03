<?php
require_once '../inc/header.php';
require_role(['owner']);
$salon_id=(int)($_GET['salon_id']??0);
$check=$pdo->prepare("SELECT * FROM salons WHERE id=? AND owner_id=?");
$check->execute([$salon_id,user()['id']]);
$salon=$check->fetch();
if(!$salon) die('Accès refusé');

if($_SERVER['REQUEST_METHOD']==='POST'){
  $pdo->prepare("DELETE FROM salon_hours WHERE salon_id=?")->execute([$salon_id]);
  foreach($_POST['open'] as $day=>$open){
    $close=$_POST['close'][$day];
    if($open && $close){
      $pdo->prepare("INSERT INTO salon_hours (salon_id,day_of_week,open_time,close_time) VALUES (?,?,?,?)")
          ->execute([$salon_id,$day,$open,$close]);
    }
  }
  header("Location: salon_hours.php?salon_id=$salon_id");
  exit;
}
$hours=$pdo->prepare("SELECT * FROM salon_hours WHERE salon_id=?");
$hours->execute([$salon_id]);
$h=[];
foreach($hours as $row){$h[$row['day_of_week']]=[$row['open_time'],$row['close_time']];}
$days=['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'];
?>
<h1>Horaires - <?= htmlspecialchars($salon['name']) ?></h1>
<form method="post">
<table class="table">
<thead><tr><th>Jour</th><th>Ouverture</th><th>Fermeture</th></tr></thead>
<tbody>
<?php for($d=0;$d<7;$d++): ?>
<tr>
<td><?= $days[$d] ?></td>
<td><input type="time" name="open[<?= $d ?>]" value="<?= $h[$d][0]??'' ?>"></td>
<td><input type="time" name="close[<?= $d ?>]" value="<?= $h[$d][1]??'' ?>"></td>
</tr>
<?php endfor; ?>
</tbody>
</table>
<button class="btn btn-success">Enregistrer</button>
</form>
<?php require_once '../inc/footer.php'; ?>
