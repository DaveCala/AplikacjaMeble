<?php
session_start();
require_once '../db.php'; // Połączenie z bazą danych

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Pobranie użytkownika z bazy
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (password_verify($password, $user['password'])) {
            // Poprawne logowanie z hasłem zahashowanym
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role'],
            ];
            header('Location: ../editor/editor_index.php');
            exit;
        } elseif ($user['password'] === $password) {
            // Hasło w bazie w postaci zwykłego tekstu - poprawne logowanie
            // Automatyczne hashowanie hasła w bazie
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $updateStmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
            $updateStmt->execute([':password' => $hashedPassword, ':id' => $user['id']]);

            // Zaloguj użytkownika
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role'],
            ];
            header('Location: ../editor/editor_index.php');
            exit;
        } else {
            // Hasło nie jest poprawne
            header('Location: index.php?error=true');
            exit;
        }
    } else {
        // Nie znaleziono użytkownika
        header('Location: index.php?error=true');
        exit;
    }
}
