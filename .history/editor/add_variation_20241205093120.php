<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $title = $_POST['title'];
    $main_image = $_POST['main_image'];
    $ean = $_POST['ean'];

    $sql = "INSERT INTO variations (product_id, title, main_image, ean, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isss', $product_id, $title, $main_image, $ean);

    if ($_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        $imagePath = 'uploads/' . basename($_FILES['main_image']['name']);
        move_uploaded_file($_FILES['main_image']['tmp_name'], $imagePath);
    } else {
        echo json_encode(['success' => false, 'message' => 'Błąd podczas przesyłania pliku.']);
        exit;
    }
    

    if ($stmt->execute()) {
        echo "Wariacja została dodana.";
    } else {
        echo "Błąd: " . $stmt->error;
    }

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Wariacja została dodana pomyślnie.']);

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

}
?>
