<?php
require_once '../inc/header.php';
require_role(['admin']);
$months=[];
for($i=11;$i>=0;$i--){
  $m=date('Y-m', strtotime("-$i months"));
  $months[$m]=0;
}
$stmt=$pdo->query("SELECT DATE_FORMAT(starts_at,'%Y-%m') as ym, COUNT(*) c FROM bookings GROUP BY ym");
foreach($stmt as $r){
  if(isset($months[$r['ym']])) $months[$r['ym']]=$r['c'];
}
$labels=json_encode(array_keys($months));
$data=json_encode(array_values($months));
?>
<h2>Réservations (12 derniers mois)</h2>
<canvas id="chart"></canvas>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const ctx=document.getElementById('chart');
new Chart(ctx,{type:'line',data:{labels:<?= $labels ?>,datasets:[{label:'Réservations',data:<?= $data ?>,fill:false}]},options:{scales:{y:{beginAtZero:true}}}});
</script>
<?php require_once '../inc/footer.php'; ?>
