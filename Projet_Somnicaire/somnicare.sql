-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : sam. 20 déc. 2025 à 00:47
-- Version du serveur : 5.7.24
-- Version de PHP : 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `somnicare`
--

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `id_commande` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `date_commande` datetime DEFAULT CURRENT_TIMESTAMP,
  `statut` enum('en_attente','en_livraison','livree','annulee') DEFAULT 'en_attente',
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id_commande`, `id_client`, `date_commande`, `statut`, `total`) VALUES
(1, 1, '2025-12-02 11:07:22', 'livree', '33.99'),
(3, 1, '2025-12-16 09:22:14', 'en_attente', '33.99'),
(4, 1, '2025-12-16 09:35:02', 'en_attente', '33.99'),
(5, 1, '2025-12-17 15:06:19', 'en_attente', '68.90'),
(6, 1, '2025-12-18 19:33:31', 'en_attente', '68.90'),
(7, 1, '2025-12-20 00:39:53', 'en_attente', '23.90'),
(8, 1, '2025-12-20 00:41:46', 'en_attente', '38.90');

-- --------------------------------------------------------

--
-- Structure de la table `commandes_details`
--

CREATE TABLE `commandes_details` (
  `id` int(11) NOT NULL,
  `id_commande` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  `prix` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `commandes_details`
--

INSERT INTO `commandes_details` (`id`, `id_commande`, `id_produit`, `quantite`, `prix`) VALUES
(1, 4, 1, 1, '29.99'),
(2, 5, 1, 1, '64.90'),
(3, 6, 1, 1, '64.90'),
(4, 7, 1, 1, '19.90'),
(5, 8, 1, 1, '34.90');

-- --------------------------------------------------------

--
-- Structure de la table `conseils_sommeil`
--

CREATE TABLE `conseils_sommeil` (
  `id_conseil` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `contenu` text NOT NULL,
  `auteur` int(11) NOT NULL,
  `date_publication` datetime DEFAULT CURRENT_TIMESTAMP,
  `categorie` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `details_commande`
--

CREATE TABLE `details_commande` (
  `id_detail` int(11) NOT NULL,
  `id_commande` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `quantite` int(11) DEFAULT '1',
  `prix_unitaire` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `details_commande`
--

INSERT INTO `details_commande` (`id_detail`, `id_commande`, `id_produit`, `quantite`, `prix_unitaire`) VALUES
(1, 1, 1, 1, '29.99');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id_message` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `contenu` text NOT NULL,
  `date_envoi` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO `messages` (`id_message`, `nom`, `email`, `telephone`, `contenu`, `date_envoi`) VALUES
(1, 'julien', 'julien@test.fr', '0789543810', 'Ceci est un message de test', '2025-11-28 15:14:11'),
(2, 'JEROME', 'jerome@etude.fr', '0623743619', 'JE VOUS SALUT', '2025-11-29 16:29:49'),
(3, 'LOUIS', 'louis@somnicaire.fr', '0621145620', 'salut', '2025-12-04 10:52:12');

-- --------------------------------------------------------

--
-- Structure de la table `notes_medicales`
--

