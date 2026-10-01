-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : mer. 02 sep. 2026 à 12:23
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `noracammacarons`
--

DELIMITER $$
--
-- Procédures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `afficher_details_commande` (IN `p_id_commande` INT, IN `p_id_user` INT)   BEGIN
    -- 1) Informations générales de la commande
    SELECT
        u.nom_utilisateur,
        u.email,
        c.date_commande,
        c.statut,
        c.adresse_livraison
    FROM Commande c
    JOIN Utilisateur u ON u.id_user = c.id_user
    WHERE c.id_commande = p_id_commande
      AND c.id_user = p_id_user;

    -- 2) Boîtes commandées
    SELECT
        cb.id_boite_commande,
        b.nom AS nom_boite,
        cb.quantite,
        cv.nom AS nom_couverture,
        cb.sous_total
    FROM CommandeBoite cb
    JOIN Boite b ON b.id_boite = cb.id_boite
    LEFT JOIN Couverture cv ON cv.id_couverture = cb.id_couverture
    WHERE cb.id_commande = p_id_commande;

    -- 3) Macarons par boîte
    SELECT
        mdb.id_boite_commande,
        m.nom AS nom_macaron,
        mdb.quantite,
        (mdb.quantite * m.prix_unitaire) AS prix_total_macaron
    FROM MacaronsDansBoite mdb
    JOIN CommandeBoite cb ON cb.id_boite_commande = mdb.id_boite_commande
    JOIN Macaron m ON m.id_macaron = mdb.id_macaron
    WHERE cb.id_commande = p_id_commande;

    -- 4) Total à payer
    SELECT
        c.total_commande AS total_a_payer
    FROM Commande c
    WHERE c.id_commande = p_id_commande
      AND c.id_user = p_id_user;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `finaliser_commande` (IN `p_id_commande` INT, IN `p_id_user` INT)   BEGIN
    DECLARE stock_insuffisant INT DEFAULT 0;

    -- Vérifier que la commande appartient bien à l'utilisateur et est "en traitement"
    IF NOT EXISTS (
        SELECT 1 FROM Commande
        WHERE id_commande = p_id_commande
          AND id_user = p_id_user
          AND statut = 'en traitement'
    ) THEN
        SELECT 'Commande introuvable ou déjà traitée.' AS message;
    ELSE
        -- Vérifier qu'il y a assez de stock pour chaque macaron de la commande
        SELECT COUNT(*) INTO stock_insuffisant
        FROM MacaronsDansBoite mdb
        JOIN CommandeBoite cb ON cb.id_boite_commande = mdb.id_boite_commande
        JOIN Macaron m ON m.id_macaron = mdb.id_macaron
        WHERE cb.id_commande = p_id_commande
          AND m.quantite_stock < mdb.quantite;

        IF stock_insuffisant > 0 THEN
            SELECT 'Stock insuffisant pour un ou plusieurs macarons de votre commande.' AS message;
        ELSE
            START TRANSACTION;

            -- Décrémenter le stock des macarons vendus
            UPDATE Macaron m
            JOIN MacaronsDansBoite mdb ON mdb.id_macaron = m.id_macaron
            JOIN CommandeBoite cb ON cb.id_boite_commande = mdb.id_boite_commande
            SET m.quantite_stock = m.quantite_stock - mdb.quantite
            WHERE cb.id_commande = p_id_commande;

            -- Changer le statut de la commande
            UPDATE Commande
            SET statut = 'expédiée'
            WHERE id_commande = p_id_commande
              AND id_user = p_id_user;

            COMMIT;

            SELECT 'Commande validée avec succès.' AS message;
        END IF;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `historique_commandes_client` (IN `p_id_user` INT)   BEGIN
    SELECT
        c.id_commande,
        c.date_commande,
        c.statut,
        c.adresse_livraison,
        c.total_commande,
        COALESCE(cb.nombre_boites, 0) AS nombre_boites,
        COALESCE(mb.nombre_macarons_total, 0) AS nombre_macarons_total
    FROM Commande c
    LEFT JOIN (
        SELECT id_commande, SUM(quantite) AS nombre_boites
        FROM CommandeBoite
        GROUP BY id_commande
    ) cb ON cb.id_commande = c.id_commande
    LEFT JOIN (
        SELECT cb2.id_commande, SUM(mdb.quantite) AS nombre_macarons_total
        FROM CommandeBoite cb2
        JOIN MacaronsDansBoite mdb ON mdb.id_boite_commande = cb2.id_boite_commande
        GROUP BY cb2.id_commande
    ) mb ON mb.id_commande = c.id_commande
    WHERE c.id_user = p_id_user
    ORDER BY c.date_commande DESC;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `boite`
