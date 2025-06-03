<?php
require_once 'inc/header.php';
require_once 'inc/auth.php';
require_login();

$stmt = $pdo->prepare("
    SELECT b.*, b.payment_status AS payment_status,
           s.name   AS salon,
           sv.name  AS service,
           (SELECT 1 FROM reviews r WHERE r.booking_id = b.id LIMIT 1) AS reviewed
    FROM bookings b
    JOIN salons   s  ON s.id  = b.salon_id
    JOIN services sv ON sv.id = b.service_id
    WHERE b.customer_id = ?
    ORDER BY b.starts_at DESC
");
$stmt->execute([user()['id']]);
$bookings = $stmt->fetchAll();
?>
<h1 class="mb-4">Mes réservations</h1>

<table id="myBookingsTable" class="table table-hover">
  <thead class="table-light">
    <tr>
      <th>Date</th>
      <th>Salon</th>
      <th>Service</th>
      <th>Statut</th>
      <th>Acompte</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($bookings as $b): ?>
    <?php
      $canCancel = ($b['status']=='confirmed') && (strtotime($b['starts_at']) - time() > 86400);
      $startsAt  = date('d/m/Y H:i', strtotime($b['starts_at']));
    ?>
    <tr>
      <td><?= $startsAt ?></td>
      <td><?= htmlspecialchars($b['salon']) ?></td>
      <td><?= htmlspecialchars($b['service']) ?></td>
      <td><?= ucfirst($b['status']) ?></td>
      <td>
        <?php if ($b['payment_status']=='paid'): ?>
          <span class="text-success">Payé</span>
        <?php else: ?>
          <a href="pay.php?booking_id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-success">Payer</a>
        <?php endif; ?>
      </td>
      <td>
        <?php if ($canCancel): ?>
          <a href="cancel_booking.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-danger me-1">Annuler</a>
        <?php endif; ?>
        <?php if (!$b['reviewed'] && $b['status']=='done'): ?>
          <a href="customer_review.php?booking_id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-primary">Noter</a>
        <?php elseif($b['reviewed']): ?>
          <span class="text-success">✓ Merci&nbsp;!</span>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<?php require_once 'inc/footer.php'; ?>