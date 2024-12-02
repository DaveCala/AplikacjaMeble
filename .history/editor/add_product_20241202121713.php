<?php
require_once '../db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $category = $_POST['category'] ?? '';
    $imagePath = null;

    // Walidacja danych
    if (empty($title) || empty($category)) {
        $response['message'] = 'Wszystkie pola muszą być wypełnione.';
        echo json_encode($response);
        exit;
    }

    // Obsługa pliku obrazu
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../img/";  // Katalog, w którym przechowywane będą zdjęcia
        $imageFileName = basename($_FILES['image']['name']);  // Pobieramy nazwę pliku
        $targetFilePath = $targetDir . $imageFileName;  // Pełna ścieżka do pliku na serwerze

        // Sprawdzamy typ pliku
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        if (!in_array($fileType, $allowedTypes)) {
            $response['message'] = 'Nieprawidłowy format pliku.';
            echo json_encode($response);
            exit;
        }

        // Przesyłanie pliku na serwer
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            $response['message'] = 'Błąd przesyłania pliku.';
            echo json_encode($response);
            exit;
        }

        $imagePath = $imageFileName;  // Zapisujemy nazwę pliku
    }

    // Dodanie produktu do bazy danych
    try {
        $stmt = $pdo->prepare("INSERT INTO products (title, category, image) VALUES (:title, :category, :image)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':image', $imagePath);

        if ($stmt->execute()) {
            $response['success'] = true;
        } else {
            $response['message'] = 'Nie udało się dodać produktu.';
        }
    } catch (Exception $e) {
        $response['message'] = 'Błąd: ' . $e->getMessage();
    }
}

echo json_encode($response);
?>