CREATE TABLE `notes_medicales` (
  `id_note` int(11) NOT NULL,
  `id_patient` int(11) NOT NULL,
  `id_specialiste` int(11) NOT NULL,
  `contenu` text NOT NULL,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `id_produit` int(11) NOT NULL,
  `nom_produit` varchar(100) NOT NULL,
  `description` text,
  `prix` decimal(8,2) NOT NULL,
  `categorie` varchar(50) DEFAULT NULL,
  `stock` int(11) DEFAULT '0',
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id_produit`, `nom_produit`, `description`, `prix`, `categorie`, `stock`, `image_url`) VALUES
(1, 'Gélules Somnyl', 'Complément naturel favorisant le sommeil', '29.99', 'Sommeil', 100, 'images/somnyl.png');

-- --------------------------------------------------------

--
-- Structure de la table `rendez_vous`
--

CREATE TABLE `rendez_vous` (
  `id_rdv` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `id_specialiste` int(11) NOT NULL,
  `date_rdv` date NOT NULL,
  `heure_rdv` time NOT NULL,
  `statut` enum('en_attente','confirme','annule','termine') DEFAULT 'en_attente',
  `note` decimal(2,1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `rendez_vous`
--

INSERT INTO `rendez_vous` (`id_rdv`, `id_client`, `id_specialiste`, `date_rdv`, `heure_rdv`, `statut`, `note`) VALUES
(6, 8, 7, '2025-01-10', '10:00:00', 'annule', NULL),
(7, 9, 7, '2025-01-11', '11:30:00', 'termine', NULL),
(8, 10, 7, '2025-01-12', '09:00:00', 'termine', NULL),
(9, 11, 7, '2025-01-13', '14:00:00', 'termine', NULL),
(10, 12, 7, '2025-01-14', '16:00:00', 'en_attente', NULL),
(11, 1, 104, '2025-12-25', '14:00:00', 'en_attente', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `specialistes`
--

CREATE TABLE `specialistes` (
  `id_specialiste` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `specialite` varchar(100) DEFAULT NULL,
  `bio` text,
  `tarif_consultation` decimal(8,2) DEFAULT NULL,
  `disponibilite` text,
  `latitude` decimal(10,6) DEFAULT NULL,
  `longitude` decimal(10,6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `specialistes`
--

INSERT INTO `specialistes` (`id_specialiste`, `id_utilisateur`, `specialite`, `bio`, `tarif_consultation`, `disponibilite`, `latitude`, `longitude`) VALUES
(3, 2, NULL, 'Apnée du sommeil', '70.00', 'Mar-Sam 10h-18h', '43.296500', '5.369800'),
(4, 3, NULL, 'ORL', '80.00', 'Lun-Ven 8h-16h', '48.856600', '2.352200'),
(5, 4, NULL, 'Psychologie du sommeil', '75.00', 'Lun-Jeu 9h-15h', '45.764000', '4.835700'),
(6, 5, NULL, 'Hypnothérapie du sommeil', '65.00', 'Lun-Sam 9h-19h', '43.604500', '1.444000'),
(7, 8, NULL, 'Nouveau spécialiste', NULL, NULL, NULL, NULL),
(102, 84, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '48.856600', '2.352200'),
(103, 83, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '45.764000', '4.835700'),
(104, 82, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '43.296500', '5.369800'),
(105, 81, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '44.837800', '-0.579200'),
(106, 80, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '43.604700', '1.444200'),
(107, 79, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '47.218400', '-1.553600'),
(108, 78, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '50.629200', '3.057300'),
(109, 77, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '48.573400', '7.752100'),
(110, 76, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '43.710200', '7.262000'),
(111, 75, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '48.117300', '-1.677800'),
(112, 74, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '47.322000', '5.041500'),
(113, 73, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '49.119300', '6.175700'),
(114, 72, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '43.611000', '3.876700'),
(115, 71, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '45.188500', '5.724500'),
(116, 70, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '49.258300', '4.031700'),
(117, 69, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '49.443100', '1.099300'),
(118, 68, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '48.390400', '-4.486100'),
(119, 67, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '46.160300', '-1.151100'),
(120, 66, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '43.483200', '-1.558600'),
(121, 65, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '47.902900', '1.909300'),
(122, 64, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '45.777200', '3.087000'),
(123, 63, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '46.580200', '0.340400'),
(124, 62, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '44.933400', '4.892400'),
(125, 61, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '43.836700', '4.360100'),
(126, 60, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '50.951300', '1.858700'),
(127, 59, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '47.748300', '-3.370200'),
(128, 58, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '46.204400', '6.143200'),
(129, 57, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '44.558800', '6.078000'),
(130, 56, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '48.692100', '6.184400'),
(131, 55, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '47.471200', '-0.547600'),
(132, 54, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '49.894100', '2.295800'),
(133, 53, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '45.899200', '6.129400'),
(134, 52, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '44.014400', '1.354500'),
(135, 51, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '48.545400', '2.653400'),
(136, 50, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '43.124200', '5.928000'),
(137, 49, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '45.748500', '4.846700'),
(138, 48, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '47.081000', '2.398800'),
(139, 47, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '48.451500', '-4.251700'),
(140, 46, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '46.670000', '-1.427000'),
(141, 45, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '43.232900', '0.078100'),
(142, 44, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '48.856700', '2.363100'),
(143, 43, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '45.764500', '4.846000'),
(144, 42, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '43.300000', '5.400000'),
(145, 41, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '44.850000', '-0.560000'),
(146, 40, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '43.620000', '1.450000'),
(147, 39, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '47.230000', '-1.560000'),
(148, 38, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '50.640000', '3.060000'),
(149, 37, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '48.580000', '7.760000'),
(150, 36, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '43.720000', '7.270000'),
(151, 35, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '48.120000', '-1.670000'),
(152, 34, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '47.330000', '5.050000'),
(153, 33, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '49.130000', '6.180000'),
(154, 32, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '43.620000', '3.880000'),
(155, 31, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '45.190000', '5.730000'),
(156, 30, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '49.270000', '4.040000'),
(157, 29, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '49.450000', '1.100000'),
(158, 28, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '48.400000', '-4.490000'),
(159, 27, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '46.170000', '-1.150000'),
(160, 26, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '43.490000', '-1.560000'),
(161, 25, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '47.910000', '1.910000'),
(162, 24, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '45.780000', '3.090000'),
(163, 23, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '46.590000', '0.350000'),
(164, 22, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '44.940000', '4.900000'),
(165, 21, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '43.840000', '4.370000'),
(166, 20, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '50.960000', '1.860000'),
(167, 19, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '47.750000', '-3.380000'),
(168, 18, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '46.210000', '6.150000'),
(169, 17, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '44.560000', '6.080000'),
(170, 16, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '48.700000', '6.190000'),
(171, 15, 'Médecin du sommeil', 'Spécialiste des troubles du sommeil et de la fatigue chronique.', '75.00', 'Lundi–Vendredi 9h–18h', '47.480000', '-0.550000');

-- --------------------------------------------------------

--
-- Structure de la table `suivi_sommeil`
--

CREATE TABLE `suivi_sommeil` (
  `id_suivi` int(11) NOT NULL,
  `id_patient` int(11) NOT NULL,
  `date_nuit` date NOT NULL,
  `heure_coucher` time DEFAULT NULL,
  `heure_lever` time DEFAULT NULL,
  `qualite` tinyint(4) DEFAULT NULL,
  `temps_endormissement` int(11) DEFAULT NULL,
  `reveils_nocturnes` tinyint(1) DEFAULT NULL,
  `commentaire` text,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `suivi_sommeil`
--

INSERT INTO `suivi_sommeil` (`id_suivi`, `id_patient`, `date_nuit`, `heure_coucher`, `heure_lever`, `qualite`, `temps_endormissement`, `reveils_nocturnes`, `commentaire`, `date_creation`) VALUES
(6, 1, '2025-01-08', '23:30:00', '07:00:00', 4, 20, 1, 'Nuit correcte', '2025-12-19 21:05:00'),
(7, 1, '2025-01-09', '00:15:00', '07:45:00', 3, 35, 2, 'Endormissement difficile', '2025-12-19 21:05:00'),
(8, 1, '2025-01-10', '23:45:00', '08:15:00', 5, 10, 0, 'Très bonne nuit', '2025-12-19 21:05:00'),
(9, 1, '2025-01-11', '01:00:00', '08:00:00', 2, 50, 3, 'Réveils fréquents', '2025-12-19 21:05:00'),
(10, 1, '2025-01-12', '22:45:00', '06:30:00', 4, 15, 1, 'Bonne récupération', '2025-12-19 21:05:00');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id_utilisateur` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('client','specialiste','admin') NOT NULL,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_utilisateur`, `nom`, `prenom`, `email`, `mot_de_passe`, `role`, `date_creation`) VALUES
(1, 'bonn', 'jean', 'jean@etu.fr', '$2y$10$oY.6tsQlR736hjWuLYLwsuo8CviMfRmUy62Rja0q.Fx8UX5vrYxLy', 'client', '2025-11-30 07:09:35'),
(2, 'Specialiste', '01', 'specialiste01@somnicare.com', 'motdepasse', 'specialiste', '2025-12-02 09:30:48'),
(3, 'Bernard', 'Louis', 'bernard@somnicare.com', '123456', 'specialiste', '2025-12-03 10:51:42'),
(4, 'Rahmani', 'Aïcha', 'rahmani@somnicare.com', '123456', 'specialiste', '2025-12-03 10:51:42'),
(5, 'Meunier', 'Omar', 'meunier@somnicare.com', '123456', 'specialiste', '2025-12-03 10:51:42'),
(6, 'Lefevre', 'Marie', 'lefevre@somnicare.com', '123456', 'specialiste', '2025-12-03 10:51:42'),
(7, 'Dubois', 'Pierre', 'dubois@somnicare.com', '123456', 'specialiste', '2025-12-03 10:51:42'),
(8, 'bartes', 'lucas', 'lucas.bartes@doc.fr', '$2y$10$dj2Z2rhl.I31CSBJL.c2MumT47ULvKgiV5V5UnqJWKc6/3PIeVk22', 'specialiste', '2025-12-18 10:21:15'),
(9, 'Martin', 'Luc', 'luc.martin@test.com', 'test', 'client', '2025-12-18 11:22:18'),
(10, 'Durand', 'Sophie', 'sophie.durand@test.com', 'test', 'client', '2025-12-18 11:22:18'),
(11, 'Petit', 'Thomas', 'thomas.petit@test.com', 'test', 'client', '2025-12-18 11:22:18'),
(12, 'Bernier', 'Camille', 'camille.bernier@test.com', 'test', 'client', '2025-12-18 11:22:18'),
(13, 'Moreau', 'Nicolas', 'nicolas.moreau@test.com', 'test', 'client', '2025-12-18 11:22:18'),
(14, 'TEST', 'TESR', 'test@test.fr', '$2y$10$iuOQUnScZz1EaqykXTjyeOpZ5G7K.M.Jfdb115wNLgNQaXkZOIGNC', 'client', '2025-12-19 20:00:04'),
(15, 'Martin', 'Paul', 'paul.martin@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(16, 'Durand', 'Claire', 'claire.durand@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(17, 'Bernard', 'Lucas', 'lucas.bernard@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(18, 'Petit', 'Emma', 'emma.petit@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(19, 'Robert', 'Hugo', 'hugo.robert@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(20, 'Richard', 'Lina', 'lina.richard@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(21, 'Dubois', 'Noah', 'noah.dubois@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(22, 'Moreau', 'Sarah', 'sarah.moreau@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(23, 'Laurent', 'Adam', 'adam.laurent@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(24, 'Simon', 'Ines', 'ines.simon@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(25, 'Michel', 'Leo', 'leo.michel@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(26, 'Lefevre', 'Nina', 'nina.lefevre@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(27, 'Garcia', 'Tom', 'tom.garcia@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(28, 'Roux', 'Maya', 'maya.roux@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(29, 'Fournier', 'Ethan', 'ethan.fournier@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(30, 'Girard', 'Yasmine', 'yasmine.girard@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(31, 'Andre', 'Liam', 'liam.andre@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(32, 'Mercier', 'Camille', 'camille.mercier@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(33, 'Dupont', 'Sami', 'sami.dupont@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(34, 'Blanc', 'Amina', 'amina.blanc@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(35, 'Gauthier', 'Alex', 'alex.gauthier@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(36, 'Chevalier', 'Julie', 'julie.chevalier@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(37, 'Francois', 'Rayan', 'rayan.francois@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(38, 'Legrand', 'Sofia', 'sofia.legrand@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(39, 'Perrin', 'Ilyes', 'ilyes.perrin@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(40, 'Morin', 'Anais', 'anais.morin@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(41, 'Boyer', 'Yassine', 'yassine.boyer@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(42, 'Clement', 'Manon', 'manon.clement@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(43, 'Lopez', 'Bilal', 'bilal.lopez@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(44, 'Fontaine', 'Rim', 'rim.fontaine@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:14:50'),
(45, 'Henry', 'Nicolas', 'nicolas.henry@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(46, 'Renaud', 'Elise', 'elise.renaud@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(47, 'Schmitt', 'Julien', 'julien.schmitt@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(48, 'Muller', 'Sarah', 'sarah.muller@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(49, 'Lemoine', 'Kevin', 'kevin.lemoine@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(50, 'Colin', 'Laura', 'laura.colin@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(51, 'Renard', 'Omar', 'omar.renard@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(52, 'Marchand', 'Eva', 'eva.marchand@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(53, 'Benoit', 'Mehdi', 'mehdi.benoit@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(54, 'Perrot', 'Chloe', 'chloe.perrot@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(55, 'Navarro', 'Amine', 'amine.navarro@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(56, 'Aubert', 'Pauline', 'pauline.aubert@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(57, 'Rolland', 'Ismael', 'ismael.rolland@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(58, 'Guillaume', 'Nora', 'nora.guillaume@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(59, 'Texier', 'Ibrahim', 'ibrahim.texier@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(60, 'Barbier', 'Lucie', 'lucie.barbier@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(61, 'Pons', 'Karim', 'karim.pons@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(62, 'Charpentier', 'Meryem', 'meryem.charpentier@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(63, 'Hoarau', 'Alexandre', 'alexandre.hoarau@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(64, 'Pelletier', 'Alicia', 'alicia.pelletier@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(65, 'Brun', 'Yanis', 'yanis.brun@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(66, 'Marechal', 'Lena', 'lena.marechal@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(67, 'Guichard', 'Farid', 'farid.guichard@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(68, 'Cordier', 'Selma', 'selma.cordier@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(69, 'Faure', 'Matteo', 'matteo.faure@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(70, 'Pascal', 'Imane', 'imane.pascal@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(71, 'Adam', 'Romain', 'romain.adam@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(72, 'Leclerc', 'Hana', 'hana.leclerc@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(73, 'Arnaud', 'Walid', 'walid.arnaud@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(74, 'Tanguy', 'Noemie', 'noemie.tanguy@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(75, 'Hebert', 'Soufiane', 'soufiane.hebert@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(76, 'Vidal', 'Ingrid', 'ingrid.vidal@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(77, 'Leroux', 'Issa', 'issa.leroux@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(78, 'Maillard', 'Lea', 'lea.maillard@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(79, 'Briand', 'Mohamed', 'mohamed.briand@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(80, 'Coste', 'Ana', 'ana.coste@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(81, 'Robin', 'Khaled', 'khaled.robin@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(82, 'Seguin', 'Sabrina', 'sabrina.seguin@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(83, 'Joly', 'Ilan', 'ilan.joly@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04'),
(84, 'Prevost', 'Yara', 'yara.prevost@sommeil.fr', 'test', 'specialiste', '2025-12-19 22:17:04');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id_commande`),
  ADD KEY `id_client` (`id_client`);

