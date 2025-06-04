<?php
// Inclure le header (charge Bootstrap, navbar, start session, user(), etc.)
require_once '../inc/header.php';

$service_id = (int)($_GET['service_id'] ?? ($_POST['service_id'] ?? 0));
$user = user(); // récupère l'utilisateur connecté ou null

// Récupérer les informations du service et du salon
$stmt = $pdo->prepare('
    SELECT s.*, sal.id AS salon_id, sal.name AS salon_name
    FROM services s
    JOIN salons sal ON sal.id = s.salon_id
    WHERE s.id = ? 
    LIMIT 1
');
$stmt->execute([$service_id]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    echo '<div class="container my-5"><div class="alert alert-warning">Service introuvable.</div></div>';
    require_once '../inc/footer.php';
    exit;
}

// Traitement AJAX pour récupérer les créneaux d'une date donnée
if (isset($_GET['slots']) && isset($_GET['date'])) {
    header('Content-Type: application/json');

    $date = $_GET['date'];
    $salon_id = $service['salon_id'];
    $duration = intval($service['duration']);

    // Obtenir les horaires d'ouverture pour le jour
    $dayOfWeek = date('N', strtotime($date)); // 1 (lundi) à 7 (dimanche)
    $hStmt = $pdo->prepare('
        SELECT open_time, close_time 
        FROM salon_hours 
        WHERE salon_id = ? AND day_of_week = ?
        LIMIT 1
    ');
    $hStmt->execute([$salon_id, $dayOfWeek]);
    $hours = $hStmt->fetch(PDO::FETCH_ASSOC);
    if (!$hours) {
        echo json_encode([]);
        exit;
    }

    // Générer créneaux
    $start = new DateTime("$date {$hours['open_time']}");
    $end = new DateTime("$date {$hours['close_time']}");
    $interval = new DateInterval('PT15M');
    $slots = [];

    // Récupérer réservations existantes sur la date pour ce service
    $bStmt = $pdo->prepare('
        SELECT starts_at, ends_at 
        FROM bookings
        WHERE service_id = ? AND DATE(starts_at) = ?
    ');
    $bStmt->execute([$service_id, $date]);
    $existing = $bStmt->fetchAll(PDO::FETCH_ASSOC);

    while ($start < $end) {
        $slotStart = $start->format('Y-m-d H:i:s');
        $slotEndDT = clone $start;
        $slotEndDT->modify("+{$duration} minutes");
        if ($slotEndDT > $end) {
            break;
        }

        // Vérifier chevauchement
        $free = true;
        foreach ($existing as $b) {
            $bStart = new DateTime($b['starts_at']);
            $bEnd = new DateTime($b['ends_at']);
            if ($start < $bEnd && $slotEndDT > $bStart) {
                $free = false;
                break;
            }
        }
        if ($free) {
            $slots[] = $start->format('H:i');
        }
        $start->add($interval);
    }

    echo json_encode($slots);
    exit;
}

// Traitement du POST pour enregistrer la réservation
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Gestion invité ou utilisateur connecté
    if (!$user) {
        $guest_name  = trim($_POST['guest_name'] ?? '');
        $guest_email = trim($_POST['guest_email'] ?? '');
        if ($guest_name === '' || $guest_email === '' || !filter_var($guest_email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Veuillez fournir un nom et un e-mail valides.';
        } else {
            $uStmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
            $uStmt->execute([$guest_email]);
            $uData = $uStmt->fetch(PDO::FETCH_ASSOC);
            if ($uData) {
                $user_id = $uData['id'];
            } else {
                $pw_hash = password_hash(bin2hex(random_bytes(8)), PASSWORD_BCRYPT);
                $iStmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?,?,?,\'guest\')');
                $iStmt->execute([$guest_name, $guest_email, $pw_hash]);
                $user_id = $pdo->lastInsertId();
            }
            $_SESSION['user_id'] = $user_id;
            $user = user();
        }
    } else {
        $user_id = $user['id'];
    }

    if (!$error) {
        $date = $_POST['date'] ?? '';
        $time = $_POST['time'] ?? '';
        if (!$date || !$time) {
            $error = 'Veuillez sélectionner une date et un créneau.';
        } else {
            $starts_at = "$date $time:00";
            $ends_at = date('Y-m-d H:i:s', strtotime("+{$service['duration']} minutes", strtotime($starts_at)));

            // Insérer la réservation
            $bInsert = $pdo->prepare('
                INSERT INTO bookings (customer_id, salon_id, service_id, starts_at, ends_at, status, payment_status, created_at)
                VALUES (?, ?, ?, ?, ?, \'pending\', \'unpaid\', NOW())
            ');
            $bInsert->execute([$user['id'], $service['salon_id'], $service_id, $starts_at, $ends_at]);
            $success = "Réservation confirmée pour le $date à $time.";
        }
    }
}
?>

<div class="container my-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="card-title mb-4">Réserver : <?= htmlspecialchars($service['name']) ?></h3>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php elseif ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="post" class="row g-3">
                <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                <?php if (!$user): ?>
                    <div class="col-12">
                        <label class="form-label" for="ownerGuestName">Votre nom</label>
                        <input type="text" id="ownerGuestName" name="guest_name" class="form-control" required value="<?= htmlspecialchars($guest_name ?? '') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="ownerGuestEmail">Votre e-mail</label>
                        <input type="email" id="ownerGuestEmail" name="guest_email" class="form-control" required value="<?= htmlspecialchars($guest_email ?? '') ?>">
                    </div>
                <?php endif; ?>
                <div class="col-md-6">
                    <label class="form-label" for="ownerDate">Date</label>
                    <input type="date" id="ownerDate" name="date" class="form-control" required min="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="ownerSlots">Créneau disponible</label>
                    <select id="ownerSlots" name="time" class="form-select" required>
                        <option value="">Sélectionnez une date</option>
                    </select>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary">Confirmer la réservation</button>
                    <a href="salon.php?id=<?= $service['salon_id'] ?>" class="btn btn-link">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById("ownerDate").addEventListener("change", function() {
    const dateVal = this.value;
    const serviceId = <?= $service_id ?>;
    const select = document.getElementById("ownerSlots");
    select.innerHTML = '<option>Chargement...</option>';
    fetch(`book_service.php?slots=1&service_id=${serviceId}&date=${dateVal}`)
        .then(res => res.json())
        .then(data => {
            select.innerHTML = '';
            if (!data.length) {
                select.innerHTML = '<option>Aucun créneau disponible</option>';
                return;
            }
            data.forEach(time => {
                const opt = document.createElement("option");
                opt.value = time;
                opt.textContent = time;
                select.appendChild(opt);
            });
        })
        .catch(() => {
            select.innerHTML = '<option>Erreur de chargement</option>';
        });
});
</script>

<?php
// Inclure le footer (ferme body, html et charge JS)
require_once '../inc/footer.php';
?>