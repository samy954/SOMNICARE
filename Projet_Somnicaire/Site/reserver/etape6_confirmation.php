<?php
session_start();
require_once("../config.php");

if (!isset($_SESSION["motif"])) {
    header("Location: etape1_motif.php");
    exit;
}

// On suppose que l’utilisateur est connecté
$id_client = $_SESSION["id_utilisateur"] ?? 1;
$id_specialiste = 1; // Spécialiste temporaire
$date_rdv = $_SESSION["date_rdv"];
$heure_rdv = $_SESSION["heure_rdv"];

$stmt = $conn->prepare("INSERT INTO rendez_vous (id_client, id_specialiste, date_rdv, heure_rdv, statut) VALUES (?, ?, ?, ?, 'confirme')");
$stmt->bind_param("iiss", $id_client, $id_specialiste, $date_rdv, $heure_rdv);
$stmt->execute();

session_destroy(); // On vide les sessions après confirmation
?>
<h2>✅ Votre rendez-vous est confirmé</h2>
<p>Date : <?= $date_rdv ?> à <?= $heure_rdv ?></p>
<a href="../espace.php">Voir mes rendez-vous</a>
