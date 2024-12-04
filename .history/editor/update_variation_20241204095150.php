if (isset($_POST['title'], $_POST['ean'], $_FILES['main_image'])) {
    $title = $_POST['title'];
    $ean = $_POST['ean'];
    $image = $_FILES['main_image']['name'];
    $variationId = $_POST['variation_id'];

    // Aktualizacja bazy danych
    $stmt = $pdo->prepare("UPDATE variations SET title = :title, ean = :ean, main_image = :image WHERE id = :id");
    $stmt->execute([
        ':title' => $title,
        ':ean' => $ean,
        ':image' => $image,
        ':id' => $variationId
    ]);
    
    // Przesunięcie zdjęcia
    move_uploaded_file($_FILES['main_image']['tmp_name'], "../img/$image");

    echo json_encode(['success' => true, 'updatedTitle' => $title, 'updatedEAN' => $ean, 'updatedImage' => $image]);
}
