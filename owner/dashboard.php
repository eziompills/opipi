<?php
require_once '../inc/header.php';
require_role(['owner']);

// Récupère le salon du propriétaire
$salonIdStmt = $pdo->prepare('SELECT id FROM salons WHERE owner_id = ? LIMIT 1');
$salonIdStmt->execute([user()['id']]);
$salon_id = $salonIdStmt->fetchColumn();

if(!$salon_id){
    echo '<p class="alert alert-warning">Vous n\'avez pas encore créé de salon.</p>';
    echo '<a class="btn btn-primary" href="salon_edit.php">Créer mon salon</a>';
    require_once '../inc/footer.php';
    exit;
}

/**
 * Statistiques rapides (totaux).
 */
$stats = [
    'bookings' => 0,
    'reviews'  => 0,
];
try {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM bookings WHERE salon_id = ?');
    $stmt->execute([$salon_id]);
    $stats['bookings'] = $stmt->fetchColumn();
} catch (\PDOException $e) {}

try {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM bookings b JOIN reviews r ON r.booking_id = b.id WHERE b.salon_id = ?');
    $stmt->execute([$salon_id]);
    $stats['reviews'] = $stmt->fetchColumn();
} catch (\PDOException $e) {
    if ($e->getCode() !== '42S02') { throw $e; }
}
?>
<h1 class="mb-4">Tableau de bord Pro</h1>

<!-- Actions rapides -->
<div class="row mb-4">
  <div class="col-md-3"><a href="salon_edit.php" class="btn btn-primary w-100">Éditer mon salon</a></div>
  <div class="col-md-3"><a href="services.php" class="btn btn-primary w-100">Mes services</a></div>
  <div class="col-md-3"><a href="staff.php" class="btn btn-primary w-100">Mon équipe</a></div>
  <div class="col-md-3"><a href="bookings.php" class="btn btn-primary w-100">Réservations</a></div>
</div>

<!-- Statistiques cartes -->
<div class="row">
  <div class="col-md-6">
    <div class="card shadow-sm border-0 mb-3">
      <div class="card-body">
        <h2 class="display-5"><?= htmlspecialchars($stats['bookings']) ?></h2>
        <p class="text-muted mb-0">Réservations totales</p>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card shadow-sm border-0 mb-3">
      <div class="card-body">
        <h2 class="display-5"><?= htmlspecialchars($stats['reviews']) ?></h2>
        <p class="text-muted mb-0">Avis reçus</p>
      </div>
    </div>
  </div>
</div>

<!-- Graphiques -->
<div class="row">
  <div class="col-12">
    <div class="card shadow-sm border-0 mb-4">
      <div class="card-body">
        <h5 class="card-title">Réservations (30 jours)</h5>
        <canvas id="chartBookings" height="80"></canvas>
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="card shadow-sm border-0 mb-4">
      <div class="card-body">
        <h5 class="card-title">Chiffre d'affaires (30 jours)</h5>
        <canvas id="chartRevenue" height="80"></canvas>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
/**
 * Construit un tableau de 30 dates (YYYY-MM-DD) du plus ancien au plus récent.
 */
const last30 = [...Array(30).keys()].map(i=>{
  const d = new Date();
  d.setDate(d.getDate()-29+i);
  return d.toISOString().slice(0,10);
});

fetch('api/stats.php')
  .then(r=>r.json())
  .then(data=>{
    // Jeu de données
    const bookings = last30.map(d=>data.bookings[d]||0);
    const revenue  = last30.map(d=>data.revenue[d]||0);

    // Réservations
    new Chart(document.getElementById('chartBookings').getContext('2d'),{
      type:'bar',
      data:{labels:last30,datasets:[{label:'Réservations',data:bookings}]},
      options:{responsive:true,maintainAspectRatio:false}
    });

    // Chiffre d'affaires
    new Chart(document.getElementById('chartRevenue').getContext('2d'),{
      type:'line',
      data:{labels:last30,datasets:[{label:'€',data:revenue,fill:false}]},
      options:{responsive:true,maintainAspectRatio:false}
    });
  })
  .catch(console.error);
</script>

<?php require_once '../inc/footer.php'; ?>
