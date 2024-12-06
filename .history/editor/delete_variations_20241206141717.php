<?php
require_once '../db.php'; // Wczytaj konfigurację bazy danych

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => "Błąd połączenia z bazą danych: " . $e->getMessage()]));
}

// Odczytaj dane POST
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['variationIds']) && is_array($data['variationIds'])) {
    $ids = $data['variationIds'];
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $query = $pdo->prepare("DELETE FROM variations WHERE id IN ($placeholders)");
    try {
        $query->execute($ids);
        echo json_encode(['success' => true, 'message' => 'Wariacje zostały usunięte.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "Błąd przy usuwaniu: " . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Nieprawidłowe dane wejściowe.']);
}
