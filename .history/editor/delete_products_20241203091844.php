<?php
// Połączenie z bazą danych
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pobranie danych z żądania
    $productIds = json_decode(file_get_contents('php://input'), true);

    if (!empty($productIds)) {
        // Konwersja ID na ciąg zapytań
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));

        $stmt = $pdo->prepare("DELETE FROM products WHERE id IN ($placeholders)");

        if ($stmt->execute($productIds)) {
            echo json_encode(['success' => true, 'message' => 'Produkty zostały usunięte.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Nie udało się usunąć produktów.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Nie podano żadnych produktów do usunięcia.']);
    }
}
?>
