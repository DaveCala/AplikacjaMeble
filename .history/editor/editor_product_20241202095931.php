<?php
require_once '../db.php';

// Sprawdzenie ID produktu
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Nieprawidłowe ID produktu.');
}

$productId = (int)$_GET['id'];

// Pobranie danych produktu
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        die('Produkt nie został znaleziony.');
    }
} catch (PDOException $e) {
    die('Błąd bazy danych: ' . $e->getMessage());
}

// Pobranie wariacji
// $variations = [];
// try {
//     $stmt = $pdo->prepare("SELECT * FROM variations WHERE product_id = :product_id");
//     $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
//     $stmt->execute();
//     $variations = $stmt->fetchAll(PDO::FETCH_ASSOC);
// } catch (PDOException $e) {
//     die('Błąd bazy danych: ' . $e->getMessage());
// }
?>



<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edytuj Produkt</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-800 text-white">
  <div class="container mx-auto p-6">
    <h1 class="text-3xl mb-6">Edytuj Produkt</h1>
    <form id="edit-product-form" enctype="multipart/form-data">
      <!-- Ukryte pole przechowujące ID produktu -->
      <input type="hidden" id="product-id" name="product_id" value="<?php echo htmlspecialchars($product['id'] ?? 0); ?>">

      <!-- Tytuł produktu -->
      <div class="mb-4">
        <label for="edit-product-title" class="block mb-2 text-sm">Tytuł produktu:</label>
        <input type="text" id="edit-product-title" name="title"
               value="<?php echo htmlspecialchars($product['title'] ?? ''); ?>"
               class="w-full p-3 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>

      <!-- Kategoria produktu -->
      <div class="mb-4">
        <label for="edit-product-category" class="block mb-2 text-sm">Kategoria:</label>
        <input type="text" id="edit-product-category" name="category"
               value="<?php echo htmlspecialchars($product['category'] ?? ''); ?>"
               class="w-full p-3 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>

      <!-- Zdjęcie produktu -->
      <div class="mb-4">
        <label for="edit-product-image" class="block mb-2 text-sm">Zdjęcie produktu:</label>
        <input type="file" id="edit-product-image" name="image"
               class="block w-full text-sm text-gray-300 bg-gray-700 border border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500">
      </div>

      <button type="button" id="save-product-details"
              class="py-3 px-6 bg-green-600 rounded-lg text-white text-lg hover:bg-green-500">
        Zapisz zmiany
      </button>
    </form>
  </div>

  <script>
    document.getElementById('save-product-details').addEventListener('click', () => {
      const form = document.getElementById('edit-product-form');
      const formData = new FormData(form);

      fetch('update_product.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Produkt został zaktualizowany.');
          location.reload();
        } else {
          alert('Błąd: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Błąd:', error);
        alert('Wystąpił błąd podczas zapisywania.');
      });
    });
  </script>
</body>
</html>


