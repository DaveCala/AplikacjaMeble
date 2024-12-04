<?php
require_once '../db.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Walidacja danych
    if (!isset($_POST['title'], $_POST['ean'], $_POST['price'], $_POST['stock_quantity'])) {
        echo json_encode(['success' => false, 'message' => 'Nieprawidłowe dane wejściowe.']);
        exit;
    }

    $title = $_POST['title'];
    $ean = $_POST['ean'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    $productId = $_POST['product_id'];

    // Przesyłanie pliku
    $mainImage = '';
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
        $mainImage = basename($_FILES['main_image']['name']);
        move_uploaded_file($_FILES['main_image']['tmp_name'], "../img/$mainImage");
    }

    // Dodawanie wariacji do bazy danych
    $stmt = $pdo->prepare("INSERT INTO variations (product_id, title, ean, price, stock_quantity, main_image) 
                           VALUES (:productId, :title, :ean, :price, :stock_quantity, :main_image)");
    $stmt->execute([
        'productId' => $productId,
        'title' => $title,
        'ean' => $ean,
        'price' => $price,
        'stock_quantity' => $stock_quantity,
        'main_image' => $mainImage
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
