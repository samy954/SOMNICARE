<?php
session_start();
require_once("../config.php");

//Sécurité : accès direct interdit sans praticien
if (!isset($_SESSION["id_specialiste"]) || !isset($_SESSION["nom_specialiste"])) {
    header("Location: etape2_praticien.php");
    exit;
}

$id_specialiste = $_SESSION["id_specialiste"];
$nom_specialiste = $_SESSION["nom_specialiste"];
$error = "";

// Créneaux horaires
$creneaux = [
    "08:00", "08:45", "09:30", "10:15", "11:00", "11:45", "12:30",
    "13:15", "14:00", "14:45", "15:30", "16:15", "17:00", "17:45",
    "18:30", "19:15"
];


$dateSelectionnee = $_POST["date_rdv"] ?? date("Y-m-d");
$creneauxIndisponibles = [];

//Vérification des créneaux disponibles 
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["check_date"])) {
    $stmt = $conn->prepare("SELECT heure_rdv FROM rendez_vous WHERE id_specialiste = ? AND date_rdv = ?");
    $stmt->bind_param("is", $id_specialiste, $dateSelectionnee);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $creneauxIndisponibles[] = substr($row["heure_rdv"], 0, 5);
    }

    $stmt->close();
}

//  Validation de la sélection 
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["valider_rdv"])) {
    $date_rdv = $_POST["date_rdv"] ?? "";
    $heure_rdv = $_POST["heure_rdv"] ?? "";
    $mode = $_POST["mode"] ?? "";

    if (empty($date_rdv) || empty($heure_rdv) || empty($mode)) {
        $error = "Veuillez sélectionner la date, l’heure et le mode de consultation.";
    } else {
        // Enregistrement temporaire en session
        $_SESSION["date_rdv"] = $date_rdv;
        $_SESSION["heure_rdv"] = $heure_rdv;
        $_SESSION["mode"] = $mode;

        header("Location: etape4_coordonnees.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choisir la date et l’heure - SomniCare</title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/reserver_date_heure.css">
</head>
<body>
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
                </ul>
            </nav>
            <div class="header-right">
                <a href="../espace.php" class="btn-identifier">Mon espace</a>
            </div>
        </div>
    </div>
</header>

<section class="page-header">
    <div class="container">
        <h1>Prendre rendez-vous</h1>
        <p>Sélectionnez la date et le créneau horaire pour votre consultation avec le 
           <strong>Dr. <?= htmlspecialchars($nom_specialiste); ?></strong></p>
    </div>
</section>

<section class="rdv-section">
    <div class="container">
        <?php if ($error): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Formulaire : Choix de la date -->
        <form method="POST" class="form-rdv">
            <div class="form-group">
                <label for="date_rdv">📅 Choisissez une date :</label>
                <input type="date" id="date_rdv" name="date_rdv"
                       value="<?= htmlspecialchars($dateSelectionnee) ?>" 
                       min="<?= date('Y-m-d') ?>" required>
                <button type="submit" name="check_date" class="btn-check">Afficher les créneaux</button>
            </div>
        </form>

        <!-- Affichage des créneaux uniquement après clic -->
        <?php if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["check_date"])): ?>
        <form method="POST">
            <input type="hidden" name="date_rdv" value="<?= htmlspecialchars($dateSelectionnee) ?>">

            <h3>🕒 Sélectionnez un créneau horaire :</h3>
            <div class="grid-creneaux">
                <?php foreach ($creneaux as $h): 
                    $disabled = in_array($h, $creneauxIndisponibles);
                    $id = "cr_" . str_replace(":", "", $h);
                ?>
                <div class="creneau <?= $disabled ? 'disabled' : ''; ?>">
                    <input type="radio" id="<?= $id ?>" name="heure_rdv" value="<?= $h ?>" <?= $disabled ? 'disabled' : '' ?>>
                    <label for="<?= $id ?>"><?= $h ?></label>
                </div>
                <?php endforeach; ?>
            </div>

            <h3>💻 Mode de consultation :</h3>
            <div class="mode-select">
                <label><input type="radio" name="mode" value="visio" required> Visio</label>
                <label><input type="radio" name="mode" value="presentiel"> Présentiel</label>
            </div>

            <div class="buttons">
                <a href="../espace.php" class="btn btn-secondary">Annuler</a>
                <button type="submit" name="valider_rdv" class="btn btn-primary">Continuer</button>
            </div>
        </form>
        <?php endif; ?>
    </div>
</section>

<!-- Animation JS visuelle sur sélection -->
<script>
document.querySelectorAll('.creneau input').forEach(radio => {
  radio.addEventListener('change', () => {
    document.querySelectorAll('.creneau label').forEach(l => l.classList.remove('active'));
    const selectedLabel = document.querySelector('label[for="' + radio.id + '"]');
    selectedLabel.classList.add('active');
  });
});
</script>
</body>
</html>
