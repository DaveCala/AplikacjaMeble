<?php
// delete_variations.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pobierz dane JSON przesłane z frontendu
    $input = json_decode(file_get_contents('php://input'), true);

    // Sprawdź, czy otrzymano poprawne dane
    if (isset($input['ids']) && is_array($input['ids'])) {
        $ids = array_map('intval', $input['ids']); // Zabezpieczenie przed SQL Injection

        // Połącz z bazą danych (przykład z użyciem PDO)
        $dsn = 'mysql:host=localhost;dbname=twoja_baza;charset=utf8';
        $username = 'twoj_uzytkownik';
        $password = 'twoje_haslo';

        try {
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            // Usuń wariacje
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("DELETE FROM variations WHERE id IN ($placeholders)");
            $stmt->execute($ids);

            // Zwróć sukces
            echo json_encode(['success' => true, 'deleted_ids' => $ids]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Niepoprawne dane wejściowe.']);
    }
} else {
    http_response_code(405); // Metoda nieobsługiwana
    echo json_encode(['success' => false, 'error' => 'Dozwolona jest tylko metoda POST.']);
}
