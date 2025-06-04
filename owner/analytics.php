<?php
require_once '../inc/config.php';
require_role(['owner']);
require_once '../inc/header.php';
include 'sidebar.php';
echo '<div class="content-wrap">';
$salon_id=(int)($_GET['salon_id']??0);
$check=$pdo->prepare("SELECT id FROM salons WHERE id=? AND owner_id=?"); $check->execute([$salon_id,user()['id']]);
if(!$check->fetch()) die('Accès refusé');

$months=[];$now=new DateTime();
for($i=11;$i>=0;$i--){$m=$now->modify("-$i months")->format('Y-m');$months[$m]=0;$now=new DateTime();}
$stmt=$pdo->prepare("SELECT DATE_FORMAT(b.starts_at,'%Y-%m') ym, SUM(sv.price_cents)/100 ca
  FROM bookings b JOIN services sv ON sv.id=b.service_id
  WHERE b.salon_id=? AND b.status='done' GROUP BY ym"); $stmt->execute([$salon_id]);
foreach($stmt as $r){$months[$r['ym']]=$r['ca'];}

$stats=['confirmed'=>0,'done'=>0,'cancelled'=>0];
$cnt=$pdo->prepare("SELECT status, COUNT(*) c FROM bookings WHERE salon_id=? GROUP BY status");
$cnt->execute([$salon_id]); foreach($cnt as $row){$stats[$row['status']]=$row['c'];}
?>
<h1 class="section-title">Statistiques</h1>
<canvas id="caChart" height="120"></canvas>
<canvas id="pieChart" height="120" class="mt-4"></canvas>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('caChart'),{type:'bar',data:{labels:<?= json_encode(array_keys($months)) ?>,datasets:[{label:'CA €',data:<?= json_encode(array_values($months)) ?>}]},options:{scales:{y:{beginAtZero:true}}}});
new Chart(document.getElementById('pieChart'),{type:'doughnut',data:{labels:['Confirmés','Terminés','Annulés'],datasets:[{data:[<?= $stats['confirmed'] ?>,<?= $stats['done'] ?>,<?= $stats['cancelled'] ?>]}]}});
</script>
</div><?php require_once '../inc/footer.php'; ?>
