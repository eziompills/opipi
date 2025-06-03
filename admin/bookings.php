<?php
require_once '../inc/header.php';
include 'sidebar.php';
echo '<div class="content-wrap">';
require_role(['admin']);
$bookings=$pdo->query("SELECT b.id,s.name salon, u.name client, b.starts_at, b.payment_status FROM bookings b JOIN salons s ON s.id=b.salon_id JOIN users u ON u.id=b.customer_id ORDER BY b.starts_at DESC");
?>
<h1 class="section-title">Réservations</h1>
<table class="table">
<thead><tr><th>ID</th><th>Salon</th><th>Client</th><th>Date</th><th>Payé</th></tr></thead>
<tbody>
<?php foreach($bookings as $b): ?>
<tr>
  <td><?= $b['id']?></td>
  <td><?= htmlspecialchars($b['salon'])?></td>
  <td><?= htmlspecialchars($b['client'])?></td>
  <td><?= $b['starts_at']?></td>
  <td><?= $b['payment_status']?></td>
</tr>
<?php endforeach;?>
</tbody></table>
</div><?php require_once '../inc/footer.php'; ?>
