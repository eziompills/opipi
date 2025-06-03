<?php
require_once '../inc/config.php';
require_role(['staff']);
$staff_id=user()['id'];
$stmt=$pdo->prepare("SELECT starts_at as start, ends_at as end, (SELECT name FROM salons WHERE id= b.salon_id) as title
    FROM bookings b WHERE staff_id=?");
$stmt->execute([$staff_id]);
echo json_encode($stmt->fetchAll());
?>
