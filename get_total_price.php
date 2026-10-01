<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    die(json_encode(['success' => false, 'error' => 'Utilisateur non connecté']));
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['id_commande'])) {
    die(json_encode(['success' => false, 'error' => 'Données invalides']));
}

$id_commande = $input['id_commande'];
$id_user = $_SESSION['id_user'];

$conn = new mysqli('localhost', 'root', '', 'noracammacarons', 3307);
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Erreur de connexion à la base de données']));
}

$sql_check = "SELECT id_commande, total_commande FROM Commande WHERE id_commande = ? AND id_user = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $id_commande, $id_user);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows === 0) {
    die(json_encode(['success' => false, 'error' => 'Commande non trouvée']));
}

$commande = $result_check->fetch_assoc();
$total_commande = $commande['total_commande'];

$conn->close();

echo json_encode(['success' => true, 'total_price' => $total_commande]);
?>