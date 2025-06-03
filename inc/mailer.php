<?php
function send_email($to, $subject, $html){
    // simple wrapper around PHP mail()
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: oplani <no-reply@oplani.fr>\r\n";
    return mail($to, $subject, $html, $headers);
}
?>


/**
 * Envoie l'email au client et au staff lors d'une nouvelle réservation
 */
function send_booking_emails(string $customer_email, string $staff_email, string $salon_name, string $service_name, string $start, string $end): void {
    $subject = "Votre réservation chez $salon_name";
    $html = "<p>Votre réservation pour le service <strong>$service_name</strong> est confirmée.</p>
             <p>Date : ".date('d/m/Y H:i', strtotime($start))." – ".date('H:i', strtotime($end))."</p>
             <p>Merci pour votre confiance.</p>";
    send_email($customer_email, $subject, $html);

    $subjectStaff = "Nouvelle réservation – $salon_name";
    $htmlStaff = "<p>Un client a réservé <strong>$service_name</strong>.</p>
                  <p>Date : ".date('d/m/Y H:i', strtotime($start))." – ".date('H:i', strtotime($end))."</p>";
    send_email($staff_email, $subjectStaff, $htmlStaff);
}
