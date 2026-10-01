<?php
session_start();

if (!isset($_SESSION["id_user"])) {
    header("Location: page_users/page_login.php");
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'noracammacarons', 3307);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$id_user = $_SESSION["id_user"];


$stmt = $conn->prepare("SELECT nom_utilisateur, email, adresse, tel, role FROM Utilisateur WHERE id_user = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();


$stmt2 = $conn->prepare("SELECT nom_titulaire, mois_expiration, annee_expiration FROM Paiement WHERE id_user = ?");
$stmt2->bind_param("i", $id_user);
$stmt2->execute();
$result2 = $stmt2->get_result();
$paiement = $result2->fetch_assoc();


$stmt3 = $conn->prepare("CALL historique_commandes_client(?)");
$stmt3->bind_param("i", $id_user);
$stmt3->execute();
$historique = $stmt3->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Compte</title>
    <style>
  

    body {
        display: flex;
        margin: 0;
        font-family: Arial, sans-serif;
    }

    .sidebar {
        width: 22%;
        background-color: rgb(248, 236, 128);
        color: white;
        height: 100vh;
        padding-top: 20px;
    }

    .sidebar a {
        display: flex;
        color: white;
        padding: 20px 180px;
        text-decoration: none;
        background-color: rgb(218, 203, 73);
        margin-bottom: 10px;
    }

    .sidebar a p {
        font-size: 25px;
        white-space: nowrap;
        margin: 0;
    }

    .content {
        flex: 1;
        padding: 30px;
    }

    .information-perso, .historique-commandes {
        border: 2px solid rgb(173, 142, 37);
        border-radius: 25px;
        margin-top: 110px;
        width: 1200px;
        height: auto;
        margin-left: auto;
        margin-right: auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 30px;
    }
    
    table {
        font-size: 20px;
        width: 100%;
    }
    
    table th {
        background-color: rgb(248, 236, 128);
        padding: 12px;
        text-align: left;
    }
    
    table td {
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }

    .content .titre {
        background: url('perso_logo/compte2.jpg') no-repeat center/cover;
        background-size: cover;
        height: 350px;
        background-position: center;
        display: flex;
        align-items: center;
        flex-direction: column;
        justify-content: center;
    }

    .sidebar .image {
        background: url('perso_logo/femme_dessin.png') no-repeat center/cover;
        background-size: 300px auto;
        background-color: rgb(252, 242, 226);
        width: 450px;
        height: 450px;
        margin-top: 60px;
        border-radius: 50%;
        margin-bottom: 50px;
        border: 3px solid rgb(230, 200, 146);
    }

  

    .sidebar h2 {
        text-align: center;
        font-size: 40px;
        margin-bottom: 100px;
    }

    .sidebar a:hover {
        background-color: rgb(156, 180, 21);
    }

    h1 {
        font-size: 50px;
        color: rgb(180, 164, 21);
        margin-left: 80px;
        margin-top: 10px;
        font-weight: 40px;
    }

    h2 {
        font-size: 45px;
        color: rgb(16, 30, 44);
    }

    p {
        font-size: 30px;
        margin: 8px 0;
    }

    strong {
        color: rgb(0, 0, 0);
        font-size: 30px;
    }

    a.logout {
        display: inline-block;
        margin-top: 20px;
        text-decoration: none;
        color: #e74c3c;
        font-weight: bold;
    }

    a.logout:hover {
        text-decoration: underline;
    }
</style>



</head>
<body>
    <div class="sidebar" style=" padding: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;">
    
    <div class="image"></div>

    <h2><?= htmlspecialchars($user['nom_utilisateur']) ?></h2>

        <a href="test.php"><p>Retour à l'accueil</p></a>
        <a href="panier.php"><p>Consulter le panier</p></a>
        <a href="#" onclick="showSection('historique')"><p>Historique&commandes</p></a>
        <a href="deconnexion.php"><p>Se déconnecter</p></a>
        
    </div>
    
    <div class="content">
        <div class="titre">
    <h1>Bienvenue  <?= htmlspecialchars($user['nom_utilisateur']) ?> , sur votre espace personnel, merci de votre fidélité !</h1>
    </div>
        <div class="information-perso">
        <h2>Informations personnelles</h2>
        <p><strong>Nom d'utilisateur :</strong> <?= htmlspecialchars($user['nom_utilisateur']) ?></p>
        <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Adresse :</strong> <?= nl2br(htmlspecialchars($user['adresse'])) ?></p>
        <p><strong>Téléphone :</strong> <?= htmlspecialchars($user['tel']) ?></p>
        <p><strong>Rôle :</strong> <?= htmlspecialchars($user['role']) ?></p>
        <br>
        <br>
        <?php if ($paiement): ?>
            <h2>Informations de paiement (partielles)</h2>
            <p><strong>Nom du titulaire :</strong> <?= htmlspecialchars($paiement['nom_titulaire']) ?></p>
            <p><strong>Date d'expiration :</strong> <?= sprintf("%02d/%d", $paiement['mois_expiration'], $paiement['annee_expiration']) ?></p>
        <?php else: ?>
            <p>Aucune information de paiement enregistrée.</p>
        <?php endif; ?>
        </div>
        
        <div id="historique" class="historique-commandes" style="display: none;">
            <h2 style="color:rgb(233, 203, 69)">Historique des commandes</h2>
            <?php if ($historique && $historique->num_rows > 0): ?>
                <table border="1" style="width: 100%; height:auto; border-collapse: collapse; margin-top: 20px;">
                    <thead>
                        <tr>
                            <th>N° Commande</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Adresse de livraison</th>
                            <th>Total</th>
                            <th>Nombre de boîtes</th>
                            <th>Nombre de macarons</th>
                        </tr>
                    </thead>
                      <tbody>
                        <?php while ($commande = $historique->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($commande['id_commande']) ?></td>
                                <td><?= htmlspecialchars($commande['date_commande']) ?></td>
                                <td><?= htmlspecialchars($commande['statut']) ?></td>
                                <td><?= htmlspecialchars($commande['adresse_livraison']) ?></td>
                                <td><?= htmlspecialchars($commande['total_commande']) ?> €</td>
                                <td><?= htmlspecialchars($commande['nombre_boites']) ?></td>
                                <td><?= isset($commande['nombre_macarons_total']) ? htmlspecialchars($commande['nombre_macarons_total']) : 'N/A' ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucune commande n'a été trouvée dans votre historique.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function showSection(sectionId) {
            
            document.querySelector('.information-perso').style.display = 'none';
            document.getElementById('historique').style.display = 'none';
            
          
            if (sectionId === 'historique') {
                document.getElementById('historique').style.display = 'block';
            } else {
                document.querySelector('.information-perso').style.display = 'flex';
            }
            
            return false; 
        }
    </script>
</body>
</html>