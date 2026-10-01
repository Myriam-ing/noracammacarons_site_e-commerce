<?php
session_start();


$conn = new mysqli('localhost', 'root', '', 'noracammacarons', 3307);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$boites = $conn->query("SELECT * FROM boite")->fetch_all(MYSQLI_ASSOC);
$couvertures = $conn->query("SELECT * FROM couverture")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noracam macarons</title>
    <link rel="icon" type="image/png" href="perso_logo/mon_logo.png">
    <link rel="stylesheet" href="test.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
</head>

<body>
<style>
    /*presentation de la page*/
.container{
    display: flex;
    justify-content: space-between;
    align-items: center;
    height:1300px;
    
}

.container .text h1 span{
    font-size: 130px;
    color: #db8a2d;
    font-weight: 100px;
    font-family: "Gill Sans", sans-serif;

}
.container .text h3{
    font-size: 40px;
    color:black;
    font-weight: 13px;
    margin-left: 0%;
   
}

#cercle{
    margin-top: 166px;
    width: 1200px;
    height: 1143px;
    position: relative;
    
}

.container .login{
    font-size: 35px;
    font-weight: 15px;
    transition: 0.2s linear;
    font-family: Brush Script MT, Brush Script Std, cursive;
    width: 270px;
    height: 60px;
    border-radius: 25px;
    background: #ffffffc2;
    color: #da974b;
    border-color: #da974bc9;
    cursor: pointer;
}
.login:hover{
    color:  #be1246;
    background: rgba(255, 255, 255, 0.658);
    backdrop-filter: blur(30px);
    
}
#macarons{
    margin-top: 20px;
    width: 900px;
}
</style>
<?php include('header.php'); ?>

    
 <section class="container">
        <div class="image">
            <img src="perso_logo/office3.jfif" alt="cercle" id="cercle">
            
        </div>
    
        <div class="text" >
           <h1><span>NORACAM <br>macarons</span></h1>
           <h3>Noracam vous invite dans l'univers des macarons gourmands ! <br>
            Découvrez des créations uniques, alliant douceur et originalité</h3> 
          
           <a href="page_users/page_login.php"><button class="login"> SHOP NOW</button> </a>
           <br> <br> <br>
           <img src="perso_logo/Macaron_Row.png" alt="macarons" id="macarons">
       </div>
       <br> 
 </section>



<section class="aboutus" id="aboutus">
    <div class="image-grid">
        <div class="image-item"> <img src="perso_logo/choc_blanc.png" alt="chocolat" id="chocolat"></div>
        <div class="image-item"> <img src="perso_logo/choc_blanc.png" alt="chocolat" id="chocolat"></div>
        <div class="image-item"> <img src="perso_logo/choc_blanc.png" alt="chocolat" id="chocolat"></div>
        <div class="image-item"> <img src="perso_logo/choc_blanc.png" alt="chocolat" id="chocolat"></div>
        <div class="image-item"> <img src="perso_logo/choc_blanc.png" alt="chocolat" id="chocolat"></div>
        <div class="image-item"> <img src="perso_logo/choc_blanc.png" alt="chocolat" id="chocolat"></div>
    </div>
    <div class="classes">
    <div class="information">
        <div class="plume"><h5>About Us</h5>
            <img src="perso_logo/plume.png" alt="plume" id="plume">
        </div>
        <p>
            <strong> Chez Noracam</strong>, la passion pour la pâtisserie guide chacune de nos créations.
            Depuis trois ans, nous perfectionnons notre savoir-faire pour offrir 
            des macarons faits maison, alliant tradition et innovation.
        </p>
    
        <p>
            Chaque macaron est confectionné avec soin à partir d'ingrédients de qualité, 
            minutieusement sélectionnés pour garantir des saveurs uniques et inoubliables.
            Notre équipe, choisie avec attention pour son talent et son engagement, 
            travaille chaque jour pour vous proposer des créations aussi élégantes que savoureuses.
        </p>
    
        <h3>Pourquoi choisir Noracam ?</h3>
        <ul>
            <li id="enum"><strong>Artisanat et qualité :</strong> Chaque macaron est fait à la main avec passion.</li>
            <li id="enum"><strong>Saveurs uniques :</strong> Des recettes originales qui éveillent les papilles.</li>
            <li id="enum"><strong>Engagement et excellence :</strong> Une expérience gustative exceptionnelle.</li>
        </ul>
    </div>
    <img src="perso_logo/femme_panier.png" alt="femme_panier" id="femme_panier">
    </div>
