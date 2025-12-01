<?php
session_start();

// === Connexion à la base de données ===
$servername = "localhost";
$username = "root";
$password = "root"; // ou "" selon ta config MAMP
$dbname = "somnicare";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$error = "";

// === Traitement du formulaire ===
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $mot_de_passe = $_POST["mot_de_passe"];

    if (empty($email) || empty($mot_de_passe)) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        $stmt = $conn->prepare("SELECT id_utilisateur, nom, prenom, email, mot_de_passe, role FROM utilisateurs WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($mot_de_passe, $user["mot_de_passe"])) {
                // Connexion réussie : on crée la session
                $_SESSION["id_utilisateur"] = $user["id_utilisateur"];
                $_SESSION["nom"] = $user["nom"];
                $_SESSION["prenom"] = $user["prenom"];
                $_SESSION["role"] = $user["role"];

                // Redirection vers l'accueil
                header("Location: index.php");
                exit;
            } else {
                $error = "Mot de passe incorrect.";
            }
        } else {
            $error = "Aucun compte trouvé avec cet e-mail.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - SomniCare</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/connexion.css">
</head>
<body>
<header>
    <div class="container">
        <div class="header-content">
            <!-- Logo -->
            <div class="logo">
                <div class="logo-image">
                    <img src="images/logo.png" alt="SomniCare">
                </div>
            </div>

            <!-- Navigation -->
            <nav class="main-nav">
                <ul class="nav-links">
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="troubles-sommeil.html">Les troubles du sommeil</a></li>
                    <li><a href="somnyl.html">Somnyl</a></li>
                    <li><a href="methode.html">Méthode</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>

            <!-- Côté droit -->
            <div class="header-right">
                <div class="language-selector">
                    <i class="fas fa-globe language-icon"></i>
                    <span class="language-text">FR</span>
                </div>
                <a href="connexion.php" class="btn-identifier active">S'identifier</a>
            </div>
        </div>
    </div>
</header>

<section class="login-section">
    <div class="form-container">
        <h2>Connexion</h2>

        <?php if ($error): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="email" name="email" placeholder="Adresse e-mail" required>
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>

        <p class="register-text">
            Pas encore de compte ? <a href="inscription.php">Créez-en un ici</a>
        </p>
    </div>
</section>

<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-logo">SomniCare</div>
            <div class="footer-description">
                Spécialistes du sommeil — accompagnement et solutions personnalisées.
            </div>
        </div>
        <div class="footer-copyright">
            © 2025 SomniCare — Tous droits réservés
        </div>
    </div>
</footer>
</body>
</html>
