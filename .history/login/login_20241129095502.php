<?php
session_start();
require_once '../db.php'; // Połączenie z bazą danych


var_dump($user);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Pobieranie użytkownika z bazy danych
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Logowanie zakończone sukcesem
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
        ];
        header('Location: ../editor/editor_index.php'); // Ścieżka do panelu
        exit;
    } else {
        // Logowanie nieudane
        header('Location: ../viewer/viewer_index.php?error=true'); // Powrót do formularza
        exit;
    }
}

     else {
        // Błąd logowania, użytkownik nie istnieje
        header('Location: index.php?error=true');
        exit;
    }

    
