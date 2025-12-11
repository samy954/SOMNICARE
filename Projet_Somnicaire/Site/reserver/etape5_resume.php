<?php
session_start();
require_once("../config.php");

// === Vérification du cheminement logique ===
if (!isset($_SESSION["id_utilisateur"], $_SESSION["id_specialiste"], $_SESSION["motif"], $_SESSION["date_rdv"], $_SESSION["heure_rdv"], $_SESSION["mode"])) {
    header("Location: etape1_motif.php");
    exit;
}

$id_client = $_SESSION["id_utilisateur"];
$id_specialiste = $_SESSION["id_specialiste"];
$motif = $_SESSION["motif"];
$date_rdv = $_SESSION["date_rdv"];
$heure_rdv = $_SESSION["heure_rdv"];
$mode = $_SESSION["mode"];

$error = "";
$success = "";

// === Récupération des infos du spécialiste ===
$stmt = $conn->prepare("
    SELECT u.nom, u.prenom, s.tarif_consultation 
    FROM specialistes s 
    JOIN utilisateurs u ON s.id_utilisateur = u.id_utilisateur 
    WHERE s.id_specialiste = ?
");
$stmt->bind_param("i", $id_specialiste);
$stmt->execute();
$stmt->bind_result($nom_spec, $prenom_spec, $tarif);
$stmt->fetch();
$stmt->close();

// Valeur par défaut si manquante
$tarif = $tarif ?: 60.00;

// === Validation de la réservation ===
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["confirmer_rdv"])) {
    $stmt = $conn->prepare("
        INSERT INTO rendez_vous (id_client, id_specialiste, date_rdv, heure_rdv, statut)
        VALUES (?, ?, ?, ?, 'en_attente')
    ");
    $stmt->bind_param("iiss", $id_client, $id_specialiste, $date_rdv, $heure_rdv);

    if ($stmt->execute()) {
        $_SESSION["confirmation_id"] = $stmt->insert_id;
        $stmt->close();
        header("Location: etape6_confirmation.php");
        exit;
    } else {
        $error = "Une erreur est survenue lors de l’enregistrement de votre rendez-vous.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résumé du rendez-vous - SomniCare</title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/reserver_resume.css">
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
                    <li><a href="../troubles-sommeil.php">Troubles du sommeil</a></li>
                    <li><a href="../somnyl.php">Somnyl</a></li>
                    <li><a href="../methode.php">Méthode</a></li>
                    <li><a href="../contact.php">Contact</a></li>
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

<!-- === PAGE HEADER === -->
<section class="page-header">
    <div class="container">
        <h1>Résumé de votre consultation</h1>
        <p>Vérifiez les détails avant de confirmer votre rendez-vous.</p>
    </div>
</section>

<!-- === SECTION PRINCIPALE === -->
<section class="resume-section">
    <div class="container grid">

        <!-- COLONNE GAUCHE : Infos RDV -->
        <div class="resume-left">
            <?php if (!empty($error)): ?>
                <div class="alert error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="info-card">
                <h2>🩺 Détails du rendez-vous</h2>
                <p><strong>Motif :</strong> <?= htmlspecialchars($motif); ?></p>
                <p><strong>Spécialiste :</strong> Dr. <?= htmlspecialchars($prenom_spec . " " . $nom_spec); ?></p>
                <p><strong>Date :</strong> <?= date("d/m/Y", strtotime($date_rdv)); ?></p>
                <p><strong>Heure :</strong> <?= htmlspecialchars($heure_rdv); ?></p>
                <p><strong>Mode :</strong> <?= ucfirst(htmlspecialchars($mode)); ?></p>
                <p><strong>Durée estimée :</strong> 45–60 min</p>
            </div>

            <!-- Boutons -->
            <div class="buttons">
                <a href="etape4_coordonnees.php" class="btn btn-secondary">⬅ Modifier mes infos</a>
                <form method="POST" class="inline-form">
                    <button type="submit" name="confirmer_rdv" class="btn btn-primary">Confirmer mon rendez-vous ✅</button>
                </form>
            </div>
        </div>

        <!-- COLONNE DROITE : Paiement -->
        <div class="resume-right">
            <div class="payment-card">
                <h3>💰 Paiement</h3>
                <div class="price">
                    <p>Tarif de la séance :</p>
                    <p class="price-amount"><strong><?= number_format($tarif, 2, ',', ' '); ?> €</strong></p>
                </div>

                <div class="payment-method">
                    <p><strong>Moyen de paiement :</strong></p>
                    <p>💳 Carte enregistrée : Visa **** 4242</p>
                    <button class="btn-edit" disabled>Modifier</button>
                </div>

                <div class="secure-info">
                    <p>🔒 Paiement sécurisé — Données chiffrées SSL</p>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- === FOOTER === -->
<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-logo">SomniCare</div>
            <div class="footer-description">
                Spécialistes du sommeil — réservations avec des médecins spécialisés et solutions naturelles validées.
            </div>
        </div>
        <div class="footer-copyright">
            © 2025 SomniCare — Tous droits réservés
        </div>
    </div>
</footer>

</body>
</html>
