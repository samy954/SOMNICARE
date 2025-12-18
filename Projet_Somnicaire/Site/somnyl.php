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

    // 🟢 Prix officiels par taille (SÉCURITÉ)
    $prixParTaille = [
        "30"  => 19.90,
        "60"  => 34.90,
        "90"  => 49.90,
        "120" => 64.90
    ];

    $taille = $_POST["taille"] ?? "30";

    // Sécurité taille
    if (!isset($prixParTaille[$taille])) {
        die("Taille invalide");
    }

    // Données du produit Somnyl
    $produit = [
        "id" => 1,
        "nom" => "Somnyl - Gélules",
        "prix" => $prixParTaille[$taille], // ✅ PRIX DYNAMIQUE
        "quantite" => 1,
        "taille" => $taille,
        "image" => "images/gelules.png"
    ];

    // Si le produit existe déjà dans le panier (même taille)
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
      </div>

      <div class="main-img">
        <img src="images/somnyl.png" class="product-main" alt="Somnyl Produit">
      </div>
    </div>

    <div class="right">
      <h1>Gélules Somnyl</h1>
      <div class="price">
        <span id="price">19.90</span> €
      </div>


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
  <script>
/* 1️⃣ On récupère tous les boutons de taille */
    const sizeBtns = document.querySelectorAll(".size");

/* 2️⃣ On récupère l’input caché */
    const sizeInput = document.getElementById("selectedSize");

/* 3️⃣ On récupère l’endroit où le prix est affiché */
    const priceSpan = document.getElementById("price");

/* 4️⃣ Tableau des prix (AFFICHAGE UNIQUEMENT) */
    const prixParTaille = {
      30: 19.90,
      60: 34.90,
      90: 49.90,
      120: 64.90
    };

/* 5️⃣ Quand on clique sur un bouton */
    sizeBtns.forEach(btn => {
      btn.addEventListener("click", () => {

        /* a) On enlève la sélection sur tous */
        sizeBtns.forEach(b => b.classList.remove("selected"));

        /* b) On sélectionne le bouton cliqué */
        btn.classList.add("selected");

        /* c) On récupère la taille du bouton */
        const taille = btn.dataset.size;

        /* d) On enregistre la taille pour le PHP */
        sizeInput.value = taille;

        /* e) On change le prix affiché */
        priceSpan.textContent = prixParTaille[taille].toFixed(2);
       });
    });
  </script>


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
<script>
  // Images miniatures
  const thumbs = document.querySelectorAll(".thumb");

  // Image principale
  const mainImage = document.querySelector(".product-main");

  thumbs.forEach(thumb => {
    thumb.addEventListener("click", () => {

      // 1️⃣ Changer l’image principale
      mainImage.src = thumb.src;

      // 2️⃣ Enlever l'état actif de toutes les miniatures
      thumbs.forEach(t => t.classList.remove("active"));

      // 3️⃣ Ajouter l'état actif à la miniature cliquée
      thumb.classList.add("active");
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
