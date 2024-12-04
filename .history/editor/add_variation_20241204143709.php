<?php
require_once '../db.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Pobranie danych z żądania POST
    $title = $_POST['title'] ?? '';
    $ean = $_POST['ean'] ?? '';
    $description = $_POST['description'] ?? '';
    $productId = $_POST['product_id'] ?? 0;
    $mainImage = $_FILES['main_image'] ?? null;

    // Sprawdzenie poprawności danych
    if (empty($title) || empty($ean) || empty($productId) || !$mainImage) {
        echo json_encode(['success' => false, 'message' => 'Wszystkie pola są wymagane.']);
        exit;
    }

    // Przesyłanie pliku
    $uploadDir = '../img/';
    $fileName = time() . '_' . basename($mainImage['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($mainImage['tmp_name'], $targetPath)) {
        // Dodanie wariacji do bazy danych
        $stmt = $pdo->prepare("INSERT INTO variations (product_id, title, ean, description, main_image) VALUES (:product_id, :title, :ean, :description, :main_image)");
        $stmt->execute([
            'product_id' => $productId,
            'title' => $title,
            'ean' => $ean,
            'main_image' => $fileName
        ]);

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nie udało się przesłać pliku.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Błąd bazy danych: ' . $e->getMessage()]);
}
