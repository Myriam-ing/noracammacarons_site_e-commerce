-- PROCEDURE POUR AFFICHER L'HISTORIQUE DES COMMANDES D'UN CLIENT

DELIMITER $$

CREATE PROCEDURE historique_commandes_client(IN p_id_user INT)
BEGIN
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



-- VOIR DETAILS D'UNE COMMANDE SPECIFIQUE
DELIMITER $$

CREATE PROCEDURE afficher_details_commande(IN p_id_commande INT, IN p_id_user INT)
BEGIN
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

DELIMITER ;








--VALIDATION DE LA COMMANDE ET MISE A JOUR DU STOCK DES MACARONS

DELIMITER $$

CREATE PROCEDURE finaliser_commande(IN p_id_commande INT, IN p_id_user INT)
BEGIN
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

DELIMITER ;