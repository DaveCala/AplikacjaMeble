<?php
require_once '../db.php'; // Połączenie do bazy danych

header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Błąd połączenia z bazą danych: ' . $e->getMessage()]);
    exit;
}

// Sprawdzenie, czy dane zostały przesłane
if (isset($_POST['title'], $_POST['ean'], $_POST['product_id'])) {
    $title = $_POST['title'];
    $ean = $_POST['ean'];
    $productId = $_POST['product_id'];

    // Sprawdzenie, czy zostało przesłane nowe zdjęcie
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        $imageName = basename($_FILES['main_image']['name']);
        $targetPath = "../img/" . $imageName;

        // Przeniesienie przesłanego pliku
        if (move_uploaded_file($_FILES['main_image']['tmp_name'], $targetPath)) {
            $sql = "INSERT INTO variations (product_id, title, ean, main_image) VALUES (:product_id, :title, :ean, :main_image)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':product_id' => $productId,
                ':title' => $title,
                ':ean' => $ean,
                ':main_image' => $imageName
            ]);
            echo json_encode(['success' => true, 'message' => 'Wariacja została dodana']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Nie udało się przesłać zdjęcia.']);
            exit;
        }
    } else {
        // Dodanie wariacji bez zdjęcia
        $sql = "INSERT INTO variations (product_id, title, ean) VALUES (:product_id, :title, :ean)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':product_id' => $productId,
            ':title' => $title,
            ':ean' => $ean
        ]);
        echo json_encode(['success' => true, 'message' => 'Wariacja została dodana']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Brak wymaganych danych.']);
}
?>