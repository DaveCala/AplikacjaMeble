<?php
require_once '../db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $imagePath = null;

    // Sprawdzenie obecności pola "is_variation"
    if (!isset($_POST['is_variation'])) {
        $response['message'] = 'Pole "Produkt z wariacjami" jest wymagane.';
        echo json_encode($response);
        exit;
    }

    $is_variation = $_POST['is_variation'] === 'true' ? 1 : 0;

    // Walidacja danych
    if (empty($title) || empty($category)) {
        $response['message'] = 'Wszystkie pola muszą być wypełnione.';
        echo json_encode($response);
        exit;
    }

    // Obsługa pliku obrazu
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../img/";
        if (!is_dir($targetDir) || !is_writable($targetDir)) {
            $response['message'] = 'Folder do przesyłania obrazów nie istnieje lub brak uprawnień.';
            echo json_encode($response);
            exit;
        }

        $imageFileName = time() . '_' . basename($_FILES['image']['name']);
        $targetFilePath = $targetDir . $imageFileName;

        // Sprawdzamy typ pliku
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        if (!in_array($fileType, $allowedTypes)) {
            $response['message'] = 'Nieprawidłowy format pliku. Dozwolone: JPG, JPEG, PNG, GIF.';
            echo json_encode($response);
            exit;
        }

        // Przesyłanie pliku
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            $response['message'] = 'Błąd przesyłania pliku: ' . $_FILES['image']['error'];
            echo json_encode($response);
            exit;
        }

        $imagePath = $imageFileName;
    }

    // Dodanie produktu do bazy danych
    try {
        $stmt = $pdo->prepare("
            INSERT INTO products (title, category, image, isVariation) 
            VALUES (:title, :category, :image, :isVariation)
        ");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':image', $imagePath);
        $stmt->bindValue(':isVariation', $is_variation, PDO::PARAM_INT);

        if ($stmt
