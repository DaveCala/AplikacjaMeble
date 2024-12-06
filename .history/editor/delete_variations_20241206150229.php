<?php
header('Content-Type: application/json');
try {
    $input = json_decode(file_get_contents('php://input'), true);
    $ids = $input['ids'] ?? [];

    if (empty($ids)) {
        echo json_encode(['success' => false, 'error' => 'Brak ID do usunięcia.']);
        exit;
    }

    $dsn = "mysql:host=localhost;dbname=twoja_baza;charset=utf8mb4";
    $username = "root";
    $password = "";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("DELETE FROM variations WHERE id IN ($placeholders)");
    $stmt->execute($ids);

    echo json_encode(['success' => true, 'deleted_ids' => $ids]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
