<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();




if (!isset($_SESSION['id_user'])) {
    debug_log("Erreur: Utilisateur non connecté");
    header('Location: page_users/page_login.php'); 
    exit();
}


$conn = new mysqli('localhost', 'root', '', 'noracammacarons', 3307);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);


$id_user = $_SESSION['id_user'];

$sql = "SELECT c.id_commande, cb.id_boite_commande, b.id_boite, b.capacite, b.prix AS prix_boite 
        FROM Commande c
        JOIN CommandeBoite cb ON cb.id_commande = c.id_commande
        JOIN Boite b ON cb.id_boite = b.id_boite
        WHERE c.id_user = ? AND c.statut = 'en traitement'
        ORDER BY c.date_commande DESC, c.id_commande DESC
        LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: macaron_citron.php');
    exit();
}

$commande = $result->fetch_assoc();
$id_commande = $commande['id_commande'];
$id_boite_commande = $commande['id_boite_commande'];
$id_boite = $commande['id_boite'];
$capacite_boite = $commande['capacite'];
$prix_boite = $commande['prix_boite'];


$sql_update_boite = "UPDATE CommandeBoite SET sous_total = ? WHERE id_boite_commande = ?";
$stmt_update = $conn->prepare($sql_update_boite);
$stmt_update->bind_param("di", $prix_boite, $id_boite_commande);
$stmt_update->execute();


$sql_macarons = "SELECT mdb.id_macaron, mdb.quantite, m.prix_unitaire, m.nom 
                FROM MacaronsDansBoite mdb
                JOIN Macaron m ON mdb.id_macaron = m.id_macaron
                WHERE mdb.id_boite_commande = ?";
$stmt_macarons = $conn->prepare($sql_macarons);
$stmt_macarons->bind_param("i", $id_boite_commande);
$stmt_macarons->execute();
$macarons_selectionnes = $stmt_macarons->get_result()->fetch_all(MYSQLI_ASSOC);


$total_macarons = array_sum(array_column($macarons_selectionnes, 'quantite'));


foreach ($macarons_selectionnes as $macaron) {
    $id_macaron = $macaron['id_macaron'];
    $quantite = $macaron['quantite'];
    $prix_unitaire = $macaron['prix_unitaire'];
    $prix_total = $quantite * $prix_unitaire;
    
    $sql_update_prix = "UPDATE MacaronsDansBoite SET prix_total = ? WHERE id_boite_commande = ? AND id_macaron = ?";
    $stmt_update_prix = $conn->prepare($sql_update_prix);
    $stmt_update_prix->bind_param("dii", $prix_total, $id_boite_commande, $id_macaron);
    $stmt_update_prix->execute();
}


$sql_update_total = "UPDATE Commande c SET c.total_commande = 
                     (SELECT COALESCE(SUM(mb.prix_total), 0) + 
                     (SELECT COALESCE(SUM(cb.sous_total), 0) FROM CommandeBoite cb WHERE cb.id_commande = c.id_commande)
                     FROM MacaronsDansBoite mb 
                     JOIN CommandeBoite cb ON mb.id_boite_commande = cb.id_boite_commande 
                     WHERE cb.id_commande = c.id_commande)
                     WHERE c.id_commande = ?";
$stmt_update_total = $conn->prepare($sql_update_total);
$stmt_update_total->bind_param("i", $id_commande);
$stmt_update_total->execute();


