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

    if ($stmt->execute()) {
        echo "Wariacja została dodana.";
    } else {
        echo "Błąd: " . $stmt->error;
    }

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Wariacja została dodana pomyślnie.']);

}
?>
