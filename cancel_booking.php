<?php
require_once 'inc/config.php';
require_role(['customer']);
$id=(int)($_GET['id']??0);
$booking=$pdo->prepare("SELECT * FROM bookings WHERE id=? AND customer_id=?");
$booking->execute([$id,user()['id']]);
$b=$booking->fetch();
if($b && $b['status']=='confirmed' && (strtotime($b['starts_at'])-time())>86400){
  $pdo->prepare("UPDATE bookings SET status='cancelled' WHERE id=?")->execute([$id]);
}
header('Location: my_bookings.php?msg=Réservation+annulée');
?>