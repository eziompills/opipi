<?php require_once '../inc/header.php';
$slug=$_GET['slug']??'';
$title='Article';
$content='Contenu';
if($slug==='bienfaits-barbe'){
  $title='5 astuces pour entretenir sa barbe';
  $content='<p>Hydratation, huiles essentielles...</p>';
}elseif($slug==='tendance-coupe-ete'){
  $title='Tendances coupe été 2025';
  $content='<p>Le shag, le mulet revisité...</p>';
}
?>
<article class="container py-5">
  <h1 data-aos="fade-down"><?= htmlspecialchars($title) ?></h1>
  <div data-aos="fade-up" data-aos-delay="100"><?= $content ?></div>
  <a href="index.php" class="btn btn-link mt-4"><i class="bi-arrow-left"></i> Retour blog</a>
</article>
<?php require_once '../inc/footer.php'; ?>
