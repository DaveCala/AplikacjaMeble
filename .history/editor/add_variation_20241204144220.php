<?php
require_once '../db.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!empty($_POST['title']) && !empty($_POST['ean']) && !empty($_FILES['main_image']['name'])) {
        $title = $_POST['title'];
        $ean = $_POST['ean'];

        // Obsługa pliku
        $uploadDir = '../img/';
        $fileName = basename($_FILES['main_image']['name']);
        $targetFilePath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['main_image']['tmp_name'], $targetFilePath)) {
            $query = $pdo->prepare("INSERT INTO variations (product_id, title, main_image, ean) VALUES (:product_id, :title, :main_image, :ean)");
            $query->execute([
                'product_id' => $_POST['product_id'],
                'title' => $title,
                'main_image' => $fileName,
                'ean' => $ean
            ]);

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Błąd podczas przesyłania zdjęcia.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Wszystkie pola są wymagane.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Błąd bazy danych: ' . $e->getMessage()]);
}
