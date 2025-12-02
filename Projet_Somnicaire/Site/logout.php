<?php
session_start();

// Supprimer toutes les variables de session
$_SESSION = [];

// Détruire la session complètement
session_destroy();

// Rediriger vers la page d'accueil
header("Location: index.php");
exit();
?>
