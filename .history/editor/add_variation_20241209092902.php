<?php
require_once '../db.php'; // Połączenie do bazy danych

header('Content-Type: application/json');


// Połączenie z bazą danych
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Błąd połączenia z bazą danych: ' . $conn->connect_error]));
}

// Pobranie danych z formularza
$titles = $_POST['title'] ?? [];
$images = $_POST['image'] ?? [];
$eans = $_POST['ean'] ?? [];

// Sprawdzenie, czy wszystkie pola są wypełnione
if (count($titles) === 0 || count($titles) !== count($images) || count($images) !== count($eans)) {
    echo json_encode(['success' => false, 'message' => 'Nieprawidłowe dane wejściowe.']);
    exit;
}

// Wstawianie danych do bazy
$query = "INSERT INTO variations (product_id, title, image, ean, created_at) VALUES (?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($query);

$product_id = 1; // Przykładowy ID produktu, powinien być dynamicznie ustawiany

foreach ($titles as $index => $title) {
    $image = $images[$index];
    $ean = $eans[$index];
    $stmt->bind_param('isss', $product_id, $title, $image, $ean);
    $stmt->execute();
}

$stmt->close();
$conn->close();

// Zwrócenie sukcesu
echo json_encode(['success' => true]);
