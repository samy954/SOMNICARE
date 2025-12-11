<?php
session_start();
require_once("../config.php");

// === Vérification du parcours utilisateur ===
if (
    !isset($_SESSION["id_utilisateur"], $_SESSION["id_specialiste"], $_SESSION["date_rdv"], $_SESSION["heure_rdv"], $_SESSION["mode"])
) {
    header("Location: etape1_motif.php");
    exit;
}

$id_client = $_SESSION["id_utilisateur"];
$id_specialiste = $_SESSION["id_specialiste"];
$date_rdv = $_SESSION["date_rdv"];
$heure_rdv = $_SESSION["heure_rdv"];
$mode = $_SESSION["mode"];
$nom_specialiste = $_SESSION["nom_specialiste"] ?? "Dr. Spécialiste";

// === Vérifie si le rendez-vous existe déjà ===
$stmt = $conn->prepare("
    SELECT id_rdv 
    FROM rendez_vous 
    WHERE id_client = ? AND id_specialiste = ? AND date_rdv = ? AND heure_rdv = ?
");
$stmt->bind_param("iiss", $id_client, $id_specialiste, $date_rdv, $heure_rdv);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    // Enregistrement du rendez-vous
    $insert = $conn->prepare("
        INSERT INTO rendez_vous (id_client, id_specialiste, date_rdv, heure_rdv, statut) 
        VALUES (?, ?, ?, ?, 'confirme')
    ");
    $insert->bind_param("iiss", $id_client, $id_specialiste, $date_rdv, $heure_rdv);
    $insert->execute();
    $insert->close();
}
$stmt->close();

// Nettoyage des variables temporaires
unset($_SESSION["motif"], $_SESSION["date_rdv"], $_SESSION["heure_rdv"], $_SESSION["mode"], $_SESSION["coordonnees"]);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Confirmation - Rendez-vous | SomniCare</title>
  <link rel="stylesheet" href="../css/base.css">
  <link rel="stylesheet" href="../css/reserver_confirmation.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

  <!-- HEADER -->
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
            <li><a href="../troubles-sommeil.php">Troubles du sommeil</a></li>
            <li><a href="../somnyl.php">Somnyl</a></li>
            <li><a href="../methode.php">Méthode</a></li>
            <li><a href="../contact.php">Contact</a></li>
          </ul>
        </nav>
        <div class="header-right">
          <a href="../espace.php" class="btn-identifier">Mon espace</a>
        </div>
      </div>
    </div>
  </header>

  <!-- SECTION CONFIRMATION -->
  <section class="confirmation-section">
    <div class="container">
      <div class="confirmation-card fade-in">
        <div class="success-icon">
          <i class="fas fa-check-circle"></i>
        </div>
        <h3>Votre rendez-vous est confirmé !</h3>
        <p>Un e-mail de confirmation vous a été envoyé. Vous pouvez consulter vos rendez-vous dans votre espace personnel.</p>

        <div class="confirmation-details">
          <div class="detail-box">
            <span class="label">Date</span>
            <span class="value"><?= date("d/m/Y", strtotime($date_rdv)); ?></span>
          </div>
          <div class="detail-box">
            <span class="label">Heure</span>
            <span class="value"><?= substr($heure_rdv, 0, 5); ?></span>
          </div>
          <div class="detail-box">
            <span class="label">Praticien</span>
            <span class="value"><?= htmlspecialchars($nom_specialiste); ?></span>
          </div>
          <div class="detail-box">
            <span class="label">Mode</span>
            <span class="value"><?= ucfirst(htmlspecialchars($mode)); ?></span>
          </div>
        </div>

        <div class="confirmation-buttons">
          <a href="../espace.php" class="btn btn-primary">Voir mes rendez-vous</a>
          <a href="../index.php" class="btn btn-secondary">Retour à l'accueil</a>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer>
    <div class="container">
      <div class="footer-content">
        <div class="footer-logo">SomniCare</div>
        <div class="footer-description">
          Spécialistes du sommeil — réservations avec des médecins spécialisés et solutions naturelles validées.
        </div>
      </div>
      <div class="footer-copyright">
        © 2025 SomniCare — Tous droits réservés
      </div>
    </div>
  </footer>

</body>
</html>
