<?php
session_start();
include '../config/db_conn.php';


if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour passer une commande.");
}


$user_id = intval($_POST['user_id']);
$product_id = intval($_POST['product_id']);
$quantity = intval($_POST['quantity']);
$code = $conn->real_escape_string($_POST['code']);
$type = $conn->real_escape_string($_POST['type']);


if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = "../uploads/payments/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $imageName = basename($_FILES['img']['name']);
    $targetPath = $uploadDir . $imageName;


    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $fileType = mime_content_type($_FILES['img']['tmp_name']);

    if (in_array($fileType, $allowedTypes) && $_FILES['img']['size'] <= 2000000) {
        if (move_uploaded_file($_FILES['img']['tmp_name'], $targetPath)) {
            $imagePath = "uploads/payments/" . $imageName;
        } else {
            die("Erreur lors du téléchargement de l'image.");
        }
    } else {
        die("Format d'image invalide ou taille trop grande.");
    }
} else {
    die("Aucune image téléchargée.");
}


$sqlProduct = "SELECT * FROM products WHERE id = $product_id";
$resultProduct = $conn->query($sqlProduct);

if ($resultProduct->num_rows > 0) {
    $product = $resultProduct->fetch_assoc();

    if ($quantity > $product['quantity']) {
        die("Quantité demandée non disponible.");
    }


    $total = $quantity * $product['price'];


    $sqlOrder = "INSERT INTO orders (user_id, product_id, total, date, etat, code, img, type) 
                 VALUES ($user_id, $product_id, $total, NOW(), 1, '$code', '$imagePath', '$type')";

    if ($conn->query($sqlOrder)) {

        $newQuantity = $product['quantity'] - $quantity;
        $sqlUpdateProduct = "UPDATE products SET quantity = $newQuantity WHERE id = $product_id";
        $conn->query($sqlUpdateProduct);


        header("Location: index.php?msg=Commande passée avec succès !");
        exit();
    } else {
        echo "Erreur lors de la commande : " . $conn->error;
    }
} else {
    echo "Produit introuvable.";
}

$conn->close();
