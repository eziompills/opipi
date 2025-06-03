<?php require_once '../inc/header.php';
$posts=[['slug'=>'bienfaits-barbe','title'=>'5 astuces pour entretenir sa barbe','excerpt'=>'Un guide rapide pour rester au top.','date'=>'2025-05-20'],
        ['slug'=>'tendance-coupe-ete','title'=>'Tendances coupe été 2025','excerpt'=>'Les styles à adopter cette saison.','date'=>'2025-05-10']];
?>
<h1 class="section-title">Le Blog ôplani</h1>
<div class="row g-4">
<?php foreach($posts as $p): ?>
  <div class="col-md-6" data-aos="fade-up">
    <div class="card h-100 p-4">
      <h3><?= htmlspecialchars($p['title']) ?></h3>
      <p class="text-muted"><?= date('d/m/Y',strtotime($p['date'])) ?></p>
      <p><?= htmlspecialchars($p['excerpt']) ?></p>
      <a href="post.php?slug=<?= $p['slug'] ?>" class="btn btn-sm btn-primary">Lire la suite</a>
    </div>
  </div>
<?php endforeach; ?>
</div>
<?php require_once '../inc/footer.php'; ?>
