<?php
header('Content-Type: application/json'); // Crucial: Set the content type

// Inicjalizacja zmiennych
$success = false;
$error_message = '';

// Połączenie z bazą danych (upewnij się, że odpowiednie dane są wprowadzone)
$conn = new mysqli('localhost', 'username', 'password', 'database');

// Sprawdzanie połączenia
if ($conn->connect_error) {
    $error_message = "Błąd połączenia z bazą danych.";
} else {
    // Pobieranie danych z formularza
    $title = $_POST['title'] ?? '';
    $ean = $_POST['ean'] ?? '';
    $imagePath = null;

    // Obsługa przesyłania pliku
    if (!empty($_FILES['main_image']['name'])) {
        $targetDir = 'uploads/';
        $imagePath = $targetDir . basename($_FILES['main_image']['name']);
        
        if (!move_uploaded_file($_FILES['main_image']['tmp_name'], $imagePath)) {
            $error_message = "Nie udało się przesłać pliku.";
        }
    }

    // Jeżeli nie wystąpiły żadne błędy w przesyłaniu pliku
    if (empty($error_message)) {
        $product_id = 1; // Zakładając, że zawsze dodajesz wariacje do produktu o ID 1
        $stmt = $conn->prepare("INSERT INTO variations (product_id, title, main_image, ean, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("isss", $product_id, $title, $imagePath, $ean);

        // Wykonanie zapytania
        if ($stmt->execute()) {
            $success = true;
            $response_message = 'Wariacja została dodana pomyślnie!';
        } else {
            $error_message = 'Błąd SQL: ' . $stmt->error;
        }
    }
}

// Zwracanie odpowiedzi w formacie JSON
if ($success) {
    $response = array(
        'success' => true,
        'message' => $response_message
    );
} else {
    if (empty($error_message)) {
        $error_message = "Nieznany błąd."; // Default error message
    }
    
    $response = array(
        'success' => false,
        'message' => $error_message
    );
}

echo json_encode($response); // Send JSON response

// Zamknięcie połączenia z bazą danych
$conn->close();
?>
