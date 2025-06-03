<?php
/**
 * Page publique du salon : détail complet avec horaires, services, avis.
 * Corrige le lien de réservation pour passer service_id.
 */
require_once 'inc/header.php';

$id = (int)($_GET['id'] ?? 0);
$salon = null;
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM salons WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $salon = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$salon) {
    echo '<p class="alert alert-warning">Salon introuvable.</p>';
    require_once 'inc/footer.php';
    exit;
}

// Horaires du salon
$hoursStmt = $pdo->prepare('SELECT day_of_week, open_time, close_time FROM salon_hours WHERE salon_id = ? ORDER BY day_of_week ASC');
$hoursStmt->execute([$id]);
$hours = $hoursStmt->fetchAll(PDO::FETCH_ASSOC);

// Services
$servicesStmt = $pdo->prepare('SELECT * FROM services WHERE salon_id = ? ORDER BY name ASC');
$servicesStmt->execute([$id]);
$services = $servicesStmt->fetchAll(PDO::FETCH_ASSOC);

// Avis et note moyenne
$ratingStmt = $pdo->prepare('
    SELECT AVG(r.rating) AS avg_rating, COUNT(r.id) AS total_reviews
      FROM reviews r
      JOIN bookings b ON r.booking_id = b.id
     WHERE b.salon_id = ?
');
$ratingStmt->execute([$id]);
$ratingData = $ratingStmt->fetch(PDO::FETCH_ASSOC);

$reviewsStmt = $pdo->prepare('
    SELECT r.rating, r.comment, u.name AS client, r.created_at
      FROM reviews r
      JOIN bookings b ON r.booking_id = b.id
      JOIN users u ON b.customer_id = u.id
     WHERE b.salon_id = ?
     ORDER BY r.created_at DESC
     LIMIT 5
');
$reviewsStmt->execute([$id]);
$reviews = $reviewsStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container my-5">
  <div class="row gx-4">
    <!-- Détail salon -->
    <div class="col-lg-8">
      <div class="card mb-4 shadow-sm">
        <?php if ($salon['logo_url']): ?>
          <img src="<?= htmlspecialchars($salon['logo_url']) ?>" class="card-img-top" alt="Logo <?= htmlspecialchars($salon['name']) ?>">
        <?php endif; ?>
        <div class="card-body">
          <h2 class="card-title"><?= htmlspecialchars($salon['name']) ?></h2>
          <p class="text-muted"><?= htmlspecialchars($salon['city']) ?> – <?= nl2br(htmlspecialchars($salon['address'])) ?></p>
          <?php if ($salon['description']): ?>
            <p><?= nl2br(htmlspecialchars($salon['description'])) ?></p>
          <?php endif; ?>
          <?php if ($ratingData['total_reviews'] > 0): ?>
            <p>
              <strong>Note moyenne : </strong>
              <?= number_format($ratingData['avg_rating'], 1) ?>/5 (<?= htmlspecialchars($ratingData['total_reviews']) ?> avis)
            </p>
          <?php else: ?>
            <p class="text-muted">Aucun avis pour le moment.</p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Horaires -->
      <div class="card mb-4 shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Horaires d'ouverture</h5>
          <table class="table table-striped mb-0">
            <thead><tr><th>Jour</th><th>Ouverture</th><th>Fermeture</th></tr></thead>
            <tbody>
              <?php
              $days = ['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche'];
              $hoursByDay = [];
              foreach ($hours as $h) {
                  $hoursByDay[$h['day_of_week']] = $h;
              }
              for ($d = 1; $d <= 7; $d++): 
                  if (isset($hoursByDay[$d])) {
                      $open = date('H:i', strtotime($hoursByDay[$d]['open_time']));
                      $close = date('H:i', strtotime($hoursByDay[$d]['close_time']));
                  } else {
                      $open = $close = 'Fermé';
                  }
              ?>
                <tr>
                  <td><?= $days[$d-1] ?></td>
                  <td><?= $open ?></td>
                  <td><?= $close ?></td>
                </tr>
              <?php endfor; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Services -->
      <div class="card mb-4 shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Services proposés</h5>
          <?php if (empty($services)): ?>
            <p class="text-muted">Aucun service disponible pour l'instant.</p>
          <?php else: ?>
            <div class="row">
              <?php foreach ($services as $s): ?>
                <div class="col-md-6 mb-3">
                  <div class="card h-100">
                    <div class="card-body">
                      <h6 class="card-title"><?= htmlspecialchars($s['name']) ?></h6>
                      <p class="card-text">
                        Durée : <?= intval($s['duration']) ?> min<br>
                        Prix : <?= number_format($s['price_cents']/100,2,',',' ') ?> €
                      </p>
                      <a href="book_service.php?service_id=<?= $s['id'] ?>" class="btn btn-primary btn-sm">Réserver</a>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Avis -->
      <div class="card mb-4 shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Avis récents</h5>
          <?php if (empty($reviews)): ?>
            <p class="text-muted">Soyez le premier à laisser un avis !</p>
          <?php else: ?>
            <?php foreach ($reviews as $r): ?>
              <div class="mb-3">
                <div>
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <?php if ($i <= $r['rating']): ?>
                      <span class="text-warning">&#9733;</span>
                    <?php else: ?>
                      <span class="text-muted">&#9733;</span>
                    <?php endif; ?>
                  <?php endfor; ?>
                </div>
                <p class="mb-1"><strong><?= htmlspecialchars($r['client']) ?></strong> <small class="text-muted"><?= date('d/m/Y', strtotime($r['created_at'])) ?></small></p>
                <p><?= nl2br(htmlspecialchars($r['comment'] ?? '')) ?></p>
                <hr>
              </div>
            <?php endforeach; ?>
            <a href="reviews.php?salon_id=<?= $id ?>" class="btn btn-sm btn-outline-secondary">Voir tous les avis</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <!-- Sidebar (optionnel : espace libre ou suggestion) -->
    <div class="col-lg-4">
      <!-- Vous pouvez ajouter une publicité ou autre contenu ici -->
    </div>
  </div>
</div>

<?php require_once 'inc/footer.php'; ?>
