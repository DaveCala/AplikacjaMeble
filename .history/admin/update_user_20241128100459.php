<?php
require_once '../db.php'; // Połączenie z bazą danych

// Pobranie danych z żądania
$data = json_decode(file_get_contents("php://input"), true);

// Sprawdzanie, czy dane są poprawne
if (isset($data['id'], $data['username'], $data['password'], $data['role'])) {
    $id = $data['id'];
    $username = $data['username'];
    $password = $data['password'];
    $role = $data['role'];

    // Aktualizacja w bazie danych
    $stmt = $pdo->prepare("UPDATE users SET username = :username, password = :password, role = :role WHERE id = :id");
    $success = $stmt->execute([
        'username' => $username,
        'password' => $password,
        'role' => $role,
        'id' => $id
    ]);

    // Odpowiedź JSON
    echo json_encode(['success' => $success]);
} else {
    echo json_encode(['success' => false, 'error' => 'Niepoprawne dane']);
}
