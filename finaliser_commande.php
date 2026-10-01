<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Non connecté']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$conn = new mysqli('localhost', 'root', '', 'noracammacarons', 3307);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Erreur de connexion']);
    exit();
}

$sql = "SELECT id_commande FROM Commande WHERE id_commande = ? AND id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $data['id_commande'], $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Commande non trouvée']);
    exit();
}

$sql = "UPDATE Commande SET statut = 'en traitement' WHERE id_commande = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $data['id_commande']);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$conn->close();
?>
