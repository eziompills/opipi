<?php
$menu=[['Dashboard','/owner/dashboard.php','bi-speedometer'],
       ['Galerie','gallery.php','bi-images'],
       ['Services','services.php','bi-scissors'],
       ['Personnel','staff.php','bi-people'],
       ['Planning','schedule.php','bi-calendar2-week'],
       ['Avis','reviews.php','bi-chat-square-quote'],
       ['Stats','analytics.php','bi-graph-up'],
       ['Marketing','marketing.php','bi-megaphone']];
?>
<nav class="sidebar d-none d-lg-block" id="sidebar">
  <h4 class="mb-4"><i class="bi-shop-window"></i> Espace Pro</h4>
  <?php foreach($menu as $m): ?>
    <a href="<?= strpos($m[1],'http')===0?$m[1]:(dirname($_SERVER['SCRIPT_NAME']).'/'.$m[1]) ?>" class="<?= strpos($_SERVER['PHP_SELF'],$m[1])!==false?'active':'' ?>"><i class="bi <?= $m[2]?> me-1"></i> <?= $m[0] ?></a>
  <?php endforeach;?>
</nav>
<button class="btn btn-outline-primary d-lg-none position-fixed" style="top:1rem;left:1rem;z-index:1050" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="bi-list"></i></button>