--
-- Index pour la table `commandes_details`
--
ALTER TABLE `commandes_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_commande` (`id_commande`),
  ADD KEY `id_produit` (`id_produit`);

--
-- Index pour la table `conseils_sommeil`
--
ALTER TABLE `conseils_sommeil`
  ADD PRIMARY KEY (`id_conseil`),
  ADD KEY `auteur` (`auteur`);

--
-- Index pour la table `details_commande`
--
ALTER TABLE `details_commande`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_commande` (`id_commande`),
  ADD KEY `id_produit` (`id_produit`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id_message`);

--
-- Index pour la table `notes_medicales`
--
ALTER TABLE `notes_medicales`
  ADD PRIMARY KEY (`id_note`),
  ADD KEY `fk_note_patient` (`id_patient`),
  ADD KEY `fk_note_specialiste` (`id_specialiste`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id_produit`);

--
-- Index pour la table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD PRIMARY KEY (`id_rdv`),
  ADD KEY `id_client` (`id_client`),
  ADD KEY `id_specialiste` (`id_specialiste`);

--
-- Index pour la table `specialistes`
--
ALTER TABLE `specialistes`
  ADD PRIMARY KEY (`id_specialiste`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `suivi_sommeil`
--
ALTER TABLE `suivi_sommeil`
  ADD PRIMARY KEY (`id_suivi`),
  ADD KEY `id_patient` (`id_patient`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id_commande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `commandes_details`
--
ALTER TABLE `commandes_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `conseils_sommeil`
--
ALTER TABLE `conseils_sommeil`
  MODIFY `id_conseil` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `details_commande`
--
ALTER TABLE `details_commande`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id_message` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `notes_medicales`
--
ALTER TABLE `notes_medicales`
  MODIFY `id_note` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id_produit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  MODIFY `id_rdv` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `specialistes`
--
ALTER TABLE `specialistes`
  MODIFY `id_specialiste` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=229;

--
-- AUTO_INCREMENT pour la table `suivi_sommeil`
--
ALTER TABLE `suivi_sommeil`
  MODIFY `id_suivi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `commandes_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `commandes_details`
--
ALTER TABLE `commandes_details`
  ADD CONSTRAINT `commandes_details_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commandes` (`id_commande`),
  ADD CONSTRAINT `commandes_details_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`);

--
-- Contraintes pour la table `conseils_sommeil`
--
ALTER TABLE `conseils_sommeil`
  ADD CONSTRAINT `conseils_sommeil_ibfk_1` FOREIGN KEY (`auteur`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `details_commande`
--
ALTER TABLE `details_commande`
  ADD CONSTRAINT `details_commande_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commandes` (`id_commande`),
  ADD CONSTRAINT `details_commande_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`);

--
-- Contraintes pour la table `notes_medicales`
--
ALTER TABLE `notes_medicales`
  ADD CONSTRAINT `fk_note_patient` FOREIGN KEY (`id_patient`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_note_specialiste` FOREIGN KEY (`id_specialiste`) REFERENCES `specialistes` (`id_specialiste`) ON DELETE CASCADE;

--
-- Contraintes pour la table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD CONSTRAINT `rendez_vous_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `utilisateurs` (`id_utilisateur`),
  ADD CONSTRAINT `rendez_vous_ibfk_2` FOREIGN KEY (`id_specialiste`) REFERENCES `specialistes` (`id_specialiste`);

--
-- Contraintes pour la table `specialistes`
--
ALTER TABLE `specialistes`
  ADD CONSTRAINT `specialistes_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `suivi_sommeil`
--
ALTER TABLE `suivi_sommeil`
  ADD CONSTRAINT `suivi_sommeil_ibfk_1` FOREIGN KEY (`id_patient`) REFERENCES `utilisateurs` (`id_utilisateur`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
