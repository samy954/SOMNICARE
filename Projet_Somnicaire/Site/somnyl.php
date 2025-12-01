<?php
session_start(); // ✅ Pour afficher le prénom connecté dans la navbar
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Somnyl - Produit</title>
  <link rel="stylesheet" href="css/somnyl.css" />
  <link rel="stylesheet" href="css/base.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
  <!-- Header SomniCare -->
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

      <h3 class="label">TAILLE</h3>
      <div class="sizes">
        <button class="size selected">30</button>
        <button class="size">60</button>
        <button class="size">90</button>
        <button class="size">120</button>
      </div>

      <button class="add">Ajouter au panier</button>
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

  <!-- Script pour changer les images -->
  <script>
  const sizeBtns = document.querySelectorAll(".size");

  sizeBtns.forEach(btn => {
      btn.addEventListener("click", () => {
          sizeBtns.forEach(b => b.classList.remove("selected"));
          btn.classList.add("selected");
      });
  });

  const mainZone = document.querySelector(".main-img");
  const thumbsList = document.querySelectorAll(".thumb");

  thumbsList.forEach(el => {
      el.addEventListener("click", () => {
          mainZone.innerHTML = "";
          if (el.tagName === "IMG") {
              const img = document.createElement("img");
              img.src = el.src;
              img.classList.add("product-main");
              mainZone.appendChild(img);
          }
          if (el.tagName === "VIDEO") {
              const vid = document.createElement("video");
              vid.src = el.src;
              vid.controls = true;
              vid.autoplay = true;
              vid.classList.add("product-main");
              mainZone.appendChild(vid);
          }
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
