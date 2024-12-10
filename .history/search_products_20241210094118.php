<?php
// Wczytanie konfiguracji bazy danych
include('../db.php');

// Rozpoczęcie sesji (jeśli potrzebujesz)
session_start();

// Pobierz frazę wyszukiwaną z parametru GET
$searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';

try {
    // Jeśli fraza wyszukiwana jest pusta, zwróć wszystkie produkty
    if ($searchQuery === '') {
        $sql = "SELECT * FROM products";
        $stmt = $pdo->query($sql);
    } else {
        // Przygotowanie zapytania SQL z filtrowaniem po nazwie lub kategorii
        $sql = "SELECT * FROM products WHERE title LIKE :query OR category LIKE :query";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['query' => '%' . $searchQuery . '%']);
    }

    // Pobranie wyników
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Zwrócenie wyników w formacie JSON
    echo json_encode($products);

} catch (Exception $e) {
    // W przypadku błędu zwróć komunikat w JSON
    echo json_encode(['error' => 'Wystąpił błąd podczas wyszukiwania produktów']);
}
