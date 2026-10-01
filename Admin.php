<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$conn = new mysqli('localhost', 'root', '', 'noracammacarons', 3307);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $nom = $_POST['nom'];
    $id_categorie = $_POST['id_categorie'];
    $ingred = $_POST['ingred'];
    $descrip = $_POST['descrip'];
    $prix_unitaire = $_POST['prix_unitaire'];
    $quantite_stock = $_POST['quantite_stock'];

    
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image_file']['tmp_name'];
        $imageExtension = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
        $nouveauNomImage = uniqid('macaron_', true) . '.' . $imageExtension;

        // Dossier images
        $dossierDestination = __DIR__ . '/images/';
        if (!is_dir($dossierDestination)) {
            if (!mkdir($dossierDestination, 0777, true)) {
                die("Erreur : impossible de créer le dossier 'images/'.");
            }
        }

        $cheminComplet = $dossierDestination . $nouveauNomImage;
        if (move_uploaded_file($imageTmpPath, $cheminComplet)) {
            $cheminRelatifImage = 'images/' . $nouveauNomImage;

           
            $stmt = $conn->prepare("INSERT INTO Macaron (nom, id_categorie, ingred, descrip, prix_unitaire, quantite_stock, image_url) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sissdis", $nom, $id_categorie, $ingred, $descrip, $prix_unitaire, $quantite_stock, $cheminRelatifImage);

            if ($stmt->execute()) {
              $message = "✅ Macaron ajouté avec succès !";

            } else {
                echo "❌ Erreur lors de l'insertion : " . $stmt->error;
            }

            $stmt->close();
        } 
    } else {
        $message = "❌ Veuillez sélectionner une image valide.";
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Admin Macarons</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

  <div class="sidebar">
    <img src="https://via.placeholder.com/100" alt="Logo Macaron">
    <div class="menu">
      <a href="#" onclick="showSection('admin-home')">Page d'administration</a>
      <a href="#" onclick="showSection('macarons')">Produits : Macarons</a>
      <a href="#" onclick="showSection('boites')">Produits : Boîtes & Illustrations</a>
      <a href="#" onclick="showSection('commandes')">Commandes & Utilisateurs</a>
      <a href="#" onclick="showSection('parametres')">Paramètres & Messages</a>
      
    </div>
    <div class="back-arrow">
    <a href="deconnexion.php" style="font-size: 25px;">&#8675; Se déconnecter </a>
      
    </div>
    <div class="back-arrow">
      <a href="test.php" style="font-size: 25px;">&#8675; Retour à la page principale</a>
    </div>
  </div>

  <div class="main-content">

    <div id="admin-home" class="section active">
      <div class="image">
        <h2>Bienvenue, Cheriguene Myriam, sur votre espace d'administration !</h2>
      </div>
      <p class="intro-text">
        Souhaitez-vous apporter des modifications aujourd’hui ? N’hésitez pas à explorer les sections à gauche.
      </p>
      <p class="quote">
        🌸 Chaque détail compte — votre boutique de macarons mérite le meilleur ! 🌸
      </p>
    </div>

    <div id="macarons" class="section">
      <h2 style="font-size: 50px; text-align:center;">Gestion des produits : Macarons</h2>

      <div class="formulaire" style="margin-bottom: 100px;">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data" style="margin-top: 20px; padding: 20px; background-color: #fff0e6; border-radius: 25px;">
          <h3 style="color:rgb(177, 110, 52); margin-left:50px; font-size:40px;">
            <i class="fas fa-plus-circle" style="color:rgb(183, 192, 53);"></i>
            Ajouter un nouveau macaron
          </h3>

          <?php if (!empty($message)): ?>
            <div style="margin: 20px auto; text-align: center; font-size: 20px; font-weight: bold; color: darkgreen;">
              <?= $message ?>
            </div>
          <?php endif; ?>

          <label>Nom :</label><br>
          <input type="text" name="nom" required style="width:100%; font-size:25px; padding:8px;"><br><br>

          <label>Catégorie :</label><br>
          <div style="display: flex; gap: 40px; margin-top: 8px; padding: 10px 50px; margin-left:100px;">
            <label><input type="radio" name="id_categorie" value="1" required> Fruits rouges</label>
            <label><input type="radio" name="id_categorie" value="2"> Chocolat & Notes boisées</label>
            <label><input type="radio" name="id_categorie" value="3"> Sucré & Exotique</label>
          </div><br>

          <label>Ingrédients :</label><br>
          <textarea name="ingred" rows="3" style="width:100%; font-size:25px; padding:8px;"></textarea><br><br>

          <label>Description :</label><br>
          <textarea name="descrip" rows="3" style="width:100%; font-size:25px; padding:8px;"></textarea><br><br>

          <label>Prix unitaire (€) :</label><br>
          <input type="number" name="prix_unitaire" step="0.01" required style="width:100%; font-size:25px; padding:8px;"><br><br>

          <label>Quantité en stock :</label><br>
          <input type="number" name="quantite_stock" value="0" required style="width:100%; font-size:25px; padding:8px;"><br><br>

          <label>Image du produit :</label><br>
          <label for="image-upload" style="display: inline-block; background-color: #fbdada; color: #5f3b3b; padding: 8px 25px; font-size: 25px; border-radius: 10px; cursor: pointer;">
            Choisir une image
          </label>
          <input type="file" id="image-upload" name="image_file" accept="image/*" style="display: none;"><br><br>
          
          <button type="submit" style="background-color:rgb(167, 107, 123); font-size: 30px; color:white; margin-left:650px; padding:12px 60px; border:none; border-radius:25px; cursor:pointer;">
            Ajouter le macaron
          </button>
        </form>

  </div>

 
  <?php
      $conn = new mysqli("localhost", "root", "", "noracammacarons", 3307);

      if ($conn->connect_error) {
        die("Erreur de connexion : " . $conn->connect_error);
      }

      $sql = "SELECT * FROM Macaron";
      $result = $conn->query($sql);

      if ($result && $result->num_rows > 0): ?>
        <table border="1" cellspacing="0" cellpadding="20" style="border-color:rgb(221, 164, 89);border-radius:20px; width:100%; border-collapse:collapse; text-align:center; margin-top:20px;">
          <thead style="background-color:rgb(253, 225, 198); color:rgb(177, 110, 52);">
            <tr>
              <th>Image</th>
              <th>Nom</th>
              <th>Ingrédients</th>
              <th>Description</th>
              <th>Prix (€)</th>
              <th>Stock</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while($macaron = $result->fetch_assoc()): ?>
              <tr data-id="<?= $macaron['id_macaron'] ?>">
                <td><img src="<?= $macaron['image_url'] ?>" alt="macaron" width="170" height="150" style="border-radius:10px;"></td>
                <td><?= htmlspecialchars($macaron['nom']) ?></td>
                <td><?= nl2br(htmlspecialchars($macaron['ingred'])) ?></td>
                <td><?= nl2br(htmlspecialchars($macaron['descrip'])) ?></td>
                <td><?= number_format($macaron['prix_unitaire'], 2) ?></td>
                <td><?= $macaron['quantite_stock'] ?></td>
                
                <td>
                  
                  <button onclick="modifier(<?= $macaron['id_macaron'] ?>)" title="Modifier le macaron">
                    <i class="fas fa-pen" style="color:rgb(190, 101, 207); font-size:18px;"></i>
                  </button>
                  <button onclick="supprimer(<?= $macaron['id_macaron'] ?>)" title="Supprimer le macaron">
                    <i class="fas fa-trash-alt" style="color:red; font-size:18px;"></i>
                  </button>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>Aucun macaron trouvé.</p>
      <?php endif; ?>

      <?php $conn->close(); ?>
    </div>

    <div id="boites" class="section">
  <h2 style="font-size: 50px; text-align:center;">Gestion des produits : Boîtes & Illustrations</h2>

 
<div class="formulaire" style="margin-bottom: 50px;">
  <form id="form-boite" enctype="multipart/form-data" style="margin-top: 20px; padding: 20px; background-color: #fff0e6; border-radius: 25px;">
    <h3 style="color:rgb(177, 110, 52); margin-left:50px; font-size:40px;">
      <i class="fas fa-box-open" style="color:rgb(183, 192, 53);"></i>
      Ajouter une nouvelle boîte
    </h3>

    <label>Nom :</label><br>
    <input type="text" name="nom" required style="width:100%; font-size:25px; padding:8px;"><br><br>

    <label>Capacité (nombre de macarons) :</label><br>
    <input type="number" name="capacite" required style="width:100%; font-size:25px; padding:8px;"><br><br>

    <label>Prix (€) :</label><br>
    <input type="number" name="prix" step="0.01" required style="width:100%; font-size:25px; padding:8px;"><br><br>

    <label>Image du produit :</label><br>
    <label for="image-upload-boite" style="display: inline-block; background-color: #fbdada; color: #5f3b3b; padding: 8px 25px; font-size: 25px; border-radius: 10px; cursor: pointer;">
      Choisir une image
    </label>
    <input type="file" id="image-upload-boite" name="image_file" accept="image/*" style="display: none;"><br><br>
    
    <button type="submit" style="background-color:rgb(167, 107, 123); font-size: 30px; color:white; margin-left:650px; padding:12px 60px; border:none; border-radius:25px; cursor:pointer;">
      Ajouter la boîte
    </button>
  </form>
</div>


  <div id="tableau-boites">
    <?php
    $conn = new mysqli("localhost", "root", "", "noracammacarons", 3307);
    $boites = $conn->query("SELECT * FROM Boite");
    ?>

    <?php if ($boites && $boites->num_rows > 0): ?>
      <table border="1" cellspacing="0" cellpadding="20" style="border-color:rgb(221, 164, 89);border-radius:20px; width:100%; border-collapse:collapse; text-align:center; margin-top:20px;">
        <thead style="background-color:rgb(253, 225, 198); color:rgb(177, 110, 52);">
          <tr>
            <th>Image</th>
            <th>Nom</th>
            <th>Capacité</th>
            <th>Prix</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while($boite = $boites->fetch_assoc()): ?>
            <tr data-id="<?= $boite['id_boite'] ?>">
              <td><img src="<?= $boite['image_url'] ?>" alt="boite" width="170" height="150" style="border-radius:10px;"></td>
              <td><?= htmlspecialchars($boite['nom']) ?></td>
              <td><?= $boite['capacite'] ?> macarons</td>
              <td><?= number_format($boite['prix'], 2) ?> €</td>
              <td>
                <button onclick="modifierBoite(<?= $boite['id_boite'] ?>)" title="Modifier">
                  <i class="fas fa-pen" style="color:rgb(190, 101, 207); font-size:18px;"></i>
                </button>
                <button onclick="supprimerBoite(<?= $boite['id_boite'] ?>)" title="Supprimer">
                  <i class="fas fa-trash-alt" style="color:red; font-size:18px;"></i>
                </button>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p style="text-align: center; font-size: 20px; margin-top: 20px;">Aucune boîte disponible.</p>
    <?php endif; ?>
    <?php $conn->close(); ?>
  </div>


<div class="formulaire" style="margin-bottom: 50px; margin-top: 50px;">
  <form id="form-couverture" enctype="multipart/form-data" style="margin-top: 20px; padding: 20px; background-color: #fff0e6; border-radius: 25px;">
    <h3 style="color:rgb(177, 110, 52); margin-left:50px; font-size:40px;">
      <i class="fas fa-image" style="color:rgb(183, 192, 53);"></i>
      Ajouter une nouvelle illustration
    </h3>

    <label>Nom :</label><br>
    <input type="text" name="nom" required style="width:100%;font-size:25px;  padding:8px;"><br><br>

    <label>Image du produit :</label><br>
    <label for="image-upload-couverture" style="display: inline-block; background-color: #fbdada; color: #5f3b3b; padding: 8px 25px; font-size: 25px; border-radius: 10px; cursor: pointer;">
      Choisir une image
    </label>
    <input type="file" id="image-upload-couverture" name="image_file" accept="image/*" style="display: none;"><br><br>
      
    <button type="submit" style="background-color:rgb(167, 107, 123); font-size: 30px; color:white; margin-left:650px; padding:12px 60px; border:none; border-radius:25px; cursor:pointer;">
      Ajouter l'illustration
    </button>
  </form>
</div>


<div id="tableau-couvertures">
    <?php
    $conn = new mysqli("localhost", "root", "", "noracammacarons", 3307);
    $couvertures = $conn->query("SELECT * FROM Couverture");
    ?>

    <?php if ($couvertures && $couvertures->num_rows > 0): ?>
        <table border="1" cellspacing="0" cellpadding="20" style="border-color:rgb(221, 164, 89);border-radius:20px; width:100%; border-collapse:collapse; text-align:center; margin-top:20px;">
            <thead style="background-color:rgb(253, 225, 198); color:rgb(177, 110, 52);">
                <tr>
                    <th>Image</th>
                    <th>Nom</th>
                    
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($couverture = $couvertures->fetch_assoc()): ?>
                    <tr data-id="<?= $couverture['id_couverture'] ?>">
                        <td><img src="<?= $couverture['image_url'] ?>" alt="couverture" width="170" height="150" style="border-radius:10px;"></td>
                        <td><?= htmlspecialchars($couverture['nom']) ?></td>
                        <td>
                           
                            <button onclick="supprimerCouverture(<?= $couverture['id_couverture'] ?>)" title="Supprimer">
                                <i class="fas fa-trash-alt" style="color:red; font-size:18px;"></i>
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align: center; font-size: 20px; margin-top: 20px;">Aucune illustration disponible.</p>
    <?php endif; ?>
    <?php $conn->close(); ?>
</div>
</div>

    <div id="commandes" class="section">
      <h2>Commandes & Utilisateurs</h2>
      <p>Voir les commandes avec détails, statut et historique par utilisateur.</p>
      <p>Gérer les utilisateurs : comptes, historiques, préférences.</p>
    </div>

    <div id="parametres" class="section">
      <h2>Paramètres & Messages</h2>
      <p>Réglages SEO, sécurité, cookies.</p>
      <p>Messages des clients : commentaires, demandes spéciales via formulaire.</p>
    </div>
  </div>

<script>
  function showSection(id) {
    const sections = document.querySelectorAll('.section');
    sections.forEach(section => {
      section.classList.remove('active');
    });
    document.getElementById(id).classList.add('active');
  }
</script>


<script>
  function showAlert(message, type = 'success') {
    
    document.querySelectorAll('.alert-message').forEach(alert => alert.remove());
    
    const alert = document.createElement('div');
    alert.className = `alert-message alert-${type}`;
    alert.style.position = 'fixed';
    alert.style.top = '20px';
    alert.style.right = '20px';
    alert.style.padding = '15px 20px';
    alert.style.borderRadius = '5px';
    alert.style.backgroundColor = type === 'success' ? '#d4edda' : '#f8d7da';
    alert.style.color = type === 'success' ? '#155724' : '#721c24';
    alert.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
    alert.style.zIndex = '1000';
    alert.style.opacity = '0';
    alert.style.transform = 'translateY(-20px)';
    alert.style.transition = 'opacity 0.3s, transform 0.3s';
    
    alert.innerHTML = `
      <span>${message}</span>
      <button onclick="this.parentElement.remove()" style="
        background: none;
        border: none;
        color: ${type === 'success' ? '#155724' : '#721c24'};
        cursor: pointer;
        margin-left: 15px;
        font-weight: bold;
      ">×</button>
    `;
    
    document.body.appendChild(alert);
    
   
    setTimeout(() => {
      alert.style.opacity = '1';
      alert.style.transform = 'translateY(0)';
    }, 10);
    
    
    setTimeout(() => {
      alert.style.opacity = '0';
      setTimeout(() => alert.remove(), 300);
    }, 5000);
  }
</script>


<script>
  function ajouter(id_macaron) {
    if (!id_macaron) return;
    
    const formData = new FormData();
    formData.append('action', 'ajouter');
    formData.append('id_macaron', id_macaron);
    
    fetch('macaron_actions.php', {
      method: 'POST',
      body: formData
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('Erreur réseau: ' + response.status);
      }
      return response.json();
    })
    .then(data => {
      if (data.status === 'success') {
       
        const row = document.querySelector(`tr[data-id="${id_macaron}"]`);
        if (row) {
          const stockCell = row.querySelector('td:nth-child(6)');
          stockCell.textContent = data.new_quantity;
          
         
          stockCell.style.backgroundColor = '#d4edda';
          setTimeout(() => {
            stockCell.style.backgroundColor = '';
          }, 1000);
        }
        
        
        showAlert('Stock incrémenté avec succès !', 'success');
      } else {
        showAlert('Erreur: ' + data.message, 'error');
      }
    })
    .catch(error => {
      console.error('Erreur:', error);
      showAlert('Une erreur est survenue. Veuillez réessayer.', 'error');
    });
  }

  

  function supprimer(id_macaron) {
    if (!id_macaron) return;
    
    if (confirm("Voulez-vous vraiment supprimer ce macaron ? Cette action est irréversible.")) {
     
      const row = document.querySelector(`tr[data-id="${id_macaron}"]`);
      if (row) {
        row.style.transition = 'all 0.3s ease';
        row.style.opacity = '0.5';
      }
      
      fetch('macaron_actions.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'action=supprimer&id_macaron=' + id_macaron
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Erreur réseau: ' + response.status);
        }
        return response.json();
      })
      .then(data => {
        if (data.status === 'success') {
          if (row) {
            row.style.height = '0';
            row.style.overflow = 'hidden';
            setTimeout(() => row.remove(), 300);
          }
          showAlert("Macaron supprimé avec succès !", 'success');
        } else {
          // Rétablir l'affichage en cas d'erreur
          if (row) row.style.opacity = '1';
          throw new Error(data.message);
        }
      })
      .catch(error => {
        console.error('Erreur:', error);
        showAlert("Erreur de suppression: " + error.message, 'error');
      });
    }
  }

  function modifier(id_macaron) {
    if (!id_macaron) return;
    window.location.href = `modifier_macaron.php?id=${id_macaron}`;
  }
