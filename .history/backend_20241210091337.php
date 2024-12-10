<?php
// Połączenie z bazą danych
require 'db_connection.php';

// Ustaw nagłówek JSON
header('Content-Type: application/json');

// Odczytaj dane wejściowe
$data = json_decode(file_get_contents('php://input'), true);
$search = isset($data['search']) ? trim($data['search']) : '';

$response = ['products' => []];

if ($search !== '') {
    // Przygotowanie zapytania SQL
    $stmt = $pdo->prepare("SELECT id, title, category, image FROM products WHERE title LIKE :search OR category LIKE :search");
    $stmt->execute(['search' => '%' . $search . '%']);
    $response['products'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Zwróć dane jako JSON
echo json_encode($response);
