<?php
require_once 'inc/config.php';
require_once 'inc/stripe.php';
$sid=$_GET['sid']??'';
$session=\Stripe\Checkout\Session::retrieve($sid);
if($session && $session->payment_status=='paid'){
  $pdo->prepare("UPDATE bookings SET payment_status='paid' WHERE stripe_session=?")->execute([$sid]);
}
header('Location: my_bookings.php?msg=Paiement+confirmé');
?>