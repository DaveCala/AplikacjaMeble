<?php
require_once '../db.php'; // Połączenie do bazy danych

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Błąd połączenia z bazą danych.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? null;
    $ean = $_POST['ean'] ?? null;
    $productId = $_POST['product_id'] ?? null;
    $image = $_FILES['image'] ?? null;

    if ($title && $ean && $productId && $image) {
        $imagePath = '../img/' . basename($image['name']);
        move_uploaded_file($image['tmp_name'], $imagePath);

        $query = $pdo->prepare("INSERT INTO variations (product_id, title, ean, main_image) VALUES (:product_id, :title, :ean, :main_image)");
        $query->execute([
            'product_id' => $productId,
            'title' => $title,
            'ean' => $ean,
            'main_image' => basename($image['name']),
        ]);

        echo json_encode([
            'success' => true,
            'newVariation' => [
                'title' => $title,
                'ean' => $ean,
                'image' => basename($image['name']),
            ],
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Niekompletne dane.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Nieprawidłowe zapytanie.']);
}
