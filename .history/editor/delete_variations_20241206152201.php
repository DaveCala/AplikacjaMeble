<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $ean = $_POST['ean'];
    $image = $_FILES['image'];

    // Walidacja danych
    if (empty($title) || empty($ean) || empty($image['name'])) {
        echo json_encode(['success' => false, 'message' => 'Wszystkie pola są wymagane.']);
        exit;
    }

    // Przesyłanie obrazu na serwer
    $imagePath = 'uploads/' . basename($image['name']);
    move_uploaded_file($image['tmp_name'], $imagePath);

    // Możesz dodać dane do bazy danych, np.:
    // $sql = "INSERT INTO wariacje (title, ean, image) VALUES ('$title', '$ean', '$imagePath')";
    // mysqli_query($conn, $sql);

    // Zwracanie odpowiedzi w formacie JSON
    echo json_encode([
        'success' => true,
        'newVariation' => [
            'title' => $title,
            'ean' => $ean,
            'image' => $imagePath
        ]
    ]);
}
?>
