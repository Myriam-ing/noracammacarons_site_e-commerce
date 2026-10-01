<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

$id_user = $_SESSION['id_user'];


$conn = new mysqli('localhost', 'root', '', 'noracammacarons', 3307);
if ($conn->connect_error) {
    die("Erreur de connexion à la base de données");
}


function getCommandeDetails($conn, $id_commande) {
    $details = [];
    
    $sql = "SELECT cb.id_boite_commande, b.nom AS nom_boite, b.capacite, 
            cb.quantite, cb.sous_total, couv.nom AS nom_couverture
            FROM CommandeBoite cb
            JOIN Boite b ON cb.id_boite = b.id_boite
            LEFT JOIN Couverture couv ON cb.id_couverture = couv.id_couverture
            WHERE cb.id_commande = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_commande);
    $stmt->execute();
    $result_boites = $stmt->get_result();
    $stmt->close();
    
    while ($boite = $result_boites->fetch_assoc()) {
        $id_boite_commande = $boite['id_boite_commande'];
        $boite['macarons'] = [];
        
        $sql = "SELECT m.nom, m.prix_unitaire, mb.quantite, mb.prix_total
                FROM MacaronsDansBoite mb
                JOIN Macaron m ON mb.id_macaron = m.id_macaron
                WHERE mb.id_boite_commande = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_boite_commande);
        $stmt->execute();
        $result_macarons = $stmt->get_result();
        $stmt->close();
        
        $total_macarons = 0;
        while ($macaron = $result_macarons->fetch_assoc()) {
            $boite['macarons'][] = $macaron;
            $total_macarons += $macaron['quantite'];
        }
        
        $boite['total_macarons'] = $total_macarons;
        $details[] = $boite;
    }
    
    return $details;
}


$sql = "SELECT c.id_commande, c.date_commande, c.total_commande, c.adresse_livraison 
        FROM Commande c 
        WHERE c.id_user = ? AND c.statut = 'en traitement'";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Erreur lors de la préparation de la requête");
}

$stmt->bind_param("i", $id_user);
$stmt->execute();
$result_commandes = $stmt->get_result();
$stmt->close();


$commande_details = null;
$commande_id_details = 0;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $id_commande = isset($_POST['id_commande']) ? intval($_POST['id_commande']) : 0;
        
        if ($id_commande > 0) {
            
            $sql_check = "SELECT id_commande FROM Commande WHERE id_commande = ? AND id_user = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("ii", $id_commande, $id_user);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            $stmt_check->close();
            
            if ($result_check->num_rows > 0) {
                if ($_POST['action'] === 'valider') {
                    $stmt = $conn->prepare("CALL finaliser_commande(?, ?)");
                    $stmt->bind_param("ii", $id_commande, $id_user);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $message_result = $result->fetch_assoc();
                    $stmt->close();
                    
                    header('Location: panier.php?success=validate');
                    exit;
                } 
                elseif ($_POST['action'] === 'supprimer') {
                    $sql_delete = "DELETE FROM Commande WHERE id_commande = ?";
                    $stmt_delete = $conn->prepare($sql_delete);
                    $stmt_delete->bind_param("i", $id_commande);
                    $success = $stmt_delete->execute();
                    $stmt_delete->close();
                    
                    if ($success) {
                        header('Location: panier.php?success=delete');
                        exit;
                    }
                }
                elseif ($_POST['action'] === 'details') {
                    $commande_id_details = $id_commande;
                }
            }
        }
    }
}


