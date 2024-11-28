<?php
require_once '../db.php'; // Połączenie z bazą danych

// Odczytaj dane z żądania JSON
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'], $data['username'], $data['password'], $data['role'])) {
    echo json_encode(['success' => false, 'message' => 'Brak wymaganych danych']);
    exit;
}

$id = $data['id'];
$username = trim($data['username']);
$password = trim($data['password']);
$role = trim($data['role']);

// Sprawdź, czy hasło zostało zmienione
$stmt = $pdo->prepare("SELECT password FROM users WHERE id = :id");
$stmt->execute([':id' => $id]);
$currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$currentUser) {
    echo json_encode(['success' => false, 'message' => 'Użytkownik nie istnieje']);
    exit;
}

$hashedPassword = $currentUser['password'];
if (!empty($password)) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
}

// Aktualizacja użytkownika w bazie danych
$stmt = $pdo->prepare("UPDATE users SET username = :username, password = :password, role = :role WHERE id = :id");
$success = $stmt->execute([
    ':username' => $username,
    ':password' => $hashedPassword,
    ':role' => $role,
    ':id' => $id,
]);

if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Nie udało się zaktualizować użytkownika']);
}
?>
