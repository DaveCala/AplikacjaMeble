<?php
require_once '../db.php'; // Połączenie z bazą danych

// Sprawdzamy, czy dane zostały przesłane przez POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];

    // Pobierz dane z formularza
    $title = $_POST['title'] ?? '';
    $category = $_POST['category'] ?? '';
    $productId = $_POST['product_id'] ?? 0;

    // Walidacja danych
    if (empty($title) || empty($category) || empty($productId)) {
        $response['message'] = 'Wszystkie pola muszą być wypełnione.';
        echo json_encode($response);
        exit;
    }

    // Sprawdź, czy przesłano plik obrazu
    $imagePath = null;
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../img/";
        $imageFileName = basename($_FILES['image']['name']);
        $targetFilePath = $targetDir . $imageFileName;

        // Sprawdź typ pliku
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        if (!in_array($fileType, $allowedTypes)) {
            $response['message'] = 'Nieprawidłowy format pliku. Dozwolone są JPG, JPEG, PNG, GIF.';
            echo json_encode($response);
            exit;
        }

        // Przenieś przesłany plik do katalogu docelowego
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            $response['message'] = 'Błąd podczas przesyłania pliku. Spróbuj ponownie.';
            echo json_encode($response);
            exit;
        }

        // Przypisz ścieżkę do pliku
        $imagePath = $imageFileName;
    }

    // Aktualizacja danych w bazie
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
            $response['message'] = 'Produkt został pomyślnie zaktualizowany.';
        } else {
            $response['message'] = 'Wystąpił problem podczas aktualizacji produktu.';
        }
    } catch (Exception $e) {
        $response['message'] = 'Błąd: ' . $e->getMessage();
    }

    echo json_encode($response);
}
