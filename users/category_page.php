<?php
include '../config/db_conn.php';

$sqlCategories = "SELECT * FROM category";
$resultCategories = $conn->query($sqlCategories);


if (!$resultCategories) {
    die("Erreur lors de la récupération des catégories : " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catégories - MonApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?php include "../src/php/user_navbar.php"; ?>


    <div class="container mt-5">
        <h1 class="text-center">Toutes les Catégories</h1>


        <?php if ($resultCategories->num_rows > 0): ?>
            <div class="row mt-4">

                <?php while ($category = $resultCategories->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($category['name']) ?></h5>

                                <a href="products_by_category.php?category_id=<?= $category['id'] ?>" class="btn btn-primary">Voir les produits</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>

            <p class="text-center">Aucune catégorie disponible.</p>
        <?php endif; ?>
    </div>


    <?php include "../src/php/footer.php"; ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php

$conn->close();
?>