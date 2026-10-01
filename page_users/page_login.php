<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli('localhost', 'root', '', 'noracammacarons', 3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom_utilisateur = $_POST["nom_utilisateur"];
    $mot_de_passe = $_POST["mot_de_passe"];

    
    $stmt = $conn->prepare("SELECT id_user, mot_de_passe, role FROM Utilisateur WHERE nom_utilisateur = ?");
    $stmt->bind_param("s", $nom_utilisateur);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id_user, $db_mot_de_passe, $role);
        $stmt->fetch();

      
if ($mot_de_passe === $db_mot_de_passe) {
    
    $_SESSION["id_user"] = $id_user;
    $_SESSION["role"] = $role;
    $_SESSION["nom_utilisateur"] = $nom_utilisateur;


    
    if ($nom_utilisateur === "myriam.cheriguene") {
        header("Location: ../admin.php");
    } else {
        header("Location: ../moncompte.php");
    }
    exit;
}
 else {
            $error = "Mot de passe incorrect.";
        }
    } else {
        $error = "Nom d'utilisateur introuvable.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Modal</title>
    <link rel="stylesheet" href="page_login.css">
</head>
<body>

<div id="modal" class="modal">
    <div class="modal-content">

        <div class="connecter">
            <div class="form">
                <h2>S'identifier...</h2>
                <form action="#" method="post">  
                    <div class="inputbox">
                        <input type="text" name="nom_utilisateur" placeholder="Nom d'utilisateur" required>
                    </div>
                    <div class="inputbox">
                        <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
                    </div>
                    <div class="inputbox">
                        <input type="submit" value="Connexion">
                    </div>
                    <?php if ($error): ?>
                        <div class="error-message" style="color:rgb(211, 70, 112); font-size: 20px; font-weight: bold; margin-top: 10px;">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                </form>
                <p class="signup-text">
                    Pas de compte ? <a href="page_inscription.php">Inscrivez-vous</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    window.onload = function() {
        document.getElementById('modal').style.display = 'block';
    };
</script>

</body>
</html>
