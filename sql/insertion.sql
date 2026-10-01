--categorie fruits rouge
INSERT INTO Macaron (id_categorie, nom, ingred, descrip, prix_unitaire, quantite_stock, image_url)
VALUES 
(1, 'Macaron Fraise', 'Amandes, sucre, blanc d’œuf, arôme naturel de fraise, colorant alimentaire, ganache à la fraise.', 'Un délice fruité avec une ganache onctueuse à la fraise, offrant une douceur sucrée et acidulée à chaque bouchée.', 220, 300, 'mac/rouge/fraise.png'),

(1, 'Macaron Fleur', 'Amandes, sucre, blanc d’œuf, infusion florale (type fleur d’oranger ou rose), colorant naturel.', 'Une touche florale raffinée, infusée aux arômes délicats de fleurs, pour une expérience gustative élégante et parfumée.', 280, 108, 'mac/rouge/parfum_fleur.png'),

(1, 'Macaron Myrtille', 'Amandes, sucre, blanc d’œuf, purée de myrtille, colorant alimentaire.', 'Une explosion de saveurs avec une ganache à la myrtille, mêlant douceur et légères notes acidulées.', 180, 60, 'mac/rouge/mirtille.png'),

(1, 'Macaron Framboise', 'Amandes, sucre, blanc d’œuf, extrait de framboise, ganache fruitée, colorant naturel.', 'Un équilibre parfait entre l’acidité et la douceur, sublimé par une crème fondante à la framboise.', 220, 206, 'mac/rouge/framboise.png'),

(1, 'Macaron Cassis', 'Amandes, sucre, blanc d’œuf, purée de cassis, arôme naturel, colorant alimentaire.', 'Une saveur intense et légèrement boisée, relevée par une touche d’acidité typique du cassis.', 180, 20, 'mac/rouge/cassis.png'),

(1, 'Macaron Cerise', 'Amandes, sucre, blanc d’œuf, extrait de cerise, ganache fruitée, colorant naturel.', 'Un macaron gourmand à la cerise, mêlant douceur et légère amertume pour un goût irrésistible.', 180, 150, 'mac/rouge/cerise.png');

--categorie choco
INSERT INTO Macaron (id_categorie, nom, ingred, descrip, prix_unitaire, quantite_stock, image_url)
VALUES 
(2, 'Macaron Noisette', 'Amandes, sucre, blanc d’œuf, éclats de noisette, ganache chocolat-noisette.', 'Un macaron gourmand mêlant le caractère du chocolat aux notes douces et croquantes de la noisette.', 250, 210, 'mac/choco/noisette.png'),

(2, 'Macaron Caramel Beurre Salé', 'Amandes, sucre, blanc d’œuf, caramel au beurre salé, crème onctueuse.', 'Une douceur fondante rehaussée par une pointe de sel pour un équilibre parfait.', 200, 45, 'mac/choco/caramel_beure.png'),

(2, 'Macaron Vanille', 'Amandes, sucre, blanc d’œuf, vanille naturelle, ganache douce.', 'Un classique délicat et parfumé, aux arômes doux et enveloppants.', 150, 80, 'mac/choco/vanille.png'),

(2, 'Macaron Oreo', 'Amandes, sucre, blanc d’œuf, éclats de biscuit chocolat, crème façon Oreo.', 'Une alliance irrésistible entre croquant et crémeux, inspirée du célèbre biscuit.', 260, 90, 'mac/choco/oreo.png'),

(2, 'Macaron Réglisse', 'Amandes, sucre, blanc d’œuf, extrait de réglisse, arôme naturel.', 'Un macaron audacieux aux saveurs boisées et anisées, idéal pour les amateurs de réglisse.', 160, 3, 'mac/choco/reglise.png'),

(2, 'Macaron Café', 'Amandes, sucre, blanc d’œuf, café moulu ou extrait, ganache au café.', 'Une explosion de saveurs corsées et aromatiques, parfaite pour les amoureux de l’expresso.', 220, 200, 'mac/choco/café.png');
--catégorie citron

