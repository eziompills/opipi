<?php
require_once 'inc/config.php';
$booking_id=(int)($_GET['booking_id']??0);
if(!$booking_id) die('id');
$stmt=$pdo->prepare("SELECT b.*, s.name AS salon, sv.name AS service FROM bookings b
  JOIN salons s ON s.id=b.salon_id
  JOIN services sv ON sv.id=b.service_id
  WHERE b.id=?");
$stmt->execute([$booking_id]);
$b=$stmt->fetch();
if(!$b) die('not found');

header('Content-Type:text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename=booking_'.$booking_id.'.ics');

$uid = $booking_id.'_'.$_SERVER['SERVER_NAME'];
$dtstart = date('Ymd\THis', strtotime($b['starts_at']));
$dtend   = date('Ymd\THis', strtotime($b['ends_at']));
echo "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//ôplani//FR\r\n";
echo "BEGIN:VEVENT\r\nUID:$uid\r\nDTSTAMP:$dtstart\r\n";
echo "DTSTART:$dtstart\r\nDTEND:$dtend\r\n";
echo "SUMMARY:".$b['service']." chez ".$b['salon']."\r\n";
echo "END:VEVENT\r\nEND:VCALENDAR";
?>
