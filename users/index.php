<?php
session_start();
include '../config/db_conn.php';

$sqlCategories = "SELECT * FROM category LIMIT 6";
$resultCategories = $conn->query($sqlCategories);


$sqlProducts = "SELECT * FROM products WHERE quantity > 0 ORDER BY id DESC LIMIT 10";
$resultProducts = $conn->query($sqlProducts);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - MonApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function calculateTotal(productId) {
            const quantity = document.getElementById('quantity_' + productId).value;
            const price = document.getElementById('price_' + productId).value;
            const totalField = document.getElementById('total_' + productId);
            totalField.value = (quantity * price).toFixed(2);
        }
    </script>
</head>

<body>
    <?php include "../src/php/user_navbar.php"; ?>
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_GET['msg']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="container mt-5">
        <h1 class="text-center">Bienvenue sur MonApp</h1>


        <h2 class="mt-4">Catégories populaires</h2>
        <?php if ($resultCategories->num_rows > 0): ?>
            <div class="row">
                <?php while ($category = $resultCategories->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($category['name']) ?></h5>
                                <a href="category_page.php?id=<?= $category['id'] ?>" class="btn btn-primary">Voir les produits</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>Aucune catégorie disponible.</p>
        <?php endif; ?>


        <h3 class="mt-4">Les 10 derniers produits disponibles</h3>
        <?php if ($resultProducts->num_rows > 0): ?>
            <div class="row">
                <?php while ($product = $resultProducts->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">

                            <?php if (!empty($product['image']) && file_exists("../" . $product['image'])): ?>
                                <img src="../<?= $product['image'] ?>" class="card-img-top" alt="Image du produit" style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <img src="../uploads/default.png" class="card-img-top" alt="Image par défaut" style="height: 200px; object-fit: cover;">
                            <?php endif; ?>

                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($product['description']) ?></p>
                                <p class="card-text"><strong>Prix :</strong> <?= number_format($product['price'], 2) ?> €</p>
                                <p class="card-text"><strong>Quantité disponible :</strong> <?= htmlspecialchars($product['quantity']) ?></p>

                                <?php if (!isset($_SESSION['user_id'])): ?>
                                    <p class="text-danger">Veuillez vous <a href="../login.php">connecter</a> pour commander.</p>
                                <?php else: ?>

                                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#orderModal_<?= $product['id'] ?>">Commander</button>


                                    <div class="modal fade" id="orderModal_<?= $product['id'] ?>" tabindex="-1" aria-labelledby="orderModalLabel_<?= $product['id'] ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="orderModalLabel_<?= $product['id'] ?>">Commander : <?= htmlspecialchars($product['name']) ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="place_order.php" method="POST" enctype="multipart/form-data">
                                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                        <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
                                                        <input type="hidden" id="price_<?= $product['id'] ?>" value="<?= $product['price'] ?>">

                                                        <div class="mb-3">
                                                            <label for="quantity_<?= $product['id'] ?>" class="form-label">Quantité</label>
                                                            <input type="number" name="quantity" id="quantity_<?= $product['id'] ?>" class="form-control" min="1" max="<?= $product['quantity'] ?>" required onchange="calculateTotal(<?= $product['id'] ?>)">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="total_<?= $product['id'] ?>" class="form-label">Total</label>
                                                            <input type="text" name="total" id="total_<?= $product['id'] ?>" class="form-control" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="code_<?= $product['id'] ?>" class="form-label">Code</label>
                                                            <input type="text" name="code" id="code_<?= $product['id'] ?>" class="form-control" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="type_<?= $product['id'] ?>" class="form-label">Type de paiement</label>
                                                            <select name="type" id="type_<?= $product['id'] ?>" class="form-select" required>
                                                                <option value="Bankily">Bankily</option>
                                                                <option value="Masrvi">Masrvi</option>
                                                                <option value="Sedade">Sedade</option>
                                                                <option value="Bimbank">Bimbank</option>
                                                                <option value="Click">Click</option>
                                                                <option value="Amanty">Amanty</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="img_<?= $product['id'] ?>" class="form-label">Image de paiement</label>
                                                            <input type="file" name="img" id="img_<?= $product['id'] ?>" class="form-control" required>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary">Valider la commande</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>Aucun produit disponible.</p>
        <?php endif; ?>
    </div>

    <?php include "../src/php/footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$conn->close();
?>