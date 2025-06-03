<?php
require_once '../inc/config.php';
header('Content-Type:application/json');

$q=$_GET['q']??'';
$city=$_GET['city']??'';
$service=$_GET['service']??'';

$sql="SELECT DISTINCT s.id,s.name,s.city,s.lat,s.lng,s.slug,(SELECT AVG(r.rating) FROM reviews r JOIN bookings b ON b.id=r.booking_id WHERE b.salon_id=s.id) as rating
      FROM salons s
      LEFT JOIN services sv ON sv.salon_id=s.id WHERE 1";
$params=[];
if($q){$sql.=" AND s.name LIKE ?"; $params[]="%$q%";}
if($city){$sql.=" AND s.city=?"; $params[]=$city;}
if($service){$sql.=" AND sv.name=?"; $params[]=$service;}
$stmt=$pdo->prepare($sql);
$stmt->execute($params);
echo json_encode($stmt->fetchAll());
?>
