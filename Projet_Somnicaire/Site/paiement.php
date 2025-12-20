<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
session_start();
require_once("config.php");

// Vérification de la connexion
if (!isset($_SESSION["id_utilisateur"])) {
    header("Location: connexion.php");
    exit;
}

// Vérifie qu’il y a bien un panier à payer
if (!isset($_SESSION["panier"]) || empty($_SESSION["panier"])) {
    header("Location: panier.php");
    exit;
}

$total = $_POST["total"] ?? 0;
$id_client = $_SESSION["id_utilisateur"];
$date = date("Y-m-d H:i:s");

// --- Enregistrement de la commande ---
$stmt = $conn->prepare("INSERT INTO commandes (id_client, date_commande, total, statut) VALUES (?, ?, ?, 'en_attente')");
$stmt->bind_param("isd", $id_client, $date, $total);
$stmt->execute();
$id_commande = $stmt->insert_id;
$stmt->close();

// --- Enregistrement des produits du panier ---
foreach ($_SESSION["panier"] as $item) {
    $stmt2 = $conn->prepare("
        INSERT INTO commandes_details (id_commande, id_produit, quantite, prix)
        VALUES (?, ?, ?, ?)
    ");
    $stmt2->bind_param("iiid", $id_commande, $item["id"], $item["quantite"], $item["prix"]);
    $stmt2->execute();
    $stmt2->close();
}


// Vide le panier après paiement
$_SESSION["panier"] = [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Paiement confirmé - SomniCare</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/paiement.css">
</head>
<body>

<header>
    <div class="container">
        <div class="header-content">

            <!-- Logo -->
            <div class="logo">
                <div class="logo-image">
                    <img src="images/logo.png" alt="SomniCare">
                </div>
            </div>

            <!-- Navigation -->
            <nav class="main-nav">
                <ul class="nav-links">
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="troubles-sommeil.php">Les troubles du sommeil</a></li>
                    <li><a href="somnyl.php">Somnyl</a></li>
                    <li><a href="methode.php">Méthode</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>

            <!-- Côté droit -->
            <div class="header-right">

                <!-- Langue -->
                <div class="language-selector">
                    <i class="fas fa-globe language-icon"></i>
                    <span class="language-text">FR</span>
                </div>

                <!-- Bouton dynamique -->
                <?php if (isset($_SESSION['id_utilisateur'], $_SESSION['role'])): ?>

                    <?php if ($_SESSION['role'] === 'specialiste'): ?>
                        <a href="espace_medecin.php" class="btn-identifier">
                            Espace médecin (<?= htmlspecialchars($_SESSION['prenom']) ?>)
                        </a>
                    <?php else: ?>
                        <a href="espace.php" class="btn-identifier">
                            Mon espace (<?= htmlspecialchars($_SESSION['prenom']) ?>)
                        </a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn-logout">Se déconnecter</a>

                <?php else: ?>
                    <a href="connexion.php" class="btn-identifier">S'identifier</a>
                <?php endif; ?>

            </div>
        </div>
    </div>
</header>

<section class="confirmation-section">
  <div class="container">
    <div class="confirmation-card">
      <div class="checkmark">✔</div>
      <h2>Paiement confirmé !</h2>
      <p>Merci pour votre confiance, votre commande a bien été enregistrée.</p>
      <p>Montant total : <strong><?= number_format($total, 2, ',', ' '); ?> €</strong></p>

      <div class="buttons">
        <a href="espace.php" class="btn btn-primary">Voir mes commandes</a>
        <a href="index.php" class="btn btn-secondary">Retour à l'accueil</a>
      </div>
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