</section>


<section class="products" id="products">
    <h1 class="heading">Nos Macarons Favoris</h1>
   
    <div class="gallery">
            <div class="premiere">
                <div class="mama">
                    <img src="mac/citron/pumpkin.png" alt="Image 1" id="image1">
                <div class="content">
                    <h3 id="titre_mac">Parfum pumpkin</h3>
                    <p>Un macaron aux saveurs douces et chaleureuses, offrant une ganache crémeuse à la citrouille, idéale pour une touche automnale.</p>
                </div></div>
               
                <div class="mama">
                    <img src="mac/rouge/fraise.png" alt="Image 2" id="image1">
                    <div class="content">
                        <h3 id="titre_mac">Parfum Fraise</h3>
                        <p>Un délice fruité avec une ganache onctueuse à la fraise, offrant une douceur sucrée et acidulée à chaque bouchée.</p>
                    </div></div>
                
                <div class="mama">
                    <img src="mac/choco/vanille.png" alt="Image 3" id="image1">
                    <div class="content">
                        <h3 id="titre_mac">Parfum Vanille</h3>
                        <p>Un classique délicat et parfumé, aux arômes doux et enveloppants</p>
                    </div></div>
                
                <div class="mama">
                    <img src="mac/choco/chocolat.png" alt="Image 4" id="image1">
                    <div class="content">
                        <h3 id="titre_mac">Parfum chocolat</h3>
                        <p>Un macaron au goût intense et profond, sublimé par des notes cacaotées gourmandes.</p>
                    </div></div>
            </div>
            
            <div class="deuxieme">
                <div class="mama">
                    <img src="mac/rouge/framboise.png" alt="Image 5" id="image1">
                    <div class="content">
                        <h3 id="titre_mac">Parfum Framboise</h3>
                        <p>Un équilibre parfait entre l'acidité et la douceur, sublimé par une crème fondante à la framboise.</p>
                    </div>
                </div>

                <div class="mama">
                    <img src="mac/citron/citron.png" alt="Image 6" id="image1">
                    <div class="content">
                        <h3 id="titre_mac">Parfum Citron</h3>
                        <p>Un macaron vibrant, alliant la fraîcheur acidulée du citron à une ganache crémeuse, pour une explosion de zeste et de douceur</p>
                    </div>
                </div>

                <div class="mama">
                    <img src="mac/choco/noisette.png" alt="Image 7" id="image1">
                    <div class="content">
                        <h3 id="titre_mac">Parfum Noisette</h3>
                        <p>un macaron révèlant un cœur fondant à la ganache pralinée, une douceur irrésistible qui évoque la chaleur des noisettes grillées.</p>
                    </div>
                </div>

                <div class="mama">
                    <img src="mac/rouge/parfum_fleur.png" alt="Image 8" id="image1">
                    <div class="content">
                        <h3 id="titre_mac">Parfum Fleur</h3>
                        <p>Une touche florale raffinée, infusée aux arômes délicats de fleurs, pour une expérience gustative élégante et parfumée</p>
                    </div>
                </div>
            </div>
    </div>
</section>



