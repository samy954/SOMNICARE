<?php
session_start();

// --- Vérifie si l'utilisateur est connecté ---
if (!isset($_SESSION["id_utilisateur"])) {
    header("Location: connexion.php");
    exit();
}

// --- Connexion à la base ---
$servername = "localhost";
$username = "root";
$password = "root"; // ou vide selon ta config
$dbname = "somnicare";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$id_rdv = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
$id_utilisateur = $_SESSION["id_utilisateur"];

// --- Récupération des infos du rendez-vous ---
$sql = "SELECT * FROM rendez_vous WHERE id_rdv = ? AND id_client = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_rdv, $id_utilisateur);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p style='text-align:center; margin-top:50px;'>Aucun rendez-vous trouvé.</p>";
    exit();
}

$rdv = $result->fetch_assoc();

// --- Si le formulaire est soumis ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nouvelle_date = $_POST["date_rdv"];
    $nouvelle_heure = $_POST["heure_rdv"];

    if (!empty($nouvelle_date) && !empty($nouvelle_heure)) {
        $update_sql = "UPDATE rendez_vous SET date_rdv = ?, heure_rdv = ?, statut = 'en_attente' WHERE id_rdv = ? AND id_client = ?";
        $stmt_update = $conn->prepare($update_sql);
        $stmt_update->bind_param("ssii", $nouvelle_date, $nouvelle_heure, $id_rdv, $id_utilisateur);
        if ($stmt_update->execute()) {
            header("Location: espace.php?success=1");
            exit();
        } else {
            $error = "Erreur lors de la mise à jour du rendez-vous.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier rendez-vous - SomniCare</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/espace.css">
    <style>
        .form-container {
            max-width: 500px;
            margin: 80px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 25px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            color: #2c3e50;
        }

        input {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        button {
            background-color: #6c63ff;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #584be8;
        }

        .cancel {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #555;
            text-decoration: none;
        }

        .cancel:hover {
            color: #6c63ff;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Modifier votre rendez-vous</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Date actuelle :</label>
        <input type="text" value="<?php echo htmlspecialchars($rdv['date_rdv']); ?>" readonly>

        <label>Nouvelle date :</label>
        <input type="date" name="date_rdv" required>

        <label>Heure actuelle :</label>
        <input type="text" value="<?php echo htmlspecialchars(substr($rdv['heure_rdv'], 0, 5)); ?>" readonly>

        <label>Nouvelle heure :</label>
        <input type="time" name="heure_rdv" required>

        <button type="submit">Enregistrer les modifications</button>
    </form>

    <a href="espace.php" class="cancel">⟵ Retour à mon espace</a>
</div>

</body>
</html>
