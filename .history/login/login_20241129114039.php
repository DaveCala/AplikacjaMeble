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
        // Przekierowanie po zalogowaniu
        header('Location: ../editor/editor_index.php');
        exit;
    } else {
        // Ustawiamy komunikat o błędzie w sesji, aby przekazać go na stronę logowania
        $_SESSION['error_message'] = 'Nieprawidłowy login lub hasło';
    }
}
?>

<!-- Następnie w pliku login_form.php odwołujemy się do zmiennej sesyjnej -->
<?php
header('Location: login_form.php'); // Zostajemy na tej samej stronie
exit;
?>
