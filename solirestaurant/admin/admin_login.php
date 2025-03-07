<?php
require 'config.php';

if (isset($_SESSION['isAdminLogin']) && $_SESSION['isAdminLogin'] === true) {
    header('Location: admin_dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if ($username === 'anouar' && $password === 'admin') {
        $_SESSION['isAdminLogin'] = true;
        header('Location: admin_dashboard.php');
        exit;
    } else {
        $error_message = "Nom d'utilisateur ou mot de passe invalide.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - SoliRestaurant</title>
    <link rel="icon" href="../assets/images/fav-icon.jpg" type="image/x-icon" />
    <link rel="stylesheet" href="../assets/admin_login.css?v=<?php echo time(); ?>">
    <link
        href="https://fonts.googleapis.com/css2?family=ADLaM+Display&family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Manrope:wght@200..800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Quicksand:wght@300..700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>

<body>
    <div class="login-container">
        <?php if (isset($error_message)): ?>
            <p class="error-message"><i class="fa-solid fa-triangle-exclamation"></i>&nbsp; <?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <h2>Connexion Administrateur</h2>
            <div class="input-container">
                <label for="username">Nom d'utilisateur:</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="input-container">
                <label for="password">Mot de passe:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>

</html>