<?php
/**
 * Page de recherche améliorée avec recommandations et images dans popups.
 * AJAX handling before any HTML output.
 */
$page_title = 'Recherche';
require_once 'inc/header.php';

if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    $q = trim($_GET['q'] ?? '');
    if ($q === '') {
        echo json_encode([]);
        exit;
    }
    $like = '%' . $q . '%';
    $stmt = $pdo->prepare('
      SELECT id, name, city, address, lat, lng, logo_url
        FROM salons
       WHERE (name LIKE ? OR city LIKE ? OR address LIKE ?)
         AND lat IS NOT NULL AND lng IS NOT NULL
       ORDER BY name ASC
       LIMIT 50
    ');
    $stmt->execute([$like, $like, $like]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);
    exit;
}

// Fetch featured salons for initial display
$featuredStmt = $pdo->prepare('
  SELECT id, name, city, address, lat, lng, logo_url
  FROM salons
  WHERE is_featured = 1 AND lat IS NOT NULL AND lng IS NOT NULL
  ORDER BY id DESC
  LIMIT 5
');
$featuredStmt->execute();
$featuredSalons = $featuredStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid mt-4">
  <div class="row">
    <!-- Sidebar: recherche, recommandations et résultats -->
    <div class="col-lg-4 mb-3">
      <div class="card h-100 shadow-sm">
        <div class="card-body d-flex flex-column">
          <h5 class="card-title">Rechercher un salon</h5>
          <div class="position-relative mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Nom, ville ou adresse..." autocomplete="off">
            <div id="suggestions" class="list-group position-absolute w-100 shadow-sm" style="z-index:1000; top: 100%;"></div>
          </div>
          <!-- Salons recommandés -->
          <?php if (!empty($featuredSalons)): ?>
          <div class="mb-3">
            <h6 class="text-primary">Salons recommandés</h6>
            <div id="featuredList">
              <?php foreach($featuredSalons as $s): ?>
                <div class="card mb-2">
                  <div class="card-body">
                    <h6 class="card-title mb-1"><?= htmlspecialchars($s['name']) ?></h6>
                    <p class="card-text small mb-1"><?= htmlspecialchars($s['city']) ?> - <?= nl2br(htmlspecialchars($s['address'])) ?></p>
                    <button class="btn btn-sm btn-outline-primary" onclick="focusOnSalon(<?= htmlspecialchars(json_encode($s)) ?>)">Voir sur la carte</button>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>
          <div id="resultsList" class="flex-grow-1 overflow-auto">
            <!-- Résultats de recherche -->
          </div>
        </div>
      </div>
    </div>
    <!-- Carte -->
    <div class="col-lg-8">
      <div id="map" class="rounded shadow-sm" style="width:100%; height:80vh;"></div>
    </div>
  </div>
</div>

<?php require_once 'inc/footer.php'; ?>

<link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet" />
<style>
  #suggestions .list-group-item {
    cursor: pointer;
  }
  #suggestions .list-group-item:hover {
    background-color: #f8f9fa;
  }
  .leaflet-container {
    border-radius: 0.5rem;
  }
  .marker-popup img {
    max-width: 100px;
    height: auto;
    display: block;
    margin-bottom: 5px;
  }
</style>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// Données des salons recommandés
const featuredSalons = <?= json_encode($featuredSalons) ?>;

