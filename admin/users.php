<?php
require_once '../inc/config.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $uid=(int)($_POST['user_id']??0);
  $role=$_POST['role']??'customer';
  $pdo->prepare("UPDATE users SET role=? WHERE id=?")->execute([$role,$uid]);
}

require_once '../inc/header.php';
include 'sidebar.php';
echo '<div class="content-wrap">';
require_role(['admin']);
$users=$pdo->query("SELECT id,name,email,role,created_at FROM users ORDER BY created_at DESC")->fetchAll();
?>
<h2>Utilisateurs</h2>
<table class="table table-sm">
<thead><tr><th>ID</th><th>Nom</th><th>Email</th><th>Rôle</th><th>Créé</th></tr></thead>
<tbody>
<?php foreach($users as $u): ?>
<tr>
<td><?= $u['id']?></td><td><?= htmlspecialchars($u['name'])?></td><td><?= htmlspecialchars($u['email'])?></td><td>
<form method="post">
<input type="hidden" name="user_id" value="<?= $u['id'] ?>">
<select name="role" class="form-select form-select-sm d-inline w-auto">
  <?php foreach(['customer','owner','staff','admin'] as $r): ?>
    <option value="<?= $r ?>" <?= $u['role']==$r?'selected':'' ?>><?= $r ?></option>
  <?php endforeach; ?>
</select>
<button class="btn btn-sm btn-secondary">Mettre à jour</button>
</form>
</td><td><?= $u['created_at']?></td>
</tr>
<?php endforeach;?>
</tbody>
</table>
</div><?php require_once '../inc/footer.php'; ?>
