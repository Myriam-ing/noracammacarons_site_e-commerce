<?php
session_start();
$destination = "page_login.php";

if (isset($_SESSION['id_user'])) {
    if (isset($_SESSION['nom_utilisateur']) && $_SESSION['nom_utilisateur'] === "myriam.cheriguene") {
        $destination = "admin.php";
    } else {
        $destination = "moncompte.php";
    }
}
?>


 <header>
        <a href="#">
            <img src="icones/menu.png" alt="menu"  id="menu">
        </a>
   
        <ul class="first">
            <li><a href="test.php">Home</a></li>
            <li><a href="test.php#aboutus">About us</a></li>
            <li><a href="test.php#products">Favoris</a></li>
            
        </ul>
   
        <img src="perso_logo/logo_name.png" alt="logo" id="logo">
       
        <ul class="second">
            <li><a href="test.php#boites_illustration">Boites&couvertures</a></li>
            <li><a href="test.php#produits">Produits</a></li>
            <li><a href="footer.php#contact">Contact us</a></li>
        </ul>
        
        <ul class="icones">
            <li> <a href="panier.php">
                <img src="icones/panier.png" alt="panier"  id="panier">
            </a>
            </li>
            <li>
    <a href="<?php echo $destination; ?>">
        <img src="icones/personnes.png" alt="utilisateur" id="user">
    </a>
</li>
        </ul>
        
 </header>
<style>

  header{
    background-color: rgba(255, 255,255,0.1);
    position: fixed;
    width: 100%;
    top: 0;
    right: 0;
    backdrop-filter: blur(40px);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: 0.2s;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
}
  .first{
    display: flex;
}
.first a{
    font-size: 2.3rem;
    padding: 10px 80px;
    color: black;
    font-weight: 500;
    text-decoration: none;
    display: inline-block; 
   
}
header .first a:hover {
    color: #da974b; 
}
.second{
    display: flex;
}
.second a{
    font-size: 2.3rem;
    padding: 10px 80px;
    color: black;
    font-weight: 500;
    text-decoration: none;
    display: inline-block; 
}
header .second a:hover {
    color: #da974b;
}


ul{
    list-style: none;
    padding: 0;
}
.icones{
    display: flex;  
}
.icones a{
    padding: 10px 10px;
}


#logo{  
    width: 180px;
    z-index: 500;margin-top: 5px;
}
#menu{
    margin-left: 10px;
    width: 40px;
}
#panier{
    width: 40px; 
}

#user{
    width: 40px;

}
</style>