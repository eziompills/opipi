<?php
require_once '../inc/header.php';
include 'sidebar.php';
echo '<div class="content-wrap">';
require_role(['owner']);
$salon_id=(int)($_GET['salon_id']??0);
$check=$pdo->prepare("SELECT * FROM salons WHERE id=? AND owner_id=?");
$check->execute([$salon_id,user()['id']]);
$salon=$check->fetch();
if(!$salon){ die('Accès refusé'); }
$reviews=$pdo->prepare("SELECT r.*, u.name as customer, s.name as service FROM reviews r
  JOIN bookings b ON b.id=r.booking_id
  JOIN users u ON u.id=b.customer_id
  JOIN services s ON s.id=b.service_id
  WHERE b.salon_id=?
  ORDER BY r.created_at DESC");
$reviews->execute([$salon_id]);
$reviews=$reviews->fetchAll();
?>
<h1>Avis pour <?= htmlspecialchars($salon['name']) ?></h1>
<table class="table">
<thead><tr><th>Date</th><th>Client</th><th>Service</th><th>Note</th><th>Commentaire</th></tr></thead>
<tbody>
<?php foreach($reviews as $r): ?>
<tr>
<td><?= $r['created_at']?></td>
<td><?= htmlspecialchars($r['customer'])?></td>
<td><?= htmlspecialchars($r['service'])?></td>
<td><?= str_repeat('★',$r['rating'])?></td>
<td><?= nl2br(htmlspecialchars($r['comment']))?></td>
</tr>
<?php endforeach;?>
</tbody>
</table>
</div><?php require_once '../inc/footer.php'; ?>
