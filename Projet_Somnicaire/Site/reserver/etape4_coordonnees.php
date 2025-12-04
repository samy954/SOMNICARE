<?php
session_start();
if (!isset($_SESSION["date_rdv"])) {
    header("Location: etape3_date_heure.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $_SESSION["telephone"] = $_POST["telephone"];
    $_SESSION["notes"] = $_POST["notes"];
    header("Location: etape5_resume.php");
    exit;
}
?>
<form method="POST">
    <label>Téléphone :</label><input type="text" name="telephone" required><br>
    <label>Notes :</label><textarea name="notes"></textarea><br>
    <button type="submit">Suivant</button>
</form>
