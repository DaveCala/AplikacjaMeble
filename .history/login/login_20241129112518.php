<?php
session_start();
require_once '../db.php'; // Połączenie z bazą danych

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

        // Przekierowanie w zależności od roli
        switch ($user['role']) {
            case 'admin':
                header('Location: ../editor/editor_index.php');
                break;
            case 'editor':
                header('Location: ../editor/editor_index.php');
                break;
            case 'viewer':
                header('Location: ../viewer/viewer_index.php');
                break;
        }
        exit;
    } else {
        // Zapisanie komunikatu o błędzie
        $errorMessage = 'Nieprawidłowy login lub hasło';
    }
}
