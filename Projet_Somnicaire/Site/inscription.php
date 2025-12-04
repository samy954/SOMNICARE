<?php
// === Connexion à la base de données ===
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "somnicare";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// === Variables de message ===
$success = "";
$error = "";

// === Traitement du formulaire ===
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $prenom = trim($_POST["prenom"]);
    $nom = trim($_POST["nom"]);
    $email = trim($_POST["email"]);
    $mot_de_passe = $_POST["mot_de_passe"];
    $confirm_mdp = $_POST["confirm_mdp"];

    if (empty($prenom) || empty($nom) || empty($email) || empty($mot_de_passe) || empty($confirm_mdp)) {
        $error = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresse e-mail invalide.";
    } elseif ($mot_de_passe !== $confirm_mdp) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        // Vérifie si l'email existe déjà
        $check = $conn->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "Un compte avec cet e-mail existe déjà.";
        } else {
            $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, 'client')");
            $stmt->bind_param("ssss", $nom, $prenom, $email, $hash);

            if ($stmt->execute()) {
                $success = "✅ Votre compte a été créé avec succès ! Vous pouvez maintenant vous connecter.";
            } else {
                $error = "❌ Erreur lors de l'inscription. Veuillez réessayer.";
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S'inscrire - SomniCare</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/inscription.css">
</head>
<body>
    <header>
    <div class="container">
        <div class="header-content">

            <!-- Logo à gauche -->
            <div class="logo">
                <div class="logo-image">
                    <img src="images/logo.png" alt="SomniCare">
                </div>
            </div>

            <!-- Navigation centrale -->
            <nav class="main-nav">
                <ul class="nav-links">
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="troubles-sommeil.php">Les troubles du sommeil</a></li>
                    <li><a href="somnyl.php">Somnyl</a></li>
                    <li><a href="methode.php">Méthode</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>

            <!-- Côté droit - Langue et identification -->
            <div class="header-right">
                <!-- Sélecteur de langue -->
                <div class="language-selector">
                    <i class="fas fa-globe language-icon"></i>
                    <span class="language-text">FR</span>
                </div>

                <!-- Bouton s'identifier -->
                <a href="connexion.html" class="btn-identifier">S'identifier</a>
            </div>
        </div>
    </div>
</header>


    <section class="register-section">
        <div class="form-container">
            <h2>Créer un compte</h2>

            <?php if ($success): ?>
                <div class="alert success"><?php echo $success; ?></div>
            <?php elseif ($error): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="row">
                    <input type="text" name="prenom" placeholder="Prénom" required>
                    <input type="text" name="nom" placeholder="Nom" required>
                </div>

                <input type="email" name="email" placeholder="Adresse e-mail" required>

                <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
                <input type="password" name="confirm_mdp" placeholder="Confirmer le mot de passe" required>

                <label class="checkbox">
                    <input type="checkbox" name="stay_connected"> Rester connecté
                </label>

                <button type="submit">S'inscrire</button>
            </form>

            <p class="login-text">Déjà un compte ? <a href="connexion.php">Connectez-vous</a></p>
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
