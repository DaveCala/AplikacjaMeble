<?php
require_once '../db.php'; // Połączenie z bazą danych

// Odczytaj dane z żądania JSON
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'], $data['username'], $data['role'])) {
    echo json_encode(['success' => false, 'message' => 'Brak wymaganych danych (id, username, rola)']);
    exit;
}

$id = (int)$data['id'];
$username = trim($data['username']);
$password = isset($data['password']) ? trim($data['password']) : null;
$role = trim($data['role']);

// Walidacja danych
if (empty($username) || empty($role)) {
    echo json_encode(['success' => false, 'message' => 'Nazwa użytkownika i rola są wymagane']);
    exit;
}

// Sprawdzenie, czy użytkownik istnieje
$stmt = $pdo->prepare("SELECT id, password FROM users WHERE id = :id");
$stmt->execute([':id' => $id]);
$currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$currentUser) {
    echo json_encode(['success' => false, 'message' => 'Użytkownik nie istnieje']);
    exit;
}

// Aktualizacja hasła, jeśli zostało zmienione
$hashedPassword = $currentUser['password'];
if (!empty($password)) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
}

// Aktualizacja danych użytkownika
$stmt = $pdo->prepare("UPDATE users SET username = :username, password = :password, role = :role WHERE id = :id");
$success = $stmt->execute([
    ':username' => $username,
    ':password' => $hashedPassword,
    ':role' => $role,
    ':id' => $id,
]);

if ($success) {
    echo json_encode(['success' => true, 'message' => 'Użytkownik został zaktualizowany']);
} else {
    echo json_encode(['success' => false, 'message' => 'Nie udało się zaktualizować użytkownika']);
}
exit;
