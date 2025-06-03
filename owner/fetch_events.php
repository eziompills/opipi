<?php
require_once '../inc/config.php';
$salon_id=(int)($_GET['salon_id']??0);
$stmt=$pdo->prepare("SELECT b.id, b.starts_at AS start, b.ends_at AS end, u.name staff, b.staff_id
  FROM bookings b JOIN users u ON u.id=b.staff_id WHERE b.salon_id=?");
$stmt->execute([$salon_id]);
$events=[];
foreach($stmt as $row){
  $color = sprintf('#%06X', crc32($row['staff_id']) & 0xffffff);
  $events[]=array('title'=>$row['staff'],'start'=>$row['start'],'end'=>$row['end'],'color'=>$color);
}
echo json_encode($events);
?>