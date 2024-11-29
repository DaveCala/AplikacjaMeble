<?php
session_start();
require_once '../db.php'; // Połączenie z bazą danych

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Wyszukiwanie użytkownika w bazie 
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Sprawdzamy, czy hasło pasuje do tego w bazie
        if ($user['password'] === $password) {
            // Poprawne logowanie
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role'],
            ];
            // Przekierowanie do odpowiedniej strony
            header('Location: ../editor/editor_index.php');
            exit;
        } else {
            // Błąd logowania, niepoprawne hasło
            header('Location: index.php?error=true');
            exit;
        }
    } else {
        // Błąd logowania, użytkownik nie istnieje
        header('Location: index.php?error=true');
        exit;
    }
}
