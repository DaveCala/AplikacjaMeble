<?php
require_once '../db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $ean = $_POST['ean'] ?? '';
    $productId = $_POST['product_id'] ?? '';
    $imagePath = null;

    // Walidacja danych
    if (empty($title) || empty($ean) || empty($productId)) {
        $response['message'] = 'Wszystkie pola muszą być wypełnione.';
        echo json_encode($response);
        exit;
    }

    // Obsługa obrazu
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../img/variations/";
        $imageFileName = basename($_FILES['image']['name']);
        $targetFilePath = $targetDir . $imageFileName;

        // Sprawdzenie formatu pliku
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        if (!in_array($fileType, $allowedTypes)) {
            $response['message'] = 'Nieprawidłowy format pliku.';
            echo json_encode($response);
            exit;
        }

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            $response['message'] = 'Błąd przesyłania pliku: ' . $_FILES['image']['error'];
            echo json_encode($response);
            exit;
        }

        $imagePath = $imageFileName;
    }

    // Zapis do bazy danych
    try {
        $stmt = $pdo->prepare("INSERT INTO variations (product_id, title, ean, image) VALUES (:product_id, :title, :ean, :image)");
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':ean', $ean);
        $stmt->bindParam(':image', $imagePath);

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
