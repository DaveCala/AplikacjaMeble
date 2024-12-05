<?php
require_once '../db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sprawdzamy czy dane są przesyłane
    $productId = $_POST['product_id'] ?? '';
    $title = $_POST['title'] ?? '';
    $ean = $_POST['ean'] ?? '';
    $imagePath = null;

    // Walidacja danych
    if (empty($productId) || empty($title) || empty($ean)) {
        $response['message'] = 'Wszystkie pola muszą być wypełnione.';
        echo json_encode($response);
        exit;
    }

    // Obsługa pliku obrazu
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../img/variations/";
        $imageFileName = basename($_FILES['image']['name']);
        $targetFilePath = $targetDir . $imageFileName;

        // Sprawdzamy typ pliku
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        if (!in_array($fileType, $allowedTypes)) {
            $response['message'] = 'Nieprawidłowy format pliku.';
            echo json_encode($response);
            exit;
        }

        // Przesyłanie pliku
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            $response['message'] = 'Błąd przesyłania pliku: ' . $_FILES['image']['error'];
            echo json_encode($response);
            exit;
        }

        $imagePath = $imageFileName;
    }

    // Dodanie wariacji do bazy danych
    try {
        $stmt = $pdo->prepare("INSERT INTO variations (product_id, title, image, ean) VALUES (:product_id, :title, :image, :ean)");
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':image', $imagePath);
        $stmt->bindParam(':ean', $ean);

        if ($stmt->execute()) {
            $response['success'] = true;
        } else {
            $response['message'] = 'Nie udało się dodać wariacji. Błąd SQL: ' . implode(" ", $stmt->errorInfo());
        }
    } catch (Exception $e) {
        $response['message'] = 'Błąd: ' . $e->getMessage();
    }
}

echo json_encode($response);
?>
