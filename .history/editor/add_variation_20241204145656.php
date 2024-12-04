<?php
require_once '../db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $ean = $_POST['ean'] ?? '';
    $product_id = $_POST['product_id'] ?? null;
    $imagePath = null;

    // Walidacja danych
    if (empty($title) || empty($ean) || empty($product_id)) {
        $response['message'] = 'Wszystkie pola muszą być wypełnione.';
        echo json_encode($response);
        exit;
    }

    // Obsługa pliku obrazu
    if (!empty($_FILES['main_image']['name'])) {
        $targetDir = "../img/";
        $imageFileName = basename($_FILES['main_image']['name']);
        $targetFilePath = $targetDir . $imageFileName;

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        if (!in_array($fileType, $allowedTypes)) {
            $response['message'] = 'Nieprawidłowy format pliku.';
            echo json_encode($response);
            exit;
        }

        if (!move_uploaded_file($_FILES['main_image']['tmp_name'], $targetFilePath)) {
            $response['message'] = 'Błąd przesyłania pliku.';
            echo json_encode($response);
            exit;
        }

        $imagePath = $imageFileName;
    }

    // Dodanie wariacji do bazy danych
    try {
        $stmt = $pdo->prepare("INSERT INTO variations (title, ean, main_image, product_id) VALUES (:title, :ean, :main_image, :product_id)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':ean', $ean);
        $stmt->bindParam(':main_image', $imagePath);
        $stmt->bindParam(':product_id', $product_id);

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