$message = '';
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'validate') {
        $message = '<div class="alert alert-success">Votre commande a été validée avec succès.</div>';
    } elseif ($_GET['success'] === 'delete') {
        $message = '<div class="alert alert-info">Votre commande a été supprimée.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier - Noracam Macarons</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
       

.container {
    padding-left: 60px; 
    width: 100%;
    max-width: 100%;
}

.row {
    display: flex;
    flex-wrap: wrap;
}


.col-md-6.col-lg-4 {
    flex: 0 0 calc(25% - 20px); 
    max-width: calc(25% - 20px);
    padding: 0 10px;
}


.card {
    margin-top: 50px;
    width: calc(100% + 20px); 
    height: 700px; 
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    overflow-y: auto;
}


.mb-5 {
    margin-bottom: 2rem !important;
}


        .card-header {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .macaron-list {
            
            max-height: 200px;
            overflow-y: auto;
            margin-bottom: 10px;
        }
        .btn-action {
             font-size: 20px;
            margin-right: 5px;
        }
        .empty-cart {
            text-align: center;
            padding: 50px 0;
        }
        .card-macaron-item {
            border-bottom: 1px solid #eee;
            padding: 5px 0;
        }
        .card-macaron-item:last-child {
            border-bottom: none;
        }
        .box-info {
            background-color: #f8f9fa;
            padding: 10px;
            font-size: 25px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .btn-details {
             font-size: 25px;
            margin-bottom: 10px;
            width: 100%;
        }
        .modal-detail-item {
            border-bottom: 1px solid #eee;
            padding: 8px 0;
        }
        .modal-detail-header {
            background-color: #f8f9fa;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .modal-macaron-item {
            padding: 5px 15px;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <?php include('header.php'); ?>

    <div class="haut_de_page">
          <h1 class="mb-4" style="
    display: flex;
    font-size:80px;
    margin-top:100px;
    color:rgb(180, 169, 9);
    justify-content: center;
    align-items: center;
    height: 250px;
    width:2710px;
    background: url('perso_logo/compte2.jpg') no-repeat center/cover;
">Mon Panier</h1>
    </div>
    <div class="container mt-5" >
      
        
        <?php echo $message; ?>
        
        <div class="row">
            <?php if ($result_commandes->num_rows === 0): ?>
                <div class="col-12 empty-cart">
                    <i class="fas fa-shopping-cart fa-4x mb-3 text-muted" style="font-size: 120px;"></i>
                    <h3 style="font-size: 50px;">Votre panier est vide</h3>
                    <p style="font-size: 30px;">Vous n'avez aucune commande en cours de traitement.</p>
                    <a href="test.php" class="btn btn-primary" style="border:none; font-size: 30px; background-color:rgb(32, 196, 17);">Continuer mes achats</a>
                </div>




            <?php else: ?>
                <?php while ($commande = $result_commandes->fetch_assoc()): ?>
                    <?php $details = getCommandeDetails($conn, $commande['id_commande']); ?>
                    <div class="col-md-6 col-lg-4 mb-5 px-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span style=" font-size: 22px;">Commande #<?php echo $commande['id_commande']; ?></span>
                                <small style=" font-size: 22px;"><?php echo date('d/m/Y à H:i', strtotime($commande['date_commande'])); ?></small>
                            </div>
                            <div class="card-body">
                                <h5 style=" font-size: 25px;" class="card-title">Total: <?php echo number_format($commande['total_commande'], 2); ?> DA</h5>
                                
                                <?php if (!empty($commande['adresse_livraison'])): ?>
                                <p style=" font-size: 25px;"><strong>Adresse de livraison:</strong><br>
                                <?php echo nl2br(htmlspecialchars($commande['adresse_livraison'])); ?></p>
                                <?php endif; ?>
                                
                                <hr>
                                
                                
                                <form method="post" class="mb-3">
                                    <input type="hidden" name="id_commande" value="<?php echo $commande['id_commande']; ?>">
                                    <input type="hidden" name="action" value="details">
                                    <button  style="border:none; background-color:rgb(212, 200, 22)";  type="button" class="btn btn-info btn-details" data-bs-toggle="modal" data-bs-target="#detailModal<?php echo $commande['id_commande']; ?>">
                                        <i class="fas fa-info-circle"></i> Voir les détails complets
                                    </button>
                                </form>
                                
                                <?php foreach ($details as $boite): ?>
                                <div class="box-info">
                                    <h6><?php echo htmlspecialchars($boite['nom_boite']); ?> 
                                        (<?php echo $boite['total_macarons']; ?>/<?php echo $boite['capacite']; ?> macarons)
                                        × <?php echo $boite['quantite']; ?>
                                    </h6>
                                    
                                    <?php if (!empty($boite['nom_couverture'])): ?>
                                    <p class="mb-1">Couverture: <?php echo htmlspecialchars($boite['nom_couverture']); ?></p>
                                    <?php endif; ?>
                                    
                                    <div class="macaron-list">
                                        <?php if (empty($boite['macarons'])): ?>
                                            <p class="text-muted">Aucun macaron sélectionné</p>
                                        <?php else: ?>
                                            <?php foreach ($boite['macarons'] as $macaron): ?>
                                                <div class="card-macaron-item d-flex justify-content-between">
                                                    <span><?php echo htmlspecialchars($macaron['nom']); ?> × <?php echo $macaron['quantite']; ?></span>
                                                    <span><?php echo number_format($macaron['prix_total'], 2); ?> DA</span>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="text-end">
                                        <strong>Sous-total: <?php echo number_format($boite['sous_total'], 2); ?> DA</strong>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                
                                <div class="d-flex justify-content-between mt-3">
                                    <form method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette commande?');">
                                        <input type="hidden" name="id_commande" value="<?php echo $commande['id_commande']; ?>">
                                        <input type="hidden" name="action" value="supprimer">
                                        <button type="submit" class="btn btn-danger btn-action">
                                            <i class="fas fa-trash-alt"></i> Supprimer
                                        </button>
                                    </form>
                                    
                                    <form method="post" onsubmit="return confirm('Confirmer la validation de votre commande?');">
                                        <input type="hidden" name="id_commande" value="<?php echo $commande['id_commande']; ?>">
                                        <input type="hidden" name="action" value="valider">
                                        <button type="submit" class="btn btn-success btn-action">
                                            <i class="fas fa-check"></i> Valider
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                 
                    <div class="modal fade" id="detailModal<?php echo $commande['id_commande']; ?>" tabindex="-1" aria-labelledby="detailModalLabel<?php echo $commande['id_commande']; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="detailModalLabel<?php echo $commande['id_commande']; ?>">
                                        Détails de la commande #<?php echo $commande['id_commande']; ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="commande-details-<?php echo $commande['id_commande']; ?>">
                                       
                                        <div class="text-center p-5">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Chargement...</span>
                                            </div>
                                            <p class="mt-2">Chargement des détails...</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
        
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
       
        function loadOrderDetails(orderId, userId) {
            const detailsContainer = document.getElementById('commande-details-' + orderId);
            
           
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'get_commande_details.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onload = function() {
                if (this.status === 200) {
                    detailsContainer.innerHTML = this.responseText;
                } else {
                    detailsContainer.innerHTML = '<div class="alert alert-danger">Erreur lors du chargement des détails</div>';
                }
            };
            
            xhr.onerror = function() {
                detailsContainer.innerHTML = '<div class="alert alert-danger">Erreur de connexion</div>';
            };
            
            xhr.send('id_commande=' + orderId + '&id_user=' + userId);
        }
        
       
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($result_commandes->num_rows > 0): ?>
                <?php mysqli_data_seek($result_commandes, 0); ?>
                <?php while ($commande = mysqli_fetch_assoc($result_commandes)): ?>
                const modal<?php echo $commande['id_commande']; ?> = document.getElementById('detailModal<?php echo $commande['id_commande']; ?>');
                if (modal<?php echo $commande['id_commande']; ?>) {
                    modal<?php echo $commande['id_commande']; ?>.addEventListener('shown.bs.modal', function() {
                        loadOrderDetails(<?php echo $commande['id_commande']; ?>, <?php echo $id_user; ?>);
                    });
                }
                <?php endwhile; ?>
            <?php endif; ?>
        });
    </script>
</body>
</html>

<?php

$conn->close();
?>