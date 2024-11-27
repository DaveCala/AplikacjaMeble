<?php
session_start();
require_once 'db.php'; // Połącz z bazą danych

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pobierz dane z formularza
    $username = trim($_POST['username']);
    $password = $_POST['password'];


    // Pobierz użytkownika z bazy danych
    $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    // Sprawdź, czy użytkownik istnieje i porównaj hasło
    if ($user && $password === $user['password']) {  // Bez haszowania
        // Poprawne dane logowania, ustaw sesję użytkownika
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role']; // Dodaj inne dane użytkownika, np. email

        // Przekierowanie w zależności od roli
        if ($user['role'] === 'editor') {
            header('Location: editor_index.html');
            exit;
        } else {
            header('Location: viewer_panel.html');
            exit;
        }
    } else {
        // Złe dane logowania
        $_SESSION['error'] = 'Nieprawidłowy login lub hasło!';
        header('Location: login_form.php'); // Przekierowanie z powrotem do formularza
        exit;
    }
}
?>
