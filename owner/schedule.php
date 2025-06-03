<?php
require_once '../inc/header.php';
include 'sidebar.php';
echo '<div class="content-wrap">';
require_role(['owner']);
$salon_id=(int)($_GET['salon_id']??0);
$salon=$pdo->prepare("SELECT * FROM salons WHERE id=? AND owner_id=?");
$salon->execute([$salon_id,user()['id']]);
$salon=$salon->fetch();
if(!$salon){ http_response_code(404); echo 'Salon non trouvé'; require '../inc/footer.php'; exit; }
?>
<h1>Planning – <?= htmlspecialchars($salon['name']) ?></h1>
<div id="calendar"></div>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded',()=>{
 const el=document.getElementById('calendar');
 const cal=new FullCalendar.Calendar(el,{initialView:'timeGridWeek',events:'/owner/fetch_events.php?salon_id=<?= $salon_id ?>'});
 cal.render();
});
</script>
</div><?php require_once '../inc/footer.php'; ?>
