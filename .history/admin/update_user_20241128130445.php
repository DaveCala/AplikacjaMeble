<?php
require_once '../db.php'; // Połączenie z bazą danych
session_start(); // Upewnij się, że sesja jest rozpoczęta

// Obsługujemy żądania POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pobieramy dane z żądania JSON
    $data = json_decode(file_get_contents('php://input'), true);

    // Sprawdzamy, czy dane są kompletne
    if (isset($data['id'], $data['username'], $data['password'], $data['role'])) {
        $id = intval($data['id']);
        $username = trim($data['username']);
        $password = trim($data['password']);
        $role = trim($data['role']);

        try {
            // Aktualizacja danych użytkownika w bazie
            $stmt = $pdo->prepare("UPDATE users SET username = :username, password = :password, role = :role WHERE id = :id");
            $stmt->execute([
                ':id' => $id,
                ':username' => $username,
                ':password' => $password,
                ':role' => $role,
            ]);

            // Jeżeli to jest aktualizowany zalogowany użytkownik, zaktualizuj sesję
            if ($_SESSION['user']['id'] == $id) {
                // Zaktualizuj rolę w sesji
                $_SESSION['user']['role'] = $role;
            }

            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Błąd bazy danych: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Niekompletne dane wejściowe']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Nieobsługiwana metoda HTTP']);
}
?>
