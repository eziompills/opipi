<?php
// Réservation d'un service
require_once 'inc/config.php';
require_once 'inc/mailer.php';
require_once 'inc/auth.php';

// Seuls les clients peuvent réserver
require_login();

$salon_id   = (int)($_POST['salon_id']   ?? 0);
$service_id = (int)($_POST['service_id'] ?? 0);
$start      = $_POST['start'] ?? '';
$end        = $_POST['end']   ?? '';

if(!$salon_id || !$service_id || !$start || !$end){
    die('Paramètres manquants');
}

// Vérifier que le créneau n’est pas déjà pris
$overlap = $pdo->prepare("""
    SELECT 1 FROM bookings 
    WHERE salon_id=? 
      AND ((starts_at BETWEEN ? AND ?) OR (ends_at BETWEEN ? AND ?))
      AND status IN ('pending','confirmed')
    LIMIT 1
""");
$overlap->execute([$salon_id,$start,$end,$start,$end]);
if($overlap->fetchColumn()){
    die('Créneau déjà réservé, merci de choisir un autre horaire.');
}

try{
    $pdo->beginTransaction();

    // Choisir un membre du staff disponible au hasard
    $staffStmt = $pdo->prepare("""
        SELECT u.id,e.day_of_week 
        FROM staff s 
        JOIN users u ON u.id = s.user_id
        LEFT JOIN staff_availability e ON e.staff_id = u.id AND e.day_of_week = WEEKDAY(?)
        WHERE s.salon_id = ?
        ORDER BY RAND()
        LIMIT 1
    """);
    $staffStmt->execute([$start, $salon_id]);
    $staff_id = $staffStmt->fetchColumn();
    if(!$staff_id){
        throw new Exception('Aucun staff disponible');
    }

    // Enregistrer la réservation
    $insert = $pdo->prepare("""
        INSERT INTO bookings
          (salon_id, service_id, customer_id, staff_id, starts_at, ends_at, status)
        VALUES (?,?,?,?,?,?, 'confirmed')
    """);
    $insert->execute([$salon_id,$service_id,user()['id'],$staff_id,$start,$end]);
    $booking_id = $pdo->lastInsertId();

    $pdo->commit();
}catch(Exception $e){
    $pdo->rollBack();
    die('Erreur : '.$e->getMessage());
}

// Récupéra// Récupération des infos pour les emails
$salonStmt = $pdo->prepare("SELECT name FROM salons WHERE id = ?");
$salonStmt->execute([$salon_id]);
$salon_name = $salonStmt->fetchColumn();

$serviceStmt = $pdo->prepare("SELECT name FROM services WHERE id = ?");
$serviceStmt->execute([$service_id]);
$service_name = $serviceStmt->fetchColumn();

$staffStmt2 = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$staffStmt2->execute([$staff_id]);
$staff_email = $staffStmt2->fetchColumn();

$customer_email = user()['email'];

// Envoi des notifications (exemple : mail() ou librairie SMTP)
send_booking_emails($customer_email, $staff_email, $salon_name, $service_name, $start, $end);

// Redirection finale
    header('Location: pay.php?booking_id=' . $booking_id);
exit;
?>