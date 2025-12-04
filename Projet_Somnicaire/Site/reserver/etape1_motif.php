<?php
session_start();

// Définir l'étape actuelle
$_SESSION["etape"] = 1;

// Si l'utilisateur est connecté (par ex. via $_SESSION["id_utilisateur"])
$id_utilisateur = $_SESSION["id_utilisateur"] ?? 1; // temporaire

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Réserver - Choix du motif | SomniCare</title>
  <link rel="stylesheet" href="../css/base.css">
  <link rel="stylesheet" href="../css/reserver_motif.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
          <span class="language-text">FR</span>
        </div>
        <a href="../espace.php" class="btn-identifier">Mon espace</a>
      </div>
    </div>
  </div>
</header>

<section class="progress">
  <div class="container">
    <ul class="steps">
      <li class="active">1 <span>Motif</span></li>
      <li>2 <span>Praticien</span></li>
      <li>3 <span>Date & Heure</span></li>
      <li>4 <span>Coordonnées</span></li>
      <li>5 <span>Résumé</span></li>
      <li>6 <span>Confirmation</span></li>
    </ul>
  </div>
</section>

<section class="motif-section">
  <div class="container">
    <h2>Choisissez votre motif de consultation</h2>

    <div class="motif-grid">

      <div class="motif-card">
        <div class="motif-type insomnia">Insomnie</div>
        <div class="motif-info">
          <h3>Suivi TCC-I</h3>
          <p>Séance de thérapie cognitivo-comportementale (30–45 min)</p>
          <a href="etape2_praticien.php?motif=Insomnie" class="btn-choose">Choisir</a>
        </div>
      </div>

      <div class="motif-card">
        <div class="motif-type apnea">Apnée</div>
        <div class="motif-info">
          <h3>Dépistage apnée du sommeil</h3>
          <p>Orientation + prescription d’examens si besoin</p>
          <a href="etape2_praticien.php?motif=Apnée" class="btn-choose">Choisir</a>
        </div>
      </div>

      <div class="motif-card">
        <div class="motif-type sleep">Sommeil</div>
        <div class="motif-info">
          <h3>Première évaluation</h3>
          <p>Bilan initial avec un spécialiste (45–60 min)</p>
          <a href="etape2_praticien.php?motif=Sommeil" class="btn-choose">Choisir</a>
        </div>
      </div>

      <div class="motif-card">
        <div class="motif-type parasomnia">Parasomnie</div>
        <div class="motif-info">
          <h3>Étude des comportements nocturnes</h3>
          <p>Analyse des comportements durant le sommeil</p>
          <a href="etape2_praticien.php?motif=Parasomnie" class="btn-choose">Choisir</a>
        </div>
      </div>

    </div>

    <div class="cancel-area">
      <a href="../espace.php" class="btn-cancel">Annuler</a>
    </div>
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

</body>
</html>
