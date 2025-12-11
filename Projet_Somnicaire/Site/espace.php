<?php

session_start();


// --- Connexion BDD ---
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "somnicare";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// --- Sécurité : accès réservé aux utilisateurs connectés ---
if (!isset($_SESSION["id_utilisateur"])) {
    header("Location: connexion.php");
    exit();
}

$id_utilisateur = $_SESSION["id_utilisateur"];

// --- Récupération infos utilisateur ---
$sql_user = "SELECT nom, prenom, email, DATE_FORMAT(date_creation, '%d/%m/%Y') AS date_creation FROM utilisateurs WHERE id_utilisateur = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $id_utilisateur);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

// --- Récupération des rendez-vous ---
$sql_rdv_avenir = "
SELECT id_rdv, date_rdv, heure_rdv, statut
FROM rendez_vous
WHERE id_client = ? AND date_rdv >= CURDATE()
ORDER BY date_rdv ASC";
$stmt_avenir = $conn->prepare($sql_rdv_avenir);
$stmt_avenir->bind_param("i", $id_utilisateur);
$stmt_avenir->execute();
$rdv_avenir = $stmt_avenir->get_result();

$sql_rdv_passe = "
SELECT id_rdv, date_rdv, heure_rdv, statut
FROM rendez_vous
WHERE id_client = ? AND date_rdv < CURDATE()
ORDER BY date_rdv DESC";
$stmt_passe = $conn->prepare($sql_rdv_passe);
$stmt_passe->bind_param("i", $id_utilisateur);
$stmt_passe->execute();
$rdv_passe = $stmt_passe->get_result();

// --- Récupération des commandes ---
$sql_cmd = "
SELECT id_commande, DATE_FORMAT(date_commande, '%d/%m/%Y') AS date_commande, statut, total
FROM commandes
WHERE id_client = ?
ORDER BY date_commande DESC";
$stmt_cmd = $conn->prepare($sql_cmd);
$stmt_cmd->bind_param("i", $id_utilisateur);
$stmt_cmd->execute();
$commandes = $stmt_cmd->get_result();

// --- Statistiques ---
$nb_rdv_total = $conn->query("SELECT COUNT(*) AS total FROM rendez_vous WHERE id_client = $id_utilisateur")->fetch_assoc()["total"];
$nb_rdv_avenir = $conn->query("SELECT COUNT(*) AS total FROM rendez_vous WHERE id_client = $id_utilisateur AND date_rdv >= CURDATE()")->fetch_assoc()["total"];
$nb_rdv_passe = $conn->query("SELECT COUNT(*) AS total FROM rendez_vous WHERE id_client = $id_utilisateur AND date_rdv < CURDATE()")->fetch_assoc()["total"];
$nb_cmd = $conn->query("SELECT COUNT(*) AS total FROM commandes WHERE id_client = $id_utilisateur")->fetch_assoc()["total"];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon espace - SomniCare</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/espace.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- HEADER -->
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

    <!-- CONTENU PRINCIPAL -->
    <section class="page-header">
        <div class="container">
            <h1>Bienvenue, <?php echo htmlspecialchars($user["prenom"]); ?> </h1>
            <p>Voici votre espace personnel SomniCare</p>
        </div>
    </section>

    <section class="dashboard container">
        <div class="stats">
            <div class="stat-box">
                <h3><?php echo $nb_rdv_avenir; ?></h3>
                <p>Rendez-vous à venir</p>
            </div>
            <div class="stat-box">
                <h3><?php echo $nb_rdv_passe; ?></h3>
                <p>Rendez-vous effectués</p>
            </div>
            <div class="stat-box">
                <h3><?php echo $nb_cmd; ?></h3>
                <p>Commandes passées</p>
            </div>
        </div>

        <div class="section">
            <h2>Mes informations</h2>
            <div class="info-box">
                <p><strong>Nom :</strong> <?php echo htmlspecialchars($user["nom"]); ?></p>
                <p><strong>Prénom :</strong> <?php echo htmlspecialchars($user["prenom"]); ?></p>
                <p><strong>Email :</strong> <?php echo htmlspecialchars($user["email"]); ?></p>
                <p><strong>Date d'inscription :</strong> <?php echo $user["date_creation"]; ?></p>
            </div>
        </div>

        <div class="section">
            <h2>Mes rendez-vous à venir</h2>
            <?php if ($rdv_avenir->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Date</th>
                        <th>Heure</th>
                        <th>Spécialiste</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                    <?php while ($row = $rdv_avenir->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row["date_rdv"]; ?></td>
                            <td><?php echo substr($row["heure_rdv"], 0, 5); ?></td>
                            <td>Dr. Spécialiste 01</td>
                            <td><?php echo ucfirst($row["statut"]); ?></td>
                            <td>
                                <a href="modifier_rdv.php?id=<?php echo $row["id_rdv"]; ?>" class="btn-table">Modifier</a>
                                <a href="supprimer_rdv.php?id=<?php echo $row["id_rdv"]; ?>" class="btn-table danger">Supprimer</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>Aucun rendez-vous à venir.</p>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>Mes rendez-vous passés</h2>
            <?php if ($rdv_passe->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Date</th>
                        <th>Heure</th>
                        <th>Spécialiste</th>
                        <th>Statut</th>
                    </tr>
                    <?php while ($row = $rdv_passe->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row["date_rdv"]; ?></td>
                            <td><?php echo substr($row["heure_rdv"], 0, 5); ?></td>
                            <td>Dr. Spécialiste 01</td>
                            <td><?php echo ucfirst($row["statut"]); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>Aucun rendez-vous passé.</p>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>Mes commandes</h2>
            <?php if ($commandes->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Total</th>
                    </tr>
                    <?php while ($cmd = $commandes->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $cmd["id_commande"]; ?></td>
                            <td><?php echo $cmd["date_commande"]; ?></td>
                            <td><?php echo ucfirst($cmd["statut"]); ?></td>
                            <td><?php echo number_format($cmd["total"], 2, ',', ' '); ?> €</td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>Aucune commande enregistrée.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">SomniCare</div>
                <div class="footer-description">
                    Spécialistes du sommeil — solutions naturelles et rendez-vous médicaux personnalisés.
                </div>
            </div>
            <div class="footer-copyright">
                © 2025 SomniCare — Tous droits réservés
            </div>
        </div>
    </footer>
</body>
</html>
