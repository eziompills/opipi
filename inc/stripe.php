<?php
require_once __DIR__.'/../vendor/autoload.php';
\Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET'] ?? 'sk_test_51RVvDpBMG6CM14TlO5bqFerL4u6Oy98CNIXmnPZ5Q4mWBHTa62JAxXAW4uzFwhwkgQ7zYxK65HuhPJZCgSaErDuk00j66ueqt7'); // remplacez par votre clé
?>