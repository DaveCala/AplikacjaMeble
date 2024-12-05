<?php
require_once '../db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? null;
    $title = $_POST['title'] ?? '';
    $ean = $_POST['ean'] ?? '';
    $imagePath = null;

    // Walidacja danych
    if (empty($product_id) || empty($title) || empty($ean)) {
        $response['message'] = 'Wszystkie pola muszą być wypełnione.';
        echo json_encode($response);
        exit;
    }

    // Sprawdzenie, czy produkt o danym ID istnieje w bazie danych
    $stmt = $pdo->prepare("SELECT id FROM products WHERE id = :product_id");
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        $response['message'] = 'Produkt o podanym ID nie istnieje.';
        echo json_encode($response);
        exit;
    }

    // Obsługa pliku obrazu
    if (!empty($_FILES['main_image']['name'])) {
        $targetDir = "../img/";
        $imageFileName = time() . '_' . basename($_FILES['main_image']['name']); // Unikalna nazwa
        $targetFilePath = $targetDir . $imageFileName;
    
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    
        if (!in_array($fileType, $allowedTypes)) {
            $response['message'] = 'Niedozwolony typ pliku. Dozwolone formaty: jpg, jpeg, png, gif.';
            echo json_encode($response);
            exit;
        }
    
        if (!move_uploaded_file($_FILES['main_image']['tmp_name'], $targetFilePath)) {
            $response['message'] = 'Nie udało się przesłać pliku.';
            echo json_encode($response);
            exit;
        }
        $imagePath = $imageFileName;
    }
    
    // Dodanie wariacji do bazy danych
    try {
        $stmt = $pdo->prepare("INSERT INTO variations (product_id, title, ean, main_image, created_at) 
                               VALUES (:product_id, :title, :ean, :main_image, NOW())");
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':ean', $ean);
        $stmt->bindParam(':main_image', $imagePath);

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
