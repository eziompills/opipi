<?php
require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/stripe.php';

// Vérifier que l'ID de session existe
if (!isset($_GET['session_id']) || empty($_GET['session_id'])) {
    echo "Session invalide.";
    exit;
}

$session_id = $_GET['session_id'];

try {
    \Stripe\Stripe::setApiKey('sk_test_51RVvDpBMG6CM14TlO5bqFerL4u6Oy98CNIXmnPZ5Q4mWBHTa62JAxXAW4uzFwhwkgQ7zYxK65HuhPJZCgSaErDuk00j66ueqt7');
    $session = \Stripe\Checkout\Session::retrieve($session_id);

    // Récupérer la réservation associée (stockée dans metadata au moment de la création)
    $booking_id = $session->metadata->booking_id;

    // Vérifier que la session est payée
    if ($session->payment_status === 'paid') {
        // Mettre à jour la réservation en base pour marquer l'acompte comme payé
        $stmt = $pdo->prepare("UPDATE bookings SET payment_status = 'paid' WHERE id = ?");
        $stmt->execute([$booking_id]);

        echo "Paiement réussi pour la réservation n°" . htmlspecialchars($booking_id) . ".";
    } else {
        echo "Le paiement n'a pas été validé.";
    }
} catch (\Stripe\Exception\ApiErrorException $e) {
    echo "Erreur Stripe: " . htmlspecialchars($e->getMessage());
    exit;
}
?>