</script>


<script>
  function modifierBoite(id) {
    if (!id) return;
    window.location.href = `modifier_boite.php?id=${id}`;
  }

  function supprimerBoite(id) {
    if (!id) return;
    
    if (confirm("Voulez-vous vraiment supprimer cette boîte ? Cette action est irréversible.")) {
      
      const row = document.querySelector(`tr[data-id="${id}"]`);
      if (row) {
        row.style.transition = 'all 0.3s ease';
        row.style.opacity = '0.5';
      }
      
      fetch('supprimer_element.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: `id=${id}&type=boite`
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Erreur réseau: ' + response.status);
        }
        return response.json();
      })
      .then(data => {
        if (data.status === 'success') {
          if (row) {
            row.style.height = '0';
            row.style.overflow = 'hidden';
            setTimeout(() => {
              row.remove();
              
              
              if (!document.querySelector('#tableau-boites tbody tr')) {
                document.getElementById('tableau-boites').innerHTML = 
                  '<p style="text-align: center; font-size: 20px; margin-top: 20px;">Aucune boîte disponible.</p>';
              }
            }, 300);
          }
          showAlert(data.message || "Boîte supprimée avec succès", 'success');
        } else {
          
          if (row) row.style.opacity = '1';
          throw new Error(data.message);
        }
      })
      .catch(error => {
        console.error('Erreur:', error);
        showAlert("Erreur de suppression: " + error.message, 'error');
      });
    }
  }

  function addNewBoxToTable(data) {
    let tableBody = document.querySelector('#tableau-boites tbody');
    const tableContainer = document.getElementById('tableau-boites');
    
   
    if (!tableBody) {
      tableContainer.innerHTML = `
        <table border="1" cellspacing="0" cellpadding="20" style="border-color:rgb(221, 164, 89);border-radius:20px; width:100%; border-collapse:collapse; text-align:center; margin-top:20px;">
          <thead style="background-color:rgb(253, 225, 198); color:rgb(177, 110, 52);">
            <tr>
              <th>Image</th>
              <th>Nom</th>
              <th>Capacité</th>
              <th>Prix</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      `;
      tableBody = document.querySelector('#tableau-boites tbody');
      
     
      const noItemMsg = tableContainer.querySelector('p');
      if (noItemMsg) noItemMsg.remove();
    }
    
   
    const newRow = document.createElement('tr');
    newRow.setAttribute('data-id', data.id);
    newRow.innerHTML = `
      <td><img src="${data.image_url}" alt="boite" width="170" height="150" style="border-radius:10px;"></td>
      <td>${data.nom}</td>
      <td>${data.capacite} macarons</td>
      <td>${parseFloat(data.prix).toFixed(2)} €</td>
      <td>
        <button onclick="modifierBoite(${data.id})" title="Modifier">
          <i class="fas fa-pen" style="color:rgb(190, 101, 207); font-size:18px;"></i>
        </button>
        <button onclick="supprimerBoite(${data.id})" title="Supprimer">
          <i class="fas fa-trash-alt" style="color:red; font-size:18px;"></i>
        </button>
      </td>
    `;
    tableBody.appendChild(newRow);
  }
