<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config/db_conn.php';


if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour accéder à votre historique. <a href='../login.php'>Connectez-vous ici</a>.");
}

$user_id = $_SESSION['user_id'];


$sqlHistory = "SELECT o.id AS order_id, o.total, o.date, o.etat, p.name AS product_name, p.price, p.image 
               FROM orders o 
               JOIN products p ON o.product_id = p.id 
               WHERE o.user_id = $user_id 
               ORDER BY o.date DESC";
$resultHistory = $conn->query($sqlHistory);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des commandes - MonApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include "../src/php/user_navbar.php"; ?>

    <div class="container mt-5">
        <h1 class="text-center">Historique des commandes</h1>

        <?php if ($resultHistory->num_rows > 0): ?>
            <div class="table-responsive mt-4">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Produit</th>
                            <th>Image</th>
                            <th>Prix unitaire</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th>État</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $resultHistory->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($order['product_name']) ?></td>
                                <td>
                                    <?php
                                    $imagePath = "../" . $order['image'];
                                    if (!empty($order['image']) && file_exists($imagePath)) {
                                        echo "<img src='" . htmlspecialchars($imagePath) . "' alt='Image du produit' style='height: 50px;'>";
                                    } else {
                                        echo "<img src='../uploads/default.png' alt='Image par défaut' style='height: 50px;'>";
                                    }
                                    ?>
                                </td>
                                <td><?= number_format($order['price'], 2) ?> €</td>
                                <td><?= number_format($order['total'], 2) ?> €</td>
                                <td><?= htmlspecialchars($order['date']) ?></td>
                                <td>
                                    <?php
                                    switch ($order['etat']) {
                                        case 1:
                                            echo "En attente";
                                            break;
                                        case 2:
                                            echo "Refusé";
                                            break;
                                        case 3:
                                            echo "Annulé";
                                            break;
                                        case 4:
                                            echo "Passé";
                                            break;
                                        default:
                                            echo "Inconnu";
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-center">Vous n'avez aucune commande dans votre historique.</p>
        <?php endif; ?>
    </div>

    <?php include "../src/php/footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php $conn->close(); ?>