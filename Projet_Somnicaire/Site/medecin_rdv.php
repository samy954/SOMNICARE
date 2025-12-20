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
// ACTIONS RDV (POST)
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id_rdv'])) {

    $idRdv  = (int) $_POST['id_rdv'];
    $action = $_POST['action'];

    $statutsAutorises = ['confirme', 'termine', 'annule'];

    if (in_array($action, $statutsAutorises, true)) {

        $stmt = $conn->prepare("
            UPDATE rendez_vous
            SET statut = ?
            WHERE id_rdv = ?
            AND id_specialiste = ?
        ");
        $stmt->bind_param("sii", $action, $idRdv, $idSpecialiste);
        $stmt->execute();
    }

    header("Location: medecin_rdv.php");
    exit;
}

// =========================
// LISTE DES RDV
// =========================
$stmt = $conn->prepare("
    SELECT r.id_rdv, r.date_rdv, r.heure_rdv, r.statut,
           u.prenom, u.nom
    FROM rendez_vous r
    JOIN utilisateurs u ON r.id_client = u.id_utilisateur
    WHERE r.id_specialiste = ?
    ORDER BY r.date_rdv ASC, r.heure_rdv ASC
");
$stmt->bind_param("i", $idSpecialiste);
$stmt->execute();
$rendezVous = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes rendez-vous | Espace Médecin</title>
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
    <h1>Mes rendez-vous</h1>
    <p>Gestion de vos consultations SomniCare</p>
</header>

<main class="dashboard">
<div style="margin-bottom: 25px;">
    <a href="espace_medecin.php" class="btn-table">
        ← Retour à mon espace médecin
    </a>
</div>
<section class="section">
    <h2>Liste des rendez-vous</h2>

    <?php if (empty($rendezVous)): ?>
        <p>Aucun rendez-vous pour le moment.</p>
    <?php else: ?>

    <table>
        <thead>
            <tr>
                <th>Patient</th>
                <th>Date</th>
                <th>Heure</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rendezVous as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['prenom'].' '.$r['nom']); ?></td>
                <td><?= date('d/m/Y', strtotime($r['date_rdv'])); ?></td>
                <td><?= substr($r['heure_rdv'], 0, 5); ?></td>
                <td>
                    <?php
                        switch ($r['statut']) {
                            case 'en_attente': echo '🕒 En attente'; break;
                            case 'confirme':   echo '✅ Confirmé'; break;
                            case 'termine':    echo '✔️ Terminé'; break;
                            case 'annule':     echo '❌ Annulé'; break;
                        }
                    ?>
                </td>
                <td>
                    <form method="POST" style="display:inline-block">
                        <input type="hidden" name="id_rdv" value="<?= $r['id_rdv']; ?>">

                        <?php if ($r['statut'] === 'en_attente'): ?>
                            <button class="btn-table" name="action" value="confirme">Confirmer</button>
                        <?php endif; ?>

                        <?php if ($r['statut'] === 'confirme'): ?>
                            <button class="btn-table" name="action" value="termine">Terminer</button>
                        <?php endif; ?>

                        <?php if ($r['statut'] !== 'annule'): ?>
                            <button class="btn-table danger" name="action" value="annule">Annuler</button>
                        <?php endif; ?>
                    </form>
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
            Gestion des rendez-vous – Espace Médecin
        </div>
    </div>
</footer>

</body>
</html>
