<?php
session_start();
require_once("../config.php");
$_SESSION["etape"] = 2;

// Vérifie si un praticien est choisi
if (isset($_POST["choisir"])) {
    $_SESSION["id_specialiste"] = $_POST["id_specialiste"];
    $_SESSION["nom_specialiste"] = $_POST["nom_specialiste"];
    header("Location: etape3_date_heure.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Choisir un praticien - SomniCare</title>
  <link rel="stylesheet" href="../css/base.css">
  <link rel="stylesheet" href="../css/reserver_praticien.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>
<body>

<header>
  <div class="container">
    <div class="header-content">
      <div class="logo">
        <div class="logo-image">
          <img src="../images/logo.png" alt="SomniCare">
        </div>
      </div>

      <nav class="main-nav">
        <ul class="nav-links">
          <li><a href="../index.php">Accueil</a></li>
          <li><a href="../troubles-sommeil.php">Les troubles du sommeil</a></li>
          <li><a href="../somnyl.php">Somnyl</a></li>
          <li><a href="../methode.php">Méthode</a></li>
          <li><a href="../contact.php">Contact</a></li>
          <li><a href="etape1_motif.php" class="active">Réserver</a></li>
        </ul>
      </nav>

      <div class="header-right">
        <div class="language-selector">
          <i class="fas fa-globe language-icon"></i>
          <span class="laguage-text">FR</span>
        </div>
        <a href="../espace.php" class="btn-identifier">Mon espace</a>
      </div>
    </div>
  </div>
</header>

<section class="page-header">
  <div class="container">
    <h1>Choisissez votre praticien</h1>
    <p>Découvrez les spécialistes SomniCare près de chez vous.</p>
  </div>
</section>

<section class="map-section">
  <div class="container grid">
    <div class="doctor-list" id="doctorList"></div>
    <div id="map"></div>
  </div>
</section>

<footer>
  <div class="container">
    <div class="footer-content">
      <div class="footer-logo">SomniCare</div>
      <div class="footer-description">
        Spécialistes du sommeil — réservations avec des médecins spécialisés et solutions naturelles validées par nos experts.
      </div>
    </div>
    <div class="footer-copyright">
      © 2025 SomniCare — Tous droits réservés
    </div>
  </div>
</footer>

<script>
const praticiens = <?php
  $result = $conn->query("
    SELECT s.id_specialiste, u.nom, u.prenom, u.email, s.bio, s.tarif_consultation, 
           s.disponibilite, s.latitude, s.longitude 
    FROM specialistes s 
    JOIN utilisateurs u ON s.id_utilisateur = u.id_utilisateur
  ");
  $data = [];
  while($row = $result->fetch_assoc()) $data[] = $row;
  echo json_encode($data);
?>;

// Initialisation carte
const map = L.map('map').setView([46.603354, 1.888334], 6);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© OpenStreetMap contributors'
}).addTo(map);

// Marqueurs praticiens
praticiens.forEach(p => {
  if (p.latitude && p.longitude) {
    const marker = L.marker([p.latitude, p.longitude]).addTo(map);
    marker.bindPopup(`<b>Dr. ${p.prenom} ${p.nom}</b><br>${p.bio}<br>${p.tarif_consultation}€`);
  }
});

// Liste praticiens
const listContainer = document.getElementById("doctorList");
praticiens.forEach(p => {
  const card = document.createElement("div");
  card.className = "doctor-card";
  card.innerHTML = `
    <div class="avatar">${p.prenom.charAt(0)}${p.nom.charAt(0)}</div>
    <div class="info">
      <h3>Dr. ${p.prenom} ${p.nom}</h3>
      <p>${p.bio}</p>
      <p><strong>${p.tarif_consultation}€</strong> — ${p.disponibilite}</p>
      <button onclick="alert('Email : ${p.email}')">Contacter</button>

      <!-- FORMULAIRE CHOISIR -->
      <form method="POST">
        <input type="hidden" name="id_specialiste" value="${p.id_specialiste}">
        <input type="hidden" name="nom_specialiste" value="Dr. ${p.prenom} ${p.nom}">
        <button type="submit" name="choisir" class="btn-choose">Choisir</button>
      </form>
    </div>
  `;
  listContainer.appendChild(card);
});

// Géolocalisation utilisateur
if (navigator.geolocation) {
  navigator.geolocation.getCurrentPosition(pos => {
    const userMarker = L.marker([pos.coords.latitude, pos.coords.longitude], {color: 'blue'}).addTo(map);
    userMarker.bindPopup("Vous êtes ici").openPopup();
    map.setView([pos.coords.latitude, pos.coords.longitude], 8);
  }, () => console.warn("Géolocalisation refusée"));
}
</script>
</body>
</html>
