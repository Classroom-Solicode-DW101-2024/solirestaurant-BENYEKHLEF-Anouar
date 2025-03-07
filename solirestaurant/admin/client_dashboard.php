<?php
require 'config.php';

if (!isset($_SESSION['isAdminLogin']) || $_SESSION['isAdminLogin'] !== true) {
    header("Location: admin_login.php");
    unset($_SESSION["isAdminLogin"]); 
    exit();
}

// Handle client deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
        $idClient = $_POST['delete_id'];
        $sql = "DELETE FROM client WHERE idClient = :idClient";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':idClient', $idClient, PDO::PARAM_INT);
        $stmt->execute();
        header("Location: client_dashboard.php");
        exit();
}

$sql = "SELECT idClient, nomCl, prenomCl, telCl FROM client";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalClients = count($clients);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - Clients</title>
    <link rel="icon" href="../assets/images/fav-icon.jpg" type="image/x-icon" />
    <link rel="stylesheet" href="../assets/admin_dashboard.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=ADLaM+Display&family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Manrope:wght@200..800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .delete-btn {
            background-color: #ff4444;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 3px;
        }
        .delete-btn:hover {
            background-color: #cc0000;
        }

        #title {
            color: #9E6752;
        }
    </style>
    
</head>
<body>
    <?php require 'aside_navbar.html'; ?>

    <main class="main-content">
        <h1>Les Clients du <span id="title">Foodventure</span></h1>

        <!-- Section: Statistics -->
        <section class="stats">
            <div class="stat-card">
                <h3><i class="fa-solid fa-person"></i> Total Clients</h3>
                <p><?= htmlspecialchars($totalClients) ?></p>
            </div>
        </section>

        <!-- Section: Client Table -->
        <section class="clientsTableContainer">
            <h2>Tous les Clients</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Client</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Tél</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $client): ?>
                        <tr>
                            <td><?= htmlspecialchars($client['idClient']) ?></td>
                            <td><?= htmlspecialchars($client['nomCl']) ?></td>
                            <td><?= htmlspecialchars($client['prenomCl']) ?></td>
                            <td><?= htmlspecialchars($client['telCl']) ?></td>
                            <td>
                                <form method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer ce client ?');">
                                    <input type="hidden" name="delete_id" value="<?= htmlspecialchars($client['idClient']) ?>">
                                    <button type="submit" class="delete-btn"><i class="fa-solid fa-trash"></i>&nbsp; Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>