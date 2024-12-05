<?php
require_once '../db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'] ?? null;
    $title = $_POST['title'] ?? '';
    $ean = $_POST['ean'] ?? '';  // EAN
    $imagePath = null;

    // Walidacja danych
    if (empty($productId) || empty($title) || empty($ean)) {
        $response['message'] = 'Wszystkie pola muszą być wypełnione.';
        echo json_encode($response);
        exit;
    }

    // Sprawdzenie, czy produkt o podanym ID istnieje
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE id = :product_id");
    $stmt->bindParam(':product_id', $productId);
    $stmt->execute();
    $productExists = $stmt->fetchColumn();

    if (!$productExists) {
        $response['message'] = 'Produkt o podanym ID nie istnieje.';
        echo json_encode($response);
        exit;
    }

    // Obsługa pliku obrazu (jeśli jest)
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
            $response['message'] = 'Błąd przesyłania pliku.';
            echo json_encode($response);
            exit;
        }

        $imagePath = $imageFileName;
    }

    // Dodanie wariacji do bazy danych
    try {
        $stmt = $pdo->prepare("INSERT INTO variations (product_id, title, ean, image) VALUES (:product_id, :title, :ean, :image)");
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':ean', $ean);
        $stmt->bindParam(':image', $imagePath);

        if ($stmt->execute()) {
            $response['success'] = true;
        } else {
            $response['message'] = 'Błąd SQL: ' . implode(" ", $stmt->errorInfo());
        }
    } catch (Exception $e) {
        $response['message'] = 'Błąd: ' . $e->getMessage();
    }
}

echo json_encode($response);
?>
