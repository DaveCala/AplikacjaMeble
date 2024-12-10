<?php
// Połączenie z bazą danych
require 'db_connection.php';

// Pobranie zapytania
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

$products = [];
if ($query !== '') {
    // Przygotowanie zapytania SQL z wyszukiwaniem
    $stmt = $pdo->prepare("SELECT * FROM products WHERE title LIKE :query OR category LIKE :query");
    $stmt->execute(['query' => '%' . $query . '%']);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
