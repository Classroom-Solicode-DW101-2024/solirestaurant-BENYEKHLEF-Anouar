<?php
require "config.php";

if (isset($_POST["submit"])) {
    $tel = $_POST["tel"];
    $resultat = tel_existe($tel);

    if (empty($resultat)) {
        header("Location: register.php");
    } else {
        $_SESSION["client"] = $resultat;
        header("Location: home.php");
        // echo "<p>User: " . htmlspecialchars($_SESSION["client"]["nomCl"]) . "</p>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./assets/images/fav-icon.jpg" type="image/x-icon" />
    <title>Login</title>
    <link rel="stylesheet" href="./assets/login.css?v=<?php echo time(); ?>">
    <link
        href="https://fonts.googleapis.com/css2?family=ADLaM+Display&family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Manrope:wght@200..800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Quicksand:wght@300..700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <form method="POST">
        <h2>Bienvenue à Foodventure!</h2>
        <label for="tel">Numéro de Téléphone :</label>
        <input type="tel" id="tel" name="tel" placeholder="+212XXXXXXXXX" pattern="^\+212\d{9}$" required>

        <a href="register.php"> Créer un compte </a>
        <button name="submit">Se connecter</button>
    </form>

</body>

</html>