--

CREATE TABLE `boite` (
  `id_boite` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `capacite` int(11) NOT NULL,
  `prix` int(11) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `boite`
--

INSERT INTO `boite` (`id_boite`, `nom`, `capacite`, `prix`, `image_url`) VALUES
(7, 'Boite de cinq', 5, 500, 'boites/boite_5.png'),
(8, 'Boite de dix', 810, 700, 'boites/boite_8.png'),
(9, 'Boite de vingt', 20, 1000, 'boites/boite_12.png'),
(10, 'Boite de vingt-cinq', 25, 4000, 'boites/boite_20.png'),
(11, 'Boite de quarante', 1040, 7500, 'boites/boite_40.png'),
(12, 'Boite de dix-huit', 18, 3700, 'boites/boite18.png');

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

CREATE TABLE `categorie` (
  `id_categorie` int(11) NOT NULL,
  `nom` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`id_categorie`, `nom`) VALUES
(1, 'Fruits Rouges'),
(2, 'Chocolat & Notes Boisées'),
(3, 'Fruités & Exotiques');

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

CREATE TABLE `commande` (
  `id_commande` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `date_commande` datetime DEFAULT current_timestamp(),
  `statut` enum('en traitement','expédiée','livrée','annulée') DEFAULT 'en traitement',
  `adresse_livraison` text DEFAULT NULL,
  `total_commande` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déclencheurs `commande`
--
DELIMITER $$
CREATE TRIGGER `after_commande_annulation` AFTER UPDATE ON `commande` FOR EACH ROW BEGIN
  -- Vérifier si le statut est modifié à "annulée"
  IF NEW.statut = 'annulée' AND OLD.statut IN ('en traitement', 'expédiée') THEN
    -- Restaurer le stock pour chaque macaron dans la commande
    UPDATE Macaron m
    JOIN (
      SELECT mdb.id_macaron, SUM(mdb.quantite) as total_quantite
      FROM MacaronsDansBoite mdb
      JOIN CommandeBoite cb ON mdb.id_boite_commande = cb.id_boite_commande
      WHERE cb.id_commande = NEW.id_commande
      GROUP BY mdb.id_macaron
    ) AS commande_items ON m.id_macaron = commande_items.id_macaron
    SET m.quantite_stock = m.quantite_stock + commande_items.total_quantite;
    
    -- Insérer dans l'historique des commandes annulées
    INSERT INTO HistoriqueCommandes (
      id_commande, 
      id_user, 
      date_commande, 
      adresse_livraison, 
      total_commande, 
      raison_annulation
    )
    VALUES (
      NEW.id_commande,
      NEW.id_user,
      NEW.date_commande,
      NEW.adresse_livraison,
      NEW.total_commande,
      'Commande annulée'
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_commande_expedition` AFTER UPDATE ON `commande` FOR EACH ROW BEGIN
  -- Vérifier si le statut passe à "expédiée"
  IF NEW.statut = 'expédiée' AND OLD.statut = 'en traitement' THEN
    -- Mettre à jour le stock pour chaque macaron dans la commande
    UPDATE Macaron m
    JOIN (
      SELECT mdb.id_macaron, SUM(mdb.quantite) as total_quantite
      FROM MacaronsDansBoite mdb
      JOIN CommandeBoite cb ON mdb.id_boite_commande = cb.id_boite_commande
      WHERE cb.id_commande = NEW.id_commande
      GROUP BY mdb.id_macaron
    ) AS commande_items ON m.id_macaron = commande_items.id_macaron
    SET m.quantite_stock = m.quantite_stock - commande_items.total_quantite;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_delete_commande` BEFORE DELETE ON `commande` FOR EACH ROW BEGIN
  -- Insérer dans l'historique des commandes annulées
  INSERT INTO HistoriqueCommandes (
    id_commande, 
    id_user, 
    date_commande, 
    adresse_livraison, 
    total_commande, 
    raison_annulation
  )
  VALUES (
    OLD.id_commande,
    OLD.id_user,
    OLD.date_commande,
    OLD.adresse_livraison,
    OLD.total_commande,
    'Commande supprimée'
  );
  
  -- Restaurer le stock pour chaque macaron dans la commande
  UPDATE Macaron m
  JOIN (
    SELECT mdb.id_macaron, SUM(mdb.quantite) as total_quantite
    FROM MacaronsDansBoite mdb
    JOIN CommandeBoite cb ON mdb.id_boite_commande = cb.id_boite_commande
    WHERE cb.id_commande = OLD.id_commande
    GROUP BY mdb.id_macaron
  ) AS commande_items ON m.id_macaron = commande_items.id_macaron
  SET m.quantite_stock = m.quantite_stock + commande_items.total_quantite;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `commandeboite`
--

CREATE TABLE `commandeboite` (
  `id_boite_commande` int(11) NOT NULL,
  `id_commande` int(11) DEFAULT NULL,
  `id_boite` int(11) DEFAULT NULL,
  `id_couverture` int(11) DEFAULT NULL,
  `quantite` int(11) DEFAULT NULL,
  `sous_total` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `couverture`
--

CREATE TABLE `couverture` (
  `id_couverture` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `couverture`
--

INSERT INTO `couverture` (`id_couverture`, `nom`, `image_url`) VALUES
(15, 'jus', 'illustration/animées/jus.jpg'),
(16, 'legumes', 'illustration/animées/legumes.jpg'),
(17, 'pino', 'illustration/animées/pino.jpg'),
(18, 'rat', 'illustration/animées/rat.jpg'),
(19, 'anniv', 'illustration/fetes/anniv.jpg'),
(20, 'maman', 'illustration/fetes/maman.jpg'),
(21, 'papa', 'illustration/fetes/papa_msg.jpg'),
(22, 'fleur', 'illustration/minimalist/fleur_1.jpg'),
(23, 'fleur jaune', 'illustration/minimalist/fleur_jaune.jpg'),
(24, 'fleur tab', 'illustration/minimalist/fleur_tab.jpg'),
(25, 'minim', 'illustration/minimalist/minim.jpg'),
(26, 'sans', 'illustration/without.png'),
(27, 'vase', 'illustration/minimalist/vase_1.jpg'),
(28, 'paque', 'illustration/fetes/paque.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `historiquecommandes`
--

CREATE TABLE `historiquecommandes` (
  `id_historique` int(11) NOT NULL,
  `id_commande` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `date_commande` datetime NOT NULL,
  `date_annulation` datetime DEFAULT current_timestamp(),
  `adresse_livraison` text DEFAULT NULL,
  `total_commande` int(11) DEFAULT NULL,
  `raison_annulation` varchar(255) DEFAULT NULL,
  `commentaire` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `macaron`
--

CREATE TABLE `macaron` (
  `id_macaron` int(11) NOT NULL,
  `id_categorie` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `ingred` text DEFAULT NULL,
  `descrip` text DEFAULT NULL,
  `prix_unitaire` int(11) NOT NULL,
  `quantite_stock` int(11) DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `macaron`
--

INSERT INTO `macaron` (`id_macaron`, `id_categorie`, `nom`, `ingred`, `descrip`, `prix_unitaire`, `quantite_stock`, `image_url`, `is_active`) VALUES
(1, 1, 'Macaron Fraise', 'Amandes, sucre, blanc d’œuf, arôme naturel de fraise, colorant alimentaire, ganache à la fraise.', 'Un délice fruité avec une ganache onctueuse à la fraise, offrant une douceur sucrée et acidulée à chaque bouchée.', 220, 300, 'mac/rouge/fraise.png', 1),
(2, 1, 'Macaron Fleur', 'Amandes, sucre, blanc d’œuf, infusion florale (type fleur d’oranger ou rose), colorant naturel.', 'Une touche florale raffinée, infusée aux arômes délicats de fleurs, pour une expérience gustative élégante et parfumée.', 280, 108, 'mac/rouge/parfum_fleur.png', 1),
(3, 1, 'Macaron Myrtille', 'Amandes, sucre, blanc d’œuf, purée de myrtille, colorant alimentaire.', 'Une explosion de saveurs avec une ganache à la myrtille, mêlant douceur et légères notes acidulées.', 180, 60, 'mac/rouge/mirtille.png', 1),
(4, 1, 'Macaron Framboise', 'Amandes, sucre, blanc d’œuf, extrait de framboise, ganache fruitée, colorant naturel.', 'Un équilibre parfait entre l’acidité et la douceur, sublimé par une crème fondante à la framboise.', 220, 212, 'mac/rouge/framboise.png', 1),
(5, 1, 'Macaron Cassis', 'Amandes, sucre, blanc d’œuf, purée de cassis, arôme naturel, colorant alimentaire.', 'Une saveur intense et légèrement boisée, relevée par une touche d’acidité typique du cassis.', 180, 24, 'mac/rouge/cassis.png', 1),
(6, 1, 'Macaron Cerise', 'Amandes, sucre, blanc d’œuf, extrait de cerise, ganache fruitée, colorant naturel.', 'Un macaron gourmand à la cerise, mêlant douceur et légère amertume pour un goût irrésistible.', 180, 150, 'mac/rouge/cerise.png', 1),
(7, 2, 'Macaron Noisette', 'Amandes, sucre, blanc d’œuf, éclats de noisette, ganache chocolat-noisette.', 'Un macaron gourmand mêlant le caractère du chocolat aux notes douces et croquantes de la noisette.', 250, 210, 'mac/choco/noisette.png', 1),
(8, 2, 'Macaron Caramel Beurre Salé', 'Amandes, sucre, blanc d’œuf, caramel au beurre salé, crème onctueuse.', 'Une douceur fondante rehaussée par une pointe de sel pour un équilibre parfait.', 200, 45, 'mac/choco/caramel_beure.png', 1),
(9, 2, 'Macaron Vanille', 'Amandes, sucre, blanc d’œuf, vanille naturelle, ganache douce.', 'Un classique délicat et parfumé, aux arômes doux et enveloppants.', 150, 80, 'mac/choco/vanille.png', 1),
(10, 2, 'Macaron Oreo', 'Amandes, sucre, blanc d’œuf, éclats de biscuit chocolat, crème façon Oreo.', 'Une alliance irrésistible entre croquant et crémeux, inspirée du célèbre biscuit.', 260, 84, 'mac/choco/oreo.png', 1),
(11, 2, 'Macaron Réglisse', 'Amandes, sucre, blanc d’œuf, extrait de réglisse, arôme naturel.', 'Un macaron audacieux aux saveurs boisées et anisées, idéal pour les amateurs de réglisse.', 160, 1, 'mac/choco/reglise.png', 1),
(12, 2, 'Macaron Café', 'Amandes, sucre, blanc d’œuf, café moulu ou extrait, ganache au café.', 'Une explosion de saveurs corsées et aromatiques, parfaite pour les amoureux de l’expresso.', 220, 188, 'mac/choco/café.png', 1),
(13, 3, 'Macaron Citron', 'Amandes, sucre, blanc d’œuf, zeste de citron, arôme naturel de citron.', 'Un macaron vif et rafraîchissant, au cœur délicatement citronné, pour un équilibre parfait entre acidité et douceur.', 180, 0, 'mac/citron/citron.png', 1),
(14, 3, 'Macaron Banane', 'Amandes, sucre, blanc d’œuf, purée de banane, arôme naturel.', 'Une douceur fondante à la saveur de banane mûre, pour un moment de gourmandise tendre et fruité.', 200, 130, 'mac/citron/banane.png', 1),
(15, 3, 'Macaron Carotte', 'Amandes, sucre, blanc d’œuf, purée de carotte, épices douces.', 'Une création originale aux notes légèrement sucrées, avec une touche épicée et une ganache onctueuse à la carotte.', 160, 15, 'mac/citron/carotte.png', 1),
(16, 3, 'Macaron Orange', 'Amandes, sucre, blanc d’œuf, zeste d’orange, arôme de fleur d’oranger.', 'Une combinaison élégante d\'orange juteuse et de fleur d\'oranger, pour une bouchée parfumée et délicate.', 160, 50, 'mac/citron/orange.png', 1),
(17, 3, 'Macaron Citrouille', 'Amandes, sucre, blanc d’œuf, purée de citrouille, épices automnales.', 'Un macaron aux saveurs chaleureuses, inspiré de l’automne, avec une ganache douce à la citrouille.', 280, 146, 'mac/citron/pumpkin.png', 1),
(18, 3, 'Macaron Pamplemousse', 'Amandes, sucre, blanc d’œuf, jus de pamplemousse, arôme naturel.', 'Un macaron tonique et fruité, au goût légèrement acidulé, qui réveille les papilles avec fraîcheur.', 220, 38, 'mac/citron/pamplemous.png', 1);

-- --------------------------------------------------------

--
-- Structure de la table `macaronsdansboite`
--

CREATE TABLE `macaronsdansboite` (
  `id_boite_commande` int(11) NOT NULL,
  `id_macaron` int(11) NOT NULL,
  `quantite` int(11) DEFAULT 0,
  `prix_total` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déclencheurs `macaronsdansboite`
--
DELIMITER $$
CREATE TRIGGER `before_insert_macaron_boite` BEFORE INSERT ON `macaronsdansboite` FOR EACH ROW BEGIN
  DECLARE stock_disponible INT;
  DECLARE macaron_nom VARCHAR(100);
  
  -- Récupérer le stock disponible et le nom du macaron
  SELECT quantite_stock, nom INTO stock_disponible, macaron_nom
  FROM Macaron
  WHERE id_macaron = NEW.id_macaron;
  
  -- Vérifier si le stock est suffisant
  IF stock_disponible < NEW.quantite THEN
    SET @message = CONCAT('Stock insuffisant pour le macaron "', macaron_nom, '". Stock disponible: ', stock_disponible, ', Quantité demandée: ', NEW.quantite);
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = @message;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_update_macaron_boite` BEFORE UPDATE ON `macaronsdansboite` FOR EACH ROW BEGIN
  DECLARE stock_disponible INT;
  DECLARE macaron_nom VARCHAR(100);
  DECLARE difference INT;
  
  -- Calcul de la différence entre nouvelle et ancienne quantité
  SET difference = NEW.quantite - OLD.quantite;
  
  -- Seulement vérifier si la quantité augmente
  IF difference > 0 THEN
    -- Récupérer le stock disponible et le nom du macaron
    SELECT quantite_stock, nom INTO stock_disponible, macaron_nom
    FROM Macaron
    WHERE id_macaron = NEW.id_macaron;
    
    -- Vérifier si le stock est suffisant pour l'augmentation
    IF stock_disponible < difference THEN
      SET @message = CONCAT('Stock insuffisant pour le macaron "', macaron_nom, 
                           '". Stock disponible: ', stock_disponible, 
                           ', Augmentation demandée: ', difference);
      SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = @message;
    END IF;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `paiement`
--

CREATE TABLE `paiement` (
  `id_paiement` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `num_carte` varbinary(255) NOT NULL,
  `cvv` varbinary(255) NOT NULL,
  `mois_expiration` int(11) NOT NULL,
  `annee_expiration` int(11) NOT NULL,
  `nom_titulaire` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id_user` int(11) NOT NULL,
  `nom_utilisateur` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `adresse` text DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `role` enum('client','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_user`, `nom_utilisateur`, `email`, `mot_de_passe`, `adresse`, `tel`, `role`) VALUES
(3, 'myriam.cheriguene', 'myriam@example.com', 'myriam@2004', 'Kouba', '0542322161', 'admin');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `boite`
--
ALTER TABLE `boite`
  ADD PRIMARY KEY (`id_boite`);

--
-- Index pour la table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`id_categorie`);

--
-- Index pour la table `commande`
--
ALTER TABLE `commande`
  ADD PRIMARY KEY (`id_commande`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `commandeboite`
--
ALTER TABLE `commandeboite`
  ADD PRIMARY KEY (`id_boite_commande`),
  ADD KEY `id_commande` (`id_commande`),
  ADD KEY `id_boite` (`id_boite`),
  ADD KEY `id_couverture` (`id_couverture`);

--
-- Index pour la table `couverture`
--
ALTER TABLE `couverture`
  ADD PRIMARY KEY (`id_couverture`);

--
-- Index pour la table `historiquecommandes`
--
ALTER TABLE `historiquecommandes`
  ADD PRIMARY KEY (`id_historique`);

--
-- Index pour la table `macaron`
--
ALTER TABLE `macaron`
  ADD PRIMARY KEY (`id_macaron`),
  ADD KEY `id_categorie` (`id_categorie`);

--
-- Index pour la table `macaronsdansboite`
--
ALTER TABLE `macaronsdansboite`
  ADD PRIMARY KEY (`id_boite_commande`,`id_macaron`),
  ADD KEY `id_macaron` (`id_macaron`);

--
-- Index pour la table `paiement`
--
ALTER TABLE `paiement`
  ADD PRIMARY KEY (`id_paiement`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `boite`
--
ALTER TABLE `boite`
  MODIFY `id_boite` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `id_categorie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `commande`
--
ALTER TABLE `commande`
  MODIFY `id_commande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `commandeboite`
--
ALTER TABLE `commandeboite`
  MODIFY `id_boite_commande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `couverture`
--
ALTER TABLE `couverture`
  MODIFY `id_couverture` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT pour la table `historiquecommandes`
--
ALTER TABLE `historiquecommandes`
  MODIFY `id_historique` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `macaron`
--
ALTER TABLE `macaron`
  MODIFY `id_macaron` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `paiement`
--
ALTER TABLE `paiement`
  MODIFY `id_paiement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commande`
--
ALTER TABLE `commande`
  ADD CONSTRAINT `commande_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `utilisateur` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `commandeboite`
--
ALTER TABLE `commandeboite`
  ADD CONSTRAINT `commandeboite_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commande` (`id_commande`) ON DELETE CASCADE,
  ADD CONSTRAINT `commandeboite_ibfk_2` FOREIGN KEY (`id_boite`) REFERENCES `boite` (`id_boite`) ON DELETE CASCADE,
  ADD CONSTRAINT `commandeboite_ibfk_3` FOREIGN KEY (`id_couverture`) REFERENCES `couverture` (`id_couverture`) ON DELETE CASCADE;

--
-- Contraintes pour la table `macaron`
--
ALTER TABLE `macaron`
  ADD CONSTRAINT `macaron_ibfk_1` FOREIGN KEY (`id_categorie`) REFERENCES `categorie` (`id_categorie`);

--
-- Contraintes pour la table `macaronsdansboite`
--
ALTER TABLE `macaronsdansboite`
  ADD CONSTRAINT `macaronsdansboite_ibfk_1` FOREIGN KEY (`id_boite_commande`) REFERENCES `commandeboite` (`id_boite_commande`) ON DELETE CASCADE,
  ADD CONSTRAINT `macaronsdansboite_ibfk_2` FOREIGN KEY (`id_macaron`) REFERENCES `macaron` (`id_macaron`) ON DELETE CASCADE;

--
-- Contraintes pour la table `paiement`
--
ALTER TABLE `paiement`
  ADD CONSTRAINT `paiement_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `utilisateur` (`id_user`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
