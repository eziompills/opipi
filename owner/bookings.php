<?php
require_once '../inc/config.php';
require_role(['owner']);

// Récupère l’id du salon lié à l’owner connecté
$salonIdStmt = $pdo->prepare('SELECT id FROM salons WHERE owner_id = ? LIMIT 1');
$salonIdStmt->execute([user()['id']]);
$salon_id = $salonIdStmt->fetchColumn();

if (!$salon_id) {
    echo '<p class="alert alert-warning">Vous n\'avez pas encore créé de salon.</p>';
    require_once '../inc/footer.php';
    exit;
}

// Requête : on joint bookings → users (client) → salons
$stmt = $pdo->prepare('
  SELECT
    b.id,
    b.starts_at,
    b.ends_at,
    b.status,
    b.payment_status,
    u.name AS client_name,
    sal.name AS salon_name,
    s.name AS service_name
  FROM bookings b
  JOIN users u ON u.id = b.customer_id
  JOIN salons sal ON sal.id = b.salon_id
  JOIN services s ON s.id = b.service_id
  WHERE b.salon_id = ?
  ORDER BY b.created_at DESC
');
$stmt->execute([$salon_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 class="mb-4">Liste des réservations</h1>
<?php if (empty($bookings)): ?>
  <p class="text-muted">Aucune réservation pour le moment.</p>
<?php else: ?>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Client</th>
        <th>Service</th>
        <th>Début</th>
        <th>Fin</th>
        <th>Statut</th>
        <th>Paiement</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($bookings as $b): ?>
        <tr>
          <td><?= htmlspecialchars($b['id']) ?></td>
          <td><?= htmlspecialchars($b['client_name']) ?></td>
          <td><?= htmlspecialchars($b['service_name']) ?></td>
          <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($b['starts_at']))) ?></td>
          <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($b['ends_at']))) ?></td>
          <td><?= htmlspecialchars($b['status']) ?></td>
          <td><?= htmlspecialchars($b['payment_status']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<?php require_once '../inc/footer.php'; ?>
