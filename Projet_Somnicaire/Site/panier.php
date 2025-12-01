<?php
session_start(); // ✅ permet d'afficher le prénom connecté dans la navbar
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Panier - SomniCare</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/somnyl.css">
  <link rel="stylesheet" href="css/panier.css" type="text/css" media="screen">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
  <!-- Header principal -->
  <header>
    <div class="container">
      <div class="header-content">

        <!-- Logo à gauche -->
        <div class="logo">
          <div class="logo-image">
            <img src="images/logo.png" alt="SomniCare">
          </div>
        </div>

        <!-- Navigation centrale -->
        <nav class="main-nav">
          <ul class="nav-links">
            <li><a href="index.php">Accueil</a></li>
            <li><a href="troubles-sommeil.php">Les troubles du sommeil</a></li>
            <li><a href="somnyl.php">Somnyl</a></li>
            <li><a href="methode.php">Méthode</a></li>
            <li><a href="contact.php">Contact</a></li>
          </ul>
        </nav>

        <!-- Côté droit - Langue et identification -->
        <div class="header-right">
          <div class="language-selector">
            <i class="fas fa-globe language-icon"></i>
            <span class="language-text">FR</span>
          </div>

          <!-- ✅ Bouton dynamique -->
          <?php if (isset($_SESSION["prenom"])): ?>
              <a href="espace.php" class="btn-identifier">
                  Mon espace (<?php echo htmlspecialchars($_SESSION["prenom"]); ?>)
              </a>
          <?php else: ?>
              <a href="connexion.php" class="btn-identifier">S'identifier</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </header>

  <!-- Contenu principal -->
  <main class="page">
    <section class="cart-area">
      <h2>Panier</h2>
      <p class="subtext">Vous avez 1 produit dans votre panier</p>

      <div class="product-card">
        <img src="images/somnyl.png" alt="Somnyl" class="product-thumb">
        <div class="product-info">
          <div class="product-title">Somnyl</div>
          <div class="product-format">Format 60 gélules</div>
        </div>
        <div class="product-controls">
          <div class="qty">
            <button class="qty-btn">−</button>
            <span class="qty-value">1</span>
            <button class="qty-btn">+</button>
          </div>
          <div class="price">29.99€</div>
          <button class="trash" aria-label="Retirer">🗑️</button>
        </div>
      </div>

      <hr class="divider">

      <h3 class="fav-title">Ajoutez vos produits favoris</h3>
      <div class="favs">
        <div class="fav-card">
          <img src="images/somnyl.png" alt="Gélules Somnyl" class="fav-thumb">
          <div class="fav-name">Gélules Somnyl</div>
          <div class="fav-price">29.99€</div>
        </div>
      </div>
    </section>

    <aside class="summary">
      <div class="summary-box">
        <div class="summary-row"><span>Sous-total</span><span>29.99€</span></div>
        <div class="summary-row"><span>Livraison</span><span>4€</span></div>
        <div class="summary-row total"><span>Total (TTC)</span><span>33.99€</span></div>

        <button class="pay-btn">
          <span class="pay-amount">33.99€</span>
          <span class="pay-text">Payer →</span>
        </button>
      </div>
    </aside>
  </main>

  <!-- Footer -->
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
