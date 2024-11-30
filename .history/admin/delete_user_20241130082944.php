<?php
require_once '../db.php';
session_start();

// Sprawdzenie, czy użytkownik jest adminem
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login/login_form.php');
    exit;
}

// Sprawdzenie, czy przesłano ID użytkownika
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['id'];

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    if ($stmt->execute([$userId])) {
        $_SESSION['message'] = 'Użytkownik został usunięty.';
    } else {
        $_SESSION['error'] = 'Wystąpił błąd podczas usuwania użytkownika.';
    }

    header('Location: users.php');
    exit;
}
?>