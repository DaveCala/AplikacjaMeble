<?php
require_once '../db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'] ?? null; // Odbierz ID produktu
    $title = $_POST['title'] ?? '';
    $price = $_POST['price'] ?? 0;

    // Walidacja danych
    if (empty($productId) || empty($title) || empty($price)) {
        $response['message'] = 'Wszystkie pola muszą być wypełnione.';
        echo json_encode($response);
        exit;
    }

    try {
        // Dodanie wariacji do bazy danych
        $stmt = $pdo->prepare("INSERT INTO variations (product_id, title, price) VALUES (:product_id, :title, :price)");
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':price', $price);

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
