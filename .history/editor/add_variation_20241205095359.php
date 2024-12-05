<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); // Wyłącz wyświetlanie błędów na stronie
ini_set('log_errors', 1);
ini_set('error_log', 'path/to/error_log.txt'); // Zapisz błędy do pliku

header('Content-Type: application/json');
$conn = new mysqli('localhost', 'username', 'password', 'database');

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Błąd połączenia z bazą danych.']);
    exit;
}

// Pobranie danych z formularza
$title = $_POST['title'] ?? '';
$ean = $_POST['ean'] ?? '';
$imagePath = null;

// Obsługa przesyłania pliku
if (!empty($_FILES['main_image']['name'])) {
    $targetDir = 'uploads/';
    $imagePath = $targetDir . basename($_FILES['main_image']['name']);
    if (move_uploaded_file($_FILES['main_image']['tmp_name'], $imagePath)) {
        $imagePath = $targetDir . $_FILES['main_image']['name'];
    } else {
        echo json_encode(['success' => false, 'message' => 'Nie udało się przesłać pliku.']);
        exit;
    }
}

// Wstawienie danych do bazy
$product_id = 1; // Zakładam, że zawsze dodajesz wariacje do produktu o ID 1
$stmt = $conn->prepare("INSERT INTO variations (product_id, title, main_image, ean, created_at) VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param("isss", $product_id, $title, $imagePath, $ean);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Wariacja została dodana.']);
    exit();
} else {
    echo json_encode(['success' => false, 'message' => 'Błąd SQL: ' . $stmt->error]);
    exit();
}

$stmt->close();
$conn->close();
?>
