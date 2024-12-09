<?php
require_once '../db.php'; // Połączenie do bazy danych

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'];
    $title = $_POST['title'];
    $ean = $_POST['ean'];
    $mainImage = $_FILES['main_image'];

    $uploadDir = '../img/';
    $imageName = time() . '_' . basename($mainImage['name']);
    $uploadFile = $uploadDir . $imageName;

    if (move_uploaded_file($mainImage['tmp_name'], $uploadFile)) {
        $stmt = $conn->prepare("INSERT INTO variations (product_id, title, main_image, ean, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param('isss', $productId, $title, $imageName, $ean);

        if ($stmt->execute()) {
            $variationId = $stmt->insert_id;
            echo json_encode(['status' => 'success', 'variation_id' => $variationId]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Błąd zapisu w bazie.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Błąd przesyłania zdjęcia.']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $productId = $_GET['product_id'];
    $stmt = $conn->prepare("SELECT * FROM variations WHERE product_id = ?");
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $variations = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode(['status' => 'success', 'variations' => $variations]);
    $stmt->close();
}
$conn->close();
?>