<?php
session_start();
if (!isset($_SESSION["telephone"])) {
    header("Location: etape4_coordonnees.php");
    exit;
}
?>
<h2>Résumé de votre rendez-vous</h2>
<ul>
    <li><strong>Motif :</strong> <?= $_SESSION["motif"] ?></li>
    <li><strong>Spécialiste :</strong> <?= $_SESSION["praticien"] ?></li>
    <li><strong>Date :</strong> <?= $_SESSION["date_rdv"] ?></li>
    <li><strong>Heure :</strong> <?= $_SESSION["heure_rdv"] ?></li>
    <li><strong>Mode :</strong> <?= $_SESSION["mode"] ?></li>
</ul>

<form method="POST" action="etape6_confirmation.php">
    <button type="submit">Confirmer</button>
</form>
