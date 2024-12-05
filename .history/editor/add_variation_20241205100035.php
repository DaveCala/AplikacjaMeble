<?php
// Włączanie raportowania błędów, aby łatwiej było znaleźć błędy
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Połączenie z bazą danych
$conn = new mysqli('localhost', 'username', 'password', 'database');

// Sprawdzanie połączenia z bazą danych
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Błąd połączenia z bazą danych.']);
    exit;
}

// Pobieranie danych z formularza
$title = $_POST['title'] ?? '';
$ean = $_POST['ean'] ?? '';
$imagePath = null;

// Obsługa przesyłania pliku
if (!empty($_FILES['main_image']['name'])) {
    $targetDir = 'uploads/'; // Katalog docelowy
    $imagePath = $targetDir . basename($_FILES['main_image']['name']);
    
    // Sprawdzenie, czy plik został poprawnie przesłany
    if (move_uploaded_file($_FILES['main_image']['tmp_name'], $imagePath)) {
        // Jeśli plik jest przesłany, zapisujemy pełną ścieżkę
        $imagePath = $targetDir . $_FILES['main_image']['name'];
    } else {
        // Zwrócenie błędu, jeśli przesyłanie pliku się nie udało
        echo json_encode(['success' => false, 'message' => 'Nie udało się przesłać pliku.']);
        exit;
    }
}

// Przygotowanie zapytania SQL do dodania wariacji
$product_id = 1; // Zakładając, że zawsze dodajesz wariacje do produktu o ID 1
$stmt = $conn->prepare("INSERT INTO variations (product_id, title, main_image, ean, created_at) VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param("isss", $product_id, $title, $imagePath, $ean);

// Wykonanie zapytania
if ($stmt->execute()) {
    // Jeśli zapytanie się powiedzie, zwrócenie odpowiedzi JSON z sukcesem
    echo json_encode(['success' => true, 'message' => 'Wariacja została dodana.']);
} else {
    // Jeśli zapytanie się nie powiedzie, zwrócenie odpowiedzi JSON z błędem
    echo json_encode(['success' => false, 'message' => 'Błąd SQL: ' . $stmt->error]);
}

// Zamknięcie przygotowanego zapytania i połączenia z bazą danych
$stmt->close();
$conn->close();
?>
