<?php
// book_service.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'inc/config.php'; // config initializes $pdo and session, and user()

// AJAX for slots
if (isset($_GET['slots']) && isset($_GET['service_id'], $_GET['date'])) {
    header('Content-Type: application/json');
    $service_id = (int) $_GET['service_id'];
    $date = $_GET['date'];

    // Fetch service duration and salon_id
    $stmt = $pdo->prepare("SELECT duration, salon_id FROM services WHERE id = ?");
    $stmt->execute([$service_id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$service) {
        echo json_encode([]);
        exit;
    }
    $salon_id = $service['salon_id'];
    $duration = (int) $service['duration'];

    // Fetch opening hours for that day
    $dayOfWeek = date('N', strtotime($date)); // 1-7
    $hStmt = $pdo->prepare("SELECT open_time, close_time FROM salon_hours WHERE salon_id = ? AND day_of_week = ?");
    $hStmt->execute([$salon_id, $dayOfWeek]);
    $hours = $hStmt->fetch(PDO::FETCH_ASSOC);
    if (!$hours) {
        echo json_encode([]);
        exit;
    }
    $start = new DateTime("$date {$hours['open_time']}");
    $end = new DateTime("$date {$hours['close_time']}");

    $interval = new DateInterval('PT15M');
    $slots = [];
    // Fetch existing bookings for that salon and date
    $bStmt = $pdo->prepare("SELECT starts_at, ends_at FROM bookings WHERE salon_id = ? AND DATE(starts_at) = ?");
    $bStmt->execute([$salon_id, $date]);
    $existing = $bStmt->fetchAll(PDO::FETCH_ASSOC);

    while ($start < $end) {
        $slotStart = $start->format('Y-m-d H:i:s');
        $slotEndDT = clone $start;
        $slotEndDT->modify("+{$duration} minutes");
        if ($slotEndDT > $end) {
            break;
        }
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

// Main logic for form
if (!isset($_GET['service_id'])) {
    die("Service introuvable.");
}
$service_id = (int) $_GET['service_id'];
$stmt = $pdo->prepare("SELECT s.*, sl.name AS salon_name FROM services s JOIN salons sl ON sl.id = s.salon_id WHERE s.id = ?");
$stmt->execute([$service_id]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$service) {
    die("Service introuvable.");
}

$error = '';
$user = user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle guest or logged in
    if (!$user) {
        $guest_name  = trim($_POST['guest_name'] ?? '');
        $guest_email = trim($_POST['guest_email'] ?? '');
        if ($guest_name === '' || $guest_email === '' || !filter_var($guest_email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Veuillez fournir un nom et un e-mail valides.';
        } else {
            $uStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $uStmt->execute([$guest_email]);
            $u = $uStmt->fetch(PDO::FETCH_ASSOC);
            if ($u) {
                $user_id = $u['id'];
            } else {
                $pw_hash = password_hash(bin2hex(random_bytes(8)), PASSWORD_BCRYPT);
                $iStmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
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
            $error = 'Choisissez une date et un créneau.';
        } else {
            $starts_at = "$date $time:00";
            $duration = (int) $service['duration'];
            $ends_at = date('Y-m-d H:i:s', strtotime("+$duration minutes", strtotime($starts_at)));
            $bStmt = $pdo->prepare("INSERT INTO bookings (customer_id, salon_id, service_id, starts_at, ends_at, status, payment_status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', 'unpaid', NOW())");
            $bStmt->execute([$user['id'], $service['salon_id'], $service_id, $starts_at, $ends_at]);
            header("Location: salon.php?id={$service['salon_id']}");
            exit;
        }
    }
}
?>
<?php $page_title = 'Réserver : ' . $service['name'];
require_once 'inc/header.php'; ?>
<div class="py-4">
  <h1 class="mb-4">Réserver : <?= htmlspecialchars($service['name']) ?></h1>
  <p>Salon : <?= htmlspecialchars($service['salon_name']) ?> &middot; Durée : <?= (int)$service['duration'] ?> min</p>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="post" class="mb-3">
    <?php if (!$user): ?>
      <div class="mb-3">
        <label class="form-label">Votre nom</label>
        <input type="text" name="guest_name" class="form-control" required value="<?= htmlspecialchars($guest_name ?? '') ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Votre e-mail</label>
        <input type="email" name="guest_email" class="form-control" required value="<?= htmlspecialchars($guest_email ?? '') ?>">
      </div>
    <?php endif; ?>

    <div class="mb-3">
      <label class="form-label">Date</label>
      <input type="date" id="dateInput" name="date" class="form-control" required min="<?= date('Y-m-d') ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Créneau disponible</label>
      <select id="timeSelect" name="time" class="form-select" required>
        <option value="">-- Choisissez une date d'abord --</option>
      </select>
    </div>
    <button class="btn btn-primary">Réserver maintenant</button>
    <a href="salon.php?id=<?= $service['salon_id'] ?>" class="btn btn-link">Annuler</a>
  </form>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('dateInput');
    const timeSelect = document.getElementById('timeSelect');
    const serviceId = <?= $service_id ?>;

    dateInput.addEventListener('change', function() {
      const date = this.value;
      timeSelect.innerHTML = '<option>Chargement...</option>';
      fetch(`book_service.php?slots=1&service_id=${serviceId}&date=${date}`)
        .then(response => {
          if (!response.ok) throw new Error('Erreur réseau');
          return response.json();
        })
        .then(slots => {
          timeSelect.innerHTML = '';
          if (!slots.length) {
            timeSelect.innerHTML = '<option value="">Aucun créneau disponible</option>';
            return;
          }
          slots.forEach(time => {
            const opt = document.createElement('option');
            opt.value = time;
            opt.textContent = time;
            timeSelect.appendChild(opt);
          });
        })
        .catch(err => {
          console.error(err);
          timeSelect.innerHTML = '<option value="">Erreur de chargement</option>';
        });
    });
  });
  </script>
</div>
<?php require_once 'inc/footer.php'; ?>
