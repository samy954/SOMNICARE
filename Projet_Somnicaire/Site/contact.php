<?php
session_start(); 

error_reporting(E_ALL);
ini_set('display_errors', 1);

// === Connexion à la base de données ===
$servername = "localhost";
$username = "root";
$password = "root"; 
$dbname = "somnicare";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifie la connexion
if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}

// Initialisation des messages
$success = "";
$error = "";

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = trim($_POST["nom"]);
    $email = trim($_POST["email"]);
    $telephone = trim($_POST["telephone"]);
    $message = trim($_POST["message"]);

    // Validation basique
    if (empty($nom) || empty($email) || empty($message)) {
        $error = "Veuillez remplir tous les champs obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Veuillez entrer une adresse email valide.";
    } else {
        $stmt = $conn->prepare("INSERT INTO messages (nom, email, telephone, contenu) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nom, $email, $telephone, $message);

        if ($stmt->execute()) {
            $success = "✅ Votre message a été enregistré avec succès !";
        } else {
            $error = "❌ Une erreur est survenue lors de l’enregistrement.";
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
    <title>Contact - SomniCare</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/contact.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                    <li><a href="troubles-sommeil.php">Les troubles du sommeil</a></li>
                    <li><a href="somnyl.php">Somnyl</a></li>
                    <li><a href="methode.php">Méthode</a></li>
                    <li><a href="contact.php" class="active">Contact</a></li>
                </ul>
            </nav>

            <!-- Côté droit -->
            <div class="header-right">
                <div class="language-selector">
                    <i class="fas fa-globe language-icon"></i>
                    <span class="language-text">FR</span>
                </div>

                <?php if (isset($_SESSION["prenom"])): ?>
                    <a href="espace.php" class="btn-identifier">
                        Mon espace (<?php echo htmlspecialchars($_SESSION["prenom"]); ?>)
                    </a>
                <?php else: ?>
                    <a href="connexion.php" class="btn-identifier">S'identifier</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<section class="page-header">
    <div class="container">
        <h1>Contactez-nous</h1>
        <p>Une question ? Notre équipe est là pour vous aider.</p>
    </div>
</section>

<section class="contact-section">
    <div class="container">
        <?php if ($success): ?>
            <div class="alert success"><?php echo $success; ?></div>
        <?php elseif ($error): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="text" name="nom" placeholder="Votre nom *" required>
            <input type="email" name="email" placeholder="Votre email *" required>
            <input type="text" name="telephone" placeholder="Votre téléphone">
            <textarea name="message" rows="5" placeholder="Votre message *" required></textarea>
            <button type="submit">Envoyer</button>
        </form>

        <div class="contact-info">
            <h2>Notre équipe</h2>
            <p>Nos spécialistes SomniCare vous répondent sous 24h.</p>
        </div>
    </div>
</section>

<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-logo">SomniCare</div>
            <div class="footer-description">
                Spécialistes du sommeil — réservations avec des médecins spécialisés et solutions naturelles validées par nos experts.
            </div>
        </div>
        <div class="footer-copyright">
            © 2025 SomniCare — Tous droits réservés
        </div>
    </div>
</footer>
</body>
</html>
