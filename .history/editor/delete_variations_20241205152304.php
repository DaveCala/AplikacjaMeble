<?php
// Dołącz plik z połączeniem do bazy danych
include('../db.php');
session_start();

// Sprawdzenie, czy użytkownik jest zalogowany
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['username'])) {
  header('Location: ../index.php?error=not_logged_in');
  exit;
}

// Pobieranie danych z POST
$data = json_decode(file_get_contents('php://input'), true);

// Sprawdzenie, czy są przekazane ID wariacji
if (is_array($data) && count($data) > 0) {
    try {
        // Usuwanie wariacji z bazy danych
        $sql = "DELETE FROM variations WHERE id IN (" . implode(',', array_map('intval', $data)) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Wariacje zostały usunięte.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Błąd podczas usuwania: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Nieprawidłowe dane wejściowe.']);
}
?>
