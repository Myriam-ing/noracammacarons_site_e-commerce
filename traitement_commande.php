<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    die(json_encode(['success' => false, 'error' => 'Utilisateur non connecté']));
}

$id_user = $_SESSION['id_user'];
$raw_input = file_get_contents('php://input');
$data = json_decode($raw_input, true);

if ($data === null) {
    die(json_encode(['success' => false, 'error' => 'Données JSON invalides']));
}

if (!isset($data['adresse_livraison']) || !isset($data['total_commande']) || !isset($data['details_commande'])) {
    die(json_encode(['success' => false, 'error' => 'Données incomplètes']));
}

$adresse = $data['adresse_livraison'];
$total = floatval($data['total_commande']);
$details = $data['details_commande'];

$conn = new mysqli('localhost', 'root', '', 'noracammacarons', 3307);
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Erreur de connexion à la base de données']));
}

$sql = "INSERT INTO Commande (id_user, total_commande, adresse_livraison, date_commande) VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(['success' => false, 'error' => 'Erreur de préparation de la requête']));
}

$stmt->bind_param("ids", $id_user, $total, $adresse);
$success = $stmt->execute();

if (!$success) {
    die(json_encode(['success' => false, 'error' => 'Erreur d\'insertion de la commande: ' . $stmt->error]));
}

$id_commande = $conn->insert_id;
$stmt->close();

foreach ($details as $detail) {
    $id_boite = isset($detail['id_boite']) ? intval($detail['id_boite']) : 0;
    $id_couverture = isset($detail['id_couverture']) ? intval($detail['id_couverture']) : 0;
    $quantite = isset($detail['quantite']) ? intval($detail['quantite']) : 1;
    $sous_total = isset($detail['sous_total']) ? floatval($detail['sous_total']) : 0;
    
    $sql = "INSERT INTO CommandeBoite (id_commande, id_boite, id_couverture, quantite, sous_total) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        continue;
    }
    
    $stmt->bind_param("iiiid", $id_commande, $id_boite, $id_couverture, $quantite, $sous_total);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
echo json_encode(['success' => true, 'id_commande' => $id_commande]);
?>