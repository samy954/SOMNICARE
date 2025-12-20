<?php
// =========================
// DEBUG (à enlever en prod)
// =========================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// =========================
// SESSION & DB
// =========================
session_start();
require_once "config.php"; // crée $conn (mysqli)

// =========================
// SÉCURITÉ : MÉDECIN UNIQUEMENT
// =========================
if (
    !isset($_SESSION['id_utilisateur'], $_SESSION['role']) ||
    $_SESSION['role'] !== 'specialiste'
) {
    header("Location: connexion.php");
    exit;
}

$idUtilisateur = (int) $_SESSION['id_utilisateur'];

// =========================
// RÉCUPÉRER ID SPECIALISTE
// =========================
$stmt = $conn->prepare("
    SELECT id_specialiste
    FROM specialistes
    WHERE id_utilisateur = ?
    LIMIT 1
");
$stmt->bind_param("i", $idUtilisateur);
$stmt->execute();
$result = $stmt->get_result();
$specialiste = $result->fetch_assoc();

if (!$specialiste) {
    die("Erreur : profil spécialiste introuvable.");
}

$idSpecialiste = (int) $specialiste['id_specialiste'];

// =========================
// STATISTIQUES DASHBOARD
// =========================

// RDV à venir
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM rendez_vous
    WHERE id_specialiste = ?
    AND statut = 'en_attente'
");
$stmt->bind_param("i", $idSpecialiste);
$stmt->execute();
$nbRdvAVenir = $stmt->get_result()->fetch_assoc()['total'];

// RDV aujourd’hui
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM rendez_vous
    WHERE id_specialiste = ?
    AND date_rdv = CURDATE()
");
$stmt->bind_param("i", $idSpecialiste);
$stmt->execute();
$nbRdvToday = $stmt->get_result()->fetch_assoc()['total'];

// Patients distincts
$stmt = $conn->prepare("
    SELECT COUNT(DISTINCT id_client) AS total
    FROM rendez_vous
    WHERE id_specialiste = ?
");
$stmt->bind_param("i", $idSpecialiste);
$stmt->execute();
$nbPatients = $stmt->get_result()->fetch_assoc()['total'];

// RDV terminés
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM rendez_vous
    WHERE id_specialiste = ?
    AND statut = 'termine'
");
$stmt->bind_param("i", $idSpecialiste);
$stmt->execute();
$nbRdvTermines = $stmt->get_result()->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Médecin | SomniCare</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/espace.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
<header class="page-header">
    <h1>Bonjour Dr <?= htmlspecialchars($_SESSION['prenom'] ?? '') ?></h1>
    <p>Votre espace professionnel SomniCare</p>
</header>

<main class="dashboard">

    <!-- STATISTIQUES -->
    <div class="stats">
        <div class="stat-box">
            <h3><?= $nbRdvAVenir ?></h3>
            <p>Rendez-vous à venir</p>
        </div>

        <div class="stat-box">
            <h3><?= $nbRdvToday ?></h3>
            <p>Aujourd’hui</p>
        </div>

        <div class="stat-box">
            <h3><?= $nbPatients ?></h3>
            <p>Patients suivis</p>
        </div>

        <div class="stat-box">
            <h3><?= $nbRdvTermines ?></h3>
            <p>Consultations terminées</p>
        </div>
    </div>

    <!-- ACTIONS -->
    <section class="section">
        <h2>Actions rapides</h2>
        <div style="display:flex; gap:15px; flex-wrap:wrap;">
            <a href="medecin_rdv.php" class="btn-table">📅 Gérer mes rendez-vous</a>
            <a href="medecin_patients.php" class="btn-table">👤 Voir mes patients</a>
        </div>
    </section>

</main>

<footer>
    <div class="footer-content">
        <div class="footer-logo">SomniCare</div>
        <div class="footer-description">
            Plateforme professionnelle de suivi du sommeil
        </div>
    </div>
    <div class="footer-copyright">
        © 2025 SomniCare — Espace Médecin
    </div>
</footer>

</body>
</html>
