<?php

session_start();
require_once "config.php"; // crée $conn (mysqli)


if (
    !isset($_SESSION['id_utilisateur'], $_SESSION['role']) ||
    $_SESSION['role'] !== 'specialiste'
) {
    header("Location: connexion.php");
    exit;
}

$idUtilisateur = (int) $_SESSION['id_utilisateur'];


if (!isset($_GET['id'])) {
    header("Location: medecin_patients.php");
    exit;
}
$idPatient = (int) $_GET['id'];

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


$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM rendez_vous
    WHERE id_specialiste = ?
    AND id_client = ?
");
$stmt->bind_param("ii", $idSpecialiste, $idPatient);
$stmt->execute();
$check = $stmt->get_result()->fetch_assoc();

if ($check['total'] == 0) {
    die("Accès interdit à ce dossier patient.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['note'])) {
    $note = trim($_POST['note']);

    if (strlen($note) >= 10) {
        $stmt = $conn->prepare("
            INSERT INTO notes_medicales (id_patient, id_specialiste, contenu)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("iis", $idPatient, $idSpecialiste, $note);
        $stmt->execute();
    }

    header("Location: medecin_dossier.php?id=" . $idPatient);
    exit;
}

$stmt = $conn->prepare("
    SELECT prenom, nom, email, date_creation
    FROM utilisateurs
    WHERE id_utilisateur = ?
");
$stmt->bind_param("i", $idPatient);
$stmt->execute();
$patient = $stmt->get_result()->fetch_assoc();

$stmt = $conn->prepare("
    SELECT date_rdv, heure_rdv, statut, note
    FROM rendez_vous
    WHERE id_specialiste = ?
    AND id_client = ?
    ORDER BY date_rdv DESC
");
$stmt->bind_param("ii", $idSpecialiste, $idPatient);
$stmt->execute();
$historique = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt = $conn->prepare("
    SELECT contenu, date_creation
    FROM notes_medicales
    WHERE id_patient = ?
    AND id_specialiste = ?
    ORDER BY date_creation DESC
");
$stmt->bind_param("ii", $idPatient, $idSpecialiste);
$stmt->execute();
$notes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dossier patient | SomniCare</title>
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
    <h1>Dossier patient</h1>
    <p><?= htmlspecialchars($patient['prenom'].' '.$patient['nom']); ?></p>
</header>

<main class="dashboard">
<div style="margin-bottom: 25px;">
    <a href="espace_medecin.php" class="btn-table">
        ← Retour à mon espace médecin
    </a>
</div>
<!-- INFOS PATIENT -->
<section class="section">
    <h2>Informations patient</h2>
    <div class="info-box">
        <p><strong>Nom :</strong> <?= htmlspecialchars($patient['nom']); ?></p>
        <p><strong>Prénom :</strong> <?= htmlspecialchars($patient['prenom']); ?></p>
        <p><strong>Email :</strong> <?= htmlspecialchars($patient['email']); ?></p>
        <p><strong>Suivi depuis :</strong> <?= date('d/m/Y', strtotime($patient['date_creation'])); ?></p>
    </div>
</section>

<!-- HISTORIQUE RDV -->
<section class="section">
    <h2>Historique des consultations</h2>

    <?php if (empty($historique)): ?>
        <p>Aucun rendez-vous enregistré.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Heure</th>
                <th>Statut</th>
                <th>Note patient</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($historique as $r): ?>
            <tr>
                <td><?= date('d/m/Y', strtotime($r['date_rdv'])); ?></td>
                <td><?= substr($r['heure_rdv'], 0, 5); ?></td>
                <td><?= ucfirst($r['statut']); ?></td>
                <td><?= $r['note'] ?? '—'; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</section>

<!-- NOTES MÉDICALES -->
<section class="section">
    <h2>Notes médicales</h2>

    <form method="POST">
        <textarea name="note" rows="5" required
            placeholder="Observation clinique, recommandations, suivi…"
            style="width:100%; padding:12px; border-radius:8px;"></textarea>

        <button type="submit" class="btn-table" style="margin-top:10px;">
            Enregistrer la note
        </button>
    </form>

    <hr style="margin:30px 0;">

    <?php if (empty($notes)): ?>
        <p>Aucune note médicale enregistrée.</p>
    <?php else: ?>
        <?php foreach ($notes as $n): ?>
            <div class="section" style="margin-bottom:20px;">
                <p><?= nl2br(htmlspecialchars($n['contenu'])); ?></p>
                <small style="color:#7f8c8d;">
                    <?= date('d/m/Y H:i', strtotime($n['date_creation'])); ?>
                </small>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<a href="medecin_patients.php" class="btn-table">← Retour aux patients</a>

</main>

<footer>
    <div class="footer-content">
        <div class="footer-logo">SomniCare</div>
        <div class="footer-description">
            Dossier patient sécurisé – Espace Médecin
        </div>
    </div>
</footer>

</body>
</html>
