<?php
session_start();

// --- Si aucun panier, on initialise ---
if (!isset($_SESSION["panier"])) {
    $_SESSION["panier"] = [];
}

// --- Gérer les actions sur le panier ---
if (isset($_GET["action"], $_GET["index"])) {
    $index = (int) $_GET["index"];

    if ($_GET["action"] === "plus" && isset($_SESSION["panier"][$index])) {
        $_SESSION["panier"][$index]["quantite"]++;
    }

    if ($_GET["action"] === "moins" && isset($_SESSION["panier"][$index])) {
        if ($_SESSION["panier"][$index]["quantite"] > 1) {
            $_SESSION["panier"][$index]["quantite"]--;
        }
    }

    if ($_GET["action"] === "remove" && isset($_SESSION["panier"][$index])) {
        array_splice($_SESSION["panier"], $index, 1); // supprime l’élément
    }

    header("Location: panier.php");
    exit;
}

// --- Calcul du total ---
$sous_total = 0;
foreach ($_SESSION["panier"] as $item) {
    $sous_total += $item["prix"] * $item["quantite"];
}
$livraison = $sous_total > 0 ? 4.00 : 0.00;
$total = $sous_total + $livraison;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Panier - SomniCare</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/panier.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

  <!-- HEADER -->
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
  <!-- CONTENU -->
  <main class="page">
    <section class="cart-area">
      <h2>🛒 Votre panier</h2>

      <?php if (empty($_SESSION["panier"])): ?>
          <p class="empty-cart">Votre panier est vide.</p>
          <a href="somnyl.php" class="btn btn-primary">Voir nos produits</a>
      <?php else: ?>
          <?php foreach ($_SESSION["panier"] as $index => $item): ?>
              <div class="product-card">
                <img src="<?= htmlspecialchars($item["image"]); ?>" alt="<?= htmlspecialchars($item["nom"]); ?>" class="product-thumb">
                <div class="product-info">
                  <div class="product-title"><?= htmlspecialchars($item["nom"]); ?></div>
                  <div class="product-format"><?= htmlspecialchars($item["taille"]); ?> gélules</div>
                </div>

                <div class="product-controls">
                  <div class="qty">
                    <a href="?action=moins&index=<?= $index; ?>" class="qty-btn">−</a>
                    <span class="qty-value"><?= $item["quantite"]; ?></span>
                    <a href="?action=plus&index=<?= $index; ?>" class="qty-btn">+</a>
                  </div>
                  <div class="price"><?= number_format($item["prix"] * $item["quantite"], 2); ?> €</div>
                  <a href="?action=remove&index=<?= $index; ?>" class="trash" title="Retirer">🗑️</a>
                </div>
              </div>
          <?php endforeach; ?>
      <?php endif; ?>
    </section>

    <!-- Résumé -->
    <aside class="summary">
      <div class="summary-box">
        <div class="summary-row"><span>Sous-total</span><span><?= number_format($sous_total, 2); ?> €</span></div>
        <div class="summary-row"><span>Livraison</span><span><?= number_format($livraison, 2); ?> €</span></div>
        <div class="summary-row total"><span>Total (TTC)</span><span><?= number_format($total, 2); ?> €</span></div>

        <?php if ($sous_total > 0): ?>
            <form action="paiement.php" method="POST">
              <input type="hidden" name="total" value="<?= $total; ?>">
              <button type="submit" class="pay-btn">
                <span class="pay-amount"><?= number_format($total, 2); ?> €</span>
                <span class="pay-text">Payer →</span>
              </button>
            </form>
        <?php endif; ?>
      </div>
    </aside>
  </main>

  <!-- FOOTER -->
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
