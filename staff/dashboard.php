<?php
require_once '../inc/header.php';
require_role(['staff']);
?>
<h1>Mon planning</h1>
<div id='calendar'></div>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var calendarEl=document.getElementById('calendar');
  var cal=new FullCalendar.Calendar(calendarEl,{
    initialView:'timeGridWeek',
    height:'auto',
    events:{
      url:'/staff/fetch_events.php',
      method:'GET'
    }
  });
  cal.render();
});
</script>
<?php require_once '../inc/footer.php'; ?>
