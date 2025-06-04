<?php
require_once __DIR__.'/config.php';
require_once __DIR__.'/flash.php';

function nav_active(string $path): string {
    return strpos($_SERVER['REQUEST_URI'], $path) === 0 ? 'active' : '';
}
?>
<?php
require_once __DIR__.'/config.php';
require_once __DIR__.'/flash.php';
function nav_active(string $path): string {
    return strpos($_SERVER['REQUEST_URI'], $path) === 0 ? 'active' : '';
}
?>
<!doctype html>
<html lang="fr">
<head>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<meta charset="utf-8">
<title>ôplani</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/lux/bootstrap.min.css" rel="stylesheet">
<link href="/assets/css/style.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
<link href="/assets/css/theme.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/datatables.net-bs5@1.13.8/css/dataTables.bootstrap5.min.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css"/>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4 sticky-top">
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css"/>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4 sticky-top">
  <div class="container">
    <a class="navbar-brand" href="/home.php">ôplani</a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
<li class="nav-item"><a class="nav-link <?= nav_active('/pro.php') ?>" href="/pro.php">Pour les pros</a></li>
<li class="nav-item"><a class="nav-link <?= nav_active('/blog') ?>" href="/blog/index.php">Blog</a></li>
      <ul class="navbar-nav ms-auto">
<li class="nav-item"><a class="nav-link <?= nav_active('/pro.php') ?>" href="/pro.php">Pour les pros</a></li>
<li class="nav-item"><a class="nav-link <?= nav_active('/blog') ?>" href="/blog/index.php">Blog</a></li>
        <?php if(is_admin()): ?>
          <li class="nav-item"><a class="nav-link" href="/admin/dashboard.php">Admin</a></li>
        <?php endif; ?>
        <?php if(is_owner()): ?>
          <li class="nav-item"><a class="nav-link" href="/owner/dashboard.php">Mon salon</a></li>
        <?php endif; ?>
        <?php if(is_staff()): ?>
          <li class="nav-item"><a class="nav-link" href="/staff/dashboard.php">Planning</a></li>
        <?php endif; ?>
        <?php if(is_logged()): ?>
            <li class="nav-item"><a class="nav-link <?= nav_active('/my_bookings.php') ?>" href="/my_bookings.php">Mes réservations</a></li>
          <li class="nav-item"><a class="nav-link" href="/logout.php">Déconnexion</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link <?= nav_active('/login.php') ?>" href="/login.php">Connexion</a></li>
          <li class="nav-item"><a class="nav-link <?= nav_active('/register.php') ?>" href="/register.php">Inscription</a></li>
        <?php endif; ?>
      
<li class="nav-item">
  <button id="themeToggle" class="btn btn-link nav-link" aria-label="Changer le thème"><i class="bi-moon"></i></button>
            <li class="nav-item"><a class="nav-link <?= nav_active('/my_bookings.php') ?>" href="/my_bookings.php">Mes réservations</a></li>
          <li class="nav-item"><a class="nav-link" href="/logout.php">Déconnexion</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link <?= nav_active('/login.php') ?>" href="/login.php">Connexion</a></li>
          <li class="nav-item"><a class="nav-link <?= nav_active('/register.php') ?>" href="/register.php">Inscription</a></li>
        <?php endif; ?>
      
<li class="nav-item">
  <button id="themeToggle" class="btn btn-link nav-link" aria-label="Changer le thème"><i class="bi-moon"></i></button>
</li>
</ul>
    </div>
  </div>
</nav>
<div class="container">
<?php display_flash(); ?>
<div class="container">
<?php display_flash(); ?>
