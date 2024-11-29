<?php
session_start();
require_once '../db.php'; // Połączenie z bazą danych

// Włączanie wyświetlania błędów
ini_set('display_errors', 1);
error_reporting(E_ALL);

$errorMessage = ''; // Przechowujemy komunikat o błędzie

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Pobieramy dane użytkownika z bazy danych
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Sprawdzamy, czy dane są poprawne
    if ($user && password_verify($password, $user['password'])) {
        // Logowanie zakończone sukcesem - zapisujemy dane w sesji
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
        ];

        // Tutaj nie wykonujemy żadnych przekierowań, tylko po prostu odświeżamy stronę
    } else {
        // Ustawiamy komunikat o błędzie
        $_SESSION['error_message'] = 'Nieprawidłowy login lub hasło';
    }

    // Usuwamy dane z formularza po błędzie
    unset($username);
    unset($password);
}
?>
