<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['newUsername']);
    $password = trim($_POST['newPassword']);
    $role = trim($_POST['newRole']);

    // Walidacja
    if (empty($username) || empty($password) || empty($role)) {
        echo json_encode(['success' => false, 'message' => 'Wszystkie pola są wymagane.']);
        exit;
    }

    // Sprawdzenie, czy użytkownik już istnieje
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Użytkownik o takiej nazwie już istnieje.']);
        exit;
    }

    // Hashowanie hasła
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Dodanie użytkownika
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
    $result = $stmt->execute([
        ':username' => $username,
        ':password' => $hashedPassword,
        ':role' => $role
    ]);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nie udało się dodać użytkownika.']);
    }
    exit;
}
