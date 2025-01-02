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

// Zakładając, że masz dostęp do identyfikatora produktu (np. z sesji)
$productId = $_POST['product_id'] ?? null;

// Sprawdzenie, czy dane zostały przesłane
if (isset($_POST['title'], $_POST['ean'], $_POST['price'], $_POST['description'], $productId)) {
    $title = $_POST['title'];
    $ean = $_POST['ean'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    try {
        // Sprawdzenie, czy product_id istnieje w tabeli products
        $sqlCheckProduct = "SELECT id FROM products WHERE id = :product_id";
        $stmtCheckProduct = $pdo->prepare($sqlCheckProduct);
        $stmtCheckProduct->execute([':product_id' => $productId]);

        // Sprawdzanie, czy produkt istnieje w tabeli products
        if ($stmtCheckProduct->rowCount() === 0) {
            echo json_encode(['success' => false, 'message' => 'Nieprawidłowy identyfikator produktu.']);
            exit;
        }

        // Sprawdzenie, czy zostało przesłane nowe zdjęcie
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
            $imageName = basename($_FILES['main_image']['name']);
            $targetPath = "../img/" . $imageName;

            // Przeniesienie przesłanego pliku
            if (move_uploaded_file($_FILES['main_image']['tmp_name'], $targetPath)) {
                // Zapytanie do bazy danych
                $sql = "INSERT INTO variations (product_id, title, ean, main_image, price, description) 
                        VALUES (:product_id, :title, :ean, :main_image, :price, :description)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':product_id' => $productId,  // Automatycznie przypisane ID produktu
                    ':title' => $title,
                    ':ean' => $ean,
                    ':main_image' => $imageName,
                    ':price' => $price,
                    ':description' => $description
                ]);
                echo json_encode(['success' => true, 'message' => 'Wariacja została dodana']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Nie udało się przesłać zdjęcia.']);
                exit;
            }
        } else {
            // Dodanie wariacji bez zdjęcia
            $sql = "INSERT INTO variations (product_id, title, ean, price, description) 
                    VALUES (:product_id, :title, :ean, :price, :description)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':product_id' => $productId,  // Automatycznie przypisane ID produktu
                ':title' => $title,
                ':ean' => $ean,
                ':price' => $price,
                ':description' => $description
            ]);
            echo json_encode(['success' => true, 'message' => 'Wariacja została dodana']);
        }
    } catch (PDOException $e) {
        // Sprawdzanie błędów i pomijanie błędów dotyczących klucza obcego
        if ($e->getCode() === '23000') {
            echo json_encode(['success' => false, 'message' => 'Błąd klucza obcego.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Błąd podczas zapisu do bazy danych: ' . $e->getMessage()]);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Brak wymaganych danych.']);
}

exit;
