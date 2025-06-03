<?php
// pay.php

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/stripe.php';

if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit;
}

// 1. Récupération de l’ID de la réservation passée en GET
if (!isset($_GET['booking_id']) || !is_numeric($_GET['booking_id'])) {
    echo "Réservation invalide.";
    exit;
}
$booking_id = (int) $_GET['booking_id'];

// 2. Récupérer le détail de la réservation + le prix du service
$stmt = $pdo->prepare("
    SELECT b.id AS booking_id,
           b.service_id,
           s.name AS service_name,
           s.price_cents
    FROM bookings b
    JOIN services s ON s.id = b.service_id
    WHERE b.id = ? 
      AND b.customer_id = ?
      AND b.status = 'pending'
");
$stmt->execute([$booking_id, $_SESSION['user']['id']]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    echo "Réservation introuvable ou non disponible pour paiement.";
    exit;
}

// 3. Calcul de l’acompte : 30 % du prix total (en centimes)
$service_price_cents = (int) $booking['price_cents'];
if ($service_price_cents <= 0) {
    echo "Le prix du service est invalide.";
    exit;
}

$deposit_cents = (int) round($service_price_cents * 0.30);
if ($deposit_cents < 50) {
    $deposit_cents = 50;
}

try {
    \Stripe\Stripe::setApiKey('sk_test_51RVvDpBMG6CM14TlO5bqFerL4u6Oy98CNIXmnPZ5Q4mWBHTa62JAxXAW4uzFwhwkgQ7zYxK65HuhPJZCgSaErDuk00j66ueqt7');

    $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['PHP_SELF']);

    $success_url = $scheme . $host . $path . '/success.php?session_id={CHECKOUT_SESSION_ID}';
    $cancel_url  = $scheme . $host . $path . '/cancel.php?booking_id=' . $booking_id;

    $checkoutSession = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => $booking['service_name'],
                    'metadata' => [
                        'booking_id' => $booking_id
                    ],
                ],
                'unit_amount' => $deposit_cents,
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => $success_url,
        'cancel_url'  => $cancel_url,
    ]);

    // 4. Enregistrer le session ID et passer le statut à unpaid
    $pdo->prepare("UPDATE bookings SET stripe_session = ?, payment_status = 'unpaid' WHERE id = ?")
        ->execute([$checkoutSession->id, $booking_id]);

    // 5. Redirection vers Stripe Checkout
    header('Location: ' . $checkoutSession->url);
    exit;

} catch (\Stripe\Exception\ApiErrorException $e) {
    echo "Erreur Stripe : " . htmlspecialchars($e->getMessage());
    exit;
}
?>
