<?php
session_start();
require_once("../config.php");

// === Vérifie l'étape précédente ===
if (!isset($_SESSION["date_rdv"]) || !isset($_SESSION["heure_rdv"]) || !isset($_SESSION["mode"])) {
    header("Location: etape3_date_heure.php");
    exit;
}

$error = "";

// === Si le formulaire est soumis ===
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Nettoyage basique
    $prenom = htmlspecialchars(trim($_POST["prenom"] ?? ""));
    $nom = htmlspecialchars(trim($_POST["nom"] ?? ""));
    $email = htmlspecialchars(trim($_POST["email"] ?? ""));
    $telephone = htmlspecialchars(trim($_POST["telephone"] ?? ""));
    $age = htmlspecialchars(trim($_POST["age"] ?? ""));
    $sexe = htmlspecialchars(trim($_POST["sexe"] ?? ""));
    $notes = htmlspecialchars(trim($_POST["notes"] ?? ""));

    // Validation
    if (empty($prenom) || empty($nom) || empty($email) || empty($telephone)) {
        $error = "Veuillez remplir tous les champs obligatoires marqués d’une étoile (*).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "L’adresse email saisie est invalide.";
    } else {
        // Enregistrement des coordonnées en session
        $_SESSION["coordonnees"] = [
            "prenom" => $prenom,
            "nom" => $nom,
            "email" => $email,
            "telephone" => $telephone,
            "age" => $age,
            "sexe" => $sexe,
            "notes" => $notes
        ];

        // Redirection vers l’étape suivante
        header("Location: etape5_resume.php");
        exit;
    }
}

// Pré-remplissage si retour arrière
$ancien = $_SESSION["coordonnees"] ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coordonnées - SomniCare</title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/reserver_coordonnees.css">
</head>
<body>

<!-- === HEADER GLOBAL === -->
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

<!-- === TITRE === -->
<section class="page-header">
    <div class="container">
        <h1>Dites-nous en plus</h1>
        <p>Vos informations nous permettront de préparer au mieux votre consultation.</p>
    </div>
</section>

<!-- === FORMULAIRE === -->
<section class="form-section">
    <div class="container form-container">

        <?php if (!empty($error)): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="coordonnees-form">
            <div class="form-grid">
                <input type="text" name="prenom" placeholder="Prénom *" value="<?= $ancien['prenom'] ?? '' ?>" required>
                <input type="text" name="nom" placeholder="Nom *" value="<?= $ancien['nom'] ?? '' ?>" required>
                <input type="email" name="email" placeholder="Adresse email *" value="<?= $ancien['email'] ?? '' ?>" required>
                <input type="text" name="telephone" placeholder="Numéro de téléphone *" value="<?= $ancien['telephone'] ?? '' ?>" required>
                <input type="number" name="age" placeholder="Âge" min="0" max="120" value="<?= $ancien['age'] ?? '' ?>">
                <select name="sexe">
                    <option value="">Sexe</option>
                    <option value="Homme" <?= (isset($ancien['sexe']) && $ancien['sexe'] === 'Homme') ? 'selected' : '' ?>>Homme</option>
                    <option value="Femme" <?= (isset($ancien['sexe']) && $ancien['sexe'] === 'Femme') ? 'selected' : '' ?>>Femme</option>
                    <option value="Autre" <?= (isset($ancien['sexe']) && $ancien['sexe'] === 'Autre') ? 'selected' : '' ?>>Autre</option>
                </select>
            </div>

            <textarea name="notes" rows="4" placeholder="Notes pour le praticien (symptômes, traitements en cours…)"><?= $ancien['notes'] ?? '' ?></textarea>

            <div class="info-box">
                <p>🔒 Vos données sont chiffrées et ne sont accessibles qu’à votre praticien SomniCare.</p>
            </div>

            <div class="buttons">
                <a href="etape3_date_heure.php" class="btn btn-secondary">⬅ Retour</a>
                <button type="submit" class="btn btn-primary">Voir le résumé ➜</button>
            </div>
        </form>
    </div>
</section>

<!-- === FOOTER === -->
<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-logo">SomniCare</div>
            <div class="footer-description">
                Spécialistes du sommeil — consultations avec des médecins spécialisés et solutions naturelles validées.
            </div>
        </div>
        <div class="footer-copyright">
            © 2025 SomniCare — Tous droits réservés
        </div>
    </div>
</footer>

</body>
</html>
