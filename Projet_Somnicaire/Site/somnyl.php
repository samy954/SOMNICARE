<?php
session_start();

// 🟣 Gestion du bouton "Ajouter au panier"
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_to_cart"])) {

    // Vérifie si l'utilisateur est connecté
    if (!isset($_SESSION["id_utilisateur"])) {
        header("Location: connexion.php");
        exit;
    }

    // Initialise le panier si vide
    if (!isset($_SESSION["panier"])) {
        $_SESSION["panier"] = [];
    }

    // Données du produit Somnyl (avec image)
    $produit = [
        "id" => 1,
        "nom" => "Somnyl - Gélules",
        "prix" => 29.99,
        "quantite" => 1,
        "taille" => $_POST["taille"] ?? "30",
        "image" => "images/gelules.png" // ✅ ajout du visuel produit
    ];

    // Si le produit existe déjà dans le panier, on augmente la quantité
    $existe = false;
    foreach ($_SESSION["panier"] as &$item) {
        if ($item["id"] == $produit["id"] && $item["taille"] == $produit["taille"]) {
            $item["quantite"]++;
            $existe = true;
            break;
        }
    }

    // Sinon, on ajoute le produit
    if (!$existe) {
        $_SESSION["panier"][] = $produit;
    }

    // Redirection vers la page panier
    header("Location: panier.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Somnyl - Produit</title>
  <link rel="stylesheet" href="css/base.css" />
  <link rel="stylesheet" href="css/somnyl.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
  <!-- ✅ HEADER IDENTIQUE À INDEX -->
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
            <li><a href="somnyl.php" class="active">Somnyl</a></li>
            <li><a href="methode.php">Méthode</a></li>
            <li><a href="contact.php">Contact</a></li>
          </ul>
        </nav>

        <!-- Côté droit -->
        <div class="header-right">
          <div class="language-selector">
            <i class="fas fa-globe language-icon"></i>
            <span class="language-text">FR</span>
          </div>

          <?php if (isset($_SESSION["prenom"])): ?>
              <a href="espace.php" class="btn-identifier">
                  Mon espace (<?= htmlspecialchars($_SESSION["prenom"]); ?>)
              </a>
          <?php else: ?>
              <a href="connexion.php" class="btn-identifier">S'identifier</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </header>

  <!-- ✅ CONTENU PRODUIT -->
  <main class="container">
    <div class="left">
      <div class="thumbs">
        <img src="images/somnyl.png" class="thumb" alt="Somnyl">
        <img src="images/gelules.png" class="thumb" alt="Gélules Somnyl">
        <img src="images/gelules2.png" class="thumb" alt="Gélules Somnyl 2">
        <video class="thumb" controls src="videos/video1.mp4"></video>
        <video class="thumb" controls src="videos/video2.mp4"></video>
      </div>

      <div class="main-img">
        <img src="images/gelules.png" class="product-main" alt="Somnyl Produit">
      </div>
    </div>

    <div class="right">
      <h1>Gélules Somnyl</h1>
      <div class="price">29.99€</div>

      <ul class="info">
        <li><strong>Sérénité, relaxation et sommeil réparateur</strong></li>
        <li>Grâce à une synergie d’extraits de plantes reconnues pour leurs vertus apaisantes et de mélatonine, Somnyl favorise naturellement un endormissement plus rapide.</li>
        <li>Idéal pour rétablir un cycle de sommeil harmonieux et profond, sans accoutumance ni sensation de fatigue au réveil.</li>
      </ul>

      <!-- 🟣 FORMULAIRE AJOUT PANIER -->
      <form method="POST">
        <h3 class="label">TAILLE</h3>
        <div class="sizes">
          <button type="button" class="size selected" data-size="30">30</button>
          <button type="button" class="size" data-size="60">60</button>
          <button type="button" class="size" data-size="90">90</button>
          <button type="button" class="size" data-size="120">120</button>
        </div>

        <input type="hidden" name="taille" id="selectedSize" value="30">

        <button type="submit" name="add_to_cart" class="add">Ajouter au panier</button>
      </form>

      <button class="fav">Ajouter aux favoris</button>

      <details class="dropdown">
        <summary>Care</summary>
        <p><strong>Conseils d’utilisation :</strong><br>
        Prenez 1 à 2 gélules environ 30 minutes avant le coucher pour favoriser un sommeil paisible et réparateur.<br><br>
        <strong>Précautions :</strong><br>
        Ne pas dépasser la dose recommandée. Conserver dans un endroit frais et sec, à l’abri de la lumière. Tenir hors de portée des enfants.</p>
      </details>

      <details class="dropdown">
        <summary>Composition</summary>
        <p><strong>Ingrédients principaux :</strong><br>
        Mélatonine, extraits de passiflore, valériane, camomille, magnésium, gélule végétale (HPMC).<br><br>
        <strong>Particularités :</strong><br>
        Formule 100 % naturelle, sans colorants artificiels ni agents controversés. Convient aux régimes végétariens.</p>
      </details>
    </div>
  </main>

  <!-- Script pour sélection de taille -->
  <script>
  const sizeBtns = document.querySelectorAll(".size");
  const sizeInput = document.getElementById("selectedSize");

  sizeBtns.forEach(btn => {
      btn.addEventListener("click", () => {
          sizeBtns.forEach(b => b.classList.remove("selected"));
          btn.classList.add("selected");
          sizeInput.value = btn.dataset.size;
      });
  });
  </script>

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
