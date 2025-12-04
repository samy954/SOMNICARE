<?php
session_start();
require_once("../config.php");

// Vérifie que le praticien est sélectionné
if (!isset($_SESSION["id_specialiste"]) || !isset($_SESSION["nom_specialiste"])) {
    header("Location: etape2_praticien.php");
    exit;
}

$id_specialiste = $_SESSION["id_specialiste"];
$nom_specialiste = $_SESSION["nom_specialiste"];
$error = "";

// Créneaux horaires (45 min)
$creneaux = [
    "08:00", "08:45", "09:30", "10:15", "11:00", "11:45", "12:30",
    "13:15", "14:00", "14:45", "15:30", "16:15", "17:00", "17:45",
    "18:30", "19:15"
];

$dateSelectionnee = $_POST["date_rdv"] ?? date("Y-m-d");
$creneauxIndisponibles = [];

// Récupère les créneaux déjà pris
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

// Validation finale
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["valider_rdv"])) {
    $date_rdv = $_POST["date_rdv"];
    $heure_rdv = $_POST["heure_rdv"];
    $mode = $_POST["mode"];

    if (empty($date_rdv) || empty($heure_rdv) || empty($mode)) {
        $error = "Veuillez sélectionner la date, l’heure et le mode de consultation.";
    } else {
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
    <style>
    /* === Correction du style interactif === */
    .grid-creneaux {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 12px;
        margin: 25px 0;
    }

    .creneau {
        position: relative;
    }

    .creneau input {
        display: none;
    }

    .creneau label {
        display: block;
        background: #f0f0f7;
        border: 1px solid #ddd;
        border-radius: 8px;
        text-align: center;
        font-weight: 600;
        color: #2c3e50;
        cursor: pointer;
        padding: 12px;
        transition: all 0.25s ease;
    }

    .creneau label:hover {
        background-color: #e2dff5;
        border-color: #4b3f72;
    }

    .creneau input:checked + label {
        background-color: #4b3f72;
        color: #fff;
        transform: scale(1.05);
        box-shadow: 0 0 8px rgba(75, 63, 114, 0.3);
    }

    .creneau.disabled label {
        background-color: #ddd;
        color: #888;
        cursor: not-allowed;
        opacity: 0.6;
        transform: none;
        box-shadow: none;
    }
    </style>
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
        <p>Sélectionnez la date et le créneau horaire pour votre consultation avec le Dr.
           <strong><?= htmlspecialchars($nom_specialiste); ?></strong></p>
    </div>
</section>

<section class="rdv-section">
    <div class="container">
        <?php if ($error): ?><div class="alert error"><?= $error ?></div><?php endif; ?>

        <form method="POST" class="form-rdv">
            <div class="form-group">
                <label for="date_rdv">📅 Choisissez une date :</label>
                <input type="date" id="date_rdv" name="date_rdv"
                       value="<?= $dateSelectionnee ?>" min="<?= date('Y-m-d') ?>" required>
                <button type="submit" name="check_date" class="btn-check">Afficher les créneaux</button>
            </div>
        </form>

        <?php if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["check_date"])): ?>
            <form method="POST">
                <input type="hidden" name="date_rdv" value="<?= $dateSelectionnee ?>">

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
</body>
</html>
