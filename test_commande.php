<?php
session_start();

$_SESSION['id_user'] = 1;

$conn = new mysqli('localhost', 'root', '', 'noracammacarons', 3307);
if ($conn->connect_error) {
    die("Erreur de connexion: " . $conn->connect_error);
}

$donnees_test = [
    'adresse_livraison' => 'Adresse de test pour débogage',
    'total_commande' => 100.0,
    'details_commande' => [
        [
            'id_boite' => 1, 
            'id_couverture' => 1, 
            'quantite' => 1,
            'sous_total' => 100.0
        ]
    ]
];

$conn->autocommit(false);
try {
    $sql = "INSERT INTO Commande (id_user, total_commande, adresse_livraison, date_commande) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Erreur préparation SQL (Commande): " . $conn->error);
    }
    
    $id_user = $_SESSION['id_user'];
    $total = $donnees_test['total_commande'];
    $adresse = $donnees_test['adresse_livraison'];
    
    $stmt->bind_param("ids", $id_user, $total, $adresse);
    
    if (!$stmt->execute()) {
        throw new Exception("Erreur exécution SQL (Commande): " . $stmt->error);
    }
    
    $id_commande = $conn->insert_id;
    $stmt->close();

    foreach ($donnees_test['details_commande'] as $detail) {
        $sql = "INSERT INTO CommandeBoite (id_commande, id_boite, id_couverture, quantite, sous_total) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Erreur préparation SQL (CommandeBoite): " . $conn->error);
        }
        
        $id_boite = $detail['id_boite'];
        $id_couverture = $detail['id_couverture'];
        $quantite = $detail['quantite'];
        $sous_total = $detail['sous_total'];
        
        $stmt->bind_param("iiiid", $id_commande, $id_boite, $id_couverture, $quantite, $sous_total);
        
        if (!$stmt->execute()) {
            throw new Exception("Erreur exécution SQL (CommandeBoite): " . $stmt->error);
        }
        
        $stmt->close();
    }
    
    $conn->commit();
    echo "<h2>Test réussi!</h2>";
    echo "<p>La commande de test a été insérée avec succès.</p>";
    echo "<p>ID de commande: $id_commande</p>";
    
} catch (Exception $e) {
    $conn->rollback();
    echo "<h2>Erreur lors du test</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}

$conn->close();
?>