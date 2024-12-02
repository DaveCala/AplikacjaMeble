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
$variations = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM variations WHERE product_id = :product_id");
    $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $variations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Błąd bazy danych: ' . $e->getMessage());
}
?>



<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Szczegóły Produktu</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-800 text-white">
  <div class="container mx-auto p-6">
  <div id="product-details" class="bg-gray-900 p-6 rounded-lg shadow-lg">
  <h1 class="text-3xl mb-6">Szczegóły Produktu</h1>

  <!-- Ukryte pole przechowujące ID produktu -->
  <input type="hidden" id="product-id" value="<?php echo htmlspecialchars($product['id'] ?? 0); ?>">

  <!-- Sekcja: Szczegóły produktu ułożone poziomo -->
  <div class="flex flex-wrap lg:flex-nowrap gap-6 items-start">
    <!-- Główne zdjęcie produktu -->
    <div class="flex-shrink-0">
      <img id="product-image" 
           src="../img/<?php echo htmlspecialchars($product['image'] ?? ''); ?>" 
           alt="Zdjęcie produktu" 
           class="w-full max-w-xs h-auto max-h-96 object-contain bg-gray-800 p-4 rounded-lg">
      <div class="mt-4">
        <label class="block mb-2 text-sm">Zmień zdjęcie:</label>
        <input type="file" id="edit-product-image" name="product-image" 
               class="block w-full text-sm text-gray-300 bg-gray-700 border border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500">
      </div>
    </div>

    <!-- Informacje o produkcie -->
    <div class="flex-1">
      <div class="mb-4">
        <label for="edit-product-title" class="block mb-2 text-sm">Tytuł produktu:</label>
        <input type="text" id="edit-product-title" name="product-title" 
               value="<?php echo htmlspecialchars($product['title'] ?? ''); ?>" 
               class="w-full p-3 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>

      <div class="mb-4">
        <label for="edit-product-category" class="block mb-2 text-sm">Kategoria:</label>
        <input type="text" id="edit-product-category" name="product-category" 
               value="<?php echo htmlspecialchars($product['category'] ?? ''); ?>" 
               class="w-full p-3 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>

      <button id="save-product-details" 
              class="py-3 px-6 bg-green-600 rounded-lg text-white text-lg hover:bg-green-500">
        Zapisz zmiany
      </button>
    </div>
  </div>
</div>

    <!-- Sekcja: Wariacje Produktu -->
    <div id="product-variations" class="mt-10">
      <h2 class="text-2xl mb-6">Wariacje Produktu:</h2>
      <?php
      $variationsQuery = "SELECT * FROM variations WHERE product_id = $productId";
      $variationsResult = $conn->query($variationsQuery);
      if ($variationsResult->num_rows > 0) {
          while ($variation = $variationsResult->fetch_assoc()) {
              ?>
              <div class="bg-gray-900 p-4 mb-4 rounded-lg shadow">
                <h3 class="text-xl"><?php echo htmlspecialchars($variation['title']); ?></h3>
                <p><strong>Opis:</strong> <?php echo htmlspecialchars($variation['description']); ?></p>
                <p><strong>Cena:</strong> <?php echo htmlspecialchars($variation['price']); ?></p>
                <p><strong>EAN:</strong> <?php echo htmlspecialchars($variation['ean']); ?></p>
              </div>
              <?php
          }
      } else {
          echo "<p>Brak wariacji dla tego produktu.</p>";
      }
      ?>
    </div>
  </div>

  <script>
    document.getElementById('save-product-details').addEventListener('click', function () {
    const title = document.getElementById('edit-product-title').value.trim();
    const category = document.getElementById('edit-product-category').value.trim();
    const imageInput = document.getElementById('edit-product-image');
    const productId = document.getElementById('product-id').value;

    const formData = new FormData();
    formData.append('title', title);
    formData.append('category', category);
    formData.append('product_id', productId); // Dodajemy id produktu
    if (imageInput.files.length > 0) {
        formData.append('image', imageInput.files[0]);
    }

    fetch('/update_product.php', {
        method: 'POST',
        body: formData, // Wysyłanie danych za pomocą POST
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




      fetch('/update_product.php', {
        method: 'POST',
        body: formData,
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
  </script>
</body>
</html>

