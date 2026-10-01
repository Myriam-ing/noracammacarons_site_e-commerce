CREATE TABLE Utilisateur (
  id_user INT PRIMARY KEY AUTO_INCREMENT,
  nom_utilisateur VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  mot_de_passe VARCHAR(255) NOT NULL,
  adresse TEXT,
  tel VARCHAR(20),
  role ENUM('client', 'admin') NOT NULL
);


CREATE TABLE Paiement (
  id_paiement INT PRIMARY KEY AUTO_INCREMENT,
  id_user INT NOT NULL,
  num_carte VARBINARY(255) NOT NULL,      
  cvv VARBINARY(255) NOT NULL,           
  mois_expiration INT NOT NULL,
  annee_expiration INT NOT NULL,
  nom_titulaire VARCHAR(100) NOT NULL,
  FOREIGN KEY (id_user) REFERENCES Utilisateur(id_user) ON DELETE CASCADE
);



CREATE TABLE Categorie (
  id_categorie INT PRIMARY KEY AUTO_INCREMENT,
  nom VARCHAR(50)
);
INSERT INTO Categorie (nom) VALUES 
('Fruits Rouges'),
('Chocolat & Notes Boisées'),
('Fruités & Exotiques');



CREATE TABLE Macaron (
  id_macaron INT PRIMARY KEY AUTO_INCREMENT,
  id_categorie INT NOT NULL,
  nom VARCHAR(100) NOT NULL,
  ingred TEXT,
  descrip TEXT,
  prix_unitaire INT NOT NULL,
  quantite_stock INT DEFAULT 0,
  image_url VARCHAR(255),
  is_active BOOLEAN DEFAULT TRUE,
  FOREIGN KEY (id_categorie) REFERENCES Categorie(id_categorie)
);


CREATE TABLE Boite (
  id_boite INT PRIMARY KEY AUTO_INCREMENT,
  nom VARCHAR(100),
  capacite INT NOT NULL,
  prix INT NOT NULL,
  image_url VARCHAR(255)
);

CREATE TABLE Couverture (
  id_couverture INT PRIMARY KEY AUTO_INCREMENT,
  nom VARCHAR(100),
  image_url VARCHAR(255)
);


CREATE TABLE Commande (
  id_commande INT PRIMARY KEY AUTO_INCREMENT,
  id_user INT NOT NULL,
  date_commande DATETIME DEFAULT CURRENT_TIMESTAMP,
 statut ENUM('en traitement', 'expédiée', 'livrée') DEFAULT 'en traitement',
  adresse_livraison TEXT,
  total_commande INT DEFAULT 0,
  FOREIGN KEY (id_user) REFERENCES Utilisateur(id_user) ON DELETE CASCADE
);


CREATE TABLE CommandeBoite (
  id_boite_commande INT PRIMARY KEY AUTO_INCREMENT,
  id_commande INT,
  id_boite INT,
  id_couverture INT,
  quantite INT,
  sous_total INT DEFAULT 0,
  FOREIGN KEY (id_commande) REFERENCES Commande(id_commande) ON DELETE CASCADE,
  FOREIGN KEY (id_boite) REFERENCES Boite(id_boite) ON DELETE CASCADE,
  FOREIGN KEY (id_couverture) REFERENCES Couverture(id_couverture) ON DELETE CASCADE
);


CREATE TABLE MacaronsDansBoite (
  id_boite_commande INT,
  id_macaron INT,
  quantite INT DEFAULT 0,
  PRIMARY KEY (id_boite_commande, id_macaron),
  FOREIGN KEY (id_boite_commande) REFERENCES CommandeBoite(id_boite_commande) ON DELETE CASCADE,
  FOREIGN KEY (id_macaron) REFERENCES Macaron(id_macaron) ON DELETE CASCADE
);

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

ALTER TABLE Commande 
MODIFY COLUMN statut ENUM('en traitement', 'expédiée', 'livrée', 'annulée') DEFAULT 'en traitement';


