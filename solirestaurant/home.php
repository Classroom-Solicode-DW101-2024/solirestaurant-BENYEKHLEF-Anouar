<?php
require 'config.php';

if (isset($_GET["destroy"])) {
    session_destroy();
    header("Location: login.php");
}

if (!isset($_SESSION["client"])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST["search"])) {
    // var_dump($_POST);
    $typeCriteria = $_POST["typeCriteria"] ?? '';
    $categorieCriteria = $_POST["categorieCriteria"] ?? '';

    if (!empty($typeCriteria) && !empty($categorieCriteria)) {
        $sql = "SELECT * FROM plat WHERE TypeCuisine = :typeCriteria AND categoriePlat = :categorieCriteria";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':typeCriteria', $typeCriteria);
        $stmt->bindParam(':categorieCriteria', $categorieCriteria);
    } else if (!empty($typeCriteria)) {
        $sql = "SELECT * FROM plat WHERE TypeCuisine = :typeCriteria";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':typeCriteria', $typeCriteria);
    } else if (!empty($categorieCriteria)) {
        $sql = "SELECT * FROM plat WHERE categoriePlat = :categorieCriteria";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':categorieCriteria', $categorieCriteria);
    } else {
        $sql = "SELECT * FROM plat";
        $stmt = $pdo->query($sql);
    }

    $stmt->execute();
    $plats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $sql = "SELECT * FROM plat";
    $stmt = $pdo->query($sql);
    $plats = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$platsByCuisine = [];
foreach ($plats as $plat) {
    $platsByCuisine[$plat['TypeCuisine']][] = $plat;
}

// session_destroy()

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoliRestaurant</title>
    <link rel="icon" href="./assets/images/fav-icon.jpg" type="image/x-icon" />
    <link rel="stylesheet" href="./assets/home.css?v=<?php echo time(); ?>">
    <link
        href="https://fonts.googleapis.com/css2?family=ADLaM+Display&family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Manrope:wght@200..800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Quicksand:wght@300..700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>

