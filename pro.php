<?php require_once 'inc/header.php'; ?>
<header class="hero text-center">
  <h1 data-aos="fade-down">Vous êtes un salon&nbsp;?</h1>
  <p data-aos="fade-up" data-aos-delay="150">Rejoignez plus de 2 000 professionnels qui remplissent leur agenda avec ôplani.</p>
  <a href="register.php" class="btn btn-primary btn-lg mt-3" data-aos="zoom-in" data-aos-delay="300"><i class="bi-person-plus"></i> Créer mon compte pro</a>
</header>

<section class="container py-5">
  <h2 class="section-title text-center">Fonctionnalités clés</h2>
  <div class="row g-4">
    <div class="col-md-4" data-aos="flip-left"><div class="card p-4">
      <i class="bi-calendar-check display-5 text-primary mb-3"></i>
      <h5>Agenda intelligent</h5><p>Gestion des plannings multi-employés, rappels automatiques aux clients.</p>
    </div></div>
    <div class="col-md-4" data-aos="flip-left" data-aos-delay="100"><div class="card p-4">
      <i class="bi-graph-up-arrow display-5 text-primary mb-3"></i>
      <h5>Statistiques en temps réel</h5><p>Suivi CA, fréquentation et taux de rétention depuis votre dashboard.</p>
    </div></div>
    <div class="col-md-4" data-aos="flip-left" data-aos-delay="200"><div class="card p-4">
      <i class="bi-megaphone display-5 text-primary mb-3"></i>
      <h5>Marketing intégré</h5><p>Campagnes SMS/email illimitées pour fidéliser votre clientèle.</p>
    </div></div>
  </div>
</section>

<section class="py-5 bg-light">
  <div class="container">
    <h2 class="section-title text-center">Tarifs transparents</h2>
    <div class="row justify-content-center g-4">
      <div class="col-md-4" data-aos="zoom-in">
        <div class="card p-4">
          <h3 class="text-center mb-3">Starter</h3>
          <p class="display-6 text-center">0€ <small>/mois</small></p>
          <ul class="list-unstyled">
            <li>✓ 100 rendez-vous / mois</li><li>✓ Page vitrine SEO</li><li>✓ Avis clients</li>
          </ul>
        </div>
      </div>
      <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100">
        <div class="card p-4 border-primary">
          <h3 class="text-center mb-3">Pro</h3>
          <p class="display-6 text-center text-primary">29€ <small>/mois</small></p>
          <ul class="list-unstyled">
            <li>✓ Rendez-vous illimités</li><li>✓ Statistiques avancées</li><li>✓ Support prioritaire</li>
          </ul>
        </div>
      </div>
    </div>
    <p class="text-center mt-4"><a href="register.php" class="btn btn-outline-primary btn-lg"><i class="bi-lightning-charge"></i> Démarrer mon essai gratuit</a></p>
  </div>
</section>

<footer class="py-4 text-center">
  <p class="mb-0">© <span id="year"></span> ôplani – Plateforme pro</p>
</footer>
<?php require_once 'inc/footer.php'; ?>
