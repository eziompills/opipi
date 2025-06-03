<?php
/**
 * API statistiques pour l'admin.
 *  {
 *    "totals": { "users": 123, "salons": 10, "bookings": 542 },
 *    "revenue": { "2025-01": 455.5, "2025-02": 612.3, ... }
 *  }
 */
require_once '../../inc/header.php';
require_role(['admin']);
header('Content-Type: application/json');

// Totaux rapides
$totals = [
  'users'    => (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
  'salons'   => (int) $pdo->query('SELECT COUNT(*) FROM salons')->fetchColumn(),
  'bookings' => (int) $pdo->query('SELECT COUNT(*) FROM bookings')->fetchColumn()
];

// Revenu mensuel global
$revStmt = $pdo->query("
  SELECT DATE_FORMAT(start,'%Y-%m') AS ym, SUM(s.price_cents)/100 AS euros
    FROM bookings b
    JOIN services s ON s.id = b.service_id
   WHERE b.payment_status = 'paid'
GROUP BY ym ORDER BY ym ASC
");
$revenue = [];
foreach ($revStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $revenue[$row['ym']] = (float)$row['euros'];
}

echo json_encode(['totals' => $totals, 'revenue' => $revenue]);
?>
