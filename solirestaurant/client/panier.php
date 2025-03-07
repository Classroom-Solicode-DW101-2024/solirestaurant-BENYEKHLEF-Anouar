<?php
require 'config.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['index'])) {
        $index = $_POST['index'];
        $action = $_POST['action'];

        if (isset($_SESSION['cart'][$index])) {
            if ($action === 'increase') {
                $_SESSION['cart'][$index]['quantity'] += 1;
            } elseif ($action === 'decrease') {
                $_SESSION['cart'][$index]['quantity'] -= 1;
                if ($_SESSION['cart'][$index]['quantity'] < 1) {
                    unset($_SESSION['cart'][$index]);
                    $_SESSION['cart'] = array_values($_SESSION['cart']);
                }
            }
        }
        header("Location: panier.php");
        exit();
    }

    if (isset($_POST['remove']) && isset($_POST['index'])) {
        $index = $_POST['index'];
        if (isset($_SESSION['cart'][$index])) {
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
        }
        header("Location: panier.php");
        exit();
    }
}

if (isset($_POST['delete_cart'])) {
    $_SESSION['cart'] = [];
    header("Location: panier.php");
    exit();
}

if (isset($_POST['validate_order'])) {
    if (empty($_SESSION['cart'])) {
        echo "Votre panier est vide.";
        exit();
    }

    if (!isset($_SESSION['client']['idClient'])) {
        echo "Erreur : client non authentifié.";
        exit();
    }

    $clientId = $_SESSION['client']['idClient'];

    try {
        $pdo->beginTransaction();

        $sql = "SELECT MAX(idCmd) AS lastId FROM commande";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $lastId = $stmt->fetchColumn();

        if ($lastId !== false && $lastId !== null) {
            $idCmd = $lastId + 1;                                     
        } else {
            $idCmd = 1; 
        }

        $maxAttempts = 30; 

        $attempt = 0; 

        $inserted = false;
        while (!$inserted && $attempt < $maxAttempts) {
            try {
                $sql = "INSERT INTO commande (idCmd, dateCmd, Statut, idCl) VALUES (:idCmd, NOW(), 'en attente', :idCl)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':idCmd', $idCmd, PDO::PARAM_INT);
                $stmt->bindParam(':idCl', $clientId);
                $stmt->execute();
                $inserted = true; 
            } catch (PDOException $e) {
                if ($e->getCode() == '23000') { 
                    $idCmd++; 
                    $attempt++;
                } else {
                    throw $e; 
                }
            }
        }
        if (!$inserted) {
            throw new Exception("Impossible de générer un idCmd unique après $maxAttempts tentatives.");
        }


        $sql = "INSERT INTO commande_plat (idPlat, idCmd, qte) 
                VALUES (:idPlat, :idCmd, :qte)
                ON DUPLICATE KEY UPDATE qte = qte + VALUES(qte)";
        $stmt = $pdo->prepare($sql);
        foreach ($_SESSION['cart'] as $item) {
            $stmt->bindParam(':idPlat', $item['idPlat']);
            $stmt->bindParam(':idCmd', $idCmd, PDO::PARAM_INT);
            $stmt->bindParam(':qte', $item['quantity']);
            $stmt->execute();
        }

        $pdo->commit();

        $_SESSION['cart'] = [];
        header("Location: confirmation.php?idCmd=" . $idCmd);
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Erreur lors de la validation : " . $e->getMessage();
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Mon Panier</title>
    <link rel="icon" href="../assets/images/fav-icon.jpg" type="image/x-icon" />
    <link rel="stylesheet" href="../assets/panier.css?v=<?php echo time(); ?>">
    <link
        href="https://fonts.googleapis.com/css2?family=ADLaM+Display&family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Manrope:wght@200..800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Quicksand:wght@300..700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <header>
        <a href="home.php"><i class="fa-solid fa-arrow-left"></i> Retour à l'accueil</a>
    </header>
    <h1>Votre Panier</h1>
    <?php if (empty($_SESSION['cart'])): ?>
        <p class="emptyReminder"><i class="fa-solid fa-triangle-exclamation"></i> Votre panier est vide.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Nom du Plat</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $grandTotal = 0;
                foreach ($_SESSION['cart'] as $index => $item):
                    $total = $item['prix'] * $item['quantity'];
                    $grandTotal += $total;
                ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nomPlat']) ?></td>
                        <td><?= htmlspecialchars($item['prix']) ?> €</td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= number_format($total, 2) ?> €</td>
                        <td>
                            <!-- Increase Quantity -->
                            <form method="POST" action="panier.php">
                                <input type="hidden" name="index" value="<?= $index ?>">
                                <button type="submit" name="action" value="increase"><i class="fa-solid fa-plus"></i></button>
                            </form>
                            <!-- Decrease Quantity -->
                            <form method="POST" action="panier.php">
                                <input type="hidden" name="index" value="<?= $index ?>">
                                <button type="submit" name="action" value="decrease"><i class="fa-solid fa-minus"></i></button>
                            </form>
                            <!-- Remove Item -->
                            <form method="POST" action="panier.php">
                                <input type="hidden" name="index" value="<?= $index ?>">
                                <button type="submit" name="remove"><i class="fa-solid fa-trash"></i> Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3"><strong>Grand Total</strong></td>
                    <td colspan="2"><strong><?= number_format($grandTotal, 2) ?> €</strong></td>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>
    <?php if (!empty($_SESSION['cart'])): ?>
        <form method="POST" action="panier.php" class="endform">
            <button type="submit" name="validate_order" class="endbtn"><i class="fa-solid fa-check"></i> Valider la commande</button>
            <button type="submit" name="delete_cart" class="endbtn"><i class="fa-solid fa-trash"></i> Supprimer le panier</button>
        </form>
    <?php endif; ?>
</body>

</html>