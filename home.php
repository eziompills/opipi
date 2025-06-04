<?php
$page_title = 'Accueil';
$page_description = "Réservez en ligne chez les meilleurs salons de beauté près de chez vous";
require_once 'inc/header.php'; ?>
<header style="position:relative;" class="hero text-center position-relative overflow-hidden">
  <h1 data-aos="fade-down">ôplani – Prenez soin de vous</h1>
  <p data-aos="fade-up" data-aos-delay="150">Réservez chez les meilleurs professionnels beauté en quelques secondes.</p>
  <a href="search.php" class="btn btn-primary btn-lg mt-3" data-aos="zoom-in" data-aos-delay="300"><i class="bi-search"></i> Trouver un salon</a>
<div style="position:absolute;top:0;left:0;right:0;bottom:0;background:linear-gradient(180deg,rgba(0,0,0,.4),rgba(0,0,0,.1));"></div></header>

<section class="container py-5">
  <h2 class="section-title text-center" data-aos="fade-up">Pourquoi choisir ôplani ?</h2>
  <div class="row text-center g-4">
    <div class="col-md-4" data-aos="zoom-in">
      <i class="bi-clock-history display-4 text-primary mb-3"></i>
      <h5>Réservation 24 / 7</h5>
      <p>Des créneaux en temps réel, même la nuit !</p>
    </div>
    <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100">
      <i class="bi-star-fill display-4 text-primary mb-3"></i>
      <h5>Avis vérifiés</h5>
      <p>Des retours authentiques pour faire le bon choix.</p>
    </div>
    <div class="col-md-4" data-aos="zoom-in" data-aos-delay="200">
      <i class="bi-shield-check display-4 text-primary mb-3"></i>
      <h5>Paiement sécurisé</h5>
      <p>Chiffrement de bout-en-bout, conformité RGPD.</p>
    </div>
  </div>
</section>

<section class="py-5 bg-light">
  <div class="container">
    <h2 class="section-title text-center" data-aos="fade-up">Ils adorent ôplani</h2>
    <div class="swiper" data-aos="fade-up" data-aos-delay="150">
      <div class="swiper-wrapper">
        <div class="swiper-slide p-4">
          <blockquote class="blockquote">“Interface ultra simple, j’ai réservé mon coiffeur en 30 s !”</blockquote>
          <figcaption class="blockquote-footer">Claire, Paris 11ᵉ</figcaption>
        </div>
        <div class="swiper-slide p-4">
          <blockquote class="blockquote">“Le rappel par mail la veille, c’est top pour ne rien oublier.”</blockquote>
          <figcaption class="blockquote-footer">Lucas, Lille</figcaption>
        </div>
        <div class="swiper-slide p-4">
          <blockquote class="blockquote">“En tant que barbier, je remplis 90 % de mon agenda grâce à ôplani.”</blockquote>
          <figcaption class="blockquote-footer">Mehdi, Bordeaux</figcaption>
        </div>
      </div>
      <div class="swiper-pagination mt-3"></div>
    </div>
  </div>
</section>

<footer class="py-4 text-center">
  <p class="mb-0">© <span id="year"></span> ôplani – Tous droits réservés</p>
</footer>

<script>
var swiper=new Swiper('.swiper',{loop:true,pagination:{el:'.swiper-pagination'}});
</script>
<?php require_once 'inc/footer.php'; ?>