<!-- SECTION BOITES & ILLUSTRATIONS - Séparée clairement -->
<!-- Insérez ce code à la place de votre section boites_illustration -->
<section class="boites_illustration" id="boites_illustration">
    <h1 class="heading_boites_illu">Boites & Illustrations</h1>
    
    <div class="boites">
        <h2 class="heading_boites">Nos Boites</h2>
        <p>Afin de passer votre commande, veuillez choisir la boîte correspondant au nombre de macarons que vous souhaitez</p>

        <?php if (!empty($boites)): ?>
            <div class="swiper boites_slider">
                <div class="swiper-wrapper">
                    <?php foreach ($boites as $boite): ?>
                        <div class="swiper-slide slide boite-option" 
                             data-id="<?= $boite['id_boite'] ?>" 
                             data-capacite="<?= $boite['capacite'] ?>"
                             data-nom="<?= htmlspecialchars($boite['nom']) ?>">
                            <img src="<?= htmlspecialchars($boite['image_url']) ?>">
                            <div class="content">
                                <h3><?= htmlspecialchars($boite['nom']) ?></h3>
                                <p><?= htmlspecialchars($boite['prix']) ?> DZD</p>
                                <button class="btn-choisir">Choisir</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        <?php else: ?>
            <p>Aucune boîte disponible.</p>
        <?php endif; ?>
    </div>

    <!-- Partie illustrations -->
    <div class="illustration">
        <h2 class="heading_illustration">Nos illustrations</h2>
        <p>Préférez-vous ajouter une couverture qui correspond à votre style parmi nos superbes choix ? <br>
        Laissez-vous séduire par celle qui vous inspire le plus pour offrir une touche unique à votre commande !</p>
        
        <?php if (!empty($couvertures)): ?>
            <div class="swiper illustration_slider">
                <div class="swiper-wrapper">
                    <?php foreach ($couvertures as $couverture): ?>
                        <div class="swiper-slide slide">
                            <img src="<?= htmlspecialchars($couverture['image_url']) ?>" >
                            <div class="content">
                                <h3><?= htmlspecialchars($couverture['nom']) ?></h3>
                                <button class="btn-choisir-couverture" 
                                        data-id="<?= $couverture['id_couverture'] ?>"
                                        data-nom="<?= htmlspecialchars($couverture['nom']) ?>">
                                    Choisir
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        <?php else: ?>
            <p>Aucune illustration disponible.</p>
        <?php endif; ?>
    </div>
    
    <!-- Conteneur de sélection unique et bien placé -->
    <div id="selection-container" style="display:none; margin:30px auto; max-width:600px; text-align:center; padding:20px; background:#f8f9fa; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
        <p>Boîte sélectionnée : <span id="selected-box-name">Aucune</span> (<span id="selected-box-capacity">0</span> macarons)</p>
        <p>Couverture sélectionnée : <span id="selected-cover-name">Aucune</span></p>
        <a href="#" id="btn-macarons" class="btn-valider">Valider ma commande</a>
    </div>
</section>





<!-- SECTION NOS PRODUITS - Bien séparée de la section précédente -->
<section class="produits" id="produits">
    <h1>Nos Produits</h1>
    <p>Pour finaliser votre commande, il vous suffit de choisir les parfums qui vous plaisent parmi les différentes catégories.
         Vous avez la possibilité de sélectionner des macarons des trois catégories simultanément.
        Faites-vous plaisir et composez votre sélection idéale, vous allez adorer !</p>
    
    <div class="fruits_rouges">
        <div class="text">
            <h3>Macarons aux Fruits Rouges</h3>
            <p>Plongez dans un tourbillon de saveurs avec notre sélection de macarons aux fruits rouges ! 
                Chaque bouchée dévoile un équilibre parfait entre douceur et acidité, 
                mettant à l'honneur des garnitures onctueuses et fruitées. 
                De la fraîcheur éclatante de la fraise à l'intensité boisée du cassis, 
                en passant par la gourmandise acidulée de la framboise, de la cerise et de la myrtille,
                 ces macarons offrent une explosion de saveurs irrésistibles. Idéal pour les amateurs
                  de fruits rouges en quête d'une pause gourmande et raffinée !</p>
            <a href="macaron_rouge.php">
                <button class="bouton">passer commande</button>
            </a>
        </div>
        <div class="image">
            <a href="macaron_rouge.php"><img src="mac/fruits_rouges.jpg" alt=""></a>
        </div>
    </div>

    <div class="mac_agrumes">
        <div class="image">
            <a href="macaron_citron.php"><img src="mac/mac_agrumes.jpg" alt=""></a>
        </div>
        <div class="text">
            <h3>Macarons Fruités & Exotiques </h3>
            <p>Laissez-vous emporter par un voyage gustatif aux saveurs fraîches et envoûtantes avec notre
                 sélection de macarons fruités et exotiques. Chaque bouchée vous 
                 plonge dans un monde de douceurs parfumées, où la fraîcheur du citron se mêle à la douceur 
                 de la banane, tandis que l'orange à la fleur d'oranger apporte une touche florale délicate. 
                 tandis que le pamplemousse réveille vos papilles avec une touche légèrement acidulée.
                 Une véritable explosion de saveurs exotiques pour un moment de plaisir unique !</p>
            <a href="macaron_citron.php">
                <button class="bouton">passer commande</button>
            </a>
        </div>
    </div>

    <div class="mac_chocolat">
        <div class="text">
            <h3>Macarons Chocolat & Notes Boisées</h3>
            <p>Découvrez nos macarons aux saveurs riches et réconfortantes : Chocolat intense et cacaoté, 
                Caramel beurre salé fondant et gourmand, Vanille douce et parfumée, Oreo croquant et crémeux,
                 Réglisse aux notes boisées et anisées, et Café corsé et aromatique. 
                Une explosion de plaisir en bouche !</p>
            <a href="macaron_choco.php">
                <button class="bouton">passer commande</button>
            </a>
        </div>
        <div class="image">
            <a href="macaron_choco.php"><img src="mac/mac_chocolat.jpg" alt=""></a>
        </div>
    </div>
    
