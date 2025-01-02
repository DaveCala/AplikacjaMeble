<?php
require_once '../db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'] ?? 0;
    $title = $_POST['title'] ?? '';
    $category = $_POST['category'] ?? '';

    if (empty($title) || empty($category) || empty($productId)) {
        $response['message'] = 'Wszystkie pola muszą być wypełnione.';
        echo json_encode($response);
        exit;
    }

    // Obsługa przesyłania obrazu
    $imagePath = null;
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../img/";
        $imageFileName = basename($_FILES['image']['name']);
        $targetFilePath = $targetDir . $imageFileName;

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        if (!in_array($fileType, $allowedTypes)) {
            $response['message'] = 'Nieprawidłowy format pliku. Dozwolone są JPG, JPEG, PNG, GIF.';
            echo json_encode($response);
            exit;
        }

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            $response['message'] = 'Błąd podczas przesyłania pliku.';
            echo json_encode($response);
            exit;
        }

        $imagePath = $imageFileName;
    }

    // Dynamiczne budowanie zapytania SQL
    $fieldsToUpdate = ['title' => $title, 'category' => $category];
    if ($imagePath) {
        $fieldsToUpdate['image'] = $imagePath;
    }

    // Dodawanie dynamicznych pól do zapytania
    foreach ($_POST as $key => $value) {
        if (!in_array($key, ['product_id', 'title', 'category']) && !empty($value)) {
            $fieldsToUpdate[$key] = $value;
        }
    }

    try {
        $query = "UPDATE products SET ";
        $setClauses = [];
        foreach ($fieldsToUpdate as $field => $value) {
            $setClauses[] = "`$field` = :$field";
        }
        $query .= implode(', ', $setClauses);
        $query .= " WHERE id = :id";

        $stmt = $pdo->prepare($query);
        foreach ($fieldsToUpdate as $field => $value) {
            $stmt->bindValue(":$field", $value);
        }
        $stmt->bindValue(':id', $productId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Produkt został zaktualizowany.';
        } else {
            $response['message'] = 'Wystąpił problem podczas aktualizacji produktu.';
        }
    } catch (Exception $e) {
        $response['message'] = 'Błąd: ' . $e->getMessage();
    }
}

echo json_encode($response);