</script>


<script>
  function supprimerCouverture(id) {
    if (!id || isNaN(id)) {
      showAlert("ID d'illustration invalide", 'error');
      return;
    }

    if (!confirm("Voulez-vous vraiment supprimer cette illustration ? Cette action est irréversible.")) {
      return;
    }

  
    const row = document.querySelector(`tr[data-id="${id}"]`);
    if (row) {
      row.style.transition = 'all 0.3s ease';
      row.style.opacity = '0';
      row.style.height = row.offsetHeight + 'px';
      
      setTimeout(() => {
        row.style.height = '0';
        row.style.padding = '0';
        row.style.margin = '0';
        row.style.border = 'none';
      }, 300);
    }

    
    fetch('supprimer_element.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: `id=${encodeURIComponent(id)}&type=couverture`
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('Erreur réseau');
      }
      return response.json();
    })
    .then(data => {
      if (data.status === 'success') {
        
        setTimeout(() => {
          if (row) row.remove();
          
         
          if (!document.querySelector('#tableau-couvertures tbody tr')) {
            document.getElementById('tableau-couvertures').innerHTML = 
              '<p style="text-align: center; font-size: 20px; margin-top: 20px;">Aucune illustration disponible.</p>';
          }
          
          showAlert(data.message || "Illustration supprimée avec succès", 'success');
        }, 500);
      } else {
        
        if (row) {
          row.style.opacity = '1';
          row.style.height = 'auto';
          row.style.padding = '';
          row.style.margin = '';
          row.style.border = '';
        }
        throw new Error(data.message || "Erreur lors de la suppression");
      }
    })
    .catch(error => {
      showAlert(error.message || "Une erreur est survenue lors de la suppression", 'error');
      console.error("Erreur suppression:", error);
    });
  }

  function addNewCoverToTable(data) {
    let tableBody = document.querySelector('#tableau-couvertures tbody');
    const tableContainer = document.getElementById('tableau-couvertures');
    
  
    if (!tableBody) {
      tableContainer.innerHTML = `
        <table border="1" cellspacing="0" cellpadding="20" style="border-color:rgb(221, 164, 89);border-radius:20px; width:100%; border-collapse:collapse; text-align:center; margin-top:20px;">
          <thead style="background-color:rgb(253, 225, 198); color:rgb(177, 110, 52);">
            <tr>
              <th>Image</th>
              <th>Nom</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      `;
      tableBody = document.querySelector('#tableau-couvertures tbody');
      
      
      const noItemMsg = tableContainer.querySelector('p');
      if (noItemMsg) noItemMsg.remove();
    }
    
    
    const newRow = document.createElement('tr');
    newRow.setAttribute('data-id', data.id);
    newRow.innerHTML = `
      <td><img src="${data.image_url}" alt="couverture" width="170" height="150" style="border-radius:10px;"></td>
      <td>${data.nom}</td>
      <td>
        <button onclick="supprimerCouverture(${data.id})" title="Supprimer">
          <i class="fas fa-trash-alt" style="color:red; font-size:18px;"></i>
        </button>
      </td>
    `;
    tableBody.appendChild(newRow);
  }