</section>

<!-- Footer -->
<?php include('footer.php'); ?>
     
<!-- Scripts Swiper -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedBox = null;
    
    // Gestion du clic sur "Choisir"
    document.querySelectorAll('.btn-choisir').forEach(btn => {
        btn.addEventListener('click', function() {
            const boxElement = this.closest('.boite-option');
            selectedBox = {
                id: boxElement.dataset.id,
                nom: boxElement.dataset.nom,
                capacite: boxElement.dataset.capacite
            };
            
            // Stocker en sessionStorage
            sessionStorage.setItem('selectedBox', JSON.stringify(selectedBox));
            
            // Afficher la sélection
            document.getElementById('selected-box-name').textContent = selectedBox.nom;
            document.getElementById('selected-box-capacity').textContent = selectedBox.capacite;
            document.getElementById('selection-container').style.display = 'block';
            
            // Mettre en évidence la boîte sélectionnée
            document.querySelectorAll('.boite-option').forEach(box => {
                box.classList.remove('selected');
            });
            boxElement.classList.add('selected');
        });
    });
    
    // Vérifier si une boîte est déjà sélectionnée au chargement
    const savedSelection = sessionStorage.getItem('selectedBox');
    if (savedSelection) {
        selectedBox = JSON.parse(savedSelection);
        document.getElementById('selected-box-name').textContent = selectedBox.nom;
        document.getElementById('selected-box-capacity').textContent = selectedBox.capacite;
        document.getElementById('selection-container').style.display = 'block';
        
        // Mettre en évidence la boîte sélectionnée
        document.querySelectorAll('.boite-option').forEach(box => {
            if (box.dataset.id === selectedBox.id) {
                box.classList.add('selected');
            }
        });
    }
});
</script>

<style>
.boite-option.selected {
    border: 2px solid #4CAF50;
    background-color: #f8f9fa;
}

.btn-valider {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    margin-top: 10px;
}

.btn-valider:hover {
    background-color: #45a049;
}
</style>


<script>
    var swiper = new Swiper(".boites_slider", { 
        loop: true,
        spaceBetween: 20,
        grabCursor: true,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        breakpoints: {
            450: {
                slidesPerView: 1,
            },
            768: {
                slidesPerView: 2,
            },
            1000: {
                slidesPerView: 3,
            },
            1200: { 
                slidesPerView: 4,
            },
        },
    });

    var swiper = new Swiper(".illustration_slider", { 
        loop: true,
        spaceBetween: 20,
        grabCursor: true,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        breakpoints: {
            450: {
                slidesPerView: 1,
            },
            768: {
                slidesPerView: 2,
            },
            1000: {
                slidesPerView: 3,
            },
            1200: { 
                slidesPerView: 4,
            },
            1400: { 
                slidesPerView: 5,
            },
            1600: { 
                slidesPerView: 6,
            },
        },
    });
