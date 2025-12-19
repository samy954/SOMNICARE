<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

/* ======================
   CONNEXION BDD
====================== */
$conn = new mysqli("localhost", "root", "root", "somnicare");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

/* ======================
   SÉCURITÉ
====================== */
if (!isset($_SESSION["id_utilisateur"])) {
    header("Location: connexion.php");
    exit();
}
$id_utilisateur = $_SESSION["id_utilisateur"];

/* ======================
   UTILISATEUR
====================== */
$stmt = $conn->prepare("
    SELECT nom, prenom, email,
    DATE_FORMAT(date_creation,'%d/%m/%Y') AS date_creation
    FROM utilisateurs
    WHERE id_utilisateur = ?
");
$stmt->bind_param("i", $id_utilisateur);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

/* ======================
   RENDEZ-VOUS
====================== */
$stmt = $conn->prepare("
    SELECT id_rdv, date_rdv, heure_rdv, statut
    FROM rendez_vous
    WHERE id_client = ? AND date_rdv >= CURDATE()
    ORDER BY date_rdv ASC
");
$stmt->bind_param("i", $id_utilisateur);
$stmt->execute();
$rdv_avenir = $stmt->get_result();

$stmt = $conn->prepare("
    SELECT id_rdv, date_rdv, heure_rdv, statut
    FROM rendez_vous
    WHERE id_client = ? AND date_rdv < CURDATE()
    ORDER BY date_rdv DESC
");
$stmt->bind_param("i", $id_utilisateur);
$stmt->execute();
$rdv_passe = $stmt->get_result();

/* ======================
   COMMANDES
====================== */
$stmt = $conn->prepare("
    SELECT id_commande,
           DATE_FORMAT(date_commande,'%d/%m/%Y') AS date_commande,
           statut, total
    FROM commandes
    WHERE id_client = ?
    ORDER BY date_commande DESC
");
$stmt->bind_param("i", $id_utilisateur);
$stmt->execute();
$commandes = $stmt->get_result();

/* ======================
   STATISTIQUES
====================== */
$nb_rdv_avenir = $conn->query("
    SELECT COUNT(*) c FROM rendez_vous
    WHERE id_client=$id_utilisateur AND date_rdv>=CURDATE()
")->fetch_assoc()["c"];

$nb_rdv_passe = $conn->query("
    SELECT COUNT(*) c FROM rendez_vous
    WHERE id_client=$id_utilisateur AND date_rdv<CURDATE()
")->fetch_assoc()["c"];

$nb_cmd = $conn->query("
    SELECT COUNT(*) c FROM commandes
    WHERE id_client=$id_utilisateur
")->fetch_assoc()["c"];

/* ======================
   SUIVI SOMMEIL
====================== */
$stmt = $conn->prepare("
    SELECT
        date_nuit,
        qualite,
        reveils_nocturnes,
        commentaire,
        ROUND(
            TIMESTAMPDIFF(
                MINUTE,
                heure_coucher,
                IF(heure_lever < heure_coucher,
                   DATE_ADD(heure_lever, INTERVAL 1 DAY),
                   heure_lever)
            ) / 60, 2
        ) AS duree
    FROM suivi_sommeil
    WHERE id_patient = ?
    ORDER BY date_nuit ASC
");
$stmt->bind_param("i", $id_utilisateur);
$stmt->execute();
$nuits = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$dates = $durees = $qualites = [];
foreach ($nuits as $n) {
    $dates[] = date('d/m', strtotime($n["date_nuit"]));
    $durees[] = (float)$n["duree"];
    $qualites[] = (int)$n["qualite"];
}
?>

<!DOCTYPE html>
    <html lang="fr">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <div style="margin-bottom:20px;">
                <a href="suivi_sommeil.php" class="btn-primary">
                    Accéder à mon suivi du sommeil
                </a>
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
        <section class="section">
        <h2>Mon suivi du sommeil</h2>

        <?php if (empty($nuits)): ?>
            <p>Aucune donnée enregistrée pour le moment.</p>
        <?php else: ?>

        <!-- GRAPHIQUES -->
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:30px; margin-bottom:40px;">
            <div class="charts">
                <div class="chart-box">
                    <canvas id="chartDuree"></canvas>
                </div>
                <div class="chart-box">
                    <canvas id="chartQualite"></canvas>
                </div>
            </div>

        </div>

        <!-- HISTORIQUE -->
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Durée</th>
                    <th>Qualité</th>
                    <th>Réveils</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($nuits as $n): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($n['date_nuit'])) ?></td>
                    <td><?= htmlspecialchars($n['duree']) ?> h</td>
                    <td><?= htmlspecialchars($n['qualite']) ?>/5</td>
                    <td><?= htmlspecialchars($n['reveils_nocturnes']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php endif; ?>
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
    <?php if (!empty($nuits)): ?>
    <script>
    const labels = <?= json_encode($dates) ?>;

    // Durée
    new Chart(document.getElementById('chartDuree'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Durée de sommeil (h)',
                data: <?= json_encode($durees) ?>,
                borderColor: '#42095C',
                backgroundColor: 'rgba(66,9,92,0.15)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Qualité
    new Chart(document.getElementById('chartQualite'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Qualité du sommeil',
                data: <?= json_encode($qualites) ?>,
                borderColor: '#b84a7e',
                backgroundColor: 'rgba(184,74,126,0.15)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
        
    </script>
    <?php endif; ?>
    </body>
</html>
