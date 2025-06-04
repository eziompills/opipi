<?php
/**
 * Edition des informations du salon (nom, adresse, ville, etc.)
 * Cette version n'utilise pas set_flash().
 */
require_once '../inc/config.php';
require_role(['owner']);
require_once '../inc/header.php';

// Affichage d'un message de succès si ?updated=1
if (isset($_GET['updated'])) {
    echo '<div class="alert alert-success">Salon enregistré avec succès !✨</div>';
}

$salonStmt = $pdo->prepare('SELECT * FROM salons WHERE owner_id = ? LIMIT 1');
$salonStmt->execute([user()['id']]);
$salon = $salonStmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = $_POST['category'] ?? '';
    $primary_color = trim($_POST['primary_color'] ?? '');
    $logo_url = trim($_POST['logo_url'] ?? '');

    // Validation basique
    $errors = [];
    if ($name === '') {
        $errors[] = 'Le nom du salon est requis.';
    }
    $allowed_categories = ['barbershop','bio','kids','mixte','spa'];
    if (!in_array($category, $allowed_categories)) {
        $errors[] = 'Catégorie invalide.';
    }

    if (empty($errors)) {
        // Générer slug simple
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($name)));
        if ($salon) {
            // update
            $stmt = $pdo->prepare('UPDATE salons SET name=?, city=?, address=?, description=?, category=?, primary_color=?, logo_url=?, slug=? WHERE id=?');
            $stmt->execute([$name, $city, $address, $description, $category, $primary_color, $logo_url, $slug, $salon['id']]);
        } else {
            // create
            $stmt = $pdo->prepare('INSERT INTO salons (owner_id,name,city,address,description,category,primary_color,logo_url,slug) VALUES (?,?,?,?,?,?,?,?,?)');
            $stmt->execute([user()['id'], $name, $city, $address, $description, $category, $primary_color, $logo_url, $slug]);
        }
        header('Location: salon_edit.php?updated=1');
        exit;
    }
}
?>

<h1 class="mb-4">Mon salon</h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" class="card shadow-sm border-0 p-4" style="max-width:600px">
  <div class="mb-3">
    <label class="form-label">Nom du salon *</label>
    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($salon['name'] ?? '') ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Ville</label>
    <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($salon['city'] ?? '') ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Adresse</label>
    <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($salon['address'] ?? '') ?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label">Description</label>
    <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($salon['description'] ?? '') ?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label">Catégorie</label>
    <select name="category" class="form-select">
      <?php 
      $opts = ['barbershop'=>'Barbershop','bio'=>'Bio','kids'=>'Kids','mixte'=>'Mixte','spa'=>'Spa'];
      foreach($opts as $val=>$label): ?>
        <option value="<?= $val ?>" <?= (isset($salon['category']) && $salon['category']==$val)?'selected':'' ?>><?= $label ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Couleur principale (hex)</label>
    <input type="color" name="primary_color" class="form-control form-control-color" value="<?= htmlspecialchars($salon['primary_color'] ?? '#000000') ?>" title="Choisissez une couleur">
  </div>
  <div class="mb-3">
    <label class="form-label">URL du logo</label>
    <input type="url" name="logo_url" class="form-control" value="<?= htmlspecialchars($salon['logo_url'] ?? '') ?>">
  </div>
  <button class="btn btn-primary">Enregistrer</button>
</form>

<?php require_once '../inc/footer.php'; ?>
