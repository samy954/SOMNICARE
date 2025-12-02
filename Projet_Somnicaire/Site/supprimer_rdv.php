<?php
session_start();

// Vérifier que l'utilisateur est bien connecté
if (!isset($_SESSION["id_utilisateur"])) {
    header("Location: connexion.php");
    exit();
}

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "root"; // ou vide selon ta config
$dbname = "somnicare";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérifier si un id_rdv est fourni
if (!isset($_GET["id"])) {
    header("Location: espace.php");
    exit();
}

$id_rdv = intval($_GET["id"]);
$id_utilisateur = $_SESSION["id_utilisateur"];

// Vérifier que le rendez-vous appartient bien à l'utilisateur connecté
$sql_check = "SELECT id_rdv FROM rendez_vous WHERE id_rdv = ? AND id_client = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $id_rdv, $id_utilisateur);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows === 0) {
    echo "<p style='text-align:center; margin-top:50px;'>⚠️ Ce rendez-vous n'existe pas ou ne vous appartient pas.</p>";
    exit();
}

// Supprimer le rendez-vous
$sql_delete = "DELETE FROM rendez_vous WHERE id_rdv = ? AND id_client = ?";
$stmt_delete = $conn->prepare($sql_delete);
$stmt_delete->bind_param("ii", $id_rdv, $id_utilisateur);

if ($stmt_delete->execute()) {
    header("Location: espace.php?deleted=1");
    exit();
} else {
    echo "<p style='text-align:center; margin-top:50px;'>❌ Erreur lors de la suppression du rendez-vous.</p>";
}

$conn->close();
?>
