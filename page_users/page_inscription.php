<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli('localhost', 'root', '', 'noracammacarons', 3307);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom_utilisateur = $_POST["nom_utilisateur"];
    $email = $_POST["email"];
    $mot_de_passe = $_POST["mot_de_passe"];
    $confirmer = $_POST["confirmer"];
    $adresse = "";
    $tel = "";
    $role = "client";

    $num_carte = $_POST["num_carte"];
    $cvv = $_POST["cvv"];
    $mois = $_POST["mois_expiration"];
    $annee = $_POST["annee_expiration"];
    $nom_titulaire = $_POST["nom_titulaire"];

    if ($mot_de_passe !== $confirmer) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        try {
            // Plus de hachage du mot de passe
            $num_carte_bin = base64_encode($num_carte);
            $cvv_bin = base64_encode($cvv);

            $conn->begin_transaction();

            $stmt = $conn->prepare("INSERT INTO Utilisateur (nom_utilisateur, email, mot_de_passe, adresse, tel, role) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $nom_utilisateur, $email, $mot_de_passe, $adresse, $tel, $role);
            $stmt->execute();
            $id_user = $conn->insert_id;

            $stmt = $conn->prepare("INSERT INTO Paiement (id_user, num_carte, cvv, mois_expiration, annee_expiration, nom_titulaire) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issiis", $id_user, $num_carte_bin, $cvv_bin, $mois, $annee, $nom_titulaire);
            $stmt->execute();

            $conn->commit();
            $success = "Inscription réussie !";
            header("Location: ../test.php");
            exit;
        }catch (mysqli_sql_exception $e) {
            $conn->rollback();
        
            if (!empty($email) && $e->getCode() === 1062 && str_contains($e->getMessage(), "for key 'email'")) {
                $error = "Cet email est déjà utilisé.";
            } else {
                $error = "Erreur lors de l'inscription : " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="page_inscription.css">
</head>
<body>

<div id="inscription" class="inscription">
    <div class="conteneur">
        <h1>INSCRIPTION</h1>

        <?php if ($_SERVER["REQUEST_METHOD"] === "POST" && $error): ?>
    <p style="color:red; font-size=20;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if ($_SERVER["REQUEST_METHOD"] === "POST" && $success): ?>
    <p style="color:green; font-size=20;"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

        <form method="POST" action="">
            <div class="inputs">
                <div class="inputboxes">
                    <input type="text" name="nom_utilisateur" placeholder="Nom d'utilisateur" required>
                </div>
                <div class="inputboxes">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
            </div>

            <div class="input_mdp">
                <div class="inputboxes">
                    <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
                </div>
                <div class="inputboxes">
                    <input type="password" name="confirmer" placeholder="Confirmer" required>
                </div>
            </div>

            <div class="form_group">
                <label>Numéro de Carte</label> <br>
                <input type="text" name="num_carte" placeholder="XXXX XXXX XXXX XXXX" maxlength="16" required>
            </div>

            <div class="form_group">
                <label>Nom du Titulaire</label> <br>
                <input type="text" name="nom_titulaire" placeholder="Nom sur la carte" required>
            </div>

            <div class="form_group_cvv">
                <div class="expiration">
                    <label>Mois Exp</label>
                    <select name="mois_expiration" required>
                        <option value="">MM</option>
                        <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?= str_pad($i, 2, "0", STR_PAD_LEFT) ?>"><?= str_pad($i, 2, "0", STR_PAD_LEFT) ?></option>
                        <?php endfor; ?>
                    </select>

                    <label>Année Exp</label>
                    <select name="annee_expiration" required>
                        <option value="">YYYY</option>
                        <?php for ($y = 2025; $y <= 2031; $y++): ?>
                            <option value="<?= $y ?>"><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="form_cvv">
                    <label>CVV</label>
                    <input type="text" name="cvv" placeholder="XXX" maxlength="3" required>
                </div>
            </div>

            <button type="submit">Inscription</button>
        </form>
    </div>
</div>

<script>
    window.onload = function () {
        document.getElementById('inscription').style.display = 'block';
    };
</script>

</body>
</html>