$order_by = isset($_GET['ordre']) ? $_GET['ordre'] : 'defaut';

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Macarons - Fruits exotiques</title>
    <link rel="stylesheet" href="macaron_citron.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
     <style>
        .filter-bar {
            display: flex;
            justify-content: center;
            margin: 20px 0;
            margin-bottom: 40px;
            padding: 20px;
            background-color: #f8f8f8;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .filter-bar a {
            margin: 0 15px;
            padding: 8px 15px;
            text-decoration: none;
            color: #333;
            font-size: 27px;
            font-weight: bold;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .filter-bar a:hover {
            background-color: #e0e0e0;
        }
        
        .filter-bar a.active {
            background-color: #d3b613;
            color: white;
        }
    </style>
</head>
<body>
<?php include('header.php'); ?>

<section class="fruits_exotique active">
    <section class="cover_citron">
        <div class="cover_overlay">
            <h1>Noracam Macarons</h1>
            <span class="slogan">Macarons Fruités & Exotiques</span>
        </div>
    </section>

    <h2>Macaron Items</h2>



 
<div class="filter-bar">
    <a href="?ordre=defaut" class="<?php echo $order_by == 'defaut' ? 'active' : ''; ?>">Par défaut</a>
    <a href="?ordre=nom" class="<?php echo $order_by == 'nom' ? 'active' : ''; ?>">Ordre alphabétique</a>
    <a href="?ordre=prix_asc" class="<?php echo $order_by == 'prix_asc' ? 'active' : ''; ?>">Prix croissant</a>
    <a href="?ordre=prix_desc" class="<?php echo $order_by == 'prix_desc' ? 'active' : ''; ?>">Prix décroissant</a>
</div>


<div id="macaron-modal" style="display:none; position:fixed; top:25%; left:50%; transform:translateX(-50%);
background:white; padding:20px; border:1px solid #ccc; box-shadow:0 0 10px rgba(0,0,0,0.3); z-index:999; width:700px;">
    <span id="close-modal" style="cursor:pointer; float:right; font-size:25px;">&times;</span>
    <h3 id="modal-nom" style="font-size:55px; margin-left:155px; color:  rgb(211, 182, 19);"></h3>
    <img id="modal-image" src="" alt="" style="width:40%; height:auto; margin-left:195px">
    <p style="font-size:30px;"><strong>Ingrédients :</strong> <span id="modal-ingred"></span></p>
    <p style="font-size:30px;"><strong>Description :</strong> <span id="modal-descrip"></span></p>
    <p style="font-size:30px;"><strong>Prix :</strong> <span id="modal-prix"></span> DA</p>
</div>


    <div class="card--list">
        <?php
       
        $sql = "SELECT * FROM macaron 
        WHERE id_categorie = 
        (SELECT id_categorie FROM categorie 
         WHERE nom = 'Fruités & Exotiques')";
       

switch ($order_by) {
    case 'nom':
        $sql .= " ORDER BY nom ASC";
        break;
    case 'prix_asc':
        $sql .= " ORDER BY prix_unitaire ASC";
        break;
    case 'prix_desc':
        $sql .= " ORDER BY prix_unitaire DESC";
        break;
    default:
       
        break;
}




       $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($macaron = $result->fetch_assoc()) {
                echo "<div class='card' data-macaron-id='" . $macaron['id_macaron'] . "' data-prix='" . $macaron['prix_unitaire'] . "'>";
                echo "<i class='fa-solid fa-circle-info info-btn' 
                data-nom='" . htmlspecialchars($macaron['nom']) . "'
                data-ingred='" . htmlspecialchars($macaron['ingred']) . "'
                data-descrip='" . htmlspecialchars($macaron['descrip']) . "'
                data-prix='" . $macaron['prix_unitaire'] . "'
                data-image='" . htmlspecialchars($macaron['image_url']) . "'>
              </i>";
                echo "<img src='" . htmlspecialchars($macaron['image_url']) . "' alt=''>";
                echo "<h4 class='card--title'>" . htmlspecialchars($macaron['nom']) . "</h4>";
                echo "<div class='card--price'>";
                echo "<div class='price'>" . htmlspecialchars($macaron['prix_unitaire']) . " DA</div>";
                echo "<i class='fa-solid fa-minus'></i>";
                echo "<i class='fa-solid fa-plus add-to-card'></i>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "Aucun macaron trouvé.";
        }

        $conn->close();
        ?>
    </div>

    <div class="commande-summary">
        <h3>Résumé de votre commande</h3>
        <p>Prix de la boîte: <?= $prix_boite ?> DA</p>
        <p id="total-macarons-count">Nombre de macarons: <?= $total_macarons ?> / <?= $capacite_boite ?></p>
        <p>Total actuel: <span id="total-price">Chargement...</span> DA</p>
    </div>

    <button class="btn-valider" style="display: <?= $total_macarons >= $capacite_boite ? 'block' : 'none' ?>;">Finaliser la commande</button>
</section>

<?php include('footer.php'); ?>

<script>
document.querySelectorAll('.info-btn').forEach(function(btn) {
btn.addEventListener('click', function() {
    document.getElementById('modal-nom').textContent = this.dataset.nom;
    document.getElementById('modal-image').src = this.dataset.image;
    document.getElementById('modal-ingred').textContent = this.dataset.ingred;
    document.getElementById('modal-descrip').textContent = this.dataset.descrip;
    document.getElementById('modal-prix').textContent = this.dataset.prix;
    document.getElementById('macaron-modal').style.display = 'block';
});
});

document.getElementById('close-modal').addEventListener('click', function() {
document.getElementById('macaron-modal').style.display = 'none';
});
</script>




<script>
    document.addEventListener('DOMContentLoaded', function() {
        const capaciteBoite = <?= $capacite_boite ?>;
        let totalMacarons = <?= $total_macarons ?>;
        const prixBoite = <?= $prix_boite ?>;
        const macaronsSelectionnes = {};
        
      
        fetchTotalPrice();

  
        <?php 
        foreach ($macarons_selectionnes as $macaron) {
            echo "macaronsSelectionnes[{$macaron['id_macaron']}] = {
                quantite: {$macaron['quantite']},
                prix: {$macaron['prix_unitaire']}
            };";
        }
        ?>

        document.querySelectorAll('.card').forEach(card => {
            const macaronId = card.dataset.macaronId;
            const prix = parseFloat(card.dataset.prix);
            
            if (!macaronsSelectionnes[macaronId]) {
                macaronsSelectionnes[macaronId] = {
                    quantite: 0,
                    prix: prix
                };
            }

            const quantityDisplay = document.createElement('span');
            quantityDisplay.className = 'quantity';
            quantityDisplay.textContent = macaronsSelectionnes[macaronId].quantite;
            card.querySelector('.add-to-card').insertAdjacentElement('beforebegin', quantityDisplay);
        });

       
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('fa-minus')) {
                const card = e.target.closest('.card');
                const macaronId = card.dataset.macaronId;

                if (macaronsSelectionnes[macaronId].quantite > 0) {
                    macaronsSelectionnes[macaronId].quantite--;
                    totalMacarons--;
                    updateQuantities();
                    updateDatabase(macaronId, macaronsSelectionnes[macaronId].quantite);
                }
            }

            if (e.target.classList.contains('add-to-card')) {
                const card = e.target.closest('.card');
                const macaronId = card.dataset.macaronId;

                if (totalMacarons < capaciteBoite) {
                    macaronsSelectionnes[macaronId].quantite++;
                    totalMacarons++;
                    updateQuantities();
                    updateDatabase(macaronId, macaronsSelectionnes[macaronId].quantite);
                } else {
                    alert(`Vous avez atteint le maximum de ${capaciteBoite} macarons pour cette boîte`);
                }
            }
            
            if (e.target.classList.contains('btn-valider')) {
                window.location.href = 'test.php';
            }
        });

        function updateQuantities() {
            document.querySelectorAll('.card').forEach(card => {
                const macaronId = card.dataset.macaronId;
                card.querySelector('.quantity').textContent = macaronsSelectionnes[macaronId].quantite;
            });

            const finalizeBtn = document.querySelector('.btn-valider');
            if (finalizeBtn) {
                finalizeBtn.style.display = totalMacarons >= capaciteBoite ? 'block' : 'none';
            }
            
            document.getElementById('total-macarons-count').textContent = `Nombre de macarons: ${totalMacarons} / ${capaciteBoite}`;
        }

        function fetchTotalPrice() {
            fetch('get_total_price.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id_commande: <?= $id_commande ?>
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('total-price').textContent = data.total_price;
                } else {
                    console.error('Erreur:', data.error);
                }
            });
        }

        function updateDatabase(macaronId, quantite) {
            fetch('update_commande.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id_boite_commande: <?= $id_boite_commande ?>,
                    id_macaron: macaronId,
                    quantite: quantite
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fetchTotalPrice(); 
                } else {
                    console.error('Erreur:', data.error);
                }
            });
        }
    });
</script>

</body>
</html>


         