</script>

<script>
  
  document.addEventListener('DOMContentLoaded', function() {
   
    const formBoite = document.getElementById('form-boite');
    if (formBoite) {
      formBoite.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('type_produit', 'boite');
        
       
        const fileInput = this.querySelector('input[type="file"]');
        if (!fileInput.files || fileInput.files.length === 0) {
          showAlert("Veuillez sélectionner une image", "error");
          return;
        }
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';
        submitBtn.disabled = true;
        
        fetch('ajouter_boite.php', {
          method: 'POST',
          body: formData,
          credentials: 'same-origin'
        })
        .then(response => parseResponse(response))
        .then(data => {
          if (data && data.status === 'success') {
            showAlert(data.message || "Boîte ajoutée avec succès", 'success');
            addNewBoxToTable(data);
            this.reset();
            
            const previewContainer = this.querySelector('.image-preview');
            if (previewContainer) {
              previewContainer.innerHTML = '';
            }
          } else {
            throw new Error(data && data.message ? data.message : "Une erreur est survenue");
          }
        })
        .catch(error => {
          console.error('Erreur:', error);
          showAlert(error.message || "Une erreur de connexion est survenue. Veuillez réessayer.", 'error');
        })
        .finally(() => {
          submitBtn.innerHTML = originalText;
          submitBtn.disabled = false;
        });
      });
    }
    
   
    const formCouverture = document.getElementById('form-couverture');
    if (formCouverture) {
      formCouverture.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('type_produit', 'couverture');
        
       
        const fileInput = this.querySelector('input[type="file"]');
        if (!fileInput.files || fileInput.files.length === 0) {
          showAlert("Veuillez sélectionner une image", "error");
          return;
        }
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';
        submitBtn.disabled = true;
        
        fetch('ajouter_boite.php', {
          method: 'POST',
          body: formData,
          credentials: 'same-origin'
        })
        .then(response => parseResponse(response))
        .then(data => {
          if (data && data.status === 'success') {
            showAlert(data.message || "Illustration ajoutée avec succès", 'success');
            addNewCoverToTable(data);
            this.reset();
            
            const previewContainer = this.querySelector('.image-preview');
            if (previewContainer) {
              previewContainer.innerHTML = '';
            }
          } else {
            throw new Error(data && data.message ? data.message : "Une erreur est survenue");
          }
        })
        .catch(error => {
          console.error('Erreur:', error);
          showAlert(error.message || "Une erreur de connexion est survenue. Veuillez réessayer.", 'error');
        })
        .finally(() => {
          submitBtn.innerHTML = originalText;
          submitBtn.disabled = false;
        });
      });
    }
    
    
    initializeImagePreviews();
  });

 
  function parseResponse(response) {
    if (!response.ok) {
      throw new Error('Erreur réseau: ' + response.status + ' ' + response.statusText);
    }
    
    const contentType = response.headers.get('content-type');
    if (contentType && contentType.includes('application/json')) {
      return response.json();
    } else {
      return response.text().then(text => {
        try {
          return JSON.parse(text);
        } catch (e) {
          console.error('Réponse non-JSON:', text);
          throw new Error('Réponse serveur invalide. Veuillez réessayer.');
        }
      });
    }
  }

  
  function initializeImagePreviews() {
    document.querySelectorAll('input[type="file"]').forEach((input, index) => {
      const uniqueId = 'image-upload-' + index;
      const label = input.previousElementSibling;
      if (label && label.tagName === 'LABEL') {
        label.setAttribute('for', uniqueId);
      }
      input.id = uniqueId;
      
      input.addEventListener('change', function() {
        let previewContainer = this.parentElement.querySelector('.image-preview');
        if (!previewContainer) {
          previewContainer = document.createElement('div');
          previewContainer.className = 'image-preview';
          previewContainer.style.marginTop = '10px';
          this.parentElement.appendChild(previewContainer);
        }
        
        if (this.files && this.files[0]) {
          const reader = new FileReader();
          reader.onload = function(e) {
            previewContainer.innerHTML = `
              <img src="${e.target.result}" style="max-width: 200px; max-height: 200px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
              <p style="margin-top: 5px; font-size: 14px;">Image sélectionnée</p>
            `;
          }
          reader.readAsDataURL(this.files[0]);
        } else {
          previewContainer.innerHTML = '';
        }
      });
    });
  }
