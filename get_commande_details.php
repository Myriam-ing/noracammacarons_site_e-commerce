<?php
session_start();
// Activer l'affichage des erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    echo '<div class="alert alert-danger">Vous devez être connecté pour voir ces détails.</div>';
    exit;
}

// Vérifier si les paramètres nécessaires sont présents
if (!isset($_POST['id_commande']) || !isset($_POST['id_user'])) {
    echo '<div class="alert alert-danger">Données manquantes.</div>';
    exit;
}

$id_commande = (int) $_POST['id_commande'];
$id_user = (int) $_POST['id_user'];

// Vérifier que l'ID utilisateur correspond à celui connecté (sécurité)
if ($id_user !== (int) $_SESSION['id_user']) {
    echo '<div class="alert alert-danger">Accès non autorisé.</div>';
    exit;
}

// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'noracammacarons', 3307);
if ($conn->connect_error) {
    echo '<div class="alert alert-danger">Erreur de connexion à la base de données.</div>';
    exit;
}

// Appeler la procédure stockée pour obtenir les détails de la commande
$stmt = $conn->prepare("CALL afficher_details_commande(?, ?)");
$stmt->bind_param("ii", $id_commande, $id_user);
$stmt->execute();

// Récupérer les informations générales de la commande
$result_commande = $stmt->get_result();
$commande_info = $result_commande->fetch_assoc();

// Vérifier si la commande existe et appartient à l'utilisateur
if (!$commande_info || isset($commande_info['message'])) {
    echo '<div class="alert alert-danger">' . 
         (isset($commande_info['message']) ? htmlspecialchars($commande_info['message']) : 'Cette commande n\'existe pas.') . 
         '</div>';
    $stmt->close();
    $conn->close();
    exit;
}

// Récupérer les boîtes de la commande (deuxième jeu de résultats)
$stmt->next_result();
$result_boites = $stmt->get_result();
$boites = [];
while ($boite = $result_boites->fetch_assoc()) {
    $boites[] = $boite;
}

// Récupérer les macarons dans chaque boîte (troisième jeu de résultats)
$stmt->next_result();
$result_macarons = $stmt->get_result();
$macarons_par_boite = [];
while ($macaron = $result_macarons->fetch_assoc()) {
    $id_boite_commande = $macaron['id_boite_commande'];
    if (!isset($macarons_par_boite[$id_boite_commande])) {
        $macarons_par_boite[$id_boite_commande] = [];
    }
    $macarons_par_boite[$id_boite_commande][] = $macaron;
}

// Récupérer le total à payer (quatrième jeu de résultats)
$stmt->next_result();
$result_total = $stmt->get_result();
$total = $result_total->fetch_assoc();

// Fermer le statement
$stmt->close();
?>

<!-- Affichage des détails de la commande -->
<div class="commande-details" style="height: 800px;">
    <div class="modal-detail-header" style="font-size: 20px;">
        Informations générales
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <p style="font-size: 20px;"><strong>Client:</strong> <?php echo htmlspecialchars($commande_info['nom_utilisateur']); ?></p>
            <p style="font-size: 20px;"><strong>Email:</strong> <?php echo htmlspecialchars($commande_info['email']); ?></p>
        </div>
        <div class="col-md-6">
            <p style="font-size: 20px;"><strong>Date:</strong> <?php echo date('d/m/Y à H:i', strtotime($commande_info['date_commande'])); ?></p>
            <p style="font-size: 20px;"><strong>Statut:</strong> <?php echo htmlspecialchars($commande_info['statut']); ?></p>
        </div>
    </div>
    
    <?php if (!empty($commande_info['adresse_livraison'])): ?>
    <div class="mb-3" style="font-size: 20px;">
        <strong>Adresse de livraison:</strong><br>
        <?php echo nl2br(htmlspecialchars($commande_info['adresse_livraison'])); ?>
    </div>
    <?php endif; ?>
    
    <div class="modal-detail-header" style="font-size: 20px;">
        Boîtes commandées
    </div>
    
    <?php foreach ($boites as $boite): ?>
    <div class="card mb-3" style="height: 400px; width:750px;">
        <div class="card-header" style="font-size: 20px;">
            <?php echo htmlspecialchars($boite['nom_boite']); ?> 
            × <?php echo $boite['quantite']; ?>
            <?php if (!empty($boite['nom_couverture'])): ?>
            - Couverture: <?php echo htmlspecialchars($boite['nom_couverture']); ?>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <h6 style="font-size: 20px;">Macarons dans cette boîte:</h6>
            
            <?php if (isset($macarons_par_boite[$boite['id_boite_commande']])): ?>
                <div class="macaron-list">
                    <?php foreach ($macarons_par_boite[$boite['id_boite_commande']] as $macaron): ?>
                    <div class="modal-macaron-item" style="font-size: 20px;">
                        <span><?php echo htmlspecialchars($macaron['nom_macaron']); ?> × <?php echo $macaron['quantite']; ?></span>
                        
                        <span><?php echo number_format($macaron['prix_total_macaron'], 2); ?> DA</span>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted" style="font-size: 20px;">Aucun macaron sélectionné pour cette boîte.</p>
            <?php endif; ?>
            
            <div class="text-end mt-2" style="font-size: 20px;">
                <strong>Sous-total: <?php echo number_format($boite['sous_total'], 2); ?> DA</strong>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    
    <div class="alert alert-info mt-3 text-end">
        <h5 class="mb-0" style="font-size: 20px;">Total à payer: <?php echo number_format($total['total_a_payer'], 2); ?> DA</h5>
    </div>
</div>

<?php
// Fermer la connexion
$conn->close();
?>