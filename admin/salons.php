<?php
require_once '../inc/header.php';
include 'sidebar.php';
echo '<div class="content-wrap">';
require_role(['admin']);
$salons=$pdo->query("SELECT s.*, u.name owner FROM salons s JOIN users u ON u.id=s.owner_id ORDER BY s.created_at DESC");
?>
<h1 class="section-title">Salons</h1>
<table class="table">
<thead><tr><th>ID</th><th>Nom</th><th>Ville</th><th>Proprio</th><th>Catégorie</th></tr></thead>
<tbody>
<?php foreach($salons as $s): ?>
<tr>
  <td><?= $s['id']?></td>
  <td><?= htmlspecialchars($s['name'])?></td>
  <td><?= htmlspecialchars($s['city'])?></td>
  <td><?= htmlspecialchars($s['owner'])?></td>
  <td><?= $s['category']?></td>
</tr>
<?php endforeach;?>
</tbody></table>
</div><?php require_once '../inc/footer.php'; ?>