INSERT INTO Macaron (id_categorie, nom, ingred, descrip, prix_unitaire, quantite_stock, image_url)
VALUES 
(3, 'Macaron Citron', 'Amandes, sucre, blanc d’œuf, zeste de citron, arôme naturel de citron.', 'Un macaron vif et rafraîchissant, au cœur délicatement citronné, pour un équilibre parfait entre acidité et douceur.', 180, 0, 'mac/citron/citron.png'),

(3, 'Macaron Banane', 'Amandes, sucre, blanc d’œuf, purée de banane, arôme naturel.', 'Une douceur fondante à la saveur de banane mûre, pour un moment de gourmandise tendre et fruité.', 200, 130, 'mac/citron/banane.png'),

(3, 'Macaron Carotte', 'Amandes, sucre, blanc d’œuf, purée de carotte, épices douces.', 'Une création originale aux notes légèrement sucrées, avec une touche épicée et une ganache onctueuse à la carotte.', 160, 15, 'mac/citron/carotte.png'),

(3, 'Macaron Orange', 'Amandes, sucre, blanc d’œuf, zeste d’orange, arôme de fleur d’oranger.', 'Une combinaison élégante d\'orange juteuse et de fleur d\'oranger, pour une bouchée parfumée et délicate.', 160, 50, 'mac/citron/orange.png'),

(3, 'Macaron Citrouille', 'Amandes, sucre, blanc d’œuf, purée de citrouille, épices automnales.', 'Un macaron aux saveurs chaleureuses, inspiré de l’automne, avec une ganache douce à la citrouille.', 280, 146, 'mac/citron/pumpkin.png'),

(3, 'Macaron Pamplemousse', 'Amandes, sucre, blanc d’œuf, jus de pamplemousse, arôme naturel.', 'Un macaron tonique et fruité, au goût légèrement acidulé, qui réveille les papilles avec fraîcheur.', 220, 38, 'mac/citron/pamplemous.png');








-- Ajouter un champ prix_total dans la table MacaronsDansBoite si ce n'est pas déjà fait
ALTER TABLE MacaronsDansBoite 
ADD COLUMN prix_total DECIMAL(10,2) DEFAULT 0 
AFTER quantite;

:::
::::
;;;;
CREATE TABLE HistoriqueCommandes (
  id_historique INT PRIMARY KEY AUTO_INCREMENT,
  id_commande INT NOT NULL,
  id_user INT NOT NULL,
  date_commande DATETIME NOT NULL,
  date_annulation DATETIME DEFAULT CURRENT_TIMESTAMP,
  adresse_livraison TEXT,
  total_commande INT,
  raison_annulation VARCHAR(255),
  commentaire TEXT
);

-- 2. Trigger qui met à jour le stock après validation d'une commande
-- Ce trigger se déclenche lorsque le statut d'une commande passe à "expédiée"
DELIMITER //
CREATE TRIGGER after_commande_expedition
AFTER UPDATE ON Commande
FOR EACH ROW
BEGIN
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
END//
DELIMITER ;

-- 3. Trigger qui empêche l'insertion si la quantité demandée dépasse le stock disponible
DELIMITER //
CREATE TRIGGER before_insert_macaron_boite
BEFORE INSERT ON MacaronsDansBoite
FOR EACH ROW
BEGIN
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
END//
DELIMITER ;

-- Nous devons aussi ajouter un trigger pour les mises à jour de quantité pour les macarons déjà dans une boîte
DELIMITER //
CREATE TRIGGER before_update_macaron_boite
BEFORE UPDATE ON MacaronsDansBoite
FOR EACH ROW
BEGIN
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
END//
DELIMITER ;



DELIMITER //
CREATE TRIGGER after_commande_annulation
AFTER UPDATE ON Commande
FOR EACH ROW
BEGIN
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
END//
DELIMITER ;

-- 5. Trigger qui garde trace des commandes annulées (complémentaire au précédent)
-- Ce trigger capture la suppression d'une commande directement (sans passer par le statut)
DELIMITER //
CREATE TRIGGER before_delete_commande
BEFORE DELETE ON Commande
FOR EACH ROW
BEGIN
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
END//
DELIMITER ;