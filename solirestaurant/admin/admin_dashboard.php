<?php
require 'config.php';


if (!isset($_SESSION['isAdminLogin']) || $_SESSION['isAdminLogin'] !== true) {
  header("Location: admin_login.php");
  unset($_SESSION["isAdminLogin"]);
  exit();
}


$today = date('Y-m-d');

// Retrieve today's orders along with client info
$stmt = $pdo->prepare("
    SELECT c.idCmd, c.dateCmd, c.Statut, cl.nomCl, cl.prenomCl 
    FROM commande c 
    INNER JOIN client cl ON c.idCl = cl.idClient 
    WHERE DATE(c.dateCmd) = :today 
    ORDER BY c.dateCmd DESC
");
$stmt->bindParam(':today', $today, PDO::PARAM_STR);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retrieve list of ordered dishes for selected date with total quantities
// Initialize selected date (default to today if not set)
$selectedDate = isset($_POST['selected_date']) ? $_POST['selected_date'] : date('Y-m-d');

$stmt = $pdo->prepare("
    SELECT cp.idPlat, p.nomPlat, SUM(cp.qte) as total_quantity
    FROM commande_plat cp 
    INNER JOIN plat p ON cp.idPlat = p.idPlat 
    INNER JOIN commande c ON cp.idCmd = c.idCmd 
    WHERE DATE(c.dateCmd) = :selectedDate 
    GROUP BY cp.idPlat, p.nomPlat
");
$stmt->bindParam(':selectedDate', $selectedDate, PDO::PARAM_STR);
$stmt->execute();
$orderedDishes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get today's total number of orders
$stmt = $pdo->prepare("SELECT COUNT(*) FROM commande WHERE DATE(dateCmd) = :today");
$stmt->bindParam(':today', $today, PDO::PARAM_STR);
$stmt->execute();
$totalOrders = $stmt->fetchColumn();

// Get total number of Plats
$stmt = $pdo->prepare("SELECT COUNT(*) FROM plat");
$stmt->execute();
$totalPlats = $stmt->fetchColumn();

// Get total number of clients 
$stmt = $pdo->prepare("SELECT COUNT(*) FROM client");
$stmt->execute();
$totalClients = $stmt->fetchColumn();

// Get number of cancelled orders 
$stmt = $pdo->prepare("SELECT COUNT(*) FROM commande WHERE Statut = 'annulée'");
$stmt->execute();
$cancelledOrders = $stmt->fetchColumn();

// Update order status if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
  $orderId = $_POST['order_id'];
  $newStatus = $_POST['new_status'];

  $stmt = $pdo->prepare("UPDATE commande SET Statut = :newStatus WHERE idCmd = :orderId");
  $stmt->bindParam(':newStatus', $newStatus, PDO::PARAM_STR);
  $stmt->bindParam(':orderId', $orderId, PDO::PARAM_STR);
  $stmt->execute();
  header("Location: admin_dashboard.php");
  exit();
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard - Statistics</title>
  <link rel="icon" href="../assets/images/fav-icon.jpg" type="image/x-icon" />
  <link rel="stylesheet" href="../assets/admin_dashboard.css?v=<?php echo time(); ?>">
  <link
    href="https://fonts.googleapis.com/css2?family=ADLaM+Display&family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Manrope:wght@200..800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Quicksand:wght@300..700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">


</head>

<body>
  <?php require 'aside_navbar.html'; ?>

  <main class="main-content">
    <h1>Bienvenue sur le Dashboard Administrateur</h1>

    <!-- Section: Statistics -->
    <section class="stats">
      <div class="stat-card">
        <h3><i class="fa-solid fa-bag-shopping"></i>&nbsp; Total Commandes Aujourd'hui</h3>
        <p><?= htmlspecialchars($totalOrders) ?></p>
      </div>
      <div class="stat-card">
        <h3><i class="fa-solid fa-person"></i>&nbsp; Total Clients</h3>
        <p><?= htmlspecialchars($totalClients) ?></p>
      </div>
      <div class="stat-card">
        <h3><i class="fa-solid fa-shop-slash"></i>&nbsp; Commandes Annulées</h3>
        <p><?= htmlspecialchars($cancelledOrders) ?></p>
      </div>

      <div class="stat-card">
        <h3><i class="fa-solid fa-utensils"></i>&nbsp; Total Plats</h3>
        <p><?= htmlspecialchars($totalPlats) ?></p>
      </div>

    </section>

    <!-- Section: Orders for Today -->
    <section>
      <h2>Commandes du Jour (<?= date('d/m/Y') ?>)</h2>
      <?php if (count($orders) > 0): ?>
        <table>
          <thead>
            <tr>
              <th>ID Commande</th>
              <th>Date / Heure</th>
              <th>Client</th>
              <th>Statut</th>
              <th>Mettre à jour</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $order): ?>
              <tr>
                <td><?= htmlspecialchars($order['idCmd']) ?></td>
                <td><?= htmlspecialchars($order['dateCmd']) ?></td>
                <td><?= htmlspecialchars($order['nomCl'] . ' ' . $order['prenomCl']) ?></td>
                <td><?= htmlspecialchars($order['Statut']) ?></td>
                <td>
                  <form method="POST" class="update-form">
                    <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['idCmd']) ?>">
                    <select name="new_status">
                      <option value="en attente" <?= ($order['Statut'] === 'en attente') ? 'selected' : '' ?>>En attente</option>
                      <option value="en cours" <?= ($order['Statut'] === 'en cours') ? 'selected' : '' ?>>En cours</option>
                      <option value="expédiée" <?= ($order['Statut'] === 'expédiée') ? 'selected' : '' ?>>Expédiée</option>
                      <option value="livrée" <?= ($order['Statut'] === 'livrée') ? 'selected' : '' ?>>Livrée</option>
                      <option value="annulée" <?= ($order['Statut'] === 'annulée') ? 'selected' : '' ?>>Annulée</option>
                    </select>
                    <button type="submit" name="update_status"><i class="fa-solid fa-check"></i></button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class="emptyWarning"><i class="fa-solid fa-triangle-exclamation"></i>&nbsp; Aucune commande pour aujourd'hui.</p>
      <?php endif; ?>
    </section>


    <!-- Section: Dishes Ordered by Date -->
    <section id="dishesTable">
      <h2>Plats Commandés</h2>
      <form method="POST" class="date-filter-form">
        <label for="selected_date">Sélectionner une date :</label>
        <input type="date" id="selected_date" name="selected_date"
          value="<?= htmlspecialchars($selectedDate) ?>">
        <button type="submit" class="filter-button">Filtrer</button>
      </form>

      <?php if (count($orderedDishes) > 0): ?>
        <table>
          <thead>
            <tr>
              <th>ID Plat</th>
              <th>Nom du Plat</th>
              <th>Quantité Totale</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orderedDishes as $dish): ?>
              <tr>
                <td><?= htmlspecialchars($dish['idPlat']) ?></td>
                <td><?= htmlspecialchars($dish['nomPlat']) ?></td>
                <td><?= htmlspecialchars($dish['total_quantity']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p id="reminder"><i class="fa-solid fa-triangle-exclamation"></i>&nbsp; Aucun plat commandé pour le <?php echo $selectedDate; ?>.</p>
      <?php endif; ?>
    </section>

  </main>

  <script>
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['update_status'])): ?>
      document.getElementById('dishesTable').scrollIntoView({
        behavior: 'smooth'
      });
    <?php endif; ?>
  </script>

</body>

</html>