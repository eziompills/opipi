<?php
require_once '../inc/config.php';
require_role(['owner']);
header('Content-Type:text/csv');
header('Content-Disposition: attachment; filename="factures_'.date('Ymd').'.csv"');
$out=fopen('php://output','w');
fputcsv($out,['ID','Date','Client','Service','Montant €','Statut']);
$salon_id=(int)($_GET['salon_id']??0);
$stmt=$pdo->prepare("SELECT b.id, b.starts_at, u.name, sv.name, sv.price_cents/100, b.payment_status
  FROM bookings b JOIN users u ON u.id=b.customer_id JOIN services sv ON sv.id=b.service_id
  WHERE b.salon_id=?");
$stmt->execute([$salon_id]);
foreach($stmt as $row){fputcsv($out,$row);}
fclose($out);
exit;
?>