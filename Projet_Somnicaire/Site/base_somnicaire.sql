CREATE DATABASE IF NOT EXISTS somnicare CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE somnicare;

CREATE TABLE utilisateurs (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('client','specialiste','admin') NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE specialistes (
    id_specialiste INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    bio TEXT,
    tarif_consultation DECIMAL(8,2),
    disponibilite TEXT,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur)
);

CREATE TABLE rendez_vous (
    id_rdv INT AUTO_INCREMENT PRIMARY KEY,
    id_client INT NOT NULL,
    id_specialiste INT NOT NULL,
    date_rdv DATE NOT NULL,
    heure_rdv TIME NOT NULL,
    statut ENUM('en_attente','confirme','annule','termine') DEFAULT 'en_attente',
    note DECIMAL(2,1),
    FOREIGN KEY (id_client) REFERENCES utilisateurs(id_utilisateur),
    FOREIGN KEY (id_specialiste) REFERENCES specialistes(id_specialiste)
);

CREATE TABLE produits (
    id_produit INT AUTO_INCREMENT PRIMARY KEY,
    nom_produit VARCHAR(100) NOT NULL,
    description TEXT,
    prix DECIMAL(8,2) NOT NULL,
    categorie VARCHAR(50),
    stock INT DEFAULT 0,
    image_url VARCHAR(255)
);

CREATE TABLE commandes (
    id_commande INT AUTO_INCREMENT PRIMARY KEY,
    id_client INT NOT NULL,
    date_commande DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en_attente','en_livraison','livree','annulee') DEFAULT 'en_attente',
    total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_client) REFERENCES utilisateurs(id_utilisateur)
);

CREATE TABLE details_commande (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_commande INT NOT NULL,
    id_produit INT NOT NULL,
    quantite INT DEFAULT 1,
    prix_unitaire DECIMAL(8,2) NOT NULL,
    FOREIGN KEY (id_commande) REFERENCES commandes(id_commande),
    FOREIGN KEY (id_produit) REFERENCES produits(id_produit)
);

CREATE TABLE avis (
    id_avis INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    type_avis ENUM('produit','specialiste') NOT NULL,
    id_cible INT NOT NULL,
    note INT CHECK(note BETWEEN 1 AND 5),
    commentaire TEXT,
    date_avis DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur)
);

CREATE TABLE messages (
    id_message INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telephone VARCHAR(20),
    contenu TEXT NOT NULL,
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE conseils_sommeil (
    id_conseil INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    auteur INT NOT NULL,
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    categorie VARCHAR(100),
    FOREIGN KEY (auteur) REFERENCES utilisateurs(id_utilisateur)
);