</script>


<style>
 #macarons tr{
  width:100px;
  font-size: 25px;
 }
 .section tbody td button{
  font-size: 25px;
  background: none;
  border: none;
  padding: 5px;
  cursor: pointer;
 }

 .formulaire form label{
    font-size: 25px;
    color: #bb6f81;
 } 
 .formulaire form textarea,input{
  border-radius: 30px;
  border-color:1px solid rgb(209, 154, 167);
 }
 input[type="radio"].custom-radio {
  width: 20px;
  height: 20px;
  accent-color: #c08080; /* couleur pastel personnalisée */
  cursor: pointer;
 }







  * {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Segoe UI', sans-serif;
}

body {
  display: flex;
  height: 100vh;
  background-color: #fff6f0;
}


.sidebar {
  width: 25%;
  background-color: rgb(252, 234, 200);
  color: #6b4c4c;
  padding: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.sidebar img {
  background: url('perso_logo/mon_logo.png') no-repeat center/cover;
  background-size: 280px auto;
  background-color: rgb(253, 228, 181);
  width: 400px;
  height: 400px;
  border-radius: 50%;
  margin-bottom: 20px;
  border: 3px solid rgb(204, 148, 44);
}

.menu {
  margin-top: 50px;
}

.menu a {
  color: rgb(88, 45, 45);
  text-decoration: none;
  display: block;
  padding: 20px 100px;
  margin: 10px 0;
  font-size: 25px;
  border-radius: 10px;
  background-color: rgb(252, 220, 160);
  width: 100%;
  text-align: center;
  transition: background-color 0.3s;
}

.menu a:hover {
  background-color: #f1c6c6;
}

.back-arrow {
  margin-top: 40px;
  text-align: center;
}

.back-arrow a {
  font-size: 20px;
  text-decoration: none;
  color: #944d60;
  transition: color 0.3s;
}

.back-arrow a:hover {
  color: #bb6f81;
}

/* Main content (70%) */
.main-content {
  width: 70%;
  padding: 30px;
  overflow-y: auto;
  background-color: #fffaf8;
}

h2 {
  margin-bottom: 15px;
  color: #944d60;
}

p {
  margin-bottom: 10px;
  color: #5f3b3b;
}

.section {
  display: none;
}

.section.active {
  display: block;
}

.image {
  width: 100%;
  height: 400px;
  display: flex;
  justify-content: center;
  align-items: center;
  overflow: hidden;
  background: url('page_users/imadmin.jpg') no-repeat center/cover;
}

.image h2 {
  text-align: center;
  font-size: 70px;
  margin-top: 60px;
  color: white;
}

 .intro-text {
  font-size: 35px;
  color: #5f3b3b;
  text-align: center;
  margin-top: 100px;
 }

 .quote {
  font-style: italic;
  color: #a86464;
  margin-top: 40px;
  text-align: center;
  font-size: 30px;
}

</style>


</body>
</html>
