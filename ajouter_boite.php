<?php
header('Content-Type: application/json');

ini_set('display_errors', 0);
ini_set('log_errors', 1);
date_default_timezone_set('Europe/Paris');

try {
    
    $conn = new mysqli('localhost', 'root', '', 'noracammacarons', 3307);
    if ($conn->connect_error) {
        throw new Exception("Échec de la connexion MySQL: " . $conn->connect_error);
    }

   
    $type_produit = $_POST['type_produit'] ?? '';
    if (!in_array($type_produit, ['boite', 'couverture'])) {
        throw new Exception("Type de produit invalide");
    }

    if (empty($_POST['nom'])) {
        throw new Exception("Le nom du produit est requis");
    }

    
    if ($type_produit === 'boite') {
        if (empty($_POST['capacite'])) {
            throw new Exception("La capacité est requise pour une boîte");
        }
        if (empty($_POST['prix'])) {
            throw new Exception("Le prix est requis pour une boîte");
        }
    }

   
    if (empty($_FILES['image_file']['tmp_name'])) {
        throw new Exception("Veuillez sélectionner une image valide");
    }

   
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $fileInfo = pathinfo($_FILES['image_file']['name']);
    $extension = strtolower($fileInfo['extension'] ?? '');

    if (!in_array($extension, $allowedExtensions)) {
        throw new Exception("Type de fichier non autorisé. Formats acceptés: " . implode(', ', $allowedExtensions));
    }

  
    $uploadDir = __DIR__ . '/images/';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception("Impossible de créer le dossier de destination");
        }
    }

    
    $prefix = $type_produit === 'boite' ? 'boite_' : 'couverture_';
    $fileName = $prefix . uniqid() . '.' . $extension;
    $filePath = $uploadDir . $fileName;

   
    if (!move_uploaded_file($_FILES['image_file']['tmp_name'], $filePath)) {
        throw new Exception("Erreur lors de l'enregistrement de l'image");
    }

   
    $imageDbPath = 'images/' . $fileName;

    
    if ($type_produit === 'boite') {
        $stmt = $conn->prepare("INSERT INTO Boite (nom, capacite, prix, image_url) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sids", $_POST['nom'], $_POST['capacite'], $_POST['prix'], $imageDbPath);
    } else {
        $stmt = $conn->prepare("INSERT INTO Couverture (nom, image_url) VALUES (?, ?)");
        $stmt->bind_param("ss", $_POST['nom'], $imageDbPath);
    }

    if (!$stmt->execute()) {
        throw new Exception("Erreur lors de l'ajout en base de données: " . $stmt->error);
    }

  
    $response = [
        'status' => 'success',
        'message' => ($type_produit === 'boite' ? 'Boîte' : 'Couverture') . ' ajoutée avec succès',
        'id' => $stmt->insert_id,
        'nom' => $_POST['nom'],
        'image_url' => $imageDbPath
    ];

    
    if ($type_produit === 'boite') {
        $response['capacite'] = $_POST['capacite'];
        $response['prix'] = $_POST['prix'];
    }

    echo json_encode($response);

} catch (Exception $e) {
    
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($conn)) $conn->close();
}
?>