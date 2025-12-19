<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'client') {
    header("Location: connexion.php");
    exit;
}

$idPatient = (int) $_SESSION['id_utilisateur'];
$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $duree = $_POST['duree'];
    $qualite = $_POST['qualite'];
    $endormissement = $_POST['endormissement'];
    $reveils = $_POST['reveils'];
    $fatigue = $_POST['fatigue'];
    $commentaire = trim($_POST['commentaire']);

    if ($date && $duree && $qualite) {
        $stmt = $conn->prepare("
            INSERT INTO suivi_sommeil
            (id_patient, date_suivi, duree_sommeil, qualite_sommeil,
             temps_endormissement, reveils_nocturnes, fatigue_reveil, commentaire)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "isdi iiis",
            $idPatient,
            $date,
            $duree,
            $qualite,
            $endormissement,
            $reveils,
            $fatigue,
            $commentaire
        );

        if ($stmt->execute()) {
            $success = "Suivi enregistré avec succès.";
        } else {
            $error = "Erreur lors de l'enregistrement.";
        }
    } else {
        $error = "Veuillez remplir les champs obligatoires.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suivi du sommeil | SomniCare</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/espace.css">
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

                <?php else: ?>
                    <a href="connexion.php" class="btn-identifier">S'identifier</a>
                <?php endif; ?>

            </div>
        </div>
    </div>
</header>
<header class="page-header">
    <h1>Mon suivi du sommeil</h1>
    <p>Renseignez vos nuits pour un accompagnement personnalisé</p>
</header>

<main class="dashboard">

    <!-- Bouton retour -->
    <div class="section" style="padding:15px;">
        <a href="espace.php" class="btn-table">
            ← Retour à mon espace
        </a>
    </div>

    <!-- Carte principale -->
    <section class="section">
        <h2>Nouvelle nuit</h2>

        <?php if (!empty($success)): ?>
            <div class="alert success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="info-box">

            <div class="form-group">
                <label>Date de la nuit</label>
                <input type="date" name="date" required>
            </div>

            <div class="form-group">
                <label>Durée de sommeil (heures)</label>
                <input type="number" name="duree" step="0.5" min="0" max="24" required>
            </div>

            <div class="form-group">
                <label>Qualité du sommeil (1 = très mauvaise · 5 = excellente)</label>
                <input type="range" name="qualite" min="1" max="5" value="3">
            </div>

            <div class="form-group">
                <label>Temps d’endormissement (minutes)</label>
                <input type="number" name="endormissement" min="0">
            </div>

            <div class="form-group">
                <label>Nombre de réveils nocturnes</label>
                <input type="number" name="reveils" min="0">
            </div>

            <div class="form-group">
                <label>Fatigue au réveil (1 = aucune · 5 = extrême)</label>
                <input type="range" name="fatigue" min="1" max="5" value="3">
            </div>

            <div class="form-group">
                <label>Commentaire libre</label>
                <textarea name="commentaire" rows="4"
                    placeholder="Stress, écrans, sport, café, médicaments, événements particuliers…"></textarea>
            </div>

            <button type="submit" class="btn-table">
                Enregistrer la nuit
            </button>

        </form>
    </section>

</main>

<footer>
    <div class="footer-content">
        <div class="footer-logo">SomniCare</div>
        <div class="footer-description">
            Suivi personnel du sommeil – Données confidentielles
        </div>
    </div>
</footer>

</body>
</html>
