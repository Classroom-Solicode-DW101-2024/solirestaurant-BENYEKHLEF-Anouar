<?php

require 'config.php';

if (!isset($_GET['idCmd'])) {
    echo "Commande non trouvée.";
    exit();
}

$idCmd = $_GET['idCmd'];

$sqlOrder = "SELECT * FROM commande WHERE idCmd = :idCmd";
$stmtOrder = $pdo->prepare($sqlOrder);
$stmtOrder->execute([':idCmd' => $idCmd]);
$order = $stmtOrder->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "Commande non trouvée.";
    exit();
}

$sqlItems = "SELECT cp.qte, p.nomPlat, p.prix 
             FROM commande_plat cp 
             JOIN plat p ON cp.idPlat = p.idPlat 
             WHERE cp.idCmd = :idCmd";
$stmtItems = $pdo->prepare($sqlItems);
$stmtItems->execute([':idCmd' => $idCmd]);
$orderItems = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Confirmation de Commande</title>
    <link rel="icon" href="../assets/images/fav-icon.jpg" type="image/x-icon" />
    <link rel="stylesheet" href="../assets/confirmation.css?php echo time(); ?>">
    <link
        href="https://fonts.googleapis.com/css2?family=ADLaM+Display&family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Manrope:wght@200..800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Quicksand:wght@300..700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <p><a href="home.php"><i class="fa-solid fa-arrow-left"></i>&nbsp; Retour à l'accueil</a></p>
    <h1>Confirmation de Commande</h1>
    <p><strong><i class="fa-solid fa-gift"></i>&nbsp; Merci pour votre commande !</strong></p>
    <p><strong>Numéro de commande :</strong> <?= htmlspecialchars($order['idCmd']); ?></p>
    <p><strong>Date de commande :</strong> <?= htmlspecialchars($order['dateCmd']); ?></p>
    <p><strong>Statut :</strong> <?= htmlspecialchars($order['Statut']); ?></p>

    <h2>Détails de la commande</h2>
    <?php if (empty($orderItems)): ?>
        <p>Aucun article trouvé pour cette commande.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Nom du Plat</th>
                <th>Quantité</th>
                <th>Prix Unitaire</th>
                <th>Total</th>
            </tr>
            <?php
            $grandTotal = 0;
            foreach ($orderItems as $item):
                $total = $item['prix'] * $item['qte'];
                $grandTotal += $total;
            ?>
                <tr>
                    <td><?= htmlspecialchars($item['nomPlat']); ?></td>
                    <td><?= htmlspecialchars($item['qte']); ?></td>
                    <td><?= htmlspecialchars($item['prix']); ?> €</td>
                    <td><?= number_format($total, 2); ?> €</td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3"><strong>Total Général</strong></td>
                <td><strong><?= number_format($grandTotal, 2); ?> €</strong></td>
            </tr>
        </table>
    <?php endif; ?>
</body>

</html>