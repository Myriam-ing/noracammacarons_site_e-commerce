<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée']);
    exit;
}


$conn = new mysqli('localhost', 'root', '', 'noracammacarons', 3307);
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Erreur de connexion: ' . $conn->connect_error]);
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
$id_macaron = isset($_POST['id_macaron']) ? intval($_POST['id_macaron']) : 0;

if ($id_macaron <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID de macaron invalide']);
    exit;
}

switch ($action) {
    case 'ajouter':
        
        $stmt = $conn->prepare("UPDATE Macaron SET quantite_stock = quantite_stock + 1 WHERE id_macaron = ?");
        $stmt->bind_param("i", $id_macaron);
        
        if ($stmt->execute()) {
           
            $query = $conn->prepare("SELECT quantite_stock FROM Macaron WHERE id_macaron = ?");
            $query->bind_param("i", $id_macaron);
            $query->execute();
            $result = $query->get_result();
            $row = $result->fetch_assoc();
            
            echo json_encode([
                'status' => 'success', 
                'message' => 'Stock augmenté avec succès',
                'new_quantity' => $row['quantite_stock']
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur de mise à jour: ' . $stmt->error]);
        }
        $stmt->close();
        break;
        
   
    case 'supprimer':
        
        $stmt = $conn->prepare("DELETE FROM Macaron WHERE id_macaron = ?");
        $stmt->bind_param("i", $id_macaron);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Macaron supprimé avec succès']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur de suppression: ' . $stmt->error]);
        }
        $stmt->close();
        break;
        
    default:
        echo json_encode(['status' => 'error', 'message' => 'Action non reconnue']);
}


$conn->close();
?>