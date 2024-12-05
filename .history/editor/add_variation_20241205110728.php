<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sprawdź czy wszystkie dane zostały przesłane
    if (!empty($_POST['product_id']) && !empty($_POST['title']) && !empty($_POST['ean']) && isset($_FILES['main_image'])) {
        $product_id = intval($_POST['product_id']); // Dynamiczne ID produktu
        $title = htmlspecialchars($_POST['title']);
        $ean = htmlspecialchars($_POST['ean']);

        // Obsługa uploadu pliku
        $uploadDir = 'uploads/';
        $fileName = basename($_FILES['main_image']['name']);
        $uploadPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['main_image']['tmp_name'], $uploadPath)) {
            // Dodaj dane do bazy
            $sql = "INSERT INTO variations (product_id, title, main_image, ean, created_at) 
                    VALUES (:product_id, :title, :main_image, :ean, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':product_id' => $product_id,
                ':title' => $title,
                ':main_image' => $fileName,
                ':ean' => $ean
            ]);

            echo "Wariacja została dodana.";
        } else {
            echo "Nie udało się przesłać pliku.";
        }
    } else {
        echo "Proszę uzupełnić wszystkie pola.";
    }
}
?>
