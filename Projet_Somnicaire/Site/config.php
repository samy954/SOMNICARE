<?php
//CONFIGURATION BASE DE DONNÉES SOMNICARE
$servername = "localhost";
$username = "root";
$password = "root"; // mot de passe par défaut de MAMP
$dbname = "somnicare";

// Connexion à la base
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification d’erreur
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Encodage UTF-8
$conn->set_charset("utf8mb4");
?>
