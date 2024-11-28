<?php
session_start();
require_once 'db.php'; // Załóżmy, że masz plik do połączenia z bazą danych

// Sprawdzamy, czy użytkownik ma odpowiednią rolę (admin)
if ($_SESSION['role'] !== 'admin') {
    die('Brak uprawnień');
}

// Pobieramy ID użytkownika z query string (np. /admin/users.php?id=4)
$user_id = $_GET['id'] ?? null;
if ($user_id) {
    // Pobieramy dane użytkownika
    $stmt = $pdo->prepare("SELECT id, username, role FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        die("Użytkownik nie istnieje.");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sprawdzamy, czy formularz został wysłany
    $new_role = $_POST['role'];

    // Aktualizujemy rolę użytkownika w bazie
    $stmt = $pdo->prepare("UPDATE users SET role = :role WHERE id = :id");
    $stmt->execute(['role' => $new_role, 'id' => $user_id]);

    // Zaktualizuj sesję, aby użytkownik miał aktualną rolę
    $_SESSION['role'] = $new_role;  // Tutaj aktualizujemy rolę w sesji

    // Możemy także zaktualizować nazwę użytkownika, jeśli trzeba
    $_SESSION['username'] = $user['username'];

    // Przekierowanie po zmianie roli
    header('Location: users.php'); // Możesz przekierować na stronę z listą użytkowników
    exit;
}
?>

<!-- Formularz do zmiany roli -->
<form method="POST">
    <label for="role">Rola:</label>
    <select name="role" id="role">
        <option value="editor" <?php if ($user['role'] === 'editor') echo 'selected'; ?>>Editor</option>
        <option value="admin" <?php if ($user['role'] === 'admin') echo 'selected'; ?>>Admin</option>
    </select>
    <button type="submit">Zapisz</button>
</form>
