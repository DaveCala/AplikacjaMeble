<?php
require_once '../db.php';

$response = ['success' => false, 'message' => 'Nieznany błąd'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $productId = (int)$_POST['product_id'];
        $title = $_POST['title'];
        $category = $_POST['category'];
        $description = $_POST['description'];
        $image = null;

        // Obsługa zdjęcia (opcjonalna)
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = '/uploads/' . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . $image);
        }

        // Aktualizacja produktu
        $query = "UPDATE products SET title = :title, category = :category, description = :description";
        if ($image) {
            $query .= ", image = :image";
        }
        $query .= " WHERE id = :id";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
        if ($image) {
            $stmt->bindParam(':image', $image);
        }

        $stmt->execute();

        $response['success'] = true;
        $response['message'] = 'Produkt zaktualizowany pomyślnie.';
    } catch (PDOException $e) {
        $response['message'] = 'Błąd bazy danych: ' . $e->getMessage();
    }
}

echo json_encode($response);
