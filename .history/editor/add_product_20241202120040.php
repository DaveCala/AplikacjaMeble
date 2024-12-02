<?php
require_once '../db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $category = $_POST['category'] ?? '';
    $imagePath = null;

    // Walidacja danych
    if (empty($title) || empty($category)) {
        $response['message'] = 'Wszystkie pola muszą być wypełnione.';
        echo json_encode($response);
        exit;
    }

    // Walidacja obrazu
    if (!empty($_FILES['image']['name'])) {
        $file = $_FILES['image'];
        $targetDir = "../img/";
        $imageFileName = basename($file['name']);
        $targetFilePath = $targetDir . $imageFileName;

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        
        if (!in_array($fileType, $allowedTypes)) {
            $response['message'] = 'Nieprawidłowy format pliku. Dozwolone formaty: jpg, jpeg, png, gif.';
            echo json_encode($response);
            exit;
        }

        // Sprawdzamy rozmiar pliku (maksymalnie 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            $response['message'] = 'Plik jest za duży.
