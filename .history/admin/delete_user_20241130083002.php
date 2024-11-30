<?php
require_once '../db.php';
session_start();

// Sprawdzenie, czy użytkownik jest adminem
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login/login_form.php');
    exit;
}

// Sprawdzenie, czy przesłano ID użytkownika
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Zabezpieczenie przed usunięciem samego siebie
    if ($id === $_SESSION['user']['id']) {
        header('Location: users.php?error=self_delete');
        exit;
    }

    // Usuwanie użytkownika z bazy danych
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    if ($stmt->execute([$userId])) {
        $_SESSION['message'] = 'Użytkownik został usunięty.';
    } else {
        $_SESSION['error'] = 'Wystąpił błąd podczas usuwania użytkownika.';
    }
    $stmt->execute([':id' => $id]);

    // Przekierowanie z komunikatem o sukcesie
    header('Location: users.php?success=deleted');
    exit;
} else {
    header('Location: users.php?error=invalid_request');
    exit;
}
