<?php
// Initialisation des messages
$success = "";
$error = "";

// Simulation de l'envoi d'email
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
        // Simulation de l'envoi du mail (fictif)
        $success = "✅ Votre message a été envoyé avec succès (simulation locale).";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - SomniCare</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/contact.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <div class="logo-image">
                        <img src="images/logo.png" alt="SomniCare">
                    </div>
                </div>
                <nav class="main-nav">
                    <ul class="nav-links">
                        <li><a href="index.html">Accueil</a></li>
                        <li><a href="troubles-sommeil.html">Les troubles du sommeil</a></li>
                        <li><a href="somnyl.html">Somnyl</a></li>
                        <li><a href="methode.html">Méthode</a></li>
                        <li><a href="contact.php" class="active">Contact</a></li>
                    </ul>
                </nav>
                <div class="header-right">
                    <div class="language-selector">
                        <i class="fas fa-globe language-icon"></i>
                        <span class="language-text">FR</span>
                    </div>
                    <a href="login.html" class="btn-identifier">S'identifier</a>
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
                <p>Nos spécialistes SomniCare vous répondent sous 24h (simulation locale).</p>
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
