<?php
// Połączenie z bazą danych
require_once '../db.php';

$response = ['success' => false, 'message' => ''];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    ]);
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Błąd połączenia z bazą danych: ' . $e->getMessage()]));
}

// Pobieranie danych produktu
$productId = $_GET['id'] ?? 0;

if ($productId) {
    $queryProduct = $pdo->prepare("SELECT * FROM products WHERE id = :productId");
    $queryProduct->execute(['productId' => $productId]);
    $product = $queryProduct->fetch(PDO::FETCH_ASSOC);

    // Sprawdzenie, czy produkt istnieje
    if ($product) {
        // Pobieranie wariacji powiązanych z produktem
        $queryVariations = $pdo->prepare("SELECT * FROM variations WHERE product_id = :productId");
        $queryVariations->execute(['productId' => $productId]);
        $variations = $queryVariations->fetchAll(PDO::FETCH_ASSOC);
    } else {
        die(json_encode(['success' => false, 'message' => 'Produkt nie został znaleziony.']));
    }
} else {
    die(json_encode(['success' => false, 'message' => 'Nie podano ID produktu.']));
}
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
      <!-- Formularz głównego produktu -->
      <input type="hidden" id="product-id" name="product_id" value="<?php echo htmlspecialchars($product['id'] ?? 0); ?>">
      <div class="mb-4">
        <label for="edit-product-title" class="block mb-2 text-sm">Tytuł produktu:</label>
        <input type="text" id="edit-product-title" name="title"
               value="<?php echo htmlspecialchars($product['title'] ?? ''); ?>"
               class="w-full p-3 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      <div class="mb-4">
        <label for="edit-product-category" class="block mb-2 text-sm">Kategoria:</label>
        <input type="text" id="edit-product-category" name="category"
               value="<?php echo htmlspecialchars($product['category'] ?? ''); ?>"
               class="w-full p-3 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      <div class="mb-4">
        <label for="edit-product-description" class="block mb-2 text-sm">Opis produktu:</label>
        <textarea id="edit-product-description" name="description" rows="5"
                  class="w-full p-3 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
      </div>
      <button type="button" id="save-product-details"
              class="py-3 px-6 bg-green-600 rounded-lg text-white text-lg hover:bg-green-500">
        Zapisz zmiany
      </button>
    </form>

  <!-- Lista wariacji -->
<div class="mt-8">
  <h2 class="text-2xl text-white mb-4">Wariacje</h2>
  <div class="grid grid-cols-1 gap-2 w-full">
    <?php foreach ($variations as $variation) : ?>
      <div class="bg-gray-900 p-4 border border-gray-700 rounded-lg shadow-md flex items-center">
        
        <!-- Checkbox do zaznaczenia wariacji -->
        <div class="flex items-center">
          <input type="checkbox" class="variation-checkbox" data-variation-id="<?php echo $variation['id']; ?>">
        </div>

        <!-- Zdjęcie wariacji - 1/6 szerokości -->
        <div class="w-1/6 flex justify-center">
          <img src="../img/<?php echo htmlspecialchars($variation['main_image']); ?>" 
               alt="Zdjęcie wariacji" 
               class="h-16 w-16 object-contain rounded-lg">
        </div>

        <!-- Tytuł wariacji - 3/6 szerokości -->
        <div class="w-3/6 px-4">
          <h3 class="text-white text-lg truncate"><?php echo htmlspecialchars($variation['title']); ?></h3>
          <p class="text-gray-400 text-sm truncate">EAN: <?php echo htmlspecialchars($variation['ean']); ?></p>
        </div>

        <!-- Przycisk "Obejrzyj" - 2/6 szerokości -->
        <div class="w-2/6 flex justify-end">
          <button class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 text-sm toggle-details" 
                  data-variation-id="<?php echo $variation['id']; ?>">
            Obejrzyj
          </button>
        </div>
      </div>

      <!-- Miejsce na szczegóły wariacji -->
      <div id="details-<?php echo $variation['id']; ?>" class="hidden mt-4 p-4 bg-gray-800 rounded-lg">
        <p class="text-white">Opis: Szczegółowy opis wariacji <?php echo $variation['title']; ?>.</p>
        <p class="text-gray-400">Cena: 100 PLN</p>
        <p class="text-gray-400">Dostępność: 10 sztuk</p>
      </div>
    <?php endforeach; ?>
  </div>
</div>


  <script>
    // Obsługa zapisania danych produktu
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

   // Obsługa rozwijania szczegółów po kliknięciu przycisku "Obejrzyj"
document.querySelectorAll('.toggle-details').forEach(button => {
  button.addEventListener('click', () => {
    const variationId = button.getAttribute('data-variation-id');
    const detailsDiv = document.getElementById(`details-${variationId}`);
    
    // Przełączanie widoczności szczegółów
    if (detailsDiv.classList.contains('hidden')) {
      detailsDiv.classList.remove('hidden');
    } else {
      detailsDiv.classList.add('hidden');
    }
  });
});


  </script>
</body>
</html>