<body>
    <header>
        <nav class="navbar">
            <img class="logo" src="./assets/images/logo2.png" alt="logo">
            <ul class="nav-links" id="navLinks">
                <li class="active"><a href="home.php"><i class="fa-solid fa-house" style="color: #9E6752;"></i>&nbsp
                        <span>Accueil</span></a></li>

                <li><a href=""><i class="fa-solid fa-cart-shopping" style="color: #9E6752;"></i>&nbsp
                        <span>Panier</span></a>
                </li>

                <div class="dropdown">
                    <li>
                        <a href="">
                            <i class="fa-solid fa-user-tie" style="color: #9E6752;"></i>&nbsp
                            <span><?= $_SESSION["client"]["nomCl"] . " " . $_SESSION["client"]["prenomCl"] ?></span>
                        </a>
                    </li>
                    <div class="dropdown-content">
                        <a href="home.php?destroy=1" id="logout"><i class="fa-solid fa-arrow-right-from-bracket" style="color: #9E6752;"></i> &nbsp;Se déconnecter</a>
                    </div>
                </div>

            </ul>
        </nav>
    </header>


    <h1 id="hero-title">Découvrez les saveurs du <span>monde</span></h1>

    <section class="filter">
        <form method="POST" action="">
            <select name="categorieCriteria" id="categorieCriteria">
                <option value="" selected disabled>Chercher par la catégorie</option>
                <option value="plat principal">Plat principal</option>
                <option value="dessert">Dessert</option>
                <option value="entrée">Entrée</option>
            </select>

            <select name="typeCriteria" id="typeCriteria">
                <option value="" selected disabled>Chercher par le type de cuisine</option>
                <option value="Marocaine">Marocaine</option>
                <option value="Italienne">Italienne</option>
                <option value="Chinoise">Chinoise</option>
                <option value="Espagnole">Espagnole</option>
                <option value="Francaise">Française</option>
            </select>

            <button type="submit" id="searchBtn" name="search">
                <i class="fa-solid fa-magnifying-glass"></i> Recherche
            </button>
            <button type="button" id="clearBtn" onclick="window.location.href='home.php'">
                <i class="fa-regular fa-circle-xmark"></i> Clair
            </button>
        </form>
    </section>

    <?php if (empty($plats)): ?>
        <p id="alert">Aucun plat ne correspond à votre recherche.</p>
    <?php else: ?>
        <?php foreach ($platsByCuisine as $typeCuisine => $plats): ?>
            <h2 class="cuisine-title"><?= htmlspecialchars($typeCuisine) ?></h2>
            <div class="cuisine-section">
                <?php foreach ($plats as $plat): ?>
                    <div class="card">
                        <img src="<?= htmlspecialchars($plat['image']) ?>" alt="<?= htmlspecialchars($plat['nomPlat']) ?>">
                        <div class="card-content">
                            <h3><?= htmlspecialchars($plat['nomPlat']) ?></h3>
                            <p id="categorie">Catégorie : <?= htmlspecialchars($plat['categoriePlat']) ?></p>
                            <p id="prix">Prix : <?= htmlspecialchars($plat['prix']) ?> €</p>
                            <button class="cta">
                                <span class="hover-underline-animation"> Commander </span>
                                <svg
                                    id="arrow-horizontal"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="30"
                                    height="10"
                                    viewBox="0 0 46 16">
                                    <path
                                        id="Path_10"
                                        data-name="Path 10"
                                        d="M8,0,6.545,1.455l5.506,5.506H-30V9.039H12.052L6.545,14.545,8,16l8-8Z"
                                        transform="translate(30)"></path>
                                </svg>
                            </button>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>


    <footer class="footer" id="footer">
        <div class="footer-container">

            <div class="footer-left">
                <img src="./assets/images/logo2.png" alt="Logo" class="footer-logo">
                <h3>Contactez-nous</h3>
                <span class="icon"><i class="fa-solid fa-envelope"></i> Email : &nbsp <a
                        href="mailto:info@solicode.co">info@foodventure.co</a></span><br>
                <span class="icon"><i class="fa-solid fa-phone"></i> Tel : (212) 0634 39 05 05</span> <br>
                <span class="icon"><i class="fa-solid fa-fax"></i> Fixe : (212) 0539 30 88 85</span> <br>

                <div class="social-icons">
                    <h3>Vous pouvez nous trouver sur :</h3>
                    <a href="" class="icon" target="_blank"><i
                            class="fa-brands fa-facebook"></i></a>
                    <a href="" class="icon" target="_blank"><i
                            class="fa-brands fa-instagram"></i></a>
                    <a href="" class="icon" target="_blank"><i
                            class="fa-brands fa-linkedin"></i></a>
                    <a href="" class="icon" target="_blank"><i
                            class="fa-brands fa-youtube"></i></a>
                </div>
            </div>

            <div class="footer-right">
                <svg fill=" #d6d5d5" height="220px" width="220px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 60.00 60.00" xml:space="preserve" stroke=" #d6d5d5" stroke-width="0.0006000000000000001" transform="matrix(1, 0, 0, 1, 0, 0)">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                    <g id="SVGRepo_iconCarrier">
                        <g>
                            <path d="M18.35,20.805c0.195,0.195,0.451,0.293,0.707,0.293c0.256,0,0.512-0.098,0.707-0.293c0.391-0.391,0.391-1.023,0-1.414 c-1.015-1.016-1.015-2.668,0-3.684c0.87-0.87,1.35-2.026,1.35-3.256s-0.479-2.386-1.35-3.256c-0.391-0.391-1.023-0.391-1.414,0 s-0.391,1.023,0,1.414c0.492,0.492,0.764,1.146,0.764,1.842s-0.271,1.35-0.764,1.842C16.555,16.088,16.555,19.01,18.35,20.805z"></path>
                            <path d="M40.35,20.805c0.195,0.195,0.451,0.293,0.707,0.293c0.256,0,0.512-0.098,0.707-0.293c0.391-0.391,0.391-1.023,0-1.414 c-1.015-1.016-1.015-2.668,0-3.684c0.87-0.87,1.35-2.026,1.35-3.256s-0.479-2.386-1.35-3.256c-0.391-0.391-1.023-0.391-1.414,0 s-0.391,1.023,0,1.414c0.492,0.492,0.764,1.146,0.764,1.842s-0.271,1.35-0.764,1.842C38.555,16.088,38.555,19.01,40.35,20.805z"></path>
                            <path d="M29.35,14.805c0.195,0.195,0.451,0.293,0.707,0.293c0.256,0,0.512-0.098,0.707-0.293c0.391-0.391,0.391-1.023,0-1.414 c-1.015-1.016-1.015-2.668,0-3.684c0.87-0.87,1.35-2.026,1.35-3.256s-0.479-2.386-1.35-3.256c-0.391-0.391-1.023-0.391-1.414,0 s-0.391,1.023,0,1.414c0.492,0.492,0.764,1.146,0.764,1.842s-0.271,1.35-0.764,1.842C27.555,10.088,27.555,13.01,29.35,14.805z"></path>
                            <path d="M55.624,43.721C53.812,33.08,45.517,24.625,34.957,22.577c0.017-0.16,0.043-0.321,0.043-0.48c0-2.757-2.243-5-5-5 s-5,2.243-5,5c0,0.159,0.025,0.32,0.043,0.48C14.483,24.625,6.188,33.08,4.376,43.721C2.286,44.904,0,46.645,0,48.598 c0,5.085,15.512,8.5,30,8.5s30-3.415,30-8.5C60,46.645,57.714,44.904,55.624,43.721z M27.006,22.27 C27.002,22.212,27,22.154,27,22.098c0-1.654,1.346-3,3-3s3,1.346,3,3c0,0.057-0.002,0.114-0.006,0.172 c-0.047-0.005-0.094-0.007-0.14-0.012c-0.344-0.038-0.69-0.065-1.038-0.089c-0.128-0.009-0.255-0.022-0.383-0.029 c-0.474-0.026-0.951-0.041-1.432-0.041s-0.958,0.015-1.432,0.041c-0.128,0.007-0.255,0.02-0.383,0.029 c-0.348,0.024-0.694,0.052-1.038,0.089C27.1,22.263,27.053,22.264,27.006,22.27z M25.126,26.635 c1.582-0.356,3.217-0.537,4.86-0.537c0.004,0,0.009,0,0.014,0c0.552,0,1,0.448,1,1.001c0,0.552-0.448,0.999-1,0.999h0 c-0.004,0-0.009,0-0.013,0c-1.496,0-2.982,0.164-4.421,0.488c-0.074,0.017-0.148,0.024-0.221,0.024c-0.457,0-0.87-0.315-0.975-0.78 C24.249,27.291,24.587,26.756,25.126,26.635z M19.15,28.997c0.476-0.281,1.088-0.124,1.37,0.351 c0.282,0.476,0.125,1.089-0.351,1.37c-4.713,2.792-8.147,7.861-9.186,13.56c-0.088,0.482-0.509,0.82-0.983,0.82 c-0.06,0-0.12-0.005-0.18-0.017c-0.543-0.099-0.904-0.619-0.805-1.163C10.158,37.658,13.947,32.08,19.15,28.997z M30,55.098 c-17.096,0-28-4.269-28-6.5c0-0.383,0.474-1.227,2.064-2.328c-0.004,0.057-0.002,0.113-0.006,0.17C4.024,46.988,4,47.54,4,48.098 v0.788l0.767,0.185c8.254,1.981,16.744,2.985,25.233,2.985s16.979-1.004,25.233-2.985L56,48.886v-0.788 c0-0.558-0.024-1.109-0.058-1.658c-0.004-0.057-0.002-0.113-0.006-0.17C57.526,47.371,58,48.215,58,48.598 C58,50.829,47.096,55.098,30,55.098z"></path>
                        </g>
                    </g>
                </svg>

            </div>

        </div>

        <div class="footer-bottom">
            <p>Copyrights © 2025 All Rights Reserved by <span>Foodventure</span>.</p>
        </div>
    </footer>


    <!-- <script src="./assets/script.js"></script> -->
</body>

</html>