document.addEventListener('DOMContentLoaded', () => {
  // Initialisation de la carte
  const map = L.map('map').setView([46.7, 2.2], 6);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap',
    maxZoom: 19
  }).addTo(map);
  const markersGroup = L.layerGroup().addTo(map);

  // Références DOM
  const input = document.getElementById('searchInput');
  const suggestions = document.getElementById('suggestions');
  const resultsList = document.getElementById('resultsList');

  // Effacer résultats
  function clearResults() {
    resultsList.innerHTML = '';
  }

  // Effacer suggestions
  function clearSuggestions() {
    suggestions.innerHTML = '';
    suggestions.classList.add('d-none');
  }

  // Génère le HTML d'une popup avec image et lien
  function generatePopupHtml(s) {
    let html = '<div class="marker-popup">';
    if (s.logo_url) {
      html += `<img src="${s.logo_url}" alt="Logo ${s.name}">`;
    }
    html += `<div><strong>${s.name}</strong></div>`;
    html += `<div>${s.city} - ${s.address.replace(/\n/g,'<br>')}</div>`;
    html += `<a href="salon.php?id=${s.id}" class="btn btn-sm btn-primary mt-2">Voir le salon</a>`;
    html += '</div>';
    return html;
  }

  // Mettre à jour liste de résultats
  function updateResultsList(salons) {
    clearResults();
    if (!salons.length) {
      const empty = document.createElement('p');
      empty.className = 'text-muted';
      empty.textContent = 'Aucun salon trouvé.';
      resultsList.appendChild(empty);
      return;
    }
    salons.forEach(s => {
      const card = document.createElement('div');
      card.className = 'card mb-2 shadow-sm';
      card.innerHTML = `
        <div class="card-body">
          <h6 class="card-title mb-1">${s.name}</h6>
          <p class="card-text small mb-1">${s.city} - ${s.address.replace(/\n/g,'<br>')}</p>
          <button class="btn btn-sm btn-primary">Voir sur la carte</button>
        </div>`;
      card.querySelector('button').addEventListener('click', () => {
        focusOnSalon(s);
      });
      resultsList.appendChild(card);
    });
  }

  // Mettre à jour marqueurs
  function updateMarkers(salons) {
    markersGroup.clearLayers();
    const bounds = [];
    salons.forEach(s => {
      if (s.lat && s.lng) {
        const marker = L.marker([s.lat, s.lng]).addTo(markersGroup);
        marker.bindPopup(generatePopupHtml(s));
        bounds.push([s.lat, s.lng]);
      }
    });
    if (bounds.length > 1) {
      map.fitBounds(bounds, { padding: [40, 40] });
    } else if (bounds.length === 1) {
      map.setView(bounds[0], 14);
    }
  }

  // Mettre à jour suggestions
  function updateSuggestions(salons) {
    suggestions.innerHTML = '';
    if (!salons.length) {
      clearSuggestions();
      return;
    }
    salons.forEach(s => {
      const item = document.createElement('button');
      item.type = 'button';
      item.className = 'list-group-item list-group-item-action';
      item.innerHTML = `<strong>${s.name}</strong><br><small>${s.city} - ${s.address.replace(/\n/g,'<br>')}</small>`;
      item.addEventListener('click', () => {
        input.value = s.name;
        clearSuggestions();
        focusOnSalon(s);
      });
      suggestions.appendChild(item);
    });
    suggestions.classList.remove('d-none');
  }

  // Fonction fetch
  function fetchSalons(query) {
    fetch(`search.php?ajax=1&q=${encodeURIComponent(query)}`)
      .then(r => r.json())
      .then(data => {
        updateSuggestions(data);
        updateResultsList(data);
        updateMarkers(data);
      })
      .catch(console.error);
  }

  // Focus sur un salon
  window.focusOnSalon = function(salon) {
    clearSuggestions();
    clearResults();
    updateResultsList([salon]);
    updateMarkers([salon]);
  };

  // Affichage initial recommandations
  updateResultsList(featuredSalons);
  updateMarkers(featuredSalons);

  // Debounce
  function debounce(fn, delay) {
    let timer;
    return function(...args) {
      clearTimeout(timer);
      timer = setTimeout(() => fn.apply(this, args), delay);
    };
  }

  // Événement input
  input.addEventListener('input', debounce(e => {
    const val = e.target.value.trim();
    if (val.length < 2) {
      clearSuggestions();
      // Réaffiche recommandations si champ vide
      updateResultsList(featuredSalons);
      updateMarkers(featuredSalons);
      return;
    }
    fetchSalons(val);
  }, 300));

  // Clic hors suggestions
  document.addEventListener('click', e => {
    if (!e.target.closest('#suggestions') && e.target !== input) {
      clearSuggestions();
    }
  });
});
</script>
