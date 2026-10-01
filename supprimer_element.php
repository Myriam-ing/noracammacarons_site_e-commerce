<?php
header('Content-Type: application/json');


ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Méthode non autorisée');
    }

   
    if (empty($_POST['id']) || empty($_POST['type'])) {
        throw new Exception('Paramètres manquants');
    }

    $id = intval($_POST['id']);
    $type = $_POST['type'];

   
    if ($id <= 0) {
        throw new Exception('ID invalide');
    }

    
    if (!in_array($type, ['boite', 'couverture'])) {
        throw new Exception('Type non reconnu');
    }

   
    if ($type === 'boite') {
        $table = 'Boite';
        $id_column = 'id_boite';
        $image_column = 'image_url';
    } else {
        $table = 'Couverture';
        $id_column = 'id_couverture';
        $image_column = 'image_url';
    }

   
    $conn = new mysqli('localhost', 'root', '', 'noracammacarons', 3307);
    if ($conn->connect_error) {
        throw new Exception('Échec de la connexion à la base de données: ' . $conn->connect_error);
    }

    
    $stmt = $conn->prepare("SELECT $image_column FROM $table WHERE $id_column = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Élément non trouvé');
    }
    
    $row = $result->fetch_assoc();
    $image_url = $row[$image_column];

    
    $delete_stmt = $conn->prepare("DELETE FROM $table WHERE $id_column = ?");
    $delete_stmt->bind_param("i", $id);
    
    if (!$delete_stmt->execute()) {
        throw new Exception('Erreur lors de la suppression: ' . $delete_stmt->error);
    }

    if ($delete_stmt->affected_rows === 0) {
        throw new Exception('Aucun élément supprimé');
    }

    
    $image_path = __DIR__ . '/images/' . $image_url;
    if (!empty($image_url) && file_exists($image_path)) {
        @unlink($image_path); 
    }


    echo json_encode([
        'status' => 'success',
        'message' => ucfirst($type) . ' supprimé(e) avec succès'
    ]);

} catch (Exception $e) {
    
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} finally {
    
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>