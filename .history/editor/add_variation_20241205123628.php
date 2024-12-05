<?php
require_once '../db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pobieranie danych z formularza
    $productId = $_POST['product_id'] ?? null;
    $title = $_POST['title'] ?? '';
    $ean = $_POST['ean'] ?? '';
    $mainImage = null;

    // Walidacja danych
    if (empty($productId) || empty($title) || empty($ean)) {
        $response['message'] = 'Wszystkie pola muszą być wypełnione.';
        echo json_encode($response);
        exit;
    }

    // Obsługa pliku obrazu
    if (!empty($_FILES['main_image']['name'])) {
        $targetDir = "../img/"; // Upewnij się, że ta ścieżka jest poprawna
        $imageFileName = basename($_FILES['main_image']['name']);
        $targetFilePath = $targetDir . $imageFileName;

        // Sprawdzanie typu pliku (opcjonalne)
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        if (!in_array($fileType, $allowedTypes)) {
            $response['message'] = 'Nieprawidłowy format pliku.';
            echo json_encode($response);
            exit;
        }

        // Przesyłanie pliku
        if (!move_uploaded_file($_FILES['main_image']['tmp_name'], $targetFilePath)) {
            $response['message'] = 'Błąd przesyłania pliku.';
            echo json_encode($response);
            exit;
        }

        $mainImage = $imageFileName;
    }

    try {
        // Dodanie wariacji do bazy danych
        $stmt = $pdo->prepare("INSERT INTO variations (product_id, title, main_image, ean, created_at) 
                                VALUES (:product_id, :title, :main_image, :ean, NOW())");
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':main_image', $mainImage);
        $stmt->bindParam(':ean', $ean);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Wariacja została dodana pomyślnie.';
        } else {
            $response['message'] = 'Nie udało się dodać wariacji.';
        }
    } catch (Exception $e) {
        $response['message'] = 'Błąd: ' . $e->getMessage();
    }
}

echo json_encode($response);

