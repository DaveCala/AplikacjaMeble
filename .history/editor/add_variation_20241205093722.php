<?php
header('Content-Type: application/json');

$conn = new mysqli('localhost', 'username', 'password', 'database');
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Błąd połączenia z bazą danych.']);
    exit;
}

$title = $_POST['title'] ?? '';
$ean = $_POST['ean'] ?? '';
$imagePath = null;

if (!empty($_FILES['main_image']['name'])) {
    $targetDir = 'uploads/';
    $imagePath = $targetDir . basename($_FILES['main_image']['name']);
    move_uploaded_file($_FILES['main_image']['tmp_name'], $imagePath);
}

$stmt = $conn->prepare("INSERT INTO variations (product_id, title, main_image, ean, created_at) VALUES (1, ?, ?, ?, NOW())");
$stmt->bind_param("sss", $title, $imagePath, $ean);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'title' => $title, 'ean' => $ean, 'image' => basename($imagePath)]);
} else {
    echo json_encode(['success' => false, 'message' => 'Błąd podczas dodawania wariacji.']);
}

$stmt->close();
$conn->close();
