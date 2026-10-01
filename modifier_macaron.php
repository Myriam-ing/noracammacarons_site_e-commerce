<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$conn = new mysqli('localhost', 'root', '', 'noracammacarons', 3307);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$macaron = null;


if (isset($_GET['id'])) {
    $id_macaron = intval($_GET['id']);
    
   
    $stmt = $conn->prepare("SELECT * FROM Macaron WHERE id_macaron = ?");
    $stmt->bind_param("i", $id_macaron);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $macaron = $result->fetch_assoc();
    } else {
        $message = "❌ Macaron non trouvé.";
    }
    $stmt->close();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_macaron'])) {
    $id_macaron = intval($_POST['id_macaron']);
    $nom = $_POST['nom'];
    $id_categorie = intval($_POST['id_categorie']);
    $ingred = $_POST['ingred'];
    $descrip = $_POST['descrip'];
    $prix_unitaire = $_POST['prix_unitaire'];
    $quantite_stock = intval($_POST['quantite_stock']);
    
    

    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
      
        $imageTmpPath = $_FILES['image_file']['tmp_name'];
        $imageExtension = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
        $nouveauNomImage = uniqid('macaron_', true) . '.' . $imageExtension;
        
        $dossierDestination = __DIR__ . '/images/';
        if (!is_dir($dossierDestination)) {
            if (!mkdir($dossierDestination, 0777, true)) {
                die("Erreur : impossible de créer le dossier 'images/'.");
            }
        }
        
        $cheminComplet = $dossierDestination . $nouveauNomImage;
        
        if (move_uploaded_file($imageTmpPath, $cheminComplet)) {
            $cheminRelatifImage = 'images/' . $nouveauNomImage;
            
            
            $query = "UPDATE Macaron SET 
                     nom = ?, 
                     id_categorie = ?, 
                     ingred = ?, 
                     descrip = ?, 
                     prix_unitaire = ?, 
                     quantite_stock = ?,
                     image_url = ? 
                     WHERE id_macaron = ?";
                     
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sissdiis", $nom, $id_categorie, $ingred, $descrip, $prix_unitaire, $quantite_stock, $cheminRelatifImage, $id_macaron);
        }
    } else {
        
        $query = "UPDATE Macaron SET 
                 nom = ?, 
                 id_categorie = ?, 
                 ingred = ?, 
                 descrip = ?, 
                 prix_unitaire = ?, 
                 quantite_stock = ? 
                 WHERE id_macaron = ?";
                 
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sissdii", $nom, $id_categorie, $ingred, $descrip, $prix_unitaire, $quantite_stock, $id_macaron);
    }
    
    if ($stmt->execute()) {
        $message = "✅ Macaron modifié avec succès !";
        
       
        header("Location: admin.php#macarons");
        exit;
    } else {
        $message = "❌ Erreur lors de la modification : " . $stmt->error;
    }
    
    $stmt->close();
}


if (!$macaron && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un Macaron</title>
    <link rel="stylesheet" href="admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #fff5f5;
        }
        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff0e6;
            border-radius: 25px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h1 {
            color: rgb(177, 110, 52);
            text-align: center;
            font-size: 40px;
            margin-bottom: 30px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #666;
            text-decoration: none;
        }
        .back-link:hover {
            color: #333;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
            font-size: 18px;
            color: #555;
        }
        input[type="text"], 
        input[type="number"], 
        textarea {
            width: 100%;
            padding: 10px;
            font-size: 20px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-sizing: border-box;
        }
        .radio-group {
            display: flex;
            gap: 40px;
            margin-top: 10px;
            padding: 5px 0;
        }
        button[type="submit"] {
            background-color: rgb(167, 107, 123);
            color: white;
            border: none;
            border-radius: 25px;
            padding: 12px 60px;
            font-size: 22px;
            cursor: pointer;
            margin-top: 20px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        button[type="submit"]:hover {
            background-color: rgb(148, 92, 106);
        }
        .message {
            margin: 20px 0;
            padding: 15px;
            text-align: center;
            border-radius: 10px;
            font-weight: bold;
            font-size: 20px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .current-image {
            text-align: center;
            margin: 20px 0;
        }
        .current-image img {
            max-width: 200px;
            border-radius: 10px;
            box-shadow: 0 0 5px rgba(0,0,0,0.2);
        }
        .file-input-label {
            display: inline-block;
            background-color: #fbdada;
            color: #5f3b3b;
            padding: 8px 25px;
            font-size: 18px;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-edit"></i> Modifier le Macaron</h1>
        
        <a href="admin.php#macarons" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour à la gestion des macarons
        </a>
        
        <?php if (!empty($message)): ?>
            <div class="message <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>
        
        <?php if ($macaron): ?>
            <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . $macaron['id_macaron']; ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_macaron" value="<?= $macaron['id_macaron'] ?>">
                
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($macaron['nom']) ?>" required>
                
                <label>Catégorie :</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="id_categorie" value="1" <?= $macaron['id_categorie'] == 1 ? 'checked' : '' ?> required> 
                        Fruits rouges
                    </label>
                    <label>
                        <input type="radio" name="id_categorie" value="2" <?= $macaron['id_categorie'] == 2 ? 'checked' : '' ?>> 
                        Chocolat & Notes boisées
                    </label>
                    <label>
                        <input type="radio" name="id_categorie" value="3" <?= $macaron['id_categorie'] == 3 ? 'checked' : '' ?>> 
                        Sucré & Exotique
                    </label>
                </div>
                
                <label for="ingred">Ingrédients :</label>
                <textarea id="ingred" name="ingred" rows="3"><?= htmlspecialchars($macaron['ingred']) ?></textarea>
                
                <label for="descrip">Description :</label>
                <textarea id="descrip" name="descrip" rows="3"><?= htmlspecialchars($macaron['descrip']) ?></textarea>
                
                <label for="prix_unitaire">Prix unitaire (€) :</label>
                <input type="number" id="prix_unitaire" name="prix_unitaire" step="0.01" value="<?= $macaron['prix_unitaire'] ?>" required>
                
                <label for="quantite_stock">Quantité en stock :</label>
                <input type="number" id="quantite_stock" name="quantite_stock" value="<?= $macaron['quantite_stock'] ?>" required>
                
                <div class="current-image">
                    <p>Image actuelle :</p>
                    <img src="<?= $macaron['image_url'] ?>" alt="<?= htmlspecialchars($macaron['nom']) ?>">
                </div>
                
                <label for="image-upload">Nouvelle image (optionnel) :</label><br>
                <label for="image-upload" class="file-input-label">
                    Choisir une nouvelle image
                </label>
                <input type="file" id="image-upload" name="image_file" accept="image/*" style="display: none;">
                
                <button type="submit">Enregistrer les modifications</button>
            </form>
        <?php else: ?>
            <p>Macaron non trouvé.</p>
        <?php endif; ?>
    </div>
</body>
</html>