</script>
// Ajoutez ce code juste avant la fermeture de votre balise </body>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script de débogage chargé');
    
    // Fonction pour les journaux détaillés
    function log(message, data) {
        console.log('DEBUG - ' + message);
        if (data) console.log(data);
    }
    
    let selectedBoite = null;
    let selectedCouverture = null;
    
    // Vérifier les éléments critiques
    if (!document.getElementById('selection-container')) {
        console.error('ERREUR: selection-container non trouvé!');
    }
    
    // Débogage des clics sur les boutons de boîte
    document.querySelectorAll('.btn-choisir').forEach(function(button) {
        log('Détection bouton boîte:', button);
        button.addEventListener('click', function() {
            log('Clic sur bouton boîte');
            
            const parent = this.closest('.boite-option');
            log('Parent trouvé:', parent);
            
            // Récupérer les attributs de données
            const id = parent.getAttribute('data-id');
            const nom = parent.getAttribute('data-nom');
            const capacite = parent.getAttribute('data-capacite');
            const prix = parent.querySelector('.content p').textContent;
            
            log('Données récupérées:', { id, nom, capacite, prix });
            
            selectedBoite = {
                id: id,
                nom: nom,
                capacite: capacite,
                prix: parseFloat(prix)
            };
            
            log('Boîte sélectionnée:', selectedBoite);
            
            // Mise à jour de l'affichage
            const boxNameEl = document.getElementById('selected-box-name');
            if (boxNameEl) {
                boxNameEl.textContent = selectedBoite.nom;
                log('Nom de boîte mis à jour');
            } else {
                console.error('ERREUR: selected-box-name non trouvé!');
            }
            
            const boxCapacityEl = document.getElementById('selected-box-capacity');
            if (boxCapacityEl) {
                boxCapacityEl.textContent = selectedBoite.capacite;
                log('Capacité de boîte mise à jour');
            } else {
                console.error('ERREUR: selected-box-capacity non trouvé!');
            }
            
            // Stockage en session
            sessionStorage.setItem('selectedBox', JSON.stringify(selectedBoite));
            log('Boîte stockée en session');
            
            // Afficher le conteneur
            const containerEl = document.getElementById('selection-container');
            if (containerEl) {
                containerEl.style.display = 'block';
                log('Conteneur affiché');
            } else {
                console.error('ERREUR: selection-container non trouvé!');
            }
        });
    });
    
    // Débogage des clics sur les boutons de couverture
    document.querySelectorAll('.btn-choisir-couverture').forEach(function(button) {
        log('Détection bouton couverture:', button);
        button.addEventListener('click', function() {
            log('Clic sur bouton couverture');
            
            // Récupérer les attributs de données
            const coverID = this.getAttribute('data-id');
            const coverNom = this.getAttribute('data-nom');
            
            log('Données couverture récupérées:', { coverID, coverNom });
            
            selectedCouverture = {
                id: coverID,
                nom: coverNom,
                prix: 10
            };
            
            log('Couverture sélectionnée:', selectedCouverture);
            
            // Mise à jour de l'affichage
            let coverNameEl = document.getElementById('selected-cover-name');
            if (coverNameEl) {
                coverNameEl.textContent = selectedCouverture.nom;
                log('Nom de couverture mis à jour');
            } else {
                console.error('ERREUR: selected-cover-name non trouvé!');
                
                // Créer l'élément s'il n'existe pas
                const containerEl = document.getElementById('selection-container');
                if (containerEl) {
                    const coverInfo = document.createElement('p');
                    coverInfo.innerHTML = 'Couverture sélectionnée : <span id="selected-cover-name">' + selectedCouverture.nom + '</span>';
                    
                    // Insérer après le premier paragraphe
                    const firstP = containerEl.querySelector('p');
                    if (firstP) {
                        firstP.insertAdjacentElement('afterend', coverInfo);
                        log('Élément de couverture créé');
                    } else {
                        containerEl.appendChild(coverInfo);
                        log('Élément de couverture ajouté à la fin');
                    }
                }
            }
            
            // Stockage en session
            sessionStorage.setItem('selectedCouverture', JSON.stringify(selectedCouverture));
            log('Couverture stockée en session');
        });
    });
    
    // Débogage du bouton de validation/soumission
    const btnMacarons = document.getElementById('btn-macarons');
    if (btnMacarons) {
        log('Bouton de validation trouvé');
        btnMacarons.addEventListener('click', function(e) {
            e.preventDefault();
            log('Clic sur bouton de validation');
            
            // Récupérer les données de session si nécessaire
            if (!selectedBoite) {
                const savedBox = sessionStorage.getItem('selectedBox');
                if (savedBox) {
                    selectedBoite = JSON.parse(savedBox);
                    log('Boîte récupérée du stockage:', selectedBoite);
                }
            }
            
            if (!selectedCouverture) {
                const savedCover = sessionStorage.getItem('selectedCouverture');
                if (savedCover) {
                    selectedCouverture = JSON.parse(savedCover);
                    log('Couverture récupérée du stockage:', selectedCouverture);
                }
            }
            
            // Vérifier que tout est sélectionné
            if (!selectedBoite) {
                alert('Veuillez sélectionner une boîte');
                log('ERREUR: Aucune boîte sélectionnée');
                return;
            }
            
            if (!selectedCouverture) {
                alert('Veuillez sélectionner une couverture');
                log('ERREUR: Aucune couverture sélectionnée');
                return;
            }
            
            // Calculer le prix total
            const sousTotal = parseFloat(selectedBoite.prix) + parseFloat(selectedCouverture.prix);
            log('Sous-total calculé:', sousTotal);
            
            // Préparer les données de commande
            const commandeData = {
                adresse_livraison: 'Adresse de test', // À remplacer par une vraie adresse
                total_commande: sousTotal,
                details_commande: [{
                    id_boite: selectedBoite.id,
                    id_couverture: selectedCouverture.id,
                    quantite: 1,
                    sous_total: sousTotal
                }]
            };
            
            log('Données de commande préparées:', commandeData);
            
            // Envoyer la requête AJAX
            log('Envoi de la requête AJAX à traitement_commande.php');
            fetch('traitement_commande.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(commandeData)
            })
            .then(response => {
                log('Réponse reçue, status:', response.status);
                if (!response.ok) {
                    throw new Error('Erreur réseau: ' + response.status);
                }
                return response.text();
            })
            .then(textData => {
                log('Données textuelles reçues:', textData);
                try {
                    return JSON.parse(textData);
                } catch (e) {
                    log('Erreur de parsing JSON:', e);
                    throw new Error('Réponse invalide du serveur');
                }
            })
            .then(data => {
    log('Données JSON parsées:', data);
    if (data.success) {
        alert('Configuration de base enregistrée! Vous pouvez maintenant continuer vos achats.');
        log('Commande réussie:', data);
        // Stocker l'ID de commande pour les étapes suivantes
        sessionStorage.setItem('id_commande', data.id_commande);
        
        // NE PAS rediriger - rester sur la page
        // window.location.href = 'macaron_rouge.php';
        
        // Optionnel : afficher un message de confirmation
        const btnMacarons = document.getElementById('btn-macarons');
        if (btnMacarons) {
            btnMacarons.innerHTML = 'Configuration enregistrée ✓';
            btnMacarons.style.backgroundColor = '#4CAF50';
            btnMacarons.onclick = null; // Désactiver le nouveau clic
        }
    } else {
        alert('Erreur: ' + (data.error || 'Erreur inconnue'));
        log('Erreur de commande:', data);
    }
})
            .catch(error => {
                console.error('Erreur AJAX:', error);
                alert('Erreur technique: ' + error.message);
            });
        });
    } else {
        console.error('ERREUR: btn-macarons non trouvé!');
    }
    
    log('Initialisation du script terminée');
});



</script>
</body>
</html>