<?php
require_once '../db.php'; // Połączenie do bazy danych

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'];
    $title = $_POST['title'];
    $ean = $_POST['ean'];
    $mainImage = $_FILES['main_image'];

    // Upload image
    $uploadDir = '../img/';
    $imageName = time() . '_' . basename($mainImage['name']);
    $uploadFile = $uploadDir . $imageName;

    if (move_uploaded_file($mainImage['tmp_name'], $uploadFile)) {
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO variations (product_id, title, main_image, ean, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param('isss', $productId, $title, $imageName, $ean);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Variation added successfully!', 'data' => [
                'id' => $stmt->insert_id,
                'title' => $title,
                'ean' => $ean,
                'main_image' => $imageName,
                'created_at' => date('Y-m-d H:i:s')
            ]]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to insert into database.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Image upload failed.']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $productId = $_GET['product_id'];
    $stmt = $conn->prepare("SELECT * FROM variations WHERE product_id = ?");
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $variations = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode(['status' => 'success', 'data' => $variations]);
    $stmt->close();
}
$conn->close();
?>
