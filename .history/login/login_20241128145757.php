<?php
session_start();
require_once '../db.php'; // Plik połączenia z bazą danych

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Wyszukiwanie użytkownika w bazie
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Pomyślne logowanie
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
        ];
        // Przekierowanie do odpowiedniej strony
        header('Location: ../editor/editor_index.php');
        exit;
    } else {
        // Błąd logowania
        header('Location: index.php?error=true');
        exit;
    }
}
