<?php
session_start();

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    die(json_encode(['success' => false, 'error' => 'Données JSON non valides']));
}

if (!isset($_SESSION['id_user'])) {
    die(json_encode(['success' => false, 'error' => 'Utilisateur non connecté']));
}

if (!isset($input['id_boite_commande']) || !isset($input['id_macaron']) || !isset($input['quantite'])) {
    die(json_encode(['success' => false, 'error' => 'Données manquantes']));
}

$conn = new mysqli('localhost', 'root', '', 'noracammacarons', 3307);
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Erreur de connexion à la base de données']));
}

$id_boite_commande = $input['id_boite_commande'];
$id_macaron = $input['id_macaron'];
$quantite = $input['quantite'];
$id_user = $_SESSION['id_user'];

$sql_check = "SELECT cb.id_boite_commande, c.id_commande 
              FROM CommandeBoite cb 
              JOIN Commande c ON cb.id_commande = c.id_commande 
              WHERE cb.id_boite_commande = ? AND c.id_user = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $id_boite_commande, $id_user);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows === 0) {
    die(json_encode(['success' => false, 'error' => 'Vous n\'avez pas accès à cette commande']));
}

$commande_info = $result_check->fetch_assoc();
$id_commande = $commande_info['id_commande'];

$sql_prix = "SELECT prix_unitaire FROM Macaron WHERE id_macaron = ?";
$stmt_prix = $conn->prepare($sql_prix);
$stmt_prix->bind_param("i", $id_macaron);
$stmt_prix->execute();
$result_prix = $stmt_prix->get_result();

if ($result_prix->num_rows === 0) {
    die(json_encode(['success' => false, 'error' => 'Macaron non trouvé']));
}

$prix_info = $result_prix->fetch_assoc();
$prix_unitaire = $prix_info['prix_unitaire'];

$conn->begin_transaction();

try {
    $sql_check_macaron = "SELECT id_macaron FROM MacaronsDansBoite WHERE id_boite_commande = ? AND id_macaron = ?";
    $stmt_check_macaron = $conn->prepare($sql_check_macaron);
    $stmt_check_macaron->bind_param("ii", $id_boite_commande, $id_macaron);
    $stmt_check_macaron->execute();
    $result_check_macaron = $stmt_check_macaron->get_result();
    
    if ($result_check_macaron->num_rows > 0) {
        if ($quantite > 0) {
            $sql_update = "UPDATE MacaronsDansBoite SET quantite = ?, prix_total = ? * ? WHERE id_boite_commande = ? AND id_macaron = ?";
            $stmt_update = $conn->prepare($sql_update);
            $prix_total = $quantite * $prix_unitaire;
            $stmt_update->bind_param("idiis", $quantite, $prix_unitaire, $quantite, $id_boite_commande, $id_macaron);
            $stmt_update->execute();
        } else {
            $sql_delete = "DELETE FROM MacaronsDansBoite WHERE id_boite_commande = ? AND id_macaron = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("ii", $id_boite_commande, $id_macaron);
            $stmt_delete->execute();
        }
    } else if ($quantite > 0) {
        $sql_insert = "INSERT INTO MacaronsDansBoite (id_boite_commande, id_macaron, quantite, prix_total) VALUES (?, ?, ?, ? * ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("iiidi", $id_boite_commande, $id_macaron, $quantite, $prix_unitaire, $quantite);
        $stmt_insert->execute();
    }
    
    $sql_update_total = "UPDATE Commande c SET c.total_commande = 
                         (SELECT COALESCE(SUM(mb.prix_total), 0) + 
                         (SELECT COALESCE(SUM(cb.sous_total), 0) FROM CommandeBoite cb WHERE cb.id_commande = c.id_commande)
                         FROM MacaronsDansBoite mb 
                         JOIN CommandeBoite cb ON mb.id_boite_commande = cb.id_boite_commande 
                         WHERE cb.id_commande = c.id_commande)
                         WHERE c.id_commande = ?";
    $stmt_update_total = $conn->prepare($sql_update_total);
    $stmt_update_total->bind_param("i", $id_commande);
    $stmt_update_total->execute();
    
    $conn->commit();
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    $conn->rollback();
    die(json_encode(['success' => false, 'error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]));
}

$conn->close();
?>