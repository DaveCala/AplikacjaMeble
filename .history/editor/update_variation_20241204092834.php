<?php
require_once '../db.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $variationId = $_POST['variation_id'];
    $price = $_POST['price'];
    $stockQuantity = $_POST['stock_quantity'];

    $query = $pdo->prepare("UPDATE variations SET price = :price, stock_quantity = :stock_quantity WHERE id = :variationId");
    $query->execute([
        'price' => $price,
        'stock_quantity' => $stockQuantity,
        'variationId' => $variationId
    ]);

    echo json_encode([
        'success' => true,
        'updatedTitle' => $_POST['title'], // Zaktualizowany tytuł wariacji
        'updatedEAN' => $_POST['ean'] // Zaktualizowany kod EAN
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
