<?php
require_once 'inc/header.php';
require_role(['customer']);

$salon_id  = (int)($_POST['salon_id']  ?? $_GET['salon_id']  ?? 0);
$service_id= (int)($_POST['service_id']?? $_GET['service_id']?? 0);

$salonStmt=$pdo->prepare("SELECT id,name FROM salons WHERE id=?");
$salonStmt->execute([$salon_id]);
$salon=$salonStmt->fetch();

$serviceStmt=$pdo->prepare("SELECT * FROM services WHERE id=? AND salon_id=?");
$serviceStmt->execute([$service_id,$salon_id]);
$service=$serviceStmt->fetch();

if(!$salon || !$service){
  http_response_code(404);
  require '404.php';
  exit;
}

$hoursStmt=$pdo->prepare("SELECT day_of_week,open_time,close_time FROM salon_hours WHERE salon_id=?");
$hoursStmt->execute([$salon_id]);
$hours=[];
$businessHours=[];
foreach($hoursStmt->fetchAll(PDO::FETCH_ASSOC) as $h){
  $businessHours[]=[
    'daysOfWeek'=>[(int)$h['day_of_week']],
    'startTime'=>substr($h['open_time'],0,5),
    'endTime'=>substr($h['close_time'],0,5)
  ];
}
?>
<div class="container py-4">
  <div class="card">
    <div class="card-body">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="home.php">Accueil</a></li>
          <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($salon['name']) ?></li>
        </ol>
      </nav>
      <h1 class="card-title"><?= htmlspecialchars($salon['name']) ?></h1>
      <h5 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($service['name']) ?> – <?= number_format($service['price_cents']/100,2) ?>€</h5>
      <div id="calendar"></div>
      <div class="mt-3 alert alert-info">Un acompte de 30% du prix total du service sera demandé lors de la réservation.</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var calendarEl = document.getElementById('calendar');
  var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'timeGridWeek',
    businessHours: <?= json_encode($businessHours) ?>,
    slotDuration: '<?= sprintf("%02d:00", $service['duration']) ?>',
    selectable: true,
    events: '/owner/fetch_events.php?salon_id=<?= $salon_id ?>',
    select: function(info) {
      if(confirm('Valider le '+info.start.toLocaleString()+' ?')){
        var f = document.createElement('form');
        f.method = 'post';
        f.action = 'book.php';
        ['salon_id','service_id','start','end'].forEach(function(n){
          var i = document.createElement('input');
          i.type = 'hidden'; i.name = n;
          f.appendChild(i);
        });
        f.salon_id.value = '<?= $salon_id ?>';
        f.service_id.value = '<?= $service_id ?>';
        f.start.value = info.startStr;
        f.end.value = info.endStr;
        document.body.appendChild(f);
        f.submit();
      }
    }
  });
  calendar.render();
});
</script>
    </div>
  </div>
<?php require_once 'inc/footer.php'; ?>