<?php
session_start();
require_once '../db.php'; // Połączenie z bazą danych

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Pobieramy dane użytkownika z bazy danych
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Logowanie zakończone sukcesem - zapisujemy dane w sesji
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
        ];

        // Przekierowanie w zależności od roli
        if ($user['role'] === 'admin') {
            header('Location: ../editor/editor_index.php'); // Przekierowanie dla admina
            exit;
        } else if ($user['role'] === 'editor') {
            header('Location: ../editor/editor_index.php'); // Przekierowanie dla edytora
            exit;
        } else if ($user['role'] === 'viewer') {
            header('Location: ../viewer/viewer_index.php'); // Przekierowanie dla widza
            exit;
        }
    } else {
        // Ustawiamy komunikat o błędzie w sesji, aby przekazać go na stronę logowania
        $_SESSION['error_message'] = 'Nieprawidłowy login lub hasło';
        header('Location: login_form.php'); // Zostajemy na stronie login_form.php
        exit;
    }
}
?>
