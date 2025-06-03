<?php
/**
 * API statistiques pour un propriétaire.
 * Renvoie les réservations et le chiffre d'affaires des 30 derniers jours.
 * Réponse JSON :
 *  {
 *    "bookings": { "2025-05-03": 4, ... },
 *    "revenue":  { "2025-05-03": 120.5, ... }
 *  }
 */
require_once '../../inc/header.php';
require_role(['owner']);
header('Content-Type: application/json');

$salonIdStmt = $pdo->prepare('SELECT id FROM salons WHERE owner_id = ? LIMIT 1');
$salonIdStmt->execute([user()['id']]);
$salon_id = $salonIdStmt->fetchColumn();
if (!$salon_id) {
    echo json_encode(['error' => 'no_salon']);
    exit;
}

// Réservations par jour
$bookingsStmt = $pdo->prepare("
  SELECT DATE(start) AS day, COUNT(*) AS total
    FROM bookings
   WHERE salon_id = ?
     AND start >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
GROUP BY day ORDER BY day ASC
");
$bookingsStmt->execute([$salon_id]);
$bookings = [];
foreach ($bookingsStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $bookings[$row['day']] = (int)$row['total'];
}

// Chiffre d'affaires par jour (en euros)
$revenueStmt = $pdo->prepare("
  SELECT DATE(start) AS day, SUM(s.price_cents)/100 AS euros
    FROM bookings b
    JOIN services s ON s.id = b.service_id
   WHERE b.salon_id = ?
     AND b.payment_status = 'paid'
     AND start >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
GROUP BY day ORDER BY day ASC
");
$revenueStmt->execute([$salon_id]);
$revenue = [];
foreach ($revenueStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $revenue[$row['day']] = (float)$row['euros'];
}

echo json_encode(['bookings' => $bookings, 'revenue' => $revenue]);
?>
