<?php
// Połączenie z bazą danych (zastąp danymi do swojego serwera)
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'app_test';

// Tworzenie połączenia
$conn = new mysqli($host, $user, $pass, $dbname);

// Sprawdzanie połączenia
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Zapytanie, aby pobrać nazwy kolumn z tabeli, z wykluczeniem określonych
$query = "
    SELECT COLUMN_NAME
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_NAME = 'products'
    AND TABLE_SCHEMA = 'app_test'
    AND COLUMN_NAME NOT IN ('id', 'title', 'category', 'image', 'description', 'isVariation', 'price')
";

$result = $conn->query($query);

$columns = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['COLUMN_NAME']; // Zwraca nazwy kolumn
    }
}

// Zwrócenie wyników w formacie JSON
echo json_encode($columns);

$conn->close();
?>
