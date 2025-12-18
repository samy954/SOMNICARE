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
$specialiste = $stmt->get_result()->fetch_assoc();

if (!$specialiste) {
    die("Spécialiste introuvable.");
}

$idSpecialiste = (int) $specialiste['id_specialiste'];

// =========================
// LISTE DES PATIENTS
// =========================
$stmt = $conn->prepare("
    SELECT 
        u.id_utilisateur,
        u.prenom,
        u.nom,
        COUNT(r.id_rdv) AS nb_consultations,
        MAX(r.date_rdv) AS dernier_rdv
    FROM rendez_vous r
    JOIN utilisateurs u ON r.id_client = u.id_utilisateur
    WHERE r.id_specialiste = ?
    GROUP BY u.id_utilisateur, u.prenom, u.nom
    ORDER BY dernier_rdv DESC
");
$stmt->bind_param("i", $idSpecialiste);
$stmt->execute();
$patients = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes patients | Espace Médecin</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/espace.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <div class="logo-image">
                        <img src="images/logo.png" alt="SomniCare">
                    </div>
                </div>

                <nav class="main-nav">
                    <ul class="nav-links">
                        <li><a href="index.php">Accueil</a></li>
                        <li><a href="troubles-sommeil.php">Les troubles du sommeil</a></li>
                        <li><a href="somnyl.php">Somnyl</a></li>
                        <li><a href="methode.php">Méthode</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </nav>

                <div class="header-right">
                    <a href="panier.php" class="btn-identifier"> Panier</a>
                    <a href="logout.php" class="btn-identifier"> Déconnexion</a>
                </div>
            </div>
        </div>
</header>
<header class="page-header">
    <h1>Mes patients</h1>
    <p>Patients suivis dans votre pratique SomniCare</p>
</header>

<main class="dashboard">
<div style="margin-bottom: 25px;">
    <a href="espace_medecin.php" class="btn-table">
        ← Retour à mon espace médecin
    </a>
</div>
<section class="section">
    <h2>Liste des patients</h2>

    <?php if (empty($patients)): ?>
        <p>Aucun patient suivi pour le moment.</p>
    <?php else: ?>

    <table>
        <thead>
            <tr>
                <th>Patient</th>
                <th>Consultations</th>
                <th>Dernier RDV</th>
                <th>Dossier</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($patients as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['prenom'].' '.$p['nom']); ?></td>
                <td><?= (int) $p['nb_consultations']; ?></td>
                <td>
                    <?= $p['dernier_rdv']
                        ? date('d/m/Y', strtotime($p['dernier_rdv']))
                        : '—'; ?>
                </td>
                <td>
                    <a href="medecin_dossier.php?id=<?= (int) $p['id_utilisateur']; ?>"
                       class="btn-table">
                       Voir dossier
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php endif; ?>
</section>

</main>

<footer>
    <div class="footer-content">
        <div class="footer-logo">SomniCare</div>
        <div class="footer-description">
            Gestion des patients – Espace Médecin
        </div>
    </div>
</footer>

</body>
</html>
