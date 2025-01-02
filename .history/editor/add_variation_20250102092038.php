<?php
require_once '../db.php'; // Połączenie do bazy danych

header('Content-Type: application/json');

try {
    // Połączenie z bazą danych
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Błąd połączenia z bazą danych: ' . $e->getMessage()]);
    exit;
}

// Sprawdzenie, czy dane zostały przesłane
if (!isset($_POST['title'], $_POST['ean'], $_POST['product_id'], $_POST['price'], $_POST['description'])) {
    echo json_encode(['success' => false, 'message' => 'Brak wymaganych danych (title, ean, product_id, price, description).']);
    exit;
}

$title = htmlspecialchars(trim($_POST['title']));
$ean = htmlspecialchars(trim($_POST['ean']));
$productId = (int) $_POST['product_id'];
$price = (float) $_POST['price'];
$description = htmlspecialchars(trim($_POST['description']));

// Obsługa przesyłania zdjęcia
$imageName = null;
if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
    $imageName = basename($_FILES['main_image']['name']);
    $targetPath = "../img/" . $imageName;

    // Walidacja typu pliku
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($_FILES['main_image']['type'], $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Nieprawidłowy typ pliku. Dozwolone są tylko obrazy JPG, PNG i GIF.']);
        exit;
    }

    // Przeniesienie przesłanego pliku
    if (!move_uploaded_file($_FILES['main_image']['tmp_name'], $targetPath)) {
        echo json_encode(['success' => false, 'message' => 'Nie udało się przesłać zdjęcia.']);
        exit;
    }
}

try {
    // Przygotowanie zapytania SQL
    $sql = "
        INSERT INTO variations (product_id, title, ean, price, description, main_image) 
        VALUES (:product_id, :title, :ean, :price, :description, :main_image)
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':product_id' => $productId,
        ':title' => $title,
        ':ean' => $ean,
        ':price' => $price,
        ':description' => $description,
        ':main_image' => $imageName
    ]);

    echo json_encode(['success' => true, 'message' => 'Wariacja została dodana']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Błąd podczas zapisu do bazy danych: ' . $e->getMessage()]);
}

// Debugging (możesz usunąć w środowisku produkcyjnym)
var_dump($_POST);
var_dump($_FILES);
exit;