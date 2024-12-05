<?php
// Połączenie z bazą danych
require_once '../db.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => "Błąd połączenia z bazą danych: " . $e->getMessage()]));
}

// Obsługa dodawania wariacji
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'] ?? null;
    $title = $_POST['title'] ?? null;
    $ean = $_POST['ean'] ?? null;

    // Walidacja danych
    if (!$productId || !$title || !$ean) {
        echo json_encode(['success' => false, 'message' => 'Wszystkie pola są wymagane.']);
        exit;
    }

    // Obsługa zdjęcia
    $mainImage = null;
    if (!empty($_FILES['main_image']['name'])) {
        $targetDir = "../img/";
        $fileName = basename($_FILES['main_image']['name']);
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['main_image']['tmp_name'], $targetFilePath)) {
            $mainImage = $fileName;
        } else {
            echo json_encode(['success' => false, 'message' => 'Nie udało się przesłać zdjęcia.']);
            exit;
        }
    }

    // Dodanie nowej wariacji
    $query = $pdo->prepare("
        INSERT INTO variations (product_id, title, main_image, ean, created_at) 
        VALUES (:productId, :title, :mainImage, :ean, NOW())
    ");
    $result = $query->execute([
        'productId' => $productId,
        'title' => $title,
        'mainImage' => $mainImage,
        'ean' => $ean,
    ]);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Wariacja została dodana.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nie udało się dodać wariacji.']);
    }
}
