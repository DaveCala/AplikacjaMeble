<?php
require_once '../db.php'; // Połączenie do bazy danych

header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Błąd połączenia z bazą danych: ' . $e->getMessage()]);
    exit;
}

// Sprawdzenie, czy wszystkie dane zostały przesłane
if (isset($_POST['title'], $_POST['ean'], $_POST['variation_id'])) {
    $title = $_POST['title'];
    $ean = $_POST['ean'];
    $variationId = $_POST['variation_id'];

    $updateFields = [
        ':title' => $title,
        ':ean' => $ean,
        ':id' => $variationId
    ];

    // Sprawdzenie, czy zostało przesłane nowe zdjęcie
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        $imageName = basename($_FILES['main_image']['name']);
        $targetPath = "../img/" . $imageName;

        // Przeniesienie przesłanego pliku do docelowego folderu
        if (move_uploaded_file($_FILES['main_image']['tmp_name'], $targetPath)) {
            $updateFields[':main_image'] = $imageName;

            // Aktualizacja z uwzględnieniem zdjęcia
            $sql = "UPDATE variations SET title = :title, ean = :ean, main_image = :main_image WHERE id = :id";
        } else {
            echo json_encode(['success' => false, 'message' => 'Nie udało się przesłać zdjęcia.']);
            exit;
        }
    } else {
        // Aktualizacja bez zmiany zdjęcia
        $sql = "UPDATE variations SET title = :title, ean = :ean WHERE id = :id";
    }

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($updateFields);

        echo json_encode([
            'success' => true,
            'updatedTitle' => $title,
            'updatedEAN' => $ean,
            'updatedImage' => $updateFields[':main_image'] ?? null
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Błąd aktualizacji: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Nieprawidłowe dane wejściowe.']);
}
