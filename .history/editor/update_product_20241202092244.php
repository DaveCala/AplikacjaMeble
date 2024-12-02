<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];

    $title = $_POST['title'] ?? '';
    $category = $_POST['category'] ?? '';
    $productId = $_POST['product_id'] ?? 0;

    if (empty($title) || empty($category) || empty($productId)) {
        $response['message'] = 'Wszystkie pola muszą być wypełnione.';
        echo json_encode($response);
        exit;
    }

    $imagePath = null;
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../img/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

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

    try {
        $query = "UPDATE products SET title = :title, category = :category";
        if ($imagePath) {
            $query .= ", image = :image";
        }
        $query .= " WHERE id = :id";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':category', $category);
        if ($imagePath) {
            $stmt->bindParam(':image', $imagePath);
        }
        $stmt->bindParam(':id', $productId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Produkt został zaktualizowany.';
        } else {
            $response['message'] = 'Nie udało się zaktualizować produktu.';
        }
    } catch (Exception $e) {
        $response['message'] = 'Błąd: ' . $e->getMessage();
    }

    echo json_encode($response);
}
?>
