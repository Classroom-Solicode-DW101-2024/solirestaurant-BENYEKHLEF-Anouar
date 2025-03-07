<?php
require 'config.php';

$erreurs = [];
if (isset($_POST["btnSubmit"])) {

    $nom = trim($_POST["nom"]);
    $prenom = trim($_POST["prenom"]);
    $tel = trim($_POST["tel"]);
    $tel_is_exist = tel_existe($tel);

    // var_dump($tel_is_exist);

    if (!empty($nom) && !empty($prenom) && !empty($tel) && empty($tel_is_exist)) {
        $sql_insert_client = "insert into CLIENT  values(:id,:nom,:prenom,:tel)";
        $stmt_insert_client = $pdo->prepare($sql_insert_client);
        $idvalue = getLastIdClient() + 1;

        $stmt_insert_client->bindParam(':id', $idvalue);
        $stmt_insert_client->bindParam(':nom', $nom);
        $stmt_insert_client->bindParam(':prenom', $prenom);
        $stmt_insert_client->bindParam(':tel', $tel);

        $stmt_insert_client->execute();
        header("Location: login.php");
    } else {
        if (empty($nom)) {
            $erreurs['nom'] = "Veuillez renseigner le nom.";
        }
        if (empty($prenom)) {
            $erreurs['prenom'] = "Veuillez renseigner le prénom.";
        }
        if (empty($tel)) {
            $erreurs['tel'] = "Veuillez renseigner le numéro de téléphone.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="icon" href="../assets/images/fav-icon.jpg" type="image/x-icon" />
    <link rel="stylesheet" href="../assets/register.css?v=<?php echo time(); ?>">
    <link
        href="https://fonts.googleapis.com/css2?family=ADLaM+Display&family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Manrope:wght@200..800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Quicksand:wght@300..700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <form method="POST">
        <h2>Créer un Compte</h2>
        <label for="nom">Nom :</label>
        <input type="text" name="nom" id="nom" placeholder="Nom" required>
        <?php if (isset($erreurs["nom"])): ?>
            <span class="error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?php echo $erreurs["nom"]; ?>
            </span>
        <?php endif; ?>


        <label for="prenom">Prénom :</label>
        <input type="text" name="prenom" id="prenom" placeholder="Prénom" required>
        <?php if (isset($erreurs["prenom"])): ?>
            <span class="error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?php echo $erreurs["prenom"]; ?>
            </span>
        <?php endif; ?>

        <label for="numTel">Numéro de téléphone :</label>
        <input type="tel" id="tel" name="tel" placeholder="+212XXXXXXXXX" pattern="^\+212\d{9}$" required>
        <?php if (isset($erreurs["tel"])): ?>
            <span class="error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?php echo $erreurs["tel"]; ?>
            </span>
        <?php endif; ?>

        <button name="btnSubmit">Créer</button>
        <a href="login.php">Vous avez déjà un compte ?</a>
    </form>


</body